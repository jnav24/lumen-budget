<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('auth/login', 'AuthController@authenticate');
$router->post('register', 'AuthController@register');

$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('auth/user', 'AuthController@currentUser');
});

$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('types/bank', 'TypesController@bank');
    $router->get('types/bill', 'TypesController@bill');
    $router->get('types/credit-card', 'TypesController@creditCard');
    $router->get('types/investment', 'TypesController@investment');
    $router->get('types/medical', 'TypesController@medical');
    $router->get('types/utility', 'TypesController@utility');
});
