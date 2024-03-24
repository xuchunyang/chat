<?php

use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::apiResource('rooms', RoomController::class);

Route::get('/', function () {
    return view('welcome');
});
