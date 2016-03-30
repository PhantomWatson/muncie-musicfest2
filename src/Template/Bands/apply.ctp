<?php
    if (in_array($band->application_step, ['songs', 'pictures', 'done'])) {
        $this->Html->script('/uploadifive/jquery.uploadifive.min.js', ['block' => 'script']);
        $this->Html->css('/uploadifive/uploadifive.css', ['block' => 'css']);
    }
?>

<div id="band-application">
    <?php if ($band->application_step == 'basic' || $band->application_step == 'done'): ?>
        <div class="alert alert-info">
            <h3>
                I want to perform at Muncie MusicFest!
            </h3>
            <p>
                Awesome! Just fill out the following form and you'll be considered
                for booking. When we're finished with the heartwrenching booking process, we'll send you an email to let
                you know whether or not we could get you in on this year's festival. Applications must be in by
                <strong>August 1<sup>st</sup></strong>.
            </p>

            <h3>
                Will I be booked?
            </h3>
            <p>
                Keep in mind that lots and lots of bands apply and we only have so many slots in which to book them,
                especially when we try to book a decent mix of crowd favorites and up-and-coming new acts in a wide
                variety of genres.
                Actually, we only had room to book <strong>48% of applicants</strong> in the past three festivals.
                So if you don't    get picked, keep in mind that you're probably one of the dozens of great performers that we wanted to
                book, but couldn't accommodate this year.
            </p>
            <?php /*
                2015: 47 of 63 (74%)
                2014: 0 of 51 (canceled)
                2013: 14 of 54 (26%)
                2012: 57 of 125 (46%)
                2011: 50 of 138 (36%)
                2010: (canceled)
                2009: 61 of 112 (54%)
                2008: 41 of 69 (59%)
            */ ?>
        </div>
    <?php endif; ?>

    <?= $this->Form->create($band, [
        'id' => 'applicationForm'
    ]) ?>

    <?php if ($band->id): ?>
        <input type="hidden" disabled="disabled" id="bandId" value="<?= $band->id ?>" />
    <?php endif; ?>

    <?php foreach ($steps as $step): ?>
        <?php if ($band->application_step == 'done' || $band->application_step == $step): ?>
            <section>
                <?= $this->element('applications'.DS.$step) ?>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php
        switch ($band->application_step) {
            case 'done':
                $label = 'Update info';
                break;
            case 'finalize':
                $label = 'Apply to Perform';
                break;
            default:
                $label = 'Next Step';
                break;
        }
    ?>

    <p>
        <?= $this->Form->button(
            $label,
            ['class' => 'btn btn-primary btn-lg']
        ) ?>
    </p>

    <?= $this->Form->end() ?>

    <?php if (! in_array($band->application_step, ['done', 'finalize'])): ?>
        <p class="alert alert-info">
            Don't worry, after you've finished all the steps of applying, you'll be able to come back to this page at any time to update your info.
        </p>
    <?php endif; ?>
</div>
