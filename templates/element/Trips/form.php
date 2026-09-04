<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 */
?>
<?= $this->Form->create($trip, ['class' => 'form-grid']) ?>
    <?= $this->Form->control('title') ?>
    <?= $this->Form->control('summary', ['type' => 'textarea']) ?>
    <?= $this->Form->control('visibility', [
        'options' => [
            'friends' => 'Friends on this trip',
            'private' => 'Only me',
            'public' => 'Anyone with the link',
        ],
    ]) ?>
    <?= $this->Form->control('starts_on', ['type' => 'date', 'empty' => true, 'label' => 'Starts']) ?>
    <?= $this->Form->control('ends_on', ['type' => 'date', 'empty' => true, 'label' => 'Ends']) ?>
    <?= $this->Form->submit($trip->isNew() ? 'Create trip' : 'Update trip') ?>
<?= $this->Form->end() ?>
