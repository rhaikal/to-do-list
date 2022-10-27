<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use PHPUnit\TextUI\XmlConfiguration\Group;

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

Route::post('/auth/login', [UserController::class, 'login'])->name('auth.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('auth.register');

Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['admin'])->group(function () {
      Route::apiResource('user', UserController::class)->except('store');
    });
   
    Route::prefix('auth')->group(function () {
       Route::controller(UserController::class)->group(function() {
           Route::get('data', 'data')->name('auth.data');
           Route::patch('setting', 'setting')->name('auth.setting');
           Route::post('refresh', 'refresh')->name('auth.refresh');
           Route::post('logout', 'logout')->name('auth.logout');
        });
    });

    Route::apiResource('todo', TodoController::class);
});
