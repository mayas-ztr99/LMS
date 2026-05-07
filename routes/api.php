<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;


Route::get('/test',[TestController::class,'test']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role.check:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::post('/assign-role', [RoleController::class, 'assignRole']);
        Route::delete('/revoke-role', [RoleController::class, 'revokeRole']);
        Route::put('/update-role', [RoleController::class, 'updateRole']);
});
