<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TripMembersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('trip_members');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Trips', ['foreignKey' => 'trip_id', 'joinType' => 'INNER']);
        $this->belongsTo('Users', ['foreignKey' => 'user_id', 'joinType' => 'INNER']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->integer('trip_id')
            ->requirePresence('trip_id', 'create')
            ->notEmptyString('trip_id')
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id')
            ->inList('role', ['owner', 'editor', 'viewer']);
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['trip_id'], 'Trips'), ['errorField' => 'trip_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->isUnique(['trip_id', 'user_id']), ['errorField' => 'user_id']);

        return $rules;
    }
}
