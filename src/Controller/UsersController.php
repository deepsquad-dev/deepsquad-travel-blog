<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login']);
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post', 'head']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $redirect = $this->request->getQuery('redirect', [
                'controller' => 'Trips',
                'action' => 'index',
            ]);

            return $this->redirect($redirect);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Invalid email or password.');
        }
    }

    public function logout()
    {
        $this->Authentication->logout();
        $this->Flash->success('You are logged out.');

        return $this->redirect(['action' => 'login']);
    }
}
