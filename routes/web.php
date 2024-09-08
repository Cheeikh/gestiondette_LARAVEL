<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('fidelite.carte');
});

Route::view('/swagger', 'swagger');
