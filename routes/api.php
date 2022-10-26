<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('data', [AuthController::class, 'data'])->name('auth.data');
    });
});

Route::prefix('user')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::get('', [AdminController::class, 'showUsers'])->name('users');
        Route::get('{id}', [AdminController::class, 'showUser'])->name('user.show');
        Route::patch('{id}', [AdminController::class, 'updateUser'])->name('user.update');
        Route::delete('{id}', [AdminController::class, 'destroyUser'])->name('user.delete');
    }); 
});