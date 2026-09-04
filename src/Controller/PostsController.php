<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

/**
 * Modern blog / “Bloggery” from the old deepsquad apps.
 *
 * @property \App\Model\Table\PostsTable $Posts
 */
class PostsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'view', 'tags', 'rss']);
    }

    public function index()
    {
        $userId = $this->optionalUserId();
        $isOwner = $this->isSiteOwner();
        $tag = trim((string)$this->request->getQuery('tag', ''));
        $q = trim((string)$this->request->getQuery('q', ''));

        $query = $this->fetchTable('Posts')
            ->find('readable', userId: $userId, isOwner: $isOwner)
            ->orderBy(['Posts.published_at' => 'DESC', 'Posts.created' => 'DESC']);

        if (!$isOwner) {
            // already filtered by readable
        } else {
            // owners see drafts too; still allow filter by status via query later
        }

        if ($tag !== '') {
            $query->find('tagged', tag: $tag);
        }
        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Posts.title LIKE' => '%' . $q . '%',
                    'Posts.body LIKE' => '%' . $q . '%',
                    'Posts.tags LIKE' => '%' . $q . '%',
                ],
            ]);
        }

        $posts = $this->paginate($query, ['limit' => 8]);
        $canManage = $this->canManageBlog();
        $this->set(compact('posts', 'tag', 'q', 'canManage'));
    }

    public function view(?string $slug = null)
    {
        $post = $this->fetchTable('Posts')->find()
            ->where(['Posts.slug' => $slug])
            ->contain(['Users', 'Trips'])
            ->first();

        if (!$post) {
            throw new NotFoundException('Post not found.');
        }

        $userId = $this->optionalUserId();
        $isOwner = $this->isSiteOwner();
        if (!$this->fetchTable('Posts')->userCanView($post, $userId, $isOwner)) {
            throw new ForbiddenException('You cannot view this post.');
        }

        $canManage = $this->canManageBlog();
        $this->set(compact('post', 'canManage'));
    }

    public function add()
    {
        $this->requireBlogManager();
        $posts = $this->fetchTable('Posts');
        $post = $posts->newEmptyEntity();

        if ($this->request->is('post')) {
            $post = $posts->patchEntity($post, $this->request->getData());
            $post->user_id = $this->identityId();
            if ($posts->save($post)) {
                $this->Flash->success('Post saved.');

                return $this->redirect(['action' => 'view', $post->slug]);
            }
            $this->Flash->error('Could not save the post.');
        }

        $trips = $this->tripListForUser();
        $this->set(compact('post', 'trips'));
    }

    public function edit(?string $slug = null)
    {
        $this->requireBlogManager();
        $posts = $this->fetchTable('Posts');
        $post = $posts->find()->where(['slug' => $slug])->firstOrFail();

        if ($this->request->is(['post', 'put', 'patch'])) {
            $post = $posts->patchEntity($post, $this->request->getData());
            if ($posts->save($post)) {
                $this->Flash->success('Post updated.');

                return $this->redirect(['action' => 'view', $post->slug]);
            }
            $this->Flash->error('Could not update the post.');
        }

        $trips = $this->tripListForUser();
        $this->set(compact('post', 'trips'));
    }

    public function delete(?string $slug = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->requireBlogManager();
        $posts = $this->fetchTable('Posts');
        $post = $posts->find()->where(['slug' => $slug])->firstOrFail();

        if ($posts->delete($post)) {
            $this->Flash->success('Post deleted.');
        } else {
            $this->Flash->error('Could not delete the post.');
        }

        return $this->redirect(['action' => 'index']);
    }

    public function tags(?string $tag = null)
    {
        return $this->redirect(['action' => 'index', '?' => ['tag' => $tag]]);
    }

    public function rss(): ?Response
    {
        $posts = $this->fetchTable('Posts')
            ->find('readable', userId: null, isOwner: false)
            ->where(['Posts.visibility' => 'public'])
            ->orderBy(['Posts.published_at' => 'DESC'])
            ->limit(20)
            ->all();

        $this->viewBuilder()->disableAutoLayout();
        $this->set(compact('posts'));
        $this->response = $this->response->withType('xml');

        return null;
    }

    private function optionalUserId(): ?int
    {
        $identity = $this->Authentication->getIdentity();

        return $identity ? (int)$identity->getIdentifier() : null;
    }

    private function isSiteOwner(): bool
    {
        $identity = $this->Authentication->getIdentity();

        return $identity !== null && $identity->get('role') === 'owner';
    }

    private function canManageBlog(): bool
    {
        return $this->isSiteOwner();
    }

    private function requireBlogManager(): void
    {
        if (!$this->canManageBlog()) {
            throw new ForbiddenException('Only the site owner can manage the blog.');
        }
    }

    /**
     * @return array<int|string, string>
     */
    private function tripListForUser(): array
    {
        return $this->fetchTable('Trips')
            ->find('list', keyField: 'id', valueField: 'title')
            ->where(['user_id' => $this->identityId()])
            ->orderBy(['title' => 'ASC'])
            ->toArray();
    }
}
