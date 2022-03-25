<?php

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


Route::post('register', [\App\Http\Controllers\RegisterController::class, 'register'])->name('api.register');
Route::post('login', [\App\Http\Controllers\RegisterController::class, 'login'])->name('api.login');

Route::middleware('auth:api')->group(function () {
    Route::get('logout', [\App\Http\Controllers\RegisterController::class, 'logout'])->name('api.logout');
});

Route::middleware('admin')->group(function () {
    Route::apiResources([
        //Создание, обновление и удаление баннеров, категорий
        'banners' => \App\Http\Controllers\BannerController::class,
        'categories' => \App\Http\Controllers\CategoryController::class,
    ], ['except' => ['show', 'index']]);
    //Восстановление удалённой категории
    Route::post('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore']);
});

Route::apiResources([
    'banners' => \App\Http\Controllers\BannerController::class,
    'categories' => \App\Http\Controllers\CategoryController::class,
], ['except' => ['update', 'store', 'destroy']]);

Route::get('categories/{category}/{option?}', [\App\Http\Controllers\CategoryController::class, 'show']);
