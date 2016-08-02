<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back to Stages',
        [
            'prefix' => 'admin',
            'controller' => 'Stages',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<?= $this->Form->create($stage) ?>
<?php if ($stage->slots): ?>
    <table class="table" id="edit-slots">
        <thead>
            <tr>
                <th>
                    Time
                </th>
                <th>
                    Delete
                </th>
                <th>
                    Band
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stage->slots as $i => $slot): ?>
                <tr>
                    <td class="form-inline">
                        <?= $this->Form->input('slots.' . $i . '.id') ?>
                        <?= $this->Form->input('slots.' . $i . '.time', [
                            'interval' => 5,
                            'label' => false,
                            'timeFormat' => 12,
                            'type' => 'time'
                        ]) ?>
                    </td>
                    <td>
                        <?= $this->Form->checkbox('deleteSlots[]', [
                            'checked' => false,
                            'hiddenField' => false,
                            'value' => $slot->id
                        ]) ?>
                    </td>
                    <td>
                        <?php if ($slot->band): ?>
                            <?= $slot->band->name ?>
                        <?php else: ?>
                            <span class="text-muted">
                                Not booked
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-info">
        This stage has no slots yet.
    </p>
<?php endif; ?>

<div class="form-inline">
    <?= $this->Form->checkbox('addSlot', [
        'checked' => false
    ]) ?>
    Add a new slot<br />
    <?= $this->Form->input('newSlot', [
        'interval' => 5,
        'label' => false,
        'timeFormat' => 12,
        'type' => 'time'
    ]) ?>
</div>

<?= $this->Form->button('Update', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>
