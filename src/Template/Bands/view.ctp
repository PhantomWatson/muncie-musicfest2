<?php $this->append('before-title'); ?>
    <p class="back-buttons">
        <?= $this->Html->link(
            '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> ' . $backLabel,
            $backUrl,
            [
                'class' => 'btn btn-default',
                'escape' => false
            ]
        ) ?>
    </p>
<?php $this->end(); ?>

<div id="band-profile">
    <p class="rundown">
        <strong>
            <?= ucfirst(str_replace(' ', '&nbsp;', $band['genre'])) ?>
        </strong>
        from&nbsp;<strong><?= str_replace(' ', '&nbsp;', $band['hometown']) ?></strong>
        performing&nbsp;at
        <strong><?= str_replace(' ', '&nbsp;', $band['slots'][0]->stage->name) ?></strong><?= $band['slots'][0]->stage->age_restriction ? '&nbsp;(21+) ' : ' ' ?>
        at&nbsp;<strong><?= $band['slots'][0]->time->format('g:ia') ?></strong>,
        September&nbsp;30<sup>th</sup>,&nbsp;2016
    </p>

    <div class="row">
        <div class="col-sm-4 pictures">
            <?php foreach ($band['pictures'] as $picture): ?>
                <?= $this->Html->image(
                    '/img/bands/thumb/' . $picture->filename,
                    [
                        'alt' => $picture->filename,
                        'class' => 'thumbnail-popup'
                    ]
                ) ?>
            <?php endforeach; ?>
        </div>
        <div class="col-sm-8">
            <?php if ($band['songs']): ?>
                <?= $this->element('player') ?>
                <?php $this->append('buffered'); ?>
                    <?php foreach ($band['songs'] as $song): ?>
                        musicPlayer.playlist.add(<?= json_encode([
                            'title' => $song->title,
                            'artist' => $band->name,
                            'mp3' => '/music/' . $song->filename
                        ]) ?>);
                    <?php endforeach; ?>
                <?php $this->end(); ?>
            <?php endif; ?>

            <blockquote>
                <?= nl2br($band['description']) ?>
            </blockquote>

            <?php if ($band['website']): ?>
                <p>
                    <strong>
                        Official website:
                    </strong>
                    <?= $this->Text->autoLinkUrls($band['website']) ?>
                </p>
            <?php endif; ?>

            <?php if ($band['social_networking']): ?>
                <p>
                    <strong>
                        Social networks:
                    </strong>
                </p>
                <ul>
                    <?php foreach (explode("\n", $band['social_networking']) as $site): ?>
                        <?php if ($site): ?>
                            <li>
                                <?= $this->Text->autoLinkUrls($site) ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->append('buffered'); ?>
    imagePopups.linkPictures();
<?php $this->end(); ?>
