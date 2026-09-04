<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->hasMany('Trips', ['foreignKey' => 'user_id']);
        $this->hasMany('TripMembers', ['foreignKey' => 'user_id']);
        $this->hasMany('Pins', ['foreignKey' => 'user_id']);
        $this->hasMany('MediaItems', ['foreignKey' => 'user_id']);
        $this->hasMany('Invites', [
            'foreignKey' => 'invited_by_user_id',
            'className' => 'Invites',
        ]);
        $this->hasMany('Posts', ['foreignKey' => 'user_id']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->email('email', false, 'Enter a valid email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->scalar('name')
            ->maxLength('name', 120)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->scalar('password')
            ->minLength('password', 8, 'Use at least 8 characters')
            ->requirePresence('password', 'create')
            ->notEmptyString('password')
            ->inList('role', ['owner', 'friend']);
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }
}
