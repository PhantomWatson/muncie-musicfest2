<p>
    Click on the following links to open up a new email to all of the applicable bands.
</p>

<?php if ($lists): ?>
    <?php foreach ($lists as $header => $list): ?>
        <h2>
            <?= $header ?>
        </h2>
        <p>
            <?php if ($list): ?>
                <strong>
                    <?= count($list) ?> email <?= __n('address', 'addresses', count($list)) ?>:
                </strong>
                <a href="mailto:<?= implode(';', $list) ?>">
                    <?= implode(';', $list) ?>
                </a>
            <?php else: ?>
                (none)
            <?php endif; ?>
        </p>
    <?php endforeach; ?>
<?php else: ?>
    <p>
        (no email lists found)
    </p>
<?php endif; ?>
