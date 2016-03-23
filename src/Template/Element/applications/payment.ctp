<h2>Payment</h2>

<?php $this->Form->templates(require(ROOT.DS.'config'.DS.'bootstrap_currency_form.php')); ?>
<?= $this->Form->input('minimum_fee', [
    'label' => 'Performance Fee',
    'type' => 'number'
]) ?>
<?php $this->Form->templates(require(ROOT.DS.'config'.DS.'bootstrap_form.php')); ?>

<ul class="footnote">
    <li>
        Please include any <strong>gas money</strong> that you would need reimbursed for getting to Muncie, IN.
        Or just leave this blank if your band has no minimum performance fee.
    </li>
    <li>
        Keep in mind that Muncie MusicFest is a fundraiser event, and
        our modest performer budget is usually stretched between 50 to 60 bands. In previous festivals, we've
        only been able to pay more than $100 to the 15% of bands with the highest local draw.
    </li>
    <li>
        We will try our best to pay every band as fairly as we can, even over your minimum,
        and <strong>we will not book your band if we cannot cover your minimum performance fee</strong>.
    </li>
</ul>

<?= $this->Form->input('check_name', [
    'label' => 'Check made out to'
]) ?>
<ul class="footnote">
    <li>
        Fill this out even if you're willing to play for free, because we'll try to get you paid regardless.
    </li>
    <li>
        If your band can take checks made out to the band name, enter the band name here.
    </li>
    <li>
        Otherwise, enter the name of someone who can accept a check on the band's behalf
        and fill out a W-9 form at the festival.
    </li>
</ul>
