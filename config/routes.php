<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * This file is loaded in the context of the `Application` class.
 * So you can use `$this` to reference the application class instance
 * if required.
 */
return function (RouteBuilder $routes): void {
    /*
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
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->connect('/pages/*', 'Pages::display');

        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/trips', ['controller' => 'Trips', 'action' => 'index']);
        $builder->connect('/trips/add', ['controller' => 'Trips', 'action' => 'add']);
        $builder->connect('/trips/{slug}/edit', ['controller' => 'Trips', 'action' => 'edit'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/trips/{slug}/delete', ['controller' => 'Trips', 'action' => 'delete'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/trips/{slug}/invite', ['controller' => 'Trips', 'action' => 'invite'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/trips/{slug}/invites/{id}/revoke', ['controller' => 'Trips', 'action' => 'revokeInvite'])
            ->setPass(['slug', 'id'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'id' => '\d+']);
        $builder->connect('/trips/{slug}/members/{id}', ['controller' => 'Trips', 'action' => 'updateMember'])
            ->setPass(['slug', 'id'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'id' => '\d+']);
        $builder->connect('/trips/{slug}/members/{id}/remove', ['controller' => 'Trips', 'action' => 'removeMember'])
            ->setPass(['slug', 'id'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'id' => '\d+']);
        $builder->connect('/trips/{slug}/pins/add', ['controller' => 'Pins', 'action' => 'add'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/trips/{slug}/pins/{id}/edit', ['controller' => 'Pins', 'action' => 'edit'])
            ->setPass(['slug', 'id'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'id' => '\d+']);
        $builder->connect('/trips/{slug}/pins/{id}/delete', ['controller' => 'Pins', 'action' => 'delete'])
            ->setPass(['slug', 'id'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'id' => '\d+']);
        $builder->connect('/trips/{slug}/pins/{pinId}/media/{mediaId}/delete', ['controller' => 'Pins', 'action' => 'deleteMedia'])
            ->setPass(['slug', 'pinId', 'mediaId'])
            ->setPatterns(['slug' => '[a-z0-9\-]+', 'pinId' => '\d+', 'mediaId' => '\d+']);
        $builder->connect('/trips/{slug}', ['controller' => 'Trips', 'action' => 'view'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/invite/{token}', ['controller' => 'Invites', 'action' => 'accept'])
            ->setPass(['token'])
            ->setPatterns(['token' => '[a-f0-9]+']);

        $builder->connect('/blog', ['controller' => 'Posts', 'action' => 'index']);
        $builder->connect('/blog/rss', ['controller' => 'Posts', 'action' => 'rss']);
        $builder->connect('/blog/add', ['controller' => 'Posts', 'action' => 'add']);
        $builder->connect('/blog/tag/{tag}', ['controller' => 'Posts', 'action' => 'tags'])
            ->setPass(['tag']);
        $builder->connect('/blog/{slug}/edit', ['controller' => 'Posts', 'action' => 'edit'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/blog/{slug}/delete', ['controller' => 'Posts', 'action' => 'delete'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);
        $builder->connect('/blog/{slug}', ['controller' => 'Posts', 'action' => 'view'])
            ->setPass(['slug'])
            ->setPatterns(['slug' => '[a-z0-9\-]+']);

        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder): void {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
