<p class="well">
    Check out all the bands who will be performing at Muncie MusicFest 2016!
    Click on any band for more information about them and to listen to streaming music.
    Keep in mind that this information isn't set in stone and that all schedule information is
    <strong>
        subject to change
    </strong>
    up until the week of the festival.
</p>

<div class="row">
    <?php $i = 1; ?>
    <?php foreach ($stages as $stage): ?>
        <div class="col-sm-6 col-md-4">
            <table class="schedule" id="s<?php echo $stage['id']; ?>">
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php echo $stage['name']; ?>
                            <br />
                            <span class="address">
                                <?php echo $stage['address']; ?>
                            </span>
                            <?php if ($stage['age_restriction']): ?>
                                <span class="age_restriction">
                                    (21+)
                                </span>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($stage['slots'])): ?>
                        <?php foreach ($stage['slots'] as $slot): ?>
                            <?php if (isset($slot['band']['name'])): ?>
                                <tr>
                                    <th>
                                        <?= $slot['time']->format('g:ia') ?>
                                    </th>
                                    <?php if (isset($slot['band'])): ?>
                                        <?php if ($slot['band']['pictures']): ?>
                                            <?php $filename = $slot['band']['pictures'][0]['filename']; ?>
                                            <td
                                                class="has_picture"
                                                style="background-image: url('/img/bands/thumb/<?= $filename ?>');"
                                            >
                                        <?php else: ?>
                                            <td>
                                        <?php endif; ?>
                                            <?= $this->Html->link(
                                                '<div class="band_name">' .
                                                    '<span>' . $slot['band']['name'] . '</span>' .
                                                    '<span class="genre">' . $slot['band']['genre'] . '</span>' .
                                                    '</div>',
                                                [
                                                    'controller' => 'Bands',
                                                    'action' => 'view',
                                                    $slot['band']['slug']
                                                ],
                                                [
                                                    'class' => 'ajax stretch_shade',
                                                    'escape' => false,
                                                    'title' => 'View band profile'
                                                ]
                                            ); ?>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <div class="stretch_shade">
                                                <div class="band_name">
                                                    <span>( T B A )</span>
                                                </div>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($i % 3 == 0): ?>
            <div class="clearfix visible-md-block visible-lg-block"></div>
        <?php endif; ?>

        <?php $i++; ?>
    <?php endforeach; ?>
</div>
