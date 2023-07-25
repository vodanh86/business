<?php

use App\Admin\Controllers\CustomUserController;
use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::resource('admin/auth/users', CustomUserController::class)->middleware(config('admin.route.middleware'));
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', 'ChartjsController@index');
    $router->resource('/core/business', Core_BusinessController::class);
    $router->resource('/core/branch', Core_BranchController::class);
    $router->resource('/core/bankaccount', Core_AccountController::class);
    $router->resource('/core/common', Core_CommonCodeController::class);
    $router->resource('/core/txn-code', Core_TransactionCodeController::class);
    $router->resource('/core/transaction', Core_TransactionController::class);
    $router->resource('/core/txn-type', Core_TxnTypeConditionController::class);
    $router->resource('/core/expense-type', Core_ExpenseTypeController::class);
    $router->resource('/core/expense-group', Core_ExpenseGroupController::class);
    $router->resource('/core/expense', Core_ExpenseController::class);
    $router->resource('/core/entries', Core_EntriesController::class);
    $router->resource('/core/transfer', Core_TransferController::class);
    $router->resource('/edu/tuition-collection', Edu_TuitionCollectionController::class);
    $router->resource('/edu/employee', Edu_EmployeeController::class);
    $router->resource('/edu/classes', Edu_ClassController::class);
    $router->resource('/edu/student', Edu_StudentController::class);
    $router->resource('/edu/teacher', Edu_TeacherController::class);
    $router->resource('/edu/schedule', Edu_ScheduleController::class);
    $router->resource('/edu/apply-leave', Edu_ApplyLeaveController::class);
    $router->resource('/edu/expenditure', Edu_ExpenditureController::class);
    $router->resource('/edu/student-report', Edu_StudentReportController::class);
    $router->resource('/edu/report-detail', Edu_StudentReportDetailController::class);


});
