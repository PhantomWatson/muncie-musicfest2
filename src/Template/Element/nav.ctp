<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

if (! function_exists('navLink')) {
    function navLink($label, $url, $view) {
        if (! isset($url['prefix'])) {
            $url['prefix'] = false;
        }
        $url = Router::url($url);
        if ($view->request->here == $url) {
            $label .= '<span class="sr-only">(current)</span>';
        }
        $link = $view->Html->link(
            $label,
            $url,
            ['escape' => false]
        );
        if ($view->request->here == $url) {
            return '<li class="active">'.$link.'</li>';
        }
        return '<li>'.$link.'</li>';
    }
}
?>

<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">
                    Toggle navigation
                </span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                Muncie MusicFest
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?= navLink('Home', '/', $this) ?>

                <?php if (Configure::read('bandApplicationsOpen')): ?>
                    <?= navLink('Apply to Perform', [
                        'controller' => 'Bands',
                        'action' => 'apply'
                    ], $this) ?>
                <?php endif; ?>

                <?= navLink('Bands', [
                    'controller' => 'Bands',
                    'action' => 'index'
                ], $this) ?>

                <?= navLink('Volunteer', [
                    'controller' => 'Pages',
                    'action' => 'volunteer'
                ], $this) ?>

                <?= navLink('Contact', [
                    'controller' => 'Pages',
                    'action' => 'contact'
                ], $this) ?>

                <?= navLink('About', [
                    'controller' => 'Pages',
                    'action' => 'about'
                ], $this) ?>

                <?php if ($authUser): ?>

                    <?php if ($authUser['role'] == 'admin'): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Admin <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->Html->link(
                                        'Bands',
                                        [
                                            'prefix' => 'admin',
                                            'controller' => 'Bands',
                                            'action' => 'index'
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?= navLink('Logout', [
                        'controller' => 'Users',
                        'action' => 'logout'
                    ], $this) ?>
                <?php else: ?>
                    <?= navLink('Register', [
                        'controller' => 'Users',
                        'action' => 'register'
                    ], $this) ?>

                    <?= navLink('Login', [
                        'controller' => 'Users',
                        'action' => 'login'
                    ], $this) ?>
                <?php endif; ?>

                <?= navLink(
                    '<i class="fa fa-facebook-square" style="font-size: 20px;" title="Facebook"></i> <span class="sr-only">Facebook</span>',
                    'https://www.facebook.com/munciemusicfest',
                    $this
                ) ?>
            </ul>
        </div>
    </div>
</nav>
