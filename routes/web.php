<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

# ------------------- Policy Module ---------------#
Route::get('/policy', function () {
    return view('policyView');
});
Route::get('/data-deletion', function () {
    return view('dataDeletion');
});
