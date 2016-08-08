<?php
    use Cake\Routing\Router;
?>

<div id="band-confirmations">
    <?php if ($stages): ?>
        <?php foreach ($stages as $stageName => $slots): ?>
            <h2>
                <?= $stageName ?>
            </h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            Time
                        </th>
                        <th>
                            Band / Fee
                        </th>
                        <th>
                            Contact
                        </th>
                        <th>
                            Confirmed?
                        </th>
                        <th>
                            Notes
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <td>
                                <?= $slot->time->format('g:ia') ?>
                            </td>
                            <?php if ($slot->band): ?>
                                <td>
                                    <?= $slot->band->name ?>
                                    <br />
                                    $<?= $slot->band->minimum_fee ?>
                                </td>
                                <td>
                                    <?= $slot->band->rep_name ?>
                                    <br />
                                    <a href="mailto:<?= $slot->band->email ?>">
                                        <?= $slot->band->email ?>
                                    </a>
                                    <br />
                                    <?= $slot->band->phone ?>
                                </td>
                                <td class="confirmation-state" data-confirmation-state="<?= $slot->band->confirmed ?>">
                                    <button
                                        class="btn btn-link edit-confirmation"
                                        data-url="<?= Router::url([
                                            'prefix' => 'admin',
                                            'controller' => 'Bands',
                                            'action' => 'editConfirmation',
                                            $slot->band->id
                                        ]) ?>"
                                    >
                                        <span class="glyphicon glyphicon-edit"></span>
                                        <span class="sr-only">Edit</span>
                                    </button>
                                </td>
                                <td>
                                    <span class="notes">
                                        <?= nl2br($slot->band->admin_notes) ?>
                                    </span>
                                    <button
                                        class="btn btn-link edit-notes"
                                        data-url="<?= Router::url([
                                            'prefix' => 'admin',
                                            'controller' => 'Bands',
                                            'action' => 'editNotes',
                                            $slot->band->id
                                        ]) ?>"
                                    >
                                        <span class="glyphicon glyphicon-edit"></span>
                                        <span class="sr-only">Edit</span>
                                    </button>
                                </td>
                            <?php else: ?>
                                <td colspan="4">
                                    (not booked)
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="alert alert-info">
            No booking info found in the database
        </p>
    <?php endif; ?>
</div>

<?php $this->append('buffered'); ?>
    bandConfirmations.init();
<?php $this->end(); ?>
