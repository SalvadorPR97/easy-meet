<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
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
        Route::patch('/changePassword', [UserController::class, 'updatePassword']);
        Route::patch('/changeProfilePic', [UserController::class, 'updateProfilePic']);

        Route::post('events/store', [EventController::class, 'store'])->name('events.store');
        Route::delete('events/delete', [EventController::class, 'delete'])->name('events.delete');
    });
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/email', [AuthController::class, 'regenerateCode']);
    Route::post('password/change', [AuthController::class, 'regeneratePassword']);
    Route::get('verifyEmail/{email}', [AuthController::class, 'verifyEmail'])->name('verifyEmail');

    Route::get('events/{city}', [EventController::class, 'indexByCity'])->name('events.indexByCity');

});
