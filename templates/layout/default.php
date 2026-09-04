<?php
/**
 * @var \App\View\AppView $this
 * @var \Authentication\IdentityInterface|null $identity
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ? h($this->fetch('title')) . ' · ' : '' ?>deepsquad</title>
    <?= $this->Html->meta('icon') ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <?= $this->Html->css(['deepsquad']) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= $this->Url->build('/') ?>">deepsquad</a>
        <nav class="site-nav">
            <?= $this->Html->link('Blog', ['controller' => 'Posts', 'action' => 'index']) ?>
            <?php if ($identity): ?>
                <?= $this->Html->link('Trips', ['controller' => 'Trips', 'action' => 'index']) ?>
                <?= $this->Html->link('New trip', ['controller' => 'Trips', 'action' => 'add']) ?>
                <span class="nav-user"><?= h($identity->get('name') ?? $identity->get('email')) ?></span>
                <?= $this->Html->link('Log out', ['controller' => 'Users', 'action' => 'logout']) ?>
            <?php else: ?>
                <?= $this->Html->link('Log in', ['controller' => 'Users', 'action' => 'login']) ?>
            <?php endif; ?>
        </nav>
    </header>
    <main class="site-main">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?= $this->Html->script(['deepsquad']) ?>
    <?= $this->fetch('script') ?>
</body>
</html>
