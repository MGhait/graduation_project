<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ICController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OTPController;
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
    Route::post('/reset-password', 'resetPassword');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});
Route::post('/forget-password', [OTPController::class, 'forgetPassword']);
Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('otp.verify');

# ------------------- Settings Module ---------------#
Route::get('/settings', SettingController::class);

# ------------------- Stores Module ---------------#
Route::get('/stores', StoreController::class);

# ------------------- Messages Module ---------------#
Route::post('/message', MessageController::class);


# ------------------- IC Module ---------------#
Route::prefix('ic')->controller(ICController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/popular', 'popularICs');
    Route::get('/get/{ic}', 'show');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/save', 'saveIC');
        Route::get('/saved', 'getSavedICs');
        Route::post('/remove', 'removeSavedIC');
    });
    Route::post('/imagestore', 'storeImage');
    Route::post('/search', 'searchIC');
    Route::post('/searchic', 'searchIC2');
    Route::post('/searchforic', 'search3');
    Route::post('/store', 'store');
    Route::post('/truthTable', 'storeTruthTable');
});

Route::post('/file/upload', [FileController::class, 'save']);

