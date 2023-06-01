<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::prefix('v1/')->group(function () {

    Route::prefix('auth/')->middleware(['api'])->group(function () {
        Route::post('register', 'App\Http\Controllers\API\AuthController@register')->name('register');
        Route::post('login', 'App\Http\Controllers\API\AuthController@login')->name('login');
        Route::middleware(['auth:sanctum'])->post('logout', 'App\Http\Controllers\API\AuthController@logout')->name('logout');
    });

    Route::prefix('loan/')->middleware(['api', 'auth:sanctum'])->group(function() {
        Route::post('create', 'App\Http\Controllers\API\LoanController@create')->name('create');
    });
});
