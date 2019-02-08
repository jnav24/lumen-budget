<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * User Auth
 */
$router->post('auth/login', 'AuthController@authenticate');
$router->post('register', 'AuthController@register');

$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('auth/user', 'AuthController@currentUser');
});

/**
 * Types
 */
$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('types/bank', 'TypesController@bank');
    $router->get('types/bill', 'TypesController@bill');
    $router->get('types/credit-card', 'TypesController@creditCard');
    $router->get('types/investment', 'TypesController@investment');
    $router->get('types/job', 'TypesController@job');
    $router->get('types/medical', 'TypesController@medical');
    $router->get('types/utility', 'TypesController@utility');
});

/**
 * Budget Templates
 */
$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('budget-templates', 'BudgetTemplateController@getAllBudgetTemplates');
    $router->post('budget-templates', 'BudgetTemplateController@saveBudgetTemplates');
    $router->delete('budget-templates', 'BudgetTemplateController@deleteBudgetTemplate');
});

/**
 * Budgets
 */
$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('budgets', 'BudgetController@getAllBudgets');
    $router->post('budgets', 'BudgetController@saveBudget');
});
