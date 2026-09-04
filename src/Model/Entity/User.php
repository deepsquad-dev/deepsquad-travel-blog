<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $role
 */
class User extends Entity
{
    protected array $_accessible = [
        'email' => true,
        'password' => true,
        'name' => true,
        'role' => true,
        'trips' => true,
        'trip_members' => true,
        'pins' => true,
        'media_items' => true,
    ];

    protected array $_hidden = [
        'password',
    ];

    protected function _setPassword(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

        return (new DefaultPasswordHasher())->hash($password);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}
