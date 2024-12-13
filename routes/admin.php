<?php

use App\Http\Controllers\Api\AdminController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'hello from admin';
});
Route::controller(AdminController::class)->group(function () {

//    Route::middleware('guest')->group(function () {
        Route::post('register', 'register');
        Route::post('/login', 'login');
        Route::get('/verify', 'verify');

    Route::middleware('auth:admin')->group(function () {
        Route::post('/sendEmail', 'sendVerificationEmail');
    });

});
