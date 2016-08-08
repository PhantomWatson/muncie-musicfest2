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
                                <td>
                                    <?php if ($slot->band->confirmed === null): ?>
                                        <span class="label label-default">
                                            Pending
                                        </span>
                                    <?php elseif ($slot->band->confirmed == 'confirmed'): ?>
                                        <span class="label label-success">
                                            Confirmed
                                        </span>
                                    <?php else: ?>
                                        <span class="label label-danger">
                                            <?= $slot->band->confirmed ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= nl2br($slot->band->admin_notes) ?>
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
