<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Invite extends Entity
{
    protected array $_accessible = [
        'trip_id' => true,
        'invited_by_user_id' => true,
        'email' => true,
        'token' => true,
        'role' => true,
        'expires_at' => true,
        'accepted_at' => true,
        'trip' => true,
        'invited_by_user' => true,
    ];

    public function isOpen(): bool
    {
        return $this->accepted_at === null
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }
}
