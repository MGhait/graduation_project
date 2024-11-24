<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/', function (Request $request) {
    dump('hello from API');
});

# ------------------- Auth Module ---------------#
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

# ------------------- Settings Module ---------------#
Route::get('/settings', SettingController::class);

# ------------------- Stores Module ---------------#
Route::get('/stores', StoreController::class);

# ------------------- Messages Module ---------------#
Route::post('/message', MessageController::class);
