<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 * @var array<int|string, string> $trips
 */
?>
<?= $this->Form->create($post, ['class' => 'form-grid']) ?>
    <?= $this->Form->control('title') ?>
    <?= $this->Form->control('slug', [
        'label' => 'Slug (optional — blank to auto-generate)',
    ]) ?>
    <?= $this->Form->control('body', [
        'type' => 'textarea',
        'rows' => 12,
        'label' => 'Body',
    ]) ?>
    <?= $this->Form->control('tags', [
        'label' => 'Tags (comma-separated)',
        'placeholder' => 'travel, iceland, hiking',
    ]) ?>
    <?= $this->Form->control('trip_id', [
        'label' => 'Linked trip (optional)',
        'options' => $trips,
        'empty' => '— none —',
    ]) ?>
    <?= $this->Form->control('status', [
        'options' => [
            'draft' => 'Draft',
            'published' => 'Published',
        ],
    ]) ?>
    <?= $this->Form->control('visibility', [
        'options' => [
            'friends' => 'Friends (logged-in)',
            'public' => 'Public',
            'private' => 'Private (owner only)',
        ],
    ]) ?>
    <?= $this->Form->submit($post->isNew() ? 'Save post' : 'Update post') ?>
<?= $this->Form->end() ?>
