<p class="alert alert-info">
    Before applying to perform,
    <?= $this->Html->link(
        'register an account',
        [
            'controller' => 'Users',
            'action' => 'register'
        ]
    ) ?>
    and log in.
    This way, you'll be able to update your band's information later.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->Form->input(
        'email',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->input(
        'password',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->input(
        'auto_login',
        [
            'label' => 'Keep me logged in on this computer',
            'type' => 'checkbox'
        ]
    );
    echo $this->Form->button(
        'Login',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
?>

<p>
    <?= $this->Html->link(
        'I forgot my password',
        [
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'forgotPassword'
        ]
    ) ?>
</p>
