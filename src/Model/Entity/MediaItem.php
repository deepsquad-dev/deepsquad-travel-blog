<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class MediaItem extends Entity
{
    protected array $_accessible = [
        'pin_id' => true,
        'user_id' => true,
        'kind' => true,
        'filename' => true,
        'original_name' => true,
        'mime' => true,
        'filesize' => true,
        'pin' => true,
        'user' => true,
    ];

    public function webPath(): string
    {
        return '/uploads/' . $this->filename;
    }
}
