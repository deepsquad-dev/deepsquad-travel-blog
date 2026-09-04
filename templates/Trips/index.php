<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Trip> $trips
 */
$this->assign('title', 'Your trips');
$tripList = collection($trips)->toList();
?>
<section class="panel">
    <h1>Your trips</h1>
    <p class="lede">Adventures you own or were invited to.</p>
    <div class="actions">
        <?= $this->Html->link('Start a trip', ['action' => 'add'], ['class' => 'button']) ?>
    </div>
    <?php if ($tripList === []): ?>
        <p>No trips yet. Create one and invite a friend.</p>
    <?php else: ?>
        <div class="trip-grid">
            <?php foreach ($tripList as $trip): ?>
                <a class="trip-card" href="<?= $this->Url->build(['action' => 'view', $trip->slug]) ?>">
                    <h2><?= h($trip->title) ?></h2>
                    <div class="meta">
                        <?= h($trip->visibility) ?>
                        · <?= h($trip->user->name ?? '') ?>
                        · <?= count($trip->pins ?? []) ?> pins
                        · <?= count($trip->trip_members ?? []) ?> travellers
                    </div>
                    <?php if ($trip->starts_on || $trip->ends_on): ?>
                        <div class="meta">
                            <?php if ($trip->starts_on): ?>
                                <?= h($trip->starts_on->i18nFormat('d MMM yyyy')) ?>
                            <?php endif; ?>
                            <?php if ($trip->starts_on && $trip->ends_on): ?> – <?php endif; ?>
                            <?php if ($trip->ends_on): ?>
                                <?= h($trip->ends_on->i18nFormat('d MMM yyyy')) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($trip->summary): ?>
                        <p><?= h($this->Text->truncate($trip->summary, 110)) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
