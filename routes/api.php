<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
Route::middleware('auth:api')->get('/logout', 'App\Http\Controllers\Api\AuthController@logout');

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('desks', 'App\Http\Controllers\Api\DeskController@index');
    Route::get('desks/{id}', 'App\Http\Controllers\Api\DeskController@show');
    Route::post('desks/create', 'App\Http\Controllers\Api\DeskController@store');
    Route::get('users', 'App\Http\Controllers\Api\UserController@index');
    Route::post('users/create', 'App\Http\Controllers\Api\UserController@store');
    Route::get('tasks', 'App\Http\Controllers\AuthMain@getTasks');
    Route::get('cards', 'App\Http\Controllers\AuthMain@getCards');
    Route::get('desk_list', 'App\Http\Controllers\AuthMain@getDeskLists');
    Route::post('task_edit', 'App\Http\Controllers\AuthMain@taskEdit');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
