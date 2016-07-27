<p>
    <?= $this->Html->link(
        'Band email lists',
        [
            'prefix' => 'admin',
            'controller' => 'Bands',
            'action' => 'emails'
        ],
        ['class' => 'btn btn-default']
    ) ?>
</p>

<?php if (empty($bands['Bands Applied']) && empty($bands['Bands Not Done Applying'])): ?>
    <p class="alert alert-info">
        No bands found.
    </p>
<?php else: ?>
    <p>
        Click on a band to view their application.
    </p>

    <?php foreach ($bands as $category => $categorizedBands): ?>
        <?php
            if (empty($categorizedBands)) {
                continue;
            }
        ?>

        <h2>
            <?= $category ?> (<?= count($categorizedBands) ?>)
        </h2>

        <ul>
            <?php foreach ($categorizedBands as $bandId => $bandName): ?>
                <li>
                    <?= $this->Html->link(
                        $bandName,
                        [
                            'prefix' => 'admin',
                            'controller' => 'Bands',
                            'action' => 'view',
                            $bandId
                        ]
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
<?php endif; ?>
