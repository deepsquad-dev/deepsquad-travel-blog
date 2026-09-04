<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 * @var bool $canEdit
 * @var bool $isOwner
 * @var string|null $role
 * @var array $pinsJson
 */
$this->assign('title', $trip->title);
$addUrl = $this->Url->build(['controller' => 'Pins', 'action' => 'add', $trip->slug]);
?>
<section class="panel">
    <h1><?= h($trip->title) ?></h1>
    <?php if ($trip->summary): ?>
        <p class="lede"><?= h($trip->summary) ?></p>
    <?php endif; ?>
    <div class="meta">
        Hosted by <?= h($trip->user->name ?? 'unknown') ?>
        · <?= h($trip->visibility) ?>
        <?php if ($role): ?>
            · your role: <?= h($role) ?>
        <?php endif; ?>
        <?php if ($trip->starts_on || $trip->ends_on): ?>
            ·
            <?php if ($trip->starts_on): ?>
                <?= h($trip->starts_on->i18nFormat('d MMM yyyy')) ?>
            <?php endif; ?>
            <?php if ($trip->starts_on && $trip->ends_on): ?> – <?php endif; ?>
            <?php if ($trip->ends_on): ?>
                <?= h($trip->ends_on->i18nFormat('d MMM yyyy')) ?>
            <?php endif; ?>
        <?php endif; ?>
        · <?= count($trip->pins ?? []) ?> pins
    </div>
    <div class="actions">
        <?php if ($canEdit): ?>
            <?= $this->Html->link('Add pin', ['controller' => 'Pins', 'action' => 'add', $trip->slug], ['class' => 'button']) ?>
        <?php endif; ?>
        <?php if ($isOwner): ?>
            <?= $this->Html->link('Edit trip', ['action' => 'edit', $trip->slug], ['class' => 'button secondary']) ?>
            <?= $this->Html->link('Invite a friend', ['action' => 'invite', $trip->slug], ['class' => 'button secondary']) ?>
        <?php endif; ?>
    </div>

    <div class="map-shell">
        <div
            id="trip-map"
            data-pins='<?= h(json_encode($pinsJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) ?>'
            data-can-edit="<?= $canEdit ? '1' : '0' ?>"
            data-add-url="<?= h($addUrl) ?>"
        ></div>
    </div>
    <?php if ($canEdit): ?>
        <p class="meta">Click the map to drop a new pin.</p>
    <?php endif; ?>

    <div class="trip-columns">
        <div>
            <h2>Pins</h2>
            <div class="pin-list">
                <?php foreach ($trip->pins as $pin): ?>
                    <article class="pin-card">
                        <h3><?= h($pin->title) ?></h3>
                        <div class="meta">
                            <?= h($pin->place_name) ?>
                            · <?= h($pin->user->name ?? '') ?>
                            <?php if ($pin->taken_at): ?>
                                · <?= h($pin->taken_at->i18nFormat('d MMM yyyy HH:mm')) ?>
                            <?php endif; ?>
                            · <?= number_format((float)$pin->latitude, 5) ?>, <?= number_format((float)$pin->longitude, 5) ?>
                        </div>
                        <?php if ($pin->body): ?>
                            <p><?= nl2br(h($pin->body)) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($pin->media_items)): ?>
                            <div class="media-row">
                                <?php foreach ($pin->media_items as $media): ?>
                                    <?php if ($media->kind === 'video'): ?>
                                        <video controls src="<?= h($media->webPath()) ?>"></video>
                                    <?php else: ?>
                                        <img src="<?= h($media->webPath()) ?>" alt="<?= h($media->original_name) ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($canEdit): ?>
                            <div class="actions">
                                <?= $this->Html->link('Edit', [
                                    'controller' => 'Pins',
                                    'action' => 'edit',
                                    $trip->slug,
                                    $pin->id,
                                ], ['class' => 'button secondary']) ?>
                                <?= $this->Form->postLink('Delete', [
                                    'controller' => 'Pins',
                                    'action' => 'delete',
                                    $trip->slug,
                                    $pin->id,
                                ], [
                                    'confirm' => 'Delete this pin?',
                                    'class' => 'button secondary',
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($trip->pins)): ?>
                    <p>No pins yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <aside class="trip-aside">
            <h2>Travellers</h2>
            <ul class="member-list">
                <?php foreach ($trip->trip_members as $member): ?>
                    <li class="member-row">
                        <div>
                            <strong><?= h($member->user->name ?? 'Unknown') ?></strong>
                            <div class="meta"><?= h($member->role) ?></div>
                        </div>
                        <?php if ($isOwner && (int)$member->user_id !== (int)$trip->user_id): ?>
                            <?= $this->Form->create(null, [
                                'url' => ['action' => 'updateMember', $trip->slug, $member->id],
                                'class' => 'inline-form',
                            ]) ?>
                                <?= $this->Form->control('role', [
                                    'label' => false,
                                    'options' => [
                                        'editor' => 'Editor',
                                        'viewer' => 'Viewer',
                                    ],
                                    'value' => $member->role,
                                ]) ?>
                                <?= $this->Form->submit('Save') ?>
                            <?= $this->Form->end() ?>
                            <?= $this->Form->postLink('Remove', [
                                'action' => 'removeMember',
                                $trip->slug,
                                $member->id,
                            ], [
                                'confirm' => 'Remove this traveller from the trip?',
                                'class' => 'button secondary',
                            ]) ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($isOwner): ?>
                <h2>Pending invites</h2>
                <?php if (empty($trip->invites)): ?>
                    <p class="meta">No open invites. <?= $this->Html->link('Invite someone', ['action' => 'invite', $trip->slug]) ?></p>
                <?php else: ?>
                    <ul class="member-list">
                        <?php foreach ($trip->invites as $invite): ?>
                            <li class="member-row">
                                <div>
                                    <strong><?= h($invite->email) ?></strong>
                                    <div class="meta">
                                        <?= h($invite->role) ?>
                                        · expires <?= h($invite->expires_at->i18nFormat('d MMM yyyy')) ?>
                                    </div>
                                </div>
                                <?= $this->Form->postLink('Revoke', [
                                    'action' => 'revokeInvite',
                                    $trip->slug,
                                    $invite->id,
                                ], [
                                    'confirm' => 'Revoke this invite?',
                                    'class' => 'button secondary',
                                ]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </aside>
    </div>
</section>
