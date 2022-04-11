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
//Route::middleware('cors')->group(function () {

    route::get('/', function () {
        return 'HELLO';
    });

    route::post('hello', function () {
        return 'HELLO';
    });



    Route::post('register', [\App\Http\Controllers\RegisterController::class, 'register'])->name('api.register');
    Route::post('login', [\App\Http\Controllers\RegisterController::class, 'login'])->name('api.login');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [\App\Http\Controllers\RegisterController::class, 'logout'])->name('api.logout');
    });

    Route::middleware('admin')->group(function () {
        Route::get('user', [\App\Http\Controllers\RegisterController::class, 'user']);
        Route::apiResources([
            //Создание, обновление и удаление баннеров, категорий
            'banners' => \App\Http\Controllers\BannerController::class,
            'categories' => \App\Http\Controllers\CategoryController::class,
        ], ['except' => ['index', 'show']]);
        //Восстановление удалённой категории
        Route::get('banners', [\App\Http\Controllers\BannerController::class, 'index']);
        Route::post('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore']);
        Route::post('banners/{banner}/restore', [\App\Http\Controllers\BannerController::class, 'restore']);
        Route::post('upload_file', [\App\Http\Controllers\FileController::class, 'upload']);
    });

    Route::apiResources([
        'banners' => \App\Http\Controllers\BannerController::class,
        'categories' => \App\Http\Controllers\CategoryController::class,
    ], ['except' => ['index', 'update', 'store', 'destroy']]);
    Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'index']);

    Route::get('categories/{category}/{option?}', [\App\Http\Controllers\CategoryController::class, 'show']);

//});
