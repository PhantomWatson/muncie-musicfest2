<h2>Basic Info</h2>
<?= $this->Form->input('name', ['label' => 'Band name']) ?>
<?= $this->Form->input('hometown', [
    'label' => 'Hometown',
    'placeholder' => 'City, State'
]) ?>
<p class="footnote">
    Based in multiple cities? Just tell us the main one your members hail from, or the one that hosts most of your local gigs.
</p>

<?= $this->Form->input('description', [
    'label' => 'Description',
    'type' => 'textarea'
]) ?>
<p class="footnote">
    A concise bio that gives festival-goers an idea of what kind of a show you put on.
</p>

<?= $this->Form->input('genre', [
    'label' => 'Genre(s)',
    'placeholder' => 'e.g. indie rock, death metal, outlaw country'
]) ?>
<p class="footnote">
    We'd prefer you just give us <strong>one</strong> genre that describes your band, but we'll take up to <strong>three</strong>, lowercase, separated by commas.
</p>

<label>
    How long have you been around?
</label>
<?php
    $tiers = [
        [
            'value' => 'new',
            'label' => 'New / Emerging',
            'description' => 'Active for less than one year / no albums released.'
        ],
        [
            'value' => 'intermediate',
            'label' => 'Intermediate',
            'description' => 'Active for 1+ years / 1+ albums released.'
        ],
        [
            'value' => 'seasoned',
            'label' => 'Seasoned',
            'description' => 'Active for 2+ years / 2+ albums released.'
        ],
        [
            'value' => 'professional',
            'label' => 'Professional',
            'description' => 'Active for 4+ years / 3+ albums released.'
        ]
    ];
?>
<dl id="tier_options">
    <?php foreach ($tiers as $tier): ?>
        <dt>
            <input
                name="tier"
                id="tier_option_<?= $tier['value'] ?>"
                value="<?= $tier['value'] ?>"
                type="radio"
                <?php if ($band['tier'] == $tier['value']): ?>
                    checked="checked"
                <?php endif; ?>
            /><label for="tier_option_<?= $tier['value'] ?>"><?= $tier['label'] ?></label>
        </dt>
        <dd>
            <?= $tier['description'] ?>
        </dd>
    <?php endforeach; ?>
</dl>
<p class="footnote">
    These designations help us book a balanced mix of old and new bands
    and to help us figure out the best time and stage for your timeslot.
    Don't worry, we love you all the same.
</p>
