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
    <a href="#" id="upload_picture">Upload media</a>
</p>

<p>
    Problems uploading your media? Email your files to <a href="mailto:submit@munciemusicfest.com?subject=Muncie MusicFest 2015 Application">submit@munciemusicfest.com</a>.
</p>

<?php
    $uploadMax = ini_get('upload_max_filesize');
    $postMax = ini_get('post_max_size');
    $fileSizeLimit = min($uploadMax, $postMax);
?>
<?php $this->append('buffered'); ?>
    pictureUpload.init({
        fileSizeLimit: <?= json_encode($fileSizeLimit) ?>,
        timestamp: <?= time() ?>,
        token: <?= json_encode(md5(Configure::read('uploadToken').time())) ?>
    });
<?php $this->end(); ?>
