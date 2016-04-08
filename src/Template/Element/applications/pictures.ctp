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

<?php if (count($band['pictures']) < $picturesLimit): ?>
    <div id="uploadPictureContainer">
        <p>
            <a href="#" id="upload_picture">Upload media</a>
        </p>

        <p>
            Problems uploading your media? Email your files to <a href="mailto:submit@munciemusicfest.com?subject=Muncie MusicFest 2015 Application">submit@munciemusicfest.com</a>.
        </p>
    </div>
<?php endif; ?>

<div class="alert alert-warning" id="pictureLimitReached" <?php if (count($band['pictures']) < $picturesLimit) echo 'style="display: none;"'; ?>>
    <p>
        You've reached your limit for uploading pictures. :(
    </p>
    <p>
        But if you'd like to replace one with another,
        just click the checkbox next to 'Delete' and submit this form to delete that image.
        Then you'll be able to upload a new one to replace it.
    </p>
</div>

<div id="uploadedImages">
    <h3>
        Uploaded Images
    </h3>
    <span class="footnote">
        Thumbnails shown, click for full-size
    </span>
    <ul>
        <?php foreach ($band['pictures'] as $picture): ?>
            <li>
                <?= $this->Html->image('/img/bands/thumb/'.$picture['filename'], [
                    'alt' => $picture['filename'],
                ]) ?>
                <label for="picturePrimary<?= $picture['id'] ?>">
                    <input id="picturePrimary<?= $picture['id'] ?>" type="radio" name="primaryPictureId" value="<?= $picture['id'] ?>" <?php if ($picture['is_primary']) echo 'checked="checked"'; ?> />
                    Main image
                </label>
                <button class="btn btn-danger btn-xs delete-picture" data-picture-id="<?= $picture['id'] ?>">
                    Delete
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
    $uploadMax = ini_get('upload_max_filesize');
    $postMax = ini_get('post_max_size');
    $fileSizeLimit = min($uploadMax, $postMax);
?>
<?php $this->append('buffered'); ?>
    applicationForm.initPictures({
        uploadParams: {
            fileSizeLimit: <?= json_encode($fileSizeLimit) ?>,
            timestamp: <?= time() ?>,
            token: <?= json_encode(md5(Configure::read('uploadToken').time())) ?>,
            limit: <?= $picturesLimit ?>
        }
    });
<?php $this->end(); ?>
