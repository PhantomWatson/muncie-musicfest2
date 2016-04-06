<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Mailer\Mailer;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Token;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        if ($this->request->action === 'register') {
            $this->loadComponent('Recaptcha.Recaptcha');
        }
        $this->Auth->allow([
            'forgotPassword',
            'login',
            'loginFacebook',
            'logout',
            'register',
            'registerFacebook',
            'resetPassword'
        ]);
    }

    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $this->request->data['password'] = $this->request->data('new_password');
                $this->request->data['role'] = 'user';
                $user = $this->Users->patchEntity($user, $this->request->data(), [
                    'fieldList' => ['name', 'email', 'password', 'role']
                ]);
                $errors = $user->errors();
                if (empty($errors)) {
                    $user = $this->Users->save($user);
                    $this->Flash->success('Your account has been registered. You may now log in.');
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('There was an error registering your account. Please try again.');
                }
            } else {
                $this->Flash->error('There was an error verifying your reCAPTCHA response. Please try again.');
            }
        }

        /* So the password fields aren't filled out automatically when the user
         * is bounced back to the page by a validation error */
        $this->request->data['new_password'] = null;
        $this->request->data['confirm_password'] = null;

        $provider = $this->getFacebookProvider('registerFacebook');
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email'],
        ]);
        $this->set([
            'facebookAuthUrl' => $authUrl,
            'pageTitle' => 'Register an Account',
            'user' => $user
        ]);
    }

    public function registerFacebook()
    {
        $provider = $this->getFacebookProvider('registerFacebook');
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email'],
        ]);
        $this->set([
            'facebookAuthUrl' => $authUrl,
            'pageTitle' => 'Register an Account With Facebook'
        ]);

        if ($this->request->query('code')) {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->request->query('code')
            ]);
            try {
                $facebookUser = $provider->getResourceOwner($token);
            } catch (Exception $e) {
                $msg = 'Sorry, but we didn\'t get any response from Facebook when we asked it who you were.';
                $msg .= ' Are you sure you\'re <a href="http://facebook.com" target="_blank">logged in to Facebook</a>?';
                $this->Flash->error($msg);
                return $this->redirect(['action' => 'register']);
            }
            $email = $facebookUser->getEmail();
            $user = $this->Users->newEntity([
                'name' => $facebookUser->getName(),
                'email' => $email,
                'role' => 'user',
                'password' => $this->generatePassword(),
                'facebook_id' => $facebookUser->getId()
            ]);
            $errors = $user->errors();
            if ($errors) {
                $adminEmail = Configure::read('adminEmail');
                $adminEmail = '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a>';
                if (isset($errors['email']['unique'])) {
                    $msg = 'It looks like you already registered an account with the email address '.$email.'.';
                    $msg .= ' Did you mean to <a href="/login">log in</a>?';
                    $this->Flash->error($msg);
                } else {
                    $msg = 'Sorry, there was an error registering you with Facebook.';
                    $msg .= " For assistance, contact $adminEmail and include the following error message:";
                    $this->Flash->error($msg);
                    $this->Flash->set('<pre>'.print_r($user->errors(), true).'</pre>');
                }
                return $this->redirect(['action' => 'register']);
            }
            $user = $this->Users->save($user);
            $this->Auth->setUser($user->toArray());
            $msg = 'You have been successfully registered and logged in as '.$email;
            $this->Flash->success($msg);
            return $this->redirect('/');
        }
    }

    /**
     * Returns a random six-character string. Ambiguous-looking alphanumeric characters are excluded.
     * @return string
     */
    private function generatePassword()
    {
        $characters = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');
        return substr($characters, 0, 6);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Flash->success('You are now logged in');
                $this->Auth->setUser($user);

                // Remember login information
                if ($this->request->data('auto_login')) {
                    $this->Cookie->configKey('CookieAuth', [
                        'expires' => '+1 year',
                        'httpOnly' => true
                    ]);
                    $this->Cookie->write('CookieAuth', [
                        'email' => $this->request->data('email'),
                        'password' => $this->request->data('password')
                    ]);
                }

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Email or password is incorrect');
            }
        } else {
            $this->request->data['auto_login'] = true;
        }
        $this->set([
            'pageTitle' => 'Log in',
            'user' => $this->Users->newEntity()
        ]);

        $provider = $this->getFacebookProvider('loginFacebook');
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email'],
        ]);
        $this->set('facebookAuthUrl', $authUrl);
    }

    public function loginFacebook()
    {
        $provider = $this->getFacebookProvider('loginFacebook');
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->request->query('code')
        ]);
        try {
            $user = $provider->getResourceOwner($token);
            $facebookId = $user->getId();
        } catch (Exception $e) {
            $msg = 'Sorry, but we didn\'t get any response from Facebook when we asked it who you were.';
            $msg .= ' Are you sure you\'re <a href="http://facebook.com" target="_blank">logged in to Facebook</a>?';
            $this->Flash->error($msg);
            return $this->render('login');
        }

        $userId = $this->Users->getIdWithFacebookId($facebookId);

        if ($userId) {
            $user = $this->Users->get($userId);
            $this->Auth->setUser($user->toArray());
            $this->Flash->success('You are now logged in as '.$user->email);
            return $this->redirect($this->Auth->redirectUrl());
        }
        $msg = 'Sorry, you have not yet registered a Muncie MusicFest account with your Facebook account.';
        $url = Router::url(['controller' => 'Users', 'action' => 'register']);
        $msg .= ' Would you like to do that now?';
        $this->Flash->error($msg);
        return $this->redirect(['controller' => 'Users', 'action' => 'register']);
    }

    private function getFacebookProvider($redirectAction)
    {
        return new \League\OAuth2\Client\Provider\Facebook([
            'clientId' => Configure::read('facebookAppId'),
            'clientSecret' => Configure::read('facebookAppSecret'),
            'redirectUri' => Router::url([
                'prefix' => false,
                'controller' => 'Users',
                'action' => $redirectAction
            ], true),
            'graphApiVersion' => 'v2.5'
        ]);
    }

    public function logout()
    {
        $this->Cookie->delete('CookieAuth');
        return $this->redirect($this->Auth->logout());
    }

    public function forgotPassword()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $email = $this->request->data('email');
            $adminEmail = Configure::read('adminEmail');
            if (empty($email)) {
                $msg = 'Please enter the email address you registered with to have your password reset. ';
                $msg .= 'Email <a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> for assistance.';
                $this->Flash->error($msg);
            } else {
                $userId = $this->Users->getIdWithEmail($email);
                if ($userId) {
                    if (Mailer::sendPasswordResetEmail($userId)) {
                        $this->Flash->success('Success! You should be shortly receiving an email with a link to reset your password.');
                        $this->request->data = [];
                    } else {
                        $msg = 'There was an error sending your password-resetting email. ';
                        $msg .= 'Please try again, or email <a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> for assistance.';
                        $this->Flash->error($msg);
                    }
                } else {
                    $msg = 'We couldn\'t find an account registered with the email address <strong>'.$email.'</strong>. ';
                    $msg .= 'Please make sure you spelled it correctly, and email ';
                    $msg .= '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> if you need assistance.';
                    $this->Flash->error($msg);
                }
            }
        }
        $this->set([
            'pageTitle' => 'Forgot Password',
            'user' => $user
        ]);
    }

    public function resetPassword($userId = null, $timestamp = null, $hash = null)
    {
        if (! $userId || ! $timestamp && ! $hash) {
            throw new NotFoundException('Incomplete URL for password-resetting. Did you leave out part of the URL when you copied and pasted it?');
        }

        if (time() - $timestamp > 60 * 60 * 24) {
            throw new ForbiddenException('Sorry, that link has expired.');
        }

        $expectedHash = Mailer::getPasswordResetHash($userId, $timestamp);
        if ($hash != $expectedHash) {
            throw new ForbiddenException('Invalid security key');
        }

        $user = $this->Users->get($userId);
        $email = $user->email;

        if ($this->request->is(['post', 'put'])) {
            $this->request->data['password'] = $this->request->data('new_password');
            $user = $this->Users->patchEntity($user, $this->request->data(), [
                'fieldList' => ['password']
            ]);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated.');
                return $this->redirect(['action' => 'login']);
            }
        }
        $this->request->data = [];

        $this->set([
            'email' => $email,
            'pageTitle' => 'Reset Password',
            'user' => $this->Users->newEntity()
        ]);
    }

    public function changePassword()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['password'] = $this->request->data('new_password');
            $user = $this->Users->patchEntity($user, $this->request->data(), [
                'fieldList' => ['password']
            ]);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated');

                // If user logs in via cookie, update cookie login credentials
                if ($this->Cookie->read('CookieAuth')) {
                    $this->Cookie->write('CookieAuth.password', $this->request->data('new_password'));
                }
            }
        }
        $this->request->data = [];
        $this->set([
            'pageTitle' => 'Change Password',
            'user' => $user
        ]);
    }
}
