<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Validation\Validator;

class InvitesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('invites');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Trips', ['foreignKey' => 'trip_id', 'joinType' => 'INNER']);
        $this->belongsTo('InvitedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'invited_by_user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->inList('role', ['editor', 'viewer'])
            ->dateTime('expires_at')
            ->requirePresence('expires_at', 'create')
            ->notEmptyDateTime('expires_at');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['trip_id'], 'Trips'), ['errorField' => 'trip_id']);
        $rules->add($rules->existsIn(['invited_by_user_id'], 'InvitedByUsers'), ['errorField' => 'invited_by_user_id']);
        $rules->add($rules->isUnique(['token']), ['errorField' => 'token']);

        return $rules;
    }

    public function createForTrip(int $tripId, int $invitedBy, string $email, string $role = 'editor'): \App\Model\Entity\Invite
    {
        $invite = $this->newEntity([
            'trip_id' => $tripId,
            'invited_by_user_id' => $invitedBy,
            'email' => strtolower(trim($email)),
            'token' => bin2hex(Security::randomBytes(24)),
            'role' => $role,
            'expires_at' => new \Cake\I18n\DateTime('+14 days'),
        ]);

        return $this->saveOrFail($invite);
    }
}
