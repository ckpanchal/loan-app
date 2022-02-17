<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\LoanApproveController;

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

Route::group(['middleware' => 'api'], function($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::group(['middleware' => 'jwt.verify',], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');

    // loan application routes
    Route::post('/apply-for-loan', [LoanApplicationController::class, 'applyForLoan'])->name('loan.applyForLoan');
    Route::post('/emi-payment', [LoanApplicationController::class, 'emiPayment'])->name('loan.emiPayment');
    
    // loan approve routes
    Route::get('/approve-loan/{id}', [LoanApproveController::class, 'approveLoan'])->name('loan.approveLoan');
});