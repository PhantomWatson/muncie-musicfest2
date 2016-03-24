<?php
    use Cake\Core\Configure;
?>

<h2>Songs</h2>

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
