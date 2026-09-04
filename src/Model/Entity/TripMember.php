<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class TripMember extends Entity
{
    protected array $_accessible = [
        'trip_id' => true,
        'user_id' => true,
        'role' => true,
        'trip' => true,
        'user' => true,
    ];

    public function canEdit(): bool
    {
        return in_array($this->role, ['owner', 'editor'], true);
    }
}
