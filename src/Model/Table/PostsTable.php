<?php
declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class PostsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('posts');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Trips', [
            'foreignKey' => 'trip_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->scalar('title')
            ->maxLength('title', 200)
            ->requirePresence('title', 'create')
            ->notEmptyString('title')
            ->scalar('slug')
            ->maxLength('slug', 220)
            ->allowEmptyString('slug')
            ->scalar('body')
            ->requirePresence('body', 'create')
            ->notEmptyString('body')
            ->scalar('tags')
            ->maxLength('tags', 255)
            ->allowEmptyString('tags')
            ->inList('status', ['draft', 'published'])
            ->inList('visibility', ['private', 'friends', 'public'])
            ->integer('trip_id')
            ->allowEmptyString('trip_id')
            ->dateTime('published_at')
            ->allowEmptyDateTime('published_at');
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['trip_id'], 'Trips'), ['errorField' => 'trip_id', 'allowNullableNulls' => true]);
        $rules->add($rules->isUnique(['slug']), ['errorField' => 'slug']);

        return $rules;
    }

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = strtolower(Text::slug((string)$data['title']));
        }

        if (isset($data['trip_id']) && $data['trip_id'] === '') {
            $data['trip_id'] = null;
        }

        if (isset($data['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', (string)$data['tags'])));
            $data['tags'] = $tags === [] ? null : implode(', ', $tags);
        }
    }

    public function beforeSave(EventInterface $event, $entity, ArrayObject $options): void
    {
        if ($entity->isDirty('status') && $entity->status === 'published' && empty($entity->published_at)) {
            $entity->published_at = new DateTime();
        }
        if ($entity->status === 'draft') {
            // keep published_at if republishing later is useful; don't clear
        }
    }

    public function findReadable(SelectQuery $query, ?int $userId, bool $isOwner = false): SelectQuery
    {
        $query = $query->contain(['Users', 'Trips']);

        if ($isOwner) {
            return $query;
        }

        $visibility = ['public'];
        if ($userId !== null) {
            $visibility[] = 'friends';
        }

        return $query->where([
            'Posts.status' => 'published',
            'Posts.visibility IN' => $visibility,
        ]);
    }

    public function findTagged(SelectQuery $query, string $tag): SelectQuery
    {
        $tag = trim($tag);

        return $query->where([
            'OR' => [
                ['Posts.tags' => $tag],
                ['Posts.tags LIKE' => $tag . ',%'],
                ['Posts.tags LIKE' => '%, ' . $tag],
                ['Posts.tags LIKE' => '%, ' . $tag . ',%'],
                ['Posts.tags LIKE' => '%,' . $tag],
                ['Posts.tags LIKE' => '%,' . $tag . ',%'],
            ],
        ]);
    }

    public function userCanManage(?int $userId, ?string $role): bool
    {
        return $userId !== null && $role === 'owner';
    }

    public function userCanView($post, ?int $userId, bool $isOwner = false): bool
    {
        if ($isOwner || ($userId !== null && (int)$post->user_id === $userId)) {
            return true;
        }
        if ($post->status !== 'published') {
            return false;
        }
        if ($post->visibility === 'public') {
            return true;
        }
        if ($post->visibility === 'friends') {
            return $userId !== null;
        }

        return false;
    }
}
