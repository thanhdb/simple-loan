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

Route::prefix('v1/auth/')
    ->controller(AuthController::class)
    ->group(function () {
        Route::middleware(['api'])->post('register', 'register')->name('register');
        Route::middleware(['api'])->post('login', 'login')->name('login');
        Route::middleware(['api', 'auth:sanctum'])->post('logout', 'logout')->name('logout');
    });
