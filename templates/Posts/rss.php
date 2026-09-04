<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Post> $posts
 */
use Cake\Routing\Router;

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
  <channel>
    <title>deepsquad blog</title>
    <link><?= h(Router::url('/', true)) ?></link>
    <description>Travel notes and adventures from deepsquad</description>
    <?php foreach ($posts as $post): ?>
      <item>
        <title><?= h($post->title) ?></title>
        <link><?= h(Router::url(['controller' => 'Posts', 'action' => 'view', $post->slug], true)) ?></link>
        <guid><?= h(Router::url(['controller' => 'Posts', 'action' => 'view', $post->slug], true)) ?></guid>
        <?php if ($post->published_at): ?>
          <pubDate><?= h($post->published_at->toRfc2822String()) ?></pubDate>
        <?php endif; ?>
        <author><?= h($post->user->name ?? '') ?></author>
        <description><![CDATA[<?= nl2br(h($post->body)) ?>]]></description>
      </item>
    <?php endforeach; ?>
  </channel>
</rss>
