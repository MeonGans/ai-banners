<?php

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


Route::post('register', [\App\Http\Controllers\RegisterController::class, 'register']);
Route::post('login', [\App\Http\Controllers\RegisterController::class, 'login']);
Route::get('logout', [\App\Http\Controllers\RegisterController::class, 'logout']);

Route::apiResources([
    'banners' => \App\Http\Controllers\BannerController::class,
    'categories' => \App\Http\Controllers\CategoryController::class,
    'bills' => \App\Http\Controllers\BillController::class,
], ['except' => ['show', 'index']]);

Route::apiResources([
    'banners' => \App\Http\Controllers\BannerController::class,
    'categories' => \App\Http\Controllers\CategoryController::class,
    'bills' => \App\Http\Controllers\BillController::class,
    'groups' => \App\Http\Controllers\GroupController::class,
], ['except' => ['update', 'store', 'destroy']]);

Route::post('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore']);
Route::get('categories/{category}/{option?}', [\App\Http\Controllers\CategoryController::class, 'show']);
