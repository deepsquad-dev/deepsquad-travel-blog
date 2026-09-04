<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\DateTime;

/**
 * @property \App\Model\Table\TripsTable $Trips
 */
class TripsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Trips = $this->fetchTable('Trips');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['view']);
    }

    public function index()
    {
        $userId = $this->identityId();
        $trips = $this->Trips->find('accessible', userId: $userId)
            ->contain(['Users', 'Pins', 'TripMembers'])
            ->orderBy(['Trips.modified' => 'DESC'])
            ->all();

        $this->set(compact('trips'));
    }

    public function view(?string $slug = null)
    {
        $trip = $this->Trips->find()
            ->where(['Trips.slug' => $slug])
            ->contain([
                'Users',
                'Pins' => function ($q) {
                    return $q->contain(['MediaItems', 'Users'])
                        ->orderBy(['Pins.sort_order' => 'ASC', 'Pins.id' => 'ASC']);
                },
                'TripMembers' => ['Users'],
                'Invites' => function ($q) {
                    return $q->where(['Invites.accepted_at IS' => null])
                        ->orderBy(['Invites.created' => 'DESC']);
                },
            ])
            ->first();

        if (!$trip) {
            throw new NotFoundException('Trip not found.');
        }

        $userId = $this->optionalUserId();
        if (!$this->Trips->userCanView($trip->id, $userId)) {
            throw new ForbiddenException('You do not have access to this trip.');
        }

        $role = $userId !== null ? $this->Trips->memberRole($trip->id, $userId) : null;
        $canEdit = $role !== null && in_array($role, ['owner', 'editor'], true);
        $isOwner = $role === 'owner';
        $pinsJson = collection($trip->pins ?? [])->map(function ($pin) use ($canEdit, $trip) {
            return [
                'id' => $pin->id,
                'title' => $pin->title,
                'body' => $pin->body,
                'place_name' => $pin->place_name,
                'taken_at' => $pin->taken_at?->i18nFormat('d MMM yyyy'),
                'lat' => (float)$pin->latitude,
                'lng' => (float)$pin->longitude,
                'edit_url' => $canEdit
                    ? \Cake\Routing\Router::url([
                        'controller' => 'Pins',
                        'action' => 'edit',
                        $trip->slug,
                        $pin->id,
                    ])
                    : null,
                'media' => collection($pin->media_items ?? [])->map(fn ($m) => [
                    'kind' => $m->kind,
                    'url' => $m->webPath(),
                    'name' => $m->original_name,
                ])->toList(),
            ];
        })->toList();

        $this->set(compact('trip', 'canEdit', 'isOwner', 'role', 'pinsJson'));
    }

    public function add()
    {
        $userId = $this->identityId();
        $trip = $this->Trips->newEmptyEntity();

        if ($this->request->is('post')) {
            $trip = $this->Trips->patchEntity($trip, $this->request->getData());
            $trip->user_id = $userId;
            if ($this->Trips->save($trip)) {
                $this->Trips->TripMembers->saveOrFail($this->Trips->TripMembers->newEntity([
                    'trip_id' => $trip->id,
                    'user_id' => $userId,
                    'role' => 'owner',
                ]));
                $this->Flash->success('Trip created. Invite friends and drop pins on the map.');

                return $this->redirect(['action' => 'view', $trip->slug]);
            }
            $this->Flash->error('Could not create the trip.');
        }

        $this->set(compact('trip'));
    }

    public function edit(?string $slug = null)
    {
        $trip = $this->getOwnedTrip((string)$slug);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $trip = $this->Trips->patchEntity($trip, $this->request->getData());
            if ($this->Trips->save($trip)) {
                $this->Flash->success('Trip updated.');

                return $this->redirect(['action' => 'view', $trip->slug]);
            }
            $this->Flash->error('Could not update the trip.');
        }

        $this->set(compact('trip'));
    }

    public function delete(?string $slug = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trip = $this->getOwnedTrip((string)$slug);

        if ($this->Trips->delete($trip)) {
            $this->Flash->success('Trip deleted.');
        } else {
            $this->Flash->error('Could not delete the trip.');
        }

        return $this->redirect(['action' => 'index']);
    }

    public function invite(?string $slug = null)
    {
        $trip = $this->getOwnedTrip((string)$slug);
        $invitesTable = $this->fetchTable('Invites');
        $invite = $invitesTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $invite = $invitesTable->createForTrip(
                $trip->id,
                $this->identityId(),
                (string)$data['email'],
                (string)($data['role'] ?? 'editor'),
            );

            $acceptUrl = \Cake\Routing\Router::url([
                'controller' => 'Invites',
                'action' => 'accept',
                $invite->token,
            ], true);

            $this->Flash->success('Invite ready for ' . $invite->email . '. Share this link: ' . $acceptUrl);

            return $this->redirect(['action' => 'view', $trip->slug]);
        }

        $pendingInvites = $invitesTable->find()
            ->where([
                'trip_id' => $trip->id,
                'accepted_at IS' => null,
                'expires_at >' => new DateTime(),
            ])
            ->orderBy(['created' => 'DESC'])
            ->all();

        $this->set(compact('trip', 'invite', 'pendingInvites'));
    }

    public function revokeInvite(?string $slug = null, ?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trip = $this->getOwnedTrip((string)$slug);
        $invite = $this->fetchTable('Invites')->get((int)$id);
        if ((int)$invite->trip_id !== (int)$trip->id) {
            throw new NotFoundException('Invite not found on this trip.');
        }

        if ($this->fetchTable('Invites')->delete($invite)) {
            $this->Flash->success('Invite revoked.');
        } else {
            $this->Flash->error('Could not revoke invite.');
        }

        return $this->redirect(['action' => 'view', $trip->slug]);
    }

    public function updateMember(?string $slug = null, ?string $id = null)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $trip = $this->getOwnedTrip((string)$slug);
        $members = $this->fetchTable('TripMembers');
        $member = $members->get((int)$id, contain: ['Users']);

        if ((int)$member->trip_id !== (int)$trip->id) {
            throw new NotFoundException('Member not found on this trip.');
        }
        if ((int)$member->user_id === (int)$trip->user_id) {
            $this->Flash->error('The trip owner role cannot be changed here.');

            return $this->redirect(['action' => 'view', $trip->slug]);
        }

        $role = (string)$this->request->getData('role');
        if (!in_array($role, ['editor', 'viewer'], true)) {
            $this->Flash->error('Invalid role.');

            return $this->redirect(['action' => 'view', $trip->slug]);
        }

        $member = $members->patchEntity($member, ['role' => $role]);
        if ($members->save($member)) {
            $this->Flash->success('Updated role for ' . ($member->user->name ?? 'member') . '.');
        } else {
            $this->Flash->error('Could not update member.');
        }

        return $this->redirect(['action' => 'view', $trip->slug]);
    }

    public function removeMember(?string $slug = null, ?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trip = $this->getOwnedTrip((string)$slug);
        $members = $this->fetchTable('TripMembers');
        $member = $members->get((int)$id, contain: ['Users']);

        if ((int)$member->trip_id !== (int)$trip->id) {
            throw new NotFoundException('Member not found on this trip.');
        }
        if ((int)$member->user_id === (int)$trip->user_id || $member->role === 'owner') {
            $this->Flash->error('The trip owner cannot be removed.');

            return $this->redirect(['action' => 'view', $trip->slug]);
        }

        if ($members->delete($member)) {
            $this->Flash->success('Removed ' . ($member->user->name ?? 'member') . ' from the trip.');
        } else {
            $this->Flash->error('Could not remove member.');
        }

        return $this->redirect(['action' => 'view', $trip->slug]);
    }

    private function getOwnedTrip(string $slug): \App\Model\Entity\Trip
    {
        $trip = $this->Trips->find()->where(['slug' => $slug])->firstOrFail();
        if ((int)$trip->user_id !== $this->identityId()) {
            throw new ForbiddenException('Only the trip owner can do that.');
        }

        return $trip;
    }

    private function optionalUserId(): ?int
    {
        $identity = $this->Authentication->getIdentity();

        return $identity ? (int)$identity->getIdentifier() : null;
    }
}
