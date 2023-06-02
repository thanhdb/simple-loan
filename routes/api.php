<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LoanController;

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

    Route::prefix('auth/')
        ->controller(AuthController::class)
        ->middleware(['api'])->group(function () {
        Route::post('register', 'register')->name('register');
        Route::post('login', 'login')->name('login');
        Route::middleware(['auth:sanctum'])->post('logout', 'logout')->name('logout');
    });

    Route::prefix('loan/')
        ->controller(LoanController::class)
        ->middleware(['api', 'auth:sanctum'])->group(function() {
        Route::get('/', 'index')->name('list-loan')->can('view_loan_list');
        Route::post('create', 'create')->name('create-loan')->can('create_loan');
        Route::get('all', 'viewAll')->name('view-all-loan')->can('view_loan_list');
        Route::get('{uuid}', 'view')->name('view-loan')->can('view_loan');

        Route::post('approve', 'approve')->name('approve-loan')->can('approve_loan');

    });

});
