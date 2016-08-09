<?php
    use Cake\Routing\Router;
?>

<p class="alert alert-info">
    Use the
    <button class="copy-message btn btn-xs btn-default" disabled="disabled">
        <span class="glyphicon glyphicon-copy"></span>
        Copy message to clipboard
    </button>
    buttons to copy the standard confirmation-request message for that band to your clipboard for easier spamming.
</p>

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
                            Band
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
                                    <?= $this->Html->link(
                                        $slot->band->name,
                                        [
                                            'prefix' => 'admin',
                                            'controller' => 'Bands',
                                            'action' => 'view',
                                            $slot->band->id
                                        ],
                                        ['title' => 'View full profile']
                                    ) ?>
                                    <br />
                                    <span class="band-details">
                                        <?= $slot->band->genre ?>
                                        <br />
                                        <?= $slot->band->hometown ?>
                                        <br />
                                        $<?= $slot->band->minimum_fee ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $slot->band->rep_name ?>
                                    <br />
                                    <a href="mailto:<?= $slot->band->email ?>">
                                        <?= $slot->band->email ?>
                                    </a>
                                    <br />
                                    <?= $slot->band->phone ?>
                                    <br />
                                    <button
                                        class="copy-message btn btn-xs btn-default"
                                    >
                                        <span class="glyphicon glyphicon-copy"></span>
                                        Copy message to clipboard
                                    </button>
                                    <textarea class="generated-message" id="generated-message-<?= $slot->band->id ?>"><?php
                                        echo 'Congratulations! We\'d like to book ' . $slot->band->name;
                                        echo ' at Muncie MusicFest 2016.';
                                        echo ' Can you confirm your availability to perform at ' . $stageName;
                                        if ($slot->band->minimum_fee) {
                                            echo ' for a payment of at least $' . $slot->band->minimum_fee;
                                        }
                                        echo " on Friday, September 30th at " . $slot->time->format('g:ia') . "?\n\n";
                                        echo 'Please let me know as soon as you can, and thanks again for applying.';
                                    ?></textarea>
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
