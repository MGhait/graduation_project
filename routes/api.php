<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ICController;
use App\Http\Controllers\Api\ICDetailsController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\ProfileController;
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
//    Route::post('/register', 'register');
    Route::post('/login', 'login');
    if (config('verification.way') == 'email'){
        Route::post('register', 'register');
        Route::get('/verify', 'verify');
    }
    Route::post('/sendEmail', 'sendVerificationEmail');
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

# ------------------- Profile Module ---------------#
Route::controller(ProfileController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/profile', 'index');
    Route::post('/profile', 'store');
});

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
    Route::post('/description', 'storeDescription');
});

Route::controller(ICDetailsController::class)->group(function () {
    Route::post('/ic/details/store-parameters', 'storeParameter');
    Route::post('/ic/details/store-details', 'storeDetails');
    Route::post('/ic/details/store-packages', 'storePackages');
    Route::post('/ic/details/store-features', 'storeFeatures');
    Route::get('/ic/details/', 'viewDetails');
});

Route::post('/file/upload', [FileController::class, 'save']);

