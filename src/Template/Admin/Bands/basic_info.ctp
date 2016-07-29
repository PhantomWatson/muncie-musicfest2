<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back to Bands',
        [
            'prefix' => 'admin',
            'controller' => 'Bands',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<?php foreach ($bands as $band): ?>
    <?php if ($band->application_step != 'done'): ?>
        <p class="alert alert-info">
            Bands with <span class="glyphicon glyphicon-alert"></span> have not yet completed their applications.
        </p>
        <?php break; ?>
    <?php endif; ?>
<?php endforeach; ?>

<?php if ($bands): ?>
    <table class="table" id="bands-basic-info">
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Hometown
                </th>
                <th>
                    Genre
                </th>
                <th>
                    Cost
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bands as $band): ?>
                <tr>
                    <td>
                        <?= $this->Html->link(
                            $band->name,
                            [
                                'prefix' => 'admin',
                                'controller' => 'Bands',
                                'action' => 'view',
                                $band->id,
                                '?' => [
                                    'back' => Cake\Routing\Router::url([
                                        'prefix' => 'admin',
                                        'controller' => 'Bands',
                                        'action' => 'basicInfo'
                                    ])
                                ]
                            ]
                        ) ?>
                        <?php if ($band->application_step != 'done'): ?>
                            <span class="glyphicon glyphicon-alert" title="Not done applying"></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $band->hometown ?>
                    </td>
                    <td>
                        <?= $band->genre ?>
                    </td>
                    <td>
                        <?php if ($band->minimum_fee): ?>
                            $<?= number_format($band->minimum_fee) ?>
                        <?php else: ?>
                            <span class="text-muted">
                                -
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        (no bands found)
    </p>
<?php endif; ?>
