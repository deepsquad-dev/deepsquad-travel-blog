<?php
/**
 * @var \App\View\AppView $this
 * @var \Authentication\IdentityInterface|null $identity
 */
$this->assign('title', 'Travel adventures with friends');
?>
<section class="hero">
    <h1>Pin the adventure. Invite your friends.</h1>
    <p class="lede">
        deepsquad is an invite-only travel map with a blog for the stories behind the pins.
        Create a trip, drop photo and video markers, write it up, and share with people you trust.
    </p>
    <div class="actions">
        <?= $this->Html->link('Read the blog', ['controller' => 'Posts', 'action' => 'index'], ['class' => 'button']) ?>
        <?php if ($identity): ?>
            <?= $this->Html->link('Your trips', ['controller' => 'Trips', 'action' => 'index'], ['class' => 'button secondary']) ?>
        <?php else: ?>
            <?= $this->Html->link('Log in', ['controller' => 'Users', 'action' => 'login'], ['class' => 'button secondary']) ?>
        <?php endif; ?>
    </div>
</section>
