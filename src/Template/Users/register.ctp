
<p class="alert alert-info">
    By registering an account, you can apply to perform at Muncie MusicFest and then later log in to update your band's details.
</p>

<section>
    <h2>
        Register with Facebook
    </h2>
    <p>
        Save time by registering using your Facebook account.
        As long as you're logged into Facebook on this computer/device, you'll be able to click
        'Login with Facebook' to instantly log in without a password.
    </p>
    <p>
        <a class="btn btn-social btn-facebook" id="loginWithFacebook" href="<?= $facebookAuthUrl ?>">
            <span class="fa fa-facebook"></span>
            Register with Facebook
        </a>
    </p>
</section>

<hr />

<section>
    <h2>
        Register with a Password
    </h2>
    <?php
        echo $this->Form->create($user);
        echo $this->Form->input(
            'name',
            [
                'placeholder' => 'Name'
            ]
        );
        echo $this->Form->input(
            'email',
            [
                'placeholder' => 'Email address'
            ]
        );
        echo $this->Form->input(
            'new_password',
            [
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Password'
            ]
        );
        echo $this->Form->input(
            'confirm_password',
            [
                'type' => 'password',
                'placeholder' => 'Confirm your password'
            ]
        );
    ?>

    <div class="input form-group">
        <label>
            Human?
        </label>
        <?= $this->Recaptcha->display() ?>
        <?php if (isset($recaptchaError)): ?>
            <div class="error-message">
                Invalid CAPTCHA response. Please try again.
            </div>
        <?php endif; ?>
    </div>

    <?php
        echo $this->Form->submit(
            'Register',
            ['class' => 'btn btn-primary']
        );
        echo $this->Form->end();
    ?>
</section>
