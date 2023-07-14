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

    $router->resource('/branch', Core_BranchController::class);
    $router->resource('/business', Core_BusinessController::class);
    $router->resource('/account_bank', Core_AccountController::class);
    $router->resource('/tuition-collection', Edu_TuitionCollectionController::class);
    $router->resource('/employee', Edu_EmployeeController::class);
    $router->resource('/classes', Edu_ClassController::class);
    $router->resource('/student', Edu_StudentController::class);
    
});
