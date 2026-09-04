<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $trip_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string|null $tags
 * @property string $status
 * @property string $visibility
 * @property \Cake\I18n\DateTime|null $published_at
 * @property \App\Model\Entity\User|null $user
 * @property \App\Model\Entity\Trip|null $trip
 */
class Post extends Entity
{
    protected array $_accessible = [
        'user_id' => true,
        'trip_id' => true,
        'title' => true,
        'slug' => true,
        'body' => true,
        'tags' => true,
        'status' => true,
        'visibility' => true,
        'published_at' => true,
        'user' => true,
        'trip' => true,
    ];

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * @return list<string>
     */
    public function tagList(): array
    {
        if ($this->tags === null || trim($this->tags) === '') {
            return [];
        }

        $tags = array_map('trim', explode(',', $this->tags));

        return array_values(array_filter($tags, static fn ($t) => $t !== ''));
    }
}
