<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('sign-in');
});

Route::get('/register', function () {
    return view('sign-up');
});
