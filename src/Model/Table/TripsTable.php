<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class TripsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('trips');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', ['foreignKey' => 'user_id', 'joinType' => 'INNER']);
        $this->hasMany('TripMembers', ['foreignKey' => 'trip_id', 'dependent' => true]);
        $this->hasMany('Pins', ['foreignKey' => 'trip_id', 'dependent' => true]);
        $this->hasMany('Invites', ['foreignKey' => 'trip_id', 'dependent' => true]);
        $this->hasMany('Posts', ['foreignKey' => 'trip_id']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->scalar('title')
            ->maxLength('title', 180)
            ->requirePresence('title', 'create')
            ->notEmptyString('title')
            ->scalar('slug')
            ->maxLength('slug', 200)
            ->allowEmptyString('slug')
            ->scalar('summary')
            ->allowEmptyString('summary')
            ->inList('visibility', ['private', 'friends', 'public'])
            ->date('starts_on')
            ->allowEmptyDate('starts_on')
            ->date('ends_on')
            ->allowEmptyDate('ends_on');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->isUnique(['slug']), ['errorField' => 'slug']);

        return $rules;
    }

    public function beforeMarshal(\Cake\Event\EventInterface $event, \ArrayObject $data, \ArrayObject $options): void
    {
        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = strtolower(Text::slug((string)$data['title']));
        }
    }

    public function findAccessible(SelectQuery $query, int $userId): SelectQuery
    {
        $memberTripIds = $this->TripMembers->find()
            ->select(['trip_id'])
            ->where(['user_id' => $userId]);

        return $query->where([
            'OR' => [
                'Trips.user_id' => $userId,
                'Trips.id IN' => $memberTripIds,
                'Trips.visibility' => 'public',
            ],
        ]);
    }

    public function memberRole(int $tripId, int $userId): ?string
    {
        $trip = $this->get($tripId);
        if ($trip->user_id === $userId) {
            return 'owner';
        }

        $member = $this->TripMembers->find()
            ->where(['trip_id' => $tripId, 'user_id' => $userId])
            ->first();

        return $member?->role;
    }

    public function userCanView(int $tripId, ?int $userId): bool
    {
        $trip = $this->get($tripId);
        if ($trip->visibility === 'public') {
            return true;
        }
        if ($userId === null) {
            return false;
        }

        return $this->memberRole($tripId, $userId) !== null;
    }

    public function userCanEdit(int $tripId, int $userId): bool
    {
        $role = $this->memberRole($tripId, $userId);

        return in_array($role, ['owner', 'editor'], true);
    }
}
