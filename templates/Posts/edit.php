<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 * @var array<int|string, string> $trips
 */
$this->assign('title', 'Edit · ' . $post->title);
?>
<section class="panel">
    <h1>Edit post</h1>
    <?= $this->element('Posts/form', compact('post', 'trips')) ?>
</section>
