<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StreamController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/analytics/user', [UserController::class, 'getUserInfo']);

Route::get('/analytics/streams', [StreamController::class, 'getLiveStreams']);



