<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::apiResource('rooms', RoomController::class);
Route::apiResource('rooms.messages', MessageController::class)->shallow();

Route::get('/', function () {
    return view('welcome');
});
