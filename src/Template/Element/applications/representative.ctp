<h2>Representative</h2>
<p>
    Enter the information of a manager or someone in your band with whom we should
    communicate about your booking and payment. This is probably you, since you're
    filling out this form now.
</p>

<?= $this->Form->input('rep_name', [
    'label' => 'Representative Name'
]) ?>

<?= $this->Form->input('email', [
    'label' => 'Email',
    'type' => 'email'
]) ?>
<p class="footnote">
    Make sure this is an address that's checked frequently, because it's the primary way we're going to communicate with you.
</p>

<?= $this->Form->input('phone', [
    'label' => 'Phone',
    'type' => 'tel',
    'placeholder' => '###-###-####'
]) ?>
