<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Log in');
?>
<section class="panel">
    <h1>Log in</h1>
    <p class="lede">Friends join by invite link. Owners and members sign in here.</p>
    <?= $this->Form->create(null, ['url' => ['action' => 'login'], 'class' => 'form-grid']) ?>
        <?= $this->Form->control('email', ['label' => 'Email', 'type' => 'email', 'required' => true]) ?>
        <?= $this->Form->control('password', ['label' => 'Password', 'type' => 'password', 'required' => true]) ?>
        <?= $this->Form->submit('Log in') ?>
    <?= $this->Form->end() ?>
</section>
