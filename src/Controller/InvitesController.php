<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\GoneException;
use Cake\Http\Exception\NotFoundException;

class InvitesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['accept']);
        // Accept creates an account; skip FormProtection field hashing for signup.
        if ($this->request->getParam('action') === 'accept') {
            $this->FormProtection->setConfig('unlockedFields', ['name', 'password', 'password_confirm']);
        }
    }

    public function accept(?string $token = null)
    {
        $invites = $this->fetchTable('Invites');
        $invite = $invites->find()
            ->where(['token' => $token])
            ->contain(['Trips'])
            ->first();

        if (!$invite) {
            throw new NotFoundException('Invite not found.');
        }
        if (!$invite->isOpen()) {
            throw new GoneException('This invite is expired or already used.');
        }

        $users = $this->fetchTable('Users');
        $user = $users->find()->where(['email' => $invite->email])->first()
            ?? $users->newEmptyEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            if (($data['password'] ?? '') !== ($data['password_confirm'] ?? '')) {
                $this->Flash->error('Passwords do not match.');
            } else {
                $user = $users->patchEntity($user, [
                    'email' => $invite->email,
                    'name' => $data['name'] ?? $user->name,
                    'password' => $data['password'],
                    'role' => $user->isNew() ? 'friend' : $user->role,
                ]);

                if ($users->save($user)) {
                    $members = $this->fetchTable('TripMembers');
                    $existing = $members->find()->where([
                        'trip_id' => $invite->trip_id,
                        'user_id' => $user->id,
                    ])->first();

                    if (!$existing) {
                        $members->saveOrFail($members->newEntity([
                            'trip_id' => $invite->trip_id,
                            'user_id' => $user->id,
                            'role' => $invite->role,
                        ]));
                    }

                    $invite->accepted_at = new \Cake\I18n\DateTime();
                    $invites->saveOrFail($invite);

                    $this->Authentication->setIdentity($user);
                    $this->Flash->success('Welcome aboard. You can explore the trip map now.');

                    return $this->redirect([
                        'controller' => 'Trips',
                        'action' => 'view',
                        $invite->trip->slug,
                    ]);
                }
                $this->Flash->error('Could not create your account.');
            }
        }

        $this->set(compact('invite', 'user'));
    }
}
