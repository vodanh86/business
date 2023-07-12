<?php

use Illuminate\Routing\Router;


Admin::routes();

Route::resource('admin/auth/users', \App\Admin\Controllers\CustomUserController::class)->middleware(config('admin.route.middleware'));

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('/branch-business', BranchController::class);
    $router->resource('/tuition-collection', TuitionCollectionController::class);
    $router->resource('/branchs', CompanyController::class);
    $router->resource('/employee', EmployeeController::class);
    $router->resource('/business', BusinessController::class);
    $router->resource('/classes', ClassController::class);
    $router->resource('/account_bank', AccountController::class);
    $router->resource('/student', StudentController::class);
    
});
