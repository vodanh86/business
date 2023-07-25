<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('business', 'BusinessController@find');
Route::get('businessType', 'BusinessTypeController@find');
Route::get('branch', 'BranchController@find');
Route::get('classes', 'ClassController@getAll');
Route::get('class', 'ClassController@find');
Route::get('class/get-by-id', 'ClassController@getById');
Route::get('student', 'StudentController@find');
Route::get('schedule', 'ScheduleController@find');
Route::get('schedule/get-by-id', 'ScheduleController@getById');




