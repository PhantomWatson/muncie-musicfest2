
<p class="well">
    Every year, we try to book as many bands as we can for one huge day of music, and include a diverse array of
    genres, age groups, and both established and up-and-coming bands who are either from or connected to Muncie.
    We hope you enjoy the lineup of <?= count($bands) ?> bands that we've put together for Muncie MusicFest 2016,
    which we've gathered from a pool of over 90 applicants.
</p>

<div id="bands">
    <?php foreach ($bands as $band_name => $band): ?>
        <?php if ($band->pictures): ?>
            <div
                class="has-picture"
                style="background-image: url('/img/bands/thumb/<?= addslashes($band->pictures[0]['filename']) ?>');"
            >
        <?php else: ?>
            <div>
        <?php endif; ?>
            <?= $this->Html->link(
                '<div class="band-name"><span>' .
                    $band['name'] .
                    '</span><span class="genre"><br />' .
                    $band['genre'] .
                    '</span></div>',
                [
                    'controller' => 'Bands',
                    'action' => 'view',
                    $band->slug,
                    '?' => [
                        'back' => 'index'
                    ]
                ],
                [
                    'class' => 'stretch-shade ajax',
                    'escape' => false,
                    'title' => 'Click for band profile'
                ]
            ) ?>
        </div>
    <?php endforeach; ?>
</div>

<br style="clear: both;" />
