<h2>Members</h2>
<?= $this->Form->input('member_count', [
    'label' => 'Number of Members',
    'type' => 'number',
    'min' => 1
]); ?>
<?= $this->Form->input('member_names', [
    'label' => 'Member Names and Roles',
    'type' => 'textarea'
]); ?>
<?= $this->Form->input('members_under_21', [
    'label' => false,
    'type' => 'radio',
    'options' => [
        0 => 'All of the members of this band are <strong>21 or older</strong>',
        1 => 'This band has members <strong>under 21 years old</strong>'
    ],
    'escape' => false
]); ?>
