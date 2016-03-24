<?php
    use Cake\Core\Configure;
?>

<h2>Pictures</h2>

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
