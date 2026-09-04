<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Pin extends Entity
{
    protected array $_accessible = [
        'trip_id' => true,
        'user_id' => true,
        'title' => true,
        'body' => true,
        'place_name' => true,
        'latitude' => true,
        'longitude' => true,
        'taken_at' => true,
        'sort_order' => true,
        'trip' => true,
        'user' => true,
        'media_items' => true,
    ];
}
