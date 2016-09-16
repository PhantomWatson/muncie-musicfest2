<?php
    use Cake\Utility\Hash;
?>

<table cellpadding="0" cellspacing="0" class="table" id="volunteers">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('name', 'Volunteer') ?></th>
            <th>Jobs</th>
            <th><?= $this->Paginator->sort('availability') ?></th>
            <th><?= $this->Paginator->sort('shirt_size') ?></th>
            <th><?= $this->Paginator->sort('emergency_contact') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($volunteers as $volunteer): ?>
        <tr>
            <td>
                <?= h($volunteer->name) ?>
                <p class="contact">
                    <?= $this->Text->autoLinkEmails($volunteer->email) ?>
                    <br />
                    <?= h($volunteer->phone) ?>
                </p>
            </td>
            <td>
                <?php if (empty($volunteer->jobs)): ?>
                    (none)
                <?php else: ?>
                    <ul>
                        <?php foreach ($volunteer->jobs as $job): ?>
                            <li>
                                <?= $job['name'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </td>
            <td class="availability">
                <?= h($volunteer->availability) ?>
            </td>
            <td>
                <?= h($volunteer->shirt_size) ?>
            </td>
            <td class="emergency-contact">
                <?= h($volunteer->emergency_contact) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
