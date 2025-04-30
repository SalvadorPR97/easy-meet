<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventsUsersController;
use App\Http\Controllers\SubcategoryController;
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

        Route::post('eventsUsers/join/{id}', [EventsUsersController::class, 'joinEvent'])->name('eventsUsers.join');
        Route::get('eventsUsers/joined', [EventsUsersController::class, 'joinedEvents'])->name('eventsUsers.getJoinedEvents');
    });
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/email', [AuthController::class, 'regenerateCode']);
    Route::post('password/change', [AuthController::class, 'regeneratePassword']);
    Route::get('verifyEmail/{email}', [AuthController::class, 'verifyEmail'])->name('verifyEmail');

    Route::get('categories', [CategoryController::class, 'index'])->name('category.index');
    Route::get('subcategories/{id}', [SubcategoryController::class, 'index'])->name('category.index');
    Route::get('cities', [EventController::class, 'cities'])->name('events.cities');
    Route::get('events/{city}', [EventController::class, 'indexByCity'])->name('events.indexByCity');

});
