<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 * @var \App\Model\Entity\Pin $pin
 */
$this->assign('title', 'Add pin · ' . $trip->title);
?>
<section class="panel">
    <h1>Pin a place on <?= h($trip->title) ?></h1>
    <p class="lede">Attach a photo or video to a map location.</p>
    <?= $this->element('Pins/form', compact('trip', 'pin')) ?>
</section>
