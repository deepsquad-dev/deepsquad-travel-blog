<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MediaItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('media_items');
        $this->setDisplayField('original_name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Pins', ['foreignKey' => 'pin_id', 'joinType' => 'INNER']);
        $this->belongsTo('Users', ['foreignKey' => 'user_id', 'joinType' => 'INNER']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->inList('kind', ['image', 'video'])
            ->scalar('filename')
            ->requirePresence('filename', 'create')
            ->notEmptyString('filename')
            ->scalar('original_name')
            ->requirePresence('original_name', 'create')
            ->notEmptyString('original_name')
            ->scalar('mime')
            ->requirePresence('mime', 'create')
            ->notEmptyString('mime')
            ->integer('filesize');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['pin_id'], 'Pins'), ['errorField' => 'pin_id']);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
