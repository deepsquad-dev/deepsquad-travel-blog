<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 */
$this->assign('title', 'New trip');
?>
<section class="panel">
    <h1>Start a trip</h1>
    <?= $this->element('Trips/form', compact('trip')) ?>
</section>
