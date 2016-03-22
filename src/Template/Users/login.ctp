<p class="alert alert-info">
    Don't have an account yet?
    <?= $this->Html->link(
        'Register an account',
        [
            'controller' => 'Users',
            'action' => 'register'
        ]
    ) ?> before logging in.
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
