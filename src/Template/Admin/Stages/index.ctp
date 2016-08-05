<p>
    <?= $this->Html->link(
        'Add New Stage',
        ['action' => 'add'],
        ['class' => 'btn btn-default']
    ) ?>
</p>

<table class="table" id="admin-stages">
    <thead>
        <tr>
            <th>Stage</th>
            <th>Slots</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stages as $stage): ?>
        <tr>
            <td>
                <strong><?= h($stage->name) ?></strong>
                <br />
                <?= nl2br($stage->address) ?>
                <?php if ($stage->age_restriction): ?>
                    <br />
                    21+
                <?php endif; ?>
                <?php if ($stage->notes): ?>
                    <br />
                    <em>
                        Notes:
                        <?= nl2br($stage->notes) ?>
                    </em>
                <?php endif; ?>
                <br />
                <?= $this->Html->link(
                    __('Edit'),
                    ['action' => 'edit', $stage->id],
                    ['class' => 'btn btn-default btn-xs']
                ) ?>
                <?php if ($authUser['id'] == 1): ?>
                    <?= $this->Form->postLink(
                        __('Delete'),
                        [
                            'action' => 'delete',
                            $stage->id
                        ],
                        [
                            'confirm' => __('Are you sure you want to delete this stage? This will cause UTTER CHAOS.'),
                            'class' => 'btn btn-default  btn-xs'
                        ])
                    ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($stage->slots): ?>
                    <ul>
                        <?php foreach ($stage->slots as $slot): ?>
                            <li>
                                <?= $slot->time->format('g:ia') ?> -
                                <?php if ($slot->band): ?>
                                    <?= $slot->band->name ?>
                                <?php else: ?>
                                    <span class="text-muted">
                                        (open)
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?= $this->Html->link(
                    __('Edit'),
                    ['action' => 'slots', $stage->id],
                    ['class' => 'btn btn-default btn-xs']
                ) ?>
            </td>
            <td>

            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
