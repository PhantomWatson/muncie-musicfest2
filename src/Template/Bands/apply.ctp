<div id="band-application">
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

    <hr />

    <?= $this->Form->create($band) ?>

    <section>
        <?= $this->element('applications'.DS.'basic') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'representative') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'websites') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'members') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'media') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'stage') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'payment') ?>
    </section>

    <hr />

    <section>
        <?= $this->element('applications'.DS.'finalize') ?>
    </section>
</div>
<?= $this->Form->end() ?>
