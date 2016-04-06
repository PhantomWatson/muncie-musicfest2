<?php
    use Cake\Routing\Router;
?>

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

<section>
    <h2>
        Log in With a Password
    </h2>
    <p>
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
            echo $this->Html->link(
                'I forgot my password',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'forgotPassword'
                ],
                ['class' => 'btn btn-default']
            );
            echo $this->Form->end();
        ?>
    </p>
</section>

<hr />

<section>
    <h2>
        Log in With Facebook
    </h2>
    <p>
        If you registered with Facebook,

        <a class="btn btn-social btn-facebook" id="loginWithFacebook" href="<?= $facebookAuthUrl ?>">
            <span class="fa fa-facebook"></span>
            Login
        </a>
    </p>
</section>
