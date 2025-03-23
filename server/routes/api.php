<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::group(['middleware' => ['cors']], function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/changePassword', [UserController::class, 'updatePassword']);
        Route::post('/changeProfilePic', [UserController::class, 'updateProfilePic']);
    });
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/email', [AuthController::class, 'regenerateCode']);
    Route::post('password/change', [AuthController::class, 'regeneratePassword']);
    Route::get('verifyEmail/{email}', [AuthController::class, 'verifyEmail'])->name('verifyEmail');
});
