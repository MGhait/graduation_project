<?php

use App\Filament\Admin\Resources\UserResource\Pages\LocationMap;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
//use App\Filament\Pages\LocationMap;

Route::get('/', function () {
    return 'hello from admin';
});
Route::controller(AdminController::class)->group(function () {

//    Route::middleware('guest')->group(function () {
        Route::post('/login', 'login');
        if (config('verification.way') == 'email'){
            Route::post('register', 'register');
            Route::get('/verify', 'verify');
        }

    Route::middleware('auth:admin')->group(function () {
        Route::post('/sendEmail', 'sendVerificationEmail');
    });

});


//Route::get('/location-map/{id}', function ($id) {
//    $page = new LocationMap();
//    $page->mount($id); // Pass the record ID to the page
//    return $page->render();
//})->middleware('web');
