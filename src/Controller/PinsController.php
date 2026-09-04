<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;

class PinsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->FormProtection->setConfig('unlockedFields', ['media']);
    }

    public function add(?string $tripSlug = null)
    {
        [$trip, $userId] = $this->requireEditableTrip((string)$tripSlug);
        $pin = $this->fetchTable('Pins')->newEmptyEntity();

        if ($this->request->is('post')) {
            $pin = $this->fetchTable('Pins')->patchEntity($pin, $this->request->getData());
            $pin->trip_id = $trip->id;
            $pin->user_id = $userId;

            if ($this->fetchTable('Pins')->save($pin)) {
                $this->handleUpload($pin->id, $userId);
                $this->Flash->success('Pin added to the map.');

                return $this->redirect(['controller' => 'Trips', 'action' => 'view', $trip->slug]);
            }
            $this->Flash->error('Could not save the pin.');
        }

        $lat = $this->request->getQuery('lat');
        $lng = $this->request->getQuery('lng');
        if ($lat !== null && $lng !== null && !$this->request->is('post')) {
            $pin->latitude = $lat;
            $pin->longitude = $lng;
        }

        $this->set(compact('trip', 'pin'));
    }

    public function edit(?string $tripSlug = null, ?string $id = null)
    {
        [$trip, $userId] = $this->requireEditableTrip((string)$tripSlug);
        $pins = $this->fetchTable('Pins');
        $pin = $pins->find()
            ->where(['Pins.id' => (int)$id, 'Pins.trip_id' => $trip->id])
            ->contain(['MediaItems'])
            ->first();

        if (!$pin) {
            throw new NotFoundException('Pin not found.');
        }

        if ($this->request->is(['post', 'put', 'patch'])) {
            $pin = $pins->patchEntity($pin, $this->request->getData());
            if ($pins->save($pin)) {
                $this->handleUpload($pin->id, $userId);
                $this->Flash->success('Pin updated.');

                return $this->redirect(['controller' => 'Trips', 'action' => 'view', $trip->slug]);
            }
            $this->Flash->error('Could not update the pin.');
        }

        $this->set(compact('trip', 'pin'));
    }

    public function delete(?string $tripSlug = null, ?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        [$trip] = $this->requireEditableTrip((string)$tripSlug);
        $pins = $this->fetchTable('Pins');
        $pin = $pins->find()
            ->where(['Pins.id' => (int)$id, 'Pins.trip_id' => $trip->id])
            ->contain(['MediaItems'])
            ->first();

        if (!$pin) {
            throw new NotFoundException('Pin not found.');
        }

        foreach ($pin->media_items ?? [] as $media) {
            $path = WWW_ROOT . 'uploads' . DS . $media->filename;
            if (is_file($path)) {
                @unlink($path);
            }
        }

        if ($pins->delete($pin)) {
            $this->Flash->success('Pin deleted.');
        } else {
            $this->Flash->error('Could not delete the pin.');
        }

        return $this->redirect(['controller' => 'Trips', 'action' => 'view', $trip->slug]);
    }

    public function deleteMedia(?string $tripSlug = null, ?string $pinId = null, ?string $mediaId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        [$trip] = $this->requireEditableTrip((string)$tripSlug);
        $mediaItems = $this->fetchTable('MediaItems');
        $media = $mediaItems->find()
            ->contain(['Pins'])
            ->where([
                'MediaItems.id' => (int)$mediaId,
                'MediaItems.pin_id' => (int)$pinId,
            ])
            ->first();

        if (!$media || (int)$media->pin->trip_id !== (int)$trip->id) {
            throw new NotFoundException('Media not found.');
        }

        $path = WWW_ROOT . 'uploads' . DS . $media->filename;
        if (is_file($path)) {
            @unlink($path);
        }

        if ($mediaItems->delete($media)) {
            $this->Flash->success('Media removed.');
        } else {
            $this->Flash->error('Could not remove media.');
        }

        return $this->redirect([
            'controller' => 'Pins',
            'action' => 'edit',
            $trip->slug,
            $pinId,
        ]);
    }

    /**
     * @return array{0: \App\Model\Entity\Trip, 1: int}
     */
    private function requireEditableTrip(string $tripSlug): array
    {
        $trips = $this->fetchTable('Trips');
        $trip = $trips->find()->where(['slug' => $tripSlug])->first();
        if (!$trip) {
            throw new NotFoundException('Trip not found.');
        }

        $userId = $this->identityId();
        if (!$trips->userCanEdit($trip->id, $userId)) {
            throw new ForbiddenException('You cannot edit pins on this trip.');
        }

        return [$trip, $userId];
    }

    private function handleUpload(int $pinId, int $userId): void
    {
        $file = $this->request->getData('media');
        if (!$file || !is_object($file) || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return;
        }
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $this->Flash->warning('Media upload failed.');

            return;
        }

        $mime = $file->getClientMediaType() ?: 'application/octet-stream';
        $kind = str_starts_with($mime, 'video/') ? 'video' : 'image';
        if (!str_starts_with($mime, 'image/') && !str_starts_with($mime, 'video/')) {
            $this->Flash->warning('Only image or video uploads are allowed.');

            return;
        }

        $ext = pathinfo($file->getClientFilename() ?? 'upload.bin', PATHINFO_EXTENSION) ?: 'bin';
        $filename = Text::uuid() . '.' . strtolower($ext);
        $target = WWW_ROOT . 'uploads' . DS . $filename;
        $file->moveTo($target);

        $this->fetchTable('MediaItems')->saveOrFail($this->fetchTable('MediaItems')->newEntity([
            'pin_id' => $pinId,
            'user_id' => $userId,
            'kind' => $kind,
            'filename' => $filename,
            'original_name' => $file->getClientFilename() ?? $filename,
            'mime' => $mime,
            'filesize' => (int)filesize($target),
        ]));
    }
}
