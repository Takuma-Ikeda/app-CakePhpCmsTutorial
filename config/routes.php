<?php
/**
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Plugin;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true,
    ]));
    $routes->applyMiddleware('csrf');
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
    $routes->fallbacks(DashedRoute::class);
});

// /articles/tagged/* パスのとき ArticlesController::tags() に接続する
Router::scope('/articles', ['controller' => 'Articles'], function ($routes) {
    // `*` は何らかのパラメーターを渡されることを定義する
    $routes->connect('/tagged/*', ['action' => 'tags']);
});

Router::scope('/', function ($routes) {
    $routes->connect('/', [
        'controller' => 'Pages',
        'action' => 'display', 'home',
    ]);

    $routes->connect('/pages/*', [
        'controller' => 'Pages',
        'action' => 'display',
    ]);

    // 規約に基づいたデフォルトルートを接続
    $routes->fallbacks();
});

Plugin::routes();
