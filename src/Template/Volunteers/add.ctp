<div class="form" id="volunteer-form">
    <div class="alert alert-info">
        <p>
            We need a bunch of people to help us put on Muncie MusicFest this year, mostly at the daytime half of the
            festival that will take place at <a href="http://cornerstonearts.org/">Cornerstone Center for the Arts</a>.
            Want to be one of them? Just sign up here, and we'll be in touch about how you can help out.
        </p>

        <p>
            And remember that volunteering is more fun with friends! Be sure to encourage a few pals to sign up too.
        </p>
    </div>

    <?= $this->Form->create($volunteer) ?>

    <fieldset>
        <?= $this->element('Volunteers' . DS . 'form') ?>
    </fieldset>

    <?= $this->Form->button(__('Submit'), [
        'class' => 'btn btn-primary'
    ]) ?>
    <?= $this->Form->end() ?>
</div>
