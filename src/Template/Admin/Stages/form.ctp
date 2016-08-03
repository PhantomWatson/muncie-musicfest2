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

<div class="stages form large-9 medium-8 columns content">
    <?= $this->Form->create($stage) ?>
    <fieldset>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('address');
            echo $this->Form->input('notes');
            echo $this->Form->input('age_restriction', ['label' => 'Age-restricted']);
        ?>
    </fieldset>
    <?= $this->Form->button(ucwords($this->request->action), ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
