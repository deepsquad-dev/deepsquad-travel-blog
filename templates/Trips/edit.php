<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 */
$this->assign('title', 'Edit · ' . $trip->title);
?>
<section class="panel">
    <h1>Edit trip</h1>
    <?= $this->element('Trips/form', compact('trip')) ?>
    <div class="actions" style="margin-top:1.5rem">
        <?= $this->Html->link('← Back to trip', ['action' => 'view', $trip->slug], ['class' => 'button secondary']) ?>
        <?= $this->Form->postLink('Delete trip', ['action' => 'delete', $trip->slug], [
            'confirm' => 'Delete this trip, all pins, invites, and memberships?',
            'class' => 'button secondary',
        ]) ?>
    </div>
</section>
