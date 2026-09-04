<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invite $invite
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'Accept invite');
?>
<section class="panel">
    <h1>You’re invited to <?= h($invite->trip->title) ?></h1>
    <p class="lede">Create your password to join as <?= h($invite->email) ?>.</p>
    <?= $this->Form->create($user, ['class' => 'form-grid']) ?>
        <?= $this->Form->control('name', ['required' => true]) ?>
        <?= $this->Form->control('password', ['type' => 'password', 'required' => true]) ?>
        <?= $this->Form->control('password_confirm', [
            'type' => 'password',
            'label' => 'Confirm password',
            'required' => true,
        ]) ?>
        <?= $this->Form->submit('Join trip') ?>
    <?= $this->Form->end() ?>
</section>
