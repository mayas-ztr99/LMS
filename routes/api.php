<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test',[TestController::class,'test']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
});
