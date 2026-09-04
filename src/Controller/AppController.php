<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('FormProtection');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->set('identity', $this->Authentication->getIdentity());
    }

    protected function requireIdentity(): \ArrayAccess|array
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity === null) {
            throw new \Cake\Http\Exception\UnauthorizedException('Please log in.');
        }

        return $identity;
    }

    protected function identityId(): int
    {
        return (int)$this->requireIdentity()->getIdentifier();
    }
}
