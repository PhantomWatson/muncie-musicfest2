<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    $routes->connect('/',          ['controller' => 'Pages', 'action' => 'home']);
    $routes->connect('/contact',   ['controller' => 'Pages', 'action' => 'contact']);
    $routes->connect('/about',     ['controller' => 'Pages', 'action' => 'about']);

    $routes->connect('/account',           ['controller' => 'Users', 'action' => 'account']);
    $routes->connect('/change-password',   ['controller' => 'Users', 'action' => 'changePassword']);
    $routes->connect('/forgot-password',   ['controller' => 'Users', 'action' => 'forgotPassword']);
    $routes->connect('/login',             ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/login-facebook',    ['controller' => 'Users', 'action' => 'loginFacebook']);
    $routes->connect('/register-facebook', ['controller' => 'Users', 'action' => 'registerFacebook']);
    $routes->connect('/logout',            ['controller' => 'Users', 'action' => 'logout']);
    $routes->connect('/register',          ['controller' => 'Users', 'action' => 'register']);
    $routes->connect('/reset-password/*',  ['controller' => 'Users', 'action' => 'resetPassword']);

    $routes->connect('/volunteer', ['controller' => 'Volunteers', 'action' => 'add']);

    $routes->connect(
        '/band/:slug',
        ['controller' => 'Bands', 'action' => 'view'],
        ['pass' => ['slug']]
    );
    $routes->connect('/schedule',  ['controller' => 'Bands', 'action' => 'schedule']);

    $routes->fallbacks(DashedRoute::class);
});

Router::prefix('admin', function ($routes) {
    $routes->fallbacks('DashedRoute');

    $routes->redirect('/bands/confirmations',  ['controller' => 'Bands', 'action' => 'booking']);
});
