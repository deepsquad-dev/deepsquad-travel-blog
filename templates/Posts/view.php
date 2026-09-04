<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 * @var bool $canManage
 */
$this->assign('title', $post->title);
?>
<article class="panel post-article">
    <p class="meta">
        <?= $this->Html->link('← Blog', ['action' => 'index']) ?>
        <?php if ($post->status === 'draft'): ?>
            · <span class="badge">draft</span>
        <?php endif; ?>
    </p>
    <h1><?= h($post->title) ?></h1>
    <div class="meta">
        <?= h($post->user->name ?? '') ?>
        <?php if ($post->published_at): ?>
            · <?= h($post->published_at->i18nFormat('d MMM yyyy')) ?>
        <?php endif; ?>
        · <?= h($post->visibility) ?>
        <?php if ($post->trip): ?>
            · linked trip:
            <?= $this->Html->link($post->trip->title, ['controller' => 'Trips', 'action' => 'view', $post->trip->slug]) ?>
        <?php endif; ?>
    </div>

    <?php if ($canManage): ?>
        <div class="actions">
            <?= $this->Html->link('Edit', ['action' => 'edit', $post->slug], ['class' => 'button secondary']) ?>
            <?= $this->Form->postLink('Delete', ['action' => 'delete', $post->slug], [
                'confirm' => 'Delete this post?',
                'class' => 'button secondary',
            ]) ?>
        </div>
    <?php endif; ?>

    <div class="post-body">
        <?= nl2br(h($post->body)) ?>
    </div>

    <?php if ($post->tagList() !== []): ?>
        <div class="tag-row">
            <?php foreach ($post->tagList() as $t): ?>
                <?= $this->Html->link('#' . $t, ['action' => 'index', '?' => ['tag' => $t]], ['class' => 'tag']) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>
