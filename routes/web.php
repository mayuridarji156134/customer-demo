<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customers.index'); // or any other view you have created
});

Route::get('/{pathMatch}', function () {
    return view('customers.index'); // or any other view you have created
})->where('pathMatch',".*");
