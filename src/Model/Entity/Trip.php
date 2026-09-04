<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $summary
 * @property string $visibility
 */
class Trip extends Entity
{
    protected array $_accessible = [
        'user_id' => true,
        'title' => true,
        'slug' => true,
        'summary' => true,
        'visibility' => true,
        'starts_on' => true,
        'ends_on' => true,
        'user' => true,
        'trip_members' => true,
        'pins' => true,
        'invites' => true,
    ];
}
