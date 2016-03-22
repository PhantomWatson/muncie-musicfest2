<?php
    use Cake\Core\Configure;
?>

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

        <?= $this->Form->input('genre', ['label' => 'Genre(s)']) ?>
        <p class="footnote">
            We'd prefer you just give us <strong>one</strong> genre that describes your band, but we'll take up to <strong>three</strong>.
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
    </section>

    <hr />

    <section>
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
            'placeholder' => '(###) ###-####'
        ]) ?>
    </section>

    <hr />

    <section>
        <h2>Websites</h2>

        <?= $this->Form->input('website', [
            'label' => 'Official Website',
            'placeholder' => 'http://'
        ]); ?>
        <p class="footnote">
            Please enter the full URL (e.g. http://MyBandName.com)
            or leave this blank if your band does not have its own website.
        </p>

        <?= $this->Form->input('social_networking', [
            'label' => 'Social Networking Sites',
            'placeholder' => 'http://',
            'type' => 'textarea'
        ]); ?>
        <p class="footnote">
            Facebook, Bandcamp, SoundCloud, ReverbNation, Twitter, etc.
            One full URL (e.g. http://facebook.com/mybandname) per line.
        </p>
    </section>

    <hr />

    <section>
        <h2>Members</h2>
        <?= $this->Form->input('member_count', [
            'label' => 'Number of Members',
            'type' => 'number',
            'min' => 1
        ]); ?>
        <?= $this->Form->input('member_names', [
            'label' => 'Member Names and Roles',
            'type' => 'textarea'
        ]); ?>
        <?= $this->Form->input('members_under_21', [
            'label' => false,
            'type' => 'radio',
            'options' => [
                0 => 'All of the members of this band are <strong>21 or older</strong>',
                1 => 'This band has members <strong>under 21 years old</strong>'
            ],
            'escape' => false
        ]); ?>
    </section>

    <hr />

    <section>
        <h2>Media</h2>

        <div class="section">
            <strong>Up to three pictures:</strong>
            <p>
                Pictures of your band will be used in promoting you and the festival, so send us the best you have.
            </p>
            <ul>
                <li>Send pictures in any <strong>web-friendly file format</strong> (PNG, JPG, GIF).</li>
                <li>Send us only photos to which <strong>you hold the copyright</strong>, and that we're allowed to republish.</li>
                <li>By sending us these photos, you give us permission to use them as part of promotion of the festival.</li>
            </ul>
        </div>

        <div class="section">
            <strong>Up to three songs:</strong>
            <p>
                Samples of your music help us get to know you, match you with similar artists, and introduce website
                visitors to your music.
            </p>
            <ul>
                <li>Submit only <strong>original music</strong> that you have full distribution rights to. This typically does not include cover songs.</li>
                <li>Submit <strong>mp3-formatted</strong> music.</li>
                <li>Apply a <strong>"Song Title.mp3"</strong> format to each song's filename before uploading it. Please remove track numbers from the filenames so we don't think they're part of a song's title.</li>
                <li>
                    <strong>Fill out the file's <a title="ID3" href="http://en.wikipedia.org/wiki/Id3">ID3</a> information</strong> with your band's name, the song's title, and any other information that you can.
                    <a href="https://www.google.com/search?q=how+to+edit+id3+tags">How to edit ID3 tags</a>.
                </li>
                <li>By submitting music tracks, you agree to allow their redistribution through the festival website, festival compilation CDs, and radio promotion for the festival.</li>
            </ul>
        </div>

        <p>
            <a href="#" id="upload_button">Upload media</a>
        </p>

        <p>
            Problems uploading your media? Email your files to <a href="mailto:submit@munciemusicfest.com?subject=Muncie MusicFest 2015 Application">submit@munciemusicfest.com</a>.
        </p>
    </section>

    <hr />

    <section>
        <h2>Stage Needs</h2>
        <p>
            Here's where you tell us...
        </p>
        <ul>
            <li><strong>Inputs</strong> you'll need on stage for instruments,</li>
            <li><strong>Microphones</strong> you'll need us to provide for vocalists and instruments, and</li>
            <li><strong>Any other equipment</strong> you <em>won't</em> be bringing and will need the festival to provide.</li>
        </ul>
        <?= $this->Form->input('stage_setup', [
            'label' => false
        ]) ?>
    </section>

    <hr />

    <section>
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
    </section>

    <hr />

    <section>
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
    </section>
</div>
<?= $this->Form->end() ?>

<?php
    echo $this->Html->script('/uploadifive/jquery.uploadifive.min.js');
    echo $this->Html->css('/uploadifive/uploadifive.css');
    $upload_max = ini_get('upload_max_filesize');
    $post_max = ini_get('post_max_size');
?>
<?php $this->append('buffered'); ?>
    $('#upload_button').uploadifive({
        'uploadScript': '/bands/upload',
        'checkScript': '/bands/file_exists',
        'onCheck': false,
        'fileSizeLimit': '<?= $post_max ?>B',
        'buttonText': 'Click to select tracks and images to upload',
        'width': 300,
        'formData': {
            'timestamp': <?= time() ?>,
            'token': '<?= md5(Configure::read('upload_token').time()) ?>'
        },
        onUpload: function (filesToUpload) {
            var band_name = $('#BandName').val();
            if (band_name == '') {
                $('.uploadifive-queue-item').each(function () {
                    var item = $(this);
                    $('#upload_button').uploadifive('cancel', item.data('file'));
                });
                alert('Upload canceled. Please enter your band\'s name in the first form field and try again.');
            } else {
                $('#upload_button').data('uploadifive').settings.formData.band_name = band_name;
            }
        },
        'onUploadComplete': function(file, data) {
            console.log(file);
            console.log(data);
        },
        'onFallback': function() {
            // Warn user that their browser is old
        },
        'onError': function(errorType, files) {
            alert('There was an error uploading that file: '+file.xhr.responseText);
        },
        'onInit': function() {
        },
        'onQueueComplete': function() {
            // this.uploadifive('clearQueue');
        }
    });
<?php $this->end(); ?>
