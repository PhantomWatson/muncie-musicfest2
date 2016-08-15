<?php if (isset($_GET['dev'])): ?>
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
                        'action' => 'view', $band->slug
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
<?php else: ?>
    <p>
        Hang tight! Booking is underway and we'll be announcing the lineup soon!
    </p>
<?php endif; ?>
