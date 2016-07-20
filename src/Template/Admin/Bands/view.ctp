<?php
    use Cake\Routing\Router;
?>
<p>
    <div class="form-inline">
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
        <div class="input-group">
            <div class="input-group-addon">Go to</div>
            <select id="band-selector" class="form-control">
                <?php foreach ($bands as $bandId => $bandName): ?>
                    <?php
                        $selected = $bandId == $band->id ? 'selected="selected"' : '';
                        $url = Router::url([
                            'controller' => 'Bands',
                            'action' => 'view',
                            $bandId
                        ]);
                    ?>
                    <option <?= $selected ?> data-url="<?= $url ?>">
                        <?= $bandName ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</p>

<div class="band-info">
    <table class="table">
        <tr>
            <th>
                Application Progress
            </th>
            <td>
                <span class="label label-<?= $band->application_step == 'done' ? 'success' : 'danger' ?>">
                    <?= ucwords($band->application_step) ?>
                </span>
            </td>
        </tr>
        <?php foreach ($fields as $field): ?>
            <tr>
                <th>
                    <?= ucwords(str_replace('_', ' ', $field)) ?>
                </th>
                <td>
                    <?php
                        $value = $band->$field;
                        $value = $this->Text->autoLink($value);
                        $value = nl2br($value);
                        echo $value;
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th>
                Members under 21?
            </th>
            <td>
                <span class="label label-<?= $band->members_under_21 ? 'danger' : 'success' ?>">
                    <?= $band->members_under_21 ? 'Yes' : 'No' ?>
                </span>
            </td>
        </tr>
        <tr>
            <th>
                Pictures
            </th>
            <td>
                <?php if ($band['pictures']): ?>
                    <?php foreach ($band['pictures'] as $picture): ?>
                        <?= $this->Html->link(
                            $this->Html->image('/img/bands/thumb/' . $picture->filename, [
                                'alt' => $picture->filename,
                            ]),
                            '/img/bands/' . $picture->filename,
                            [
                                'escape' => false
                            ]
                        ) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    None
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>
                Songs
            </th>
            <td>
                <?php if ($band['songs']): ?>
                    <ul>
                        <?php foreach ($band['songs'] as $song): ?>
                            <li>
                                <?= $this->Html->link(
                                    $song->title,
                                    '/music/' . $song->filename
                                ) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    None
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

<?php $this->append('buffered'); ?>
    bandSelector.init();
<?php $this->end(); ?>
