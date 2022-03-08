<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TransactionController;
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

Route::post('/login',       [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('/',    [StatusController::class, 'get']);

    Route::prefix('currency')->group(function() {
        Route::post('/',   [CurrencyController::class, 'new']);
        Route::get('/',    [CurrencyController::class, 'get']);
        Route::delete('/', [CurrencyController::class, 'delete']);
    });

    Route::prefix('status')->group(function() {
        Route::post('/',   [StatusController::class, 'new']);
        Route::get('/',    [StatusController::class, 'get']);
    });

    Route::prefix('tax')->group(function() {
        Route::post('/',   [TaxController::class, 'new']);
        Route::get('/',    [TaxController::class, 'get']);
        Route::put('/',    [TaxController::class, 'update']);
    });

    Route::prefix('transaction')->group(function() {
        Route::post('/',                [TransactionController::class, 'new']);
        Route::get('/',                 [TransactionController::class, 'get']);
        Route::put('/confirmPayment',   [TransactionController::class, 'confirmPayment']);
    });

});
