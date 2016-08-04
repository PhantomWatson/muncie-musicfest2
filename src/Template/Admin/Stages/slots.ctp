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

<div class="alert alert-info">
    Bands not yet booked (select to view profile)
    <br />
    <select id="bands-master-list">
        <option></option>
        <?php foreach ($bands as $band): ?>
            <option
                value="<?= $band->id ?>"
                data-application-complete="<?= $band->application_step == 'done' ? 1 : 0 ?>"
                data-booked="<?= empty($band->slots) ? 0 : 1 ?>"
            >
                <?= $band->name ?>
                <?php if ($band->minimum_fee): ?>
                    ($<?= $band->minimum_fee ?>)
                <?php endif; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="well" id="band-profile-ajax">
    </div>
</div>

<?= $this->Form->create($stage) ?>
<?php if ($stage->slots): ?>
    <table class="table" id="edit-slots">
        <thead>
            <tr>
                <th>
                    Remove Slot
                </th>
                <th>
                    Time
                </th>
                <th colspan="2">
                    Band
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stage->slots as $i => $slot): ?>
                <tr
                    data-band-id="<?= $slot->band ? $slot->band->id : '' ?>"
                    data-slot-key="<?= $i ?>"
                >
                    <td>
                        <?= $this->Form->checkbox('deleteSlots[]', [
                            'checked' => false,
                            'hiddenField' => false,
                            'value' => $slot->id
                        ]) ?>
                    </td>
                    <td class="form-inline">
                        <?= $this->Form->input('slots.' . $i . '.id') ?>
                        <?= $this->Form->input('slots.' . $i . '.time', [
                            'interval' => 5,
                            'label' => false,
                            'timeFormat' => 12,
                            'type' => 'time'
                        ]) ?>
                    </td>
                    <td class="band">
                        <?php if ($slot->band): ?>
                            <?= $slot->band->name ?>
                        <?php else: ?>
                            <span class="text-muted">
                                Not booked
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="band-action">
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

<div class="form-inline alert alert-info">
    <?= $this->Form->input('newSlot', [
        'interval' => 5,
        'label' => false,
        'timeFormat' => 12,
        'type' => 'time'
    ]) ?>
    <?= $this->Form->checkbox('addSlot', [
        'checked' => false,
        'id' => 'add-slot-checkbox'
    ]) ?>
    <label for="add-slot-checkbox">
        Add new slot
    </label>
</div>

<?= $this->Form->button('Update', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>

<?php $this->Html->script('script', ['block' => 'script']); ?>
<?php $this->append('buffered'); ?>
    scheduleEditor.init();
<?php $this->end();
