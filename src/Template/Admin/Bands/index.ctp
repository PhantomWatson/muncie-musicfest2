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
    <?= $this->Html->link(
        'Basic info table',
        [
            'prefix' => 'admin',
            'controller' => 'Bands',
            'action' => 'basicInfo'
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
            <?php foreach ($categorizedBands as $band): ?>
                <li>
                    <?= $this->Html->link(
                        $band['name'],
                        [
                            'prefix' => 'admin',
                            'controller' => 'Bands',
                            'action' => 'view',
                            $band['id']
                        ]
                    ) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
<?php endif; ?>
