<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PinsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pins');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Trips', ['foreignKey' => 'trip_id', 'joinType' => 'INNER']);
        $this->belongsTo('Users', ['foreignKey' => 'user_id', 'joinType' => 'INNER']);
        $this->hasMany('MediaItems', ['foreignKey' => 'pin_id', 'dependent' => true]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->scalar('title')
            ->maxLength('title', 180)
            ->requirePresence('title', 'create')
            ->notEmptyString('title')
            ->scalar('body')
            ->allowEmptyString('body')
            ->scalar('place_name')
            ->maxLength('place_name', 255)
            ->allowEmptyString('place_name')
            ->decimal('latitude')
            ->requirePresence('latitude', 'create')
            ->notEmptyString('latitude')
            ->decimal('longitude')
            ->requirePresence('longitude', 'create')
            ->notEmptyString('longitude')
            ->dateTime('taken_at')
            ->allowEmptyDateTime('taken_at')
            ->integer('sort_order');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['trip_id'], 'Trips'), ['errorField' => 'trip_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
