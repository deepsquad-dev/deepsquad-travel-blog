<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Post $post
 * @var array<int|string, string> $trips
 */
$this->assign('title', 'Write a post');
?>
<section class="panel">
    <h1>Write a post</h1>
    <p class="lede">Modern take on the old blog / Bloggery — publish travel notes, optionally link a trip.</p>
    <?= $this->element('Posts/form', compact('post', 'trips')) ?>
</section>
