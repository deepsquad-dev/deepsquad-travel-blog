<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trip $trip
 * @var \App\Model\Entity\Invite $invite
 * @var iterable<\App\Model\Entity\Invite> $pendingInvites
 */
$this->assign('title', 'Invite · ' . $trip->title);
?>
<section class="panel">
    <h1>Invite a friend to <?= h($trip->title) ?></h1>
    <p class="lede">They get a one-time link to join this trip. In development the link is shown after you submit.</p>
    <?= $this->Form->create($invite, ['class' => 'form-grid']) ?>
        <?= $this->Form->control('email', ['type' => 'email', 'required' => true]) ?>
        <?= $this->Form->control('role', [
            'options' => [
                'editor' => 'Can add pins & media',
                'viewer' => 'View only',
            ],
        ]) ?>
        <?= $this->Form->submit('Create invite link') ?>
    <?= $this->Form->end() ?>

    <h2>Open invites</h2>
    <?php $list = collection($pendingInvites)->toList(); ?>
    <?php if ($list === []): ?>
        <p class="meta">None pending.</p>
    <?php else: ?>
        <ul class="member-list">
            <?php foreach ($list as $pending): ?>
                <li class="member-row">
                    <div>
                        <strong><?= h($pending->email) ?></strong>
                        <div class="meta">
                            <?= h($pending->role) ?>
                            · expires <?= h($pending->expires_at->i18nFormat('d MMM yyyy')) ?>
                        </div>
                    </div>
                    <?= $this->Form->postLink('Revoke', [
                        'action' => 'revokeInvite',
                        $trip->slug,
                        $pending->id,
                    ], [
                        'confirm' => 'Revoke this invite?',
                        'class' => 'button secondary',
                    ]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="actions">
        <?= $this->Html->link('← Back to trip', ['action' => 'view', $trip->slug], ['class' => 'button secondary']) ?>
    </div>
</section>
