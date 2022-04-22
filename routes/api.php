<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\CompaniesController;

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

Route::get('/users', [UsersController::class, 'index']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::post('/users', [UsersController::class, 'store']);
Route::post('/users/{id}', [UsersController::class, 'update']);
Route::get('/destroy_users', [UsersController::class, 'destroy']);
Route::get('/users/payments', [UsersController::class, 'payments']);



Route::get('/payments', [PaymentsController::class, 'index']);
Route::get('/payments/{id}', [PaymentsController::class, 'show']);
Route::post('/payments', [PaymentsController::class, 'store']);

Route::get('/receipts/{payment}', [PaymentsController::class, 'paymentReceipt']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/otps', [OtpController::class, 'index']);
Route::post('/checkOtp', [OtpController::class, 'check']);
Route::post('/destroyOtp', [OtpController::class, 'destroy']);

Route::get('/companies', [CompaniesController::class, 'index']);
Route::get('/companies/{id}/payments', [CompaniesController::class, 'payments']);