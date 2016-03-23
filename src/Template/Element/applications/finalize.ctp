<h2>Almost done!</h2>
<p>
    If you have a message for the Muncie MusicFest organizers, here's where you include it.
</p>
<?= $this->Form->input('message', [
    'label' => false,
    'type' => 'textarea'
]) ?>

<div class="input apply">
    <div>
        <?= $this->Form->button(
            'Apply to Perform',
            ['class' => 'btn btn-primary']
        ) ?>
    </div>
</div>
