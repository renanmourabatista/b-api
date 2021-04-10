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


Route::group(
    [
        'middleware' => 'api',
        'prefix'     => 'auth'
    ], function ($router) {
    Route::post('login', 'App\Presentation\Controllers\AuthController@login');
    Route::post('logout', 'App\Presentation\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Presentation\Controllers\AuthController@refresh');
    Route::get('me', 'App\Presentation\Controllers\AuthController@me');
});

Route::group(
    [
        'middleware' => 'api',
        'prefix'     => 'wallets'
    ], function ($router) {
    Route::post('/transfers', 'App\Presentation\Controllers\CreateTransferController@handle');
});

