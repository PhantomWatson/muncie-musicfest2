<div class="form" id="volunteer-form">
    <?= $this->Form->create($volunteer) ?>

    <fieldset>
        <?= $this->element('Volunteers' . DS . 'form') ?>
    </fieldset>

    <?= $this->Form->button(__('Submit'), [
        'class' => 'btn btn-primary'
    ]) ?>

    <?= $this->Form->end() ?>
</div>
