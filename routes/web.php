<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * User Auth
 */
$router->post('auth/login', 'AuthController@authenticate');
$router->post('register', 'AuthController@register');
$router->post('auth/forgetpassword','AuthController@forgetPassword');
$router->post('auth/resetpassword','AuthController@resetPassword');

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
    $router->get('budgets/{id}', 'BudgetController@getSingleBudgetExpenses');
    $router->post('budgets', 'BudgetController@saveBudget');
    $router->delete('budgets/{id}', 'BudgetController@deleteBudget');
});

/**
 * Aggregation
 */
$router->group(['middleware' => 'jwt.auth'], function ($router) {
    $router->get('budget-aggregate', 'BudgetAggregationController@getYearlyAggregation');
    $router->get('current-budget-aggregate/{year}', 'BudgetAggregationController@getSingleYearAggregation');
    $router->get('unpaid-aggregate', 'BudgetAggregationController@getCountOfUnPaidBills');
});
