<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 * @var \App\Model\Entity\Pin $pin
 */
?>
<?= $this->Form->create($pin, ['type' => 'file', 'class' => 'form-grid']) ?>
    <?= $this->Form->control('title') ?>
    <?= $this->Form->control('place_name', ['label' => 'Place name']) ?>
    <?= $this->Form->control('body', ['type' => 'textarea', 'label' => 'Story']) ?>
    <?= $this->Form->control('latitude', ['type' => 'number', 'step' => 'any']) ?>
    <?= $this->Form->control('longitude', ['type' => 'number', 'step' => 'any']) ?>
    <?= $this->Form->control('taken_at', ['type' => 'datetime-local', 'empty' => true, 'label' => 'Taken at']) ?>
    <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => 'Sort order']) ?>
    <?= $this->Form->control('media', [
        'type' => 'file',
        'label' => $pin->isNew() ? 'Photo or video' : 'Add another photo or video',
        'required' => false,
    ]) ?>
    <?= $this->Form->submit($pin->isNew() ? 'Save pin' : 'Update pin') ?>
<?= $this->Form->end() ?>
