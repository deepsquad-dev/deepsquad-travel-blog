<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\Paging\PaginatedInterface<\App\Model\Entity\Post> $posts
 * @var string $tag
 * @var string $q
 * @var bool $canManage
 */
$this->assign('title', $tag !== '' ? 'Blog · #' . $tag : 'Blog');
$empty = true;
?>
<section class="panel">
    <div class="panel-head">
        <div>
            <h1>Blog</h1>
            <p class="lede">Travel notes from the road — the old Bloggery, rebuilt.</p>
        </div>
        <div class="actions">
            <?php if ($canManage): ?>
                <?= $this->Html->link('Write a post', ['action' => 'add'], ['class' => 'button']) ?>
            <?php endif; ?>
            <?= $this->Html->link('RSS', ['action' => 'rss'], ['class' => 'button secondary']) ?>
        </div>
    </div>

    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'blog-search', 'valueSources' => ['query']]) ?>
        <?= $this->Form->control('q', [
            'label' => false,
            'placeholder' => 'Search posts…',
            'value' => $q,
        ]) ?>
        <?php if ($tag !== ''): ?>
            <?= $this->Form->hidden('tag', ['value' => $tag]) ?>
        <?php endif; ?>
        <?= $this->Form->submit('Search') ?>
    <?= $this->Form->end() ?>

    <?php if ($tag !== ''): ?>
        <p class="meta">
            Tagged <strong>#<?= h($tag) ?></strong>
            · <?= $this->Html->link('Clear', ['action' => 'index']) ?>
        </p>
    <?php endif; ?>

    <div class="post-list">
        <?php foreach ($posts as $post): ?>
            <?php $empty = false; ?>
            <article class="post-card">
                <h2><?= $this->Html->link($post->title, ['action' => 'view', $post->slug]) ?></h2>
                <div class="meta">
                    <?= h($post->user->name ?? '') ?>
                    <?php if ($post->published_at): ?>
                        · <?= h($post->published_at->i18nFormat('d MMM yyyy')) ?>
                    <?php endif; ?>
                    · <?= h($post->visibility) ?>
                    <?php if ($post->status === 'draft'): ?>
                        · <span class="badge">draft</span>
                    <?php endif; ?>
                    <?php if ($post->trip): ?>
                        · trip: <?= $this->Html->link($post->trip->title, ['controller' => 'Trips', 'action' => 'view', $post->trip->slug]) ?>
                    <?php endif; ?>
                </div>
                <p><?= h($this->Text->truncate(strip_tags($post->body), 180)) ?></p>
                <?php if ($post->tagList() !== []): ?>
                    <div class="tag-row">
                        <?php foreach ($post->tagList() as $t): ?>
                            <?= $this->Html->link('#' . $t, ['action' => 'index', '?' => ['tag' => $t]], ['class' => 'tag']) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <?php if ($empty): ?>
            <p>No posts yet<?= $canManage ? ' — write the first one.' : '.' ?></p>
        <?php endif; ?>
    </div>

    <div class="pager">
        <?= $this->Paginator->prev('← Newer') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('Older →') ?>
    </div>
</section>
