<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 * @var \App\Model\Entity\Pin $pin
 */
$this->assign('title', 'Edit pin · ' . $pin->title);
?>
<section class="panel">
    <h1>Edit pin</h1>
    <p class="meta">On <?= $this->Html->link($trip->title, ['controller' => 'Trips', 'action' => 'view', $trip->slug]) ?></p>
    <?= $this->element('Pins/form', compact('trip', 'pin')) ?>

    <?php if (!empty($pin->media_items)): ?>
        <h2>Media</h2>
        <div class="media-row">
            <?php foreach ($pin->media_items as $media): ?>
                <div class="media-item">
                    <?php if ($media->kind === 'video'): ?>
                        <video controls src="<?= h($media->webPath()) ?>"></video>
                    <?php else: ?>
                        <img src="<?= h($media->webPath()) ?>" alt="<?= h($media->original_name) ?>">
                    <?php endif; ?>
                    <?= $this->Form->postLink('Remove', [
                        'controller' => 'Pins',
                        'action' => 'deleteMedia',
                        $trip->slug,
                        $pin->id,
                        $media->id,
                    ], [
                        'confirm' => 'Remove this file?',
                        'class' => 'button secondary',
                    ]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="actions" style="margin-top:1.5rem">
        <?= $this->Form->postLink('Delete pin', [
            'controller' => 'Pins',
            'action' => 'delete',
            $trip->slug,
            $pin->id,
        ], [
            'confirm' => 'Delete this pin and its media?',
            'class' => 'button secondary',
        ]) ?>
    </div>
</section>
