<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('auth/login', 'AuthController@authenticate');

$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('auth/user', 'AuthController@currentUser');
});