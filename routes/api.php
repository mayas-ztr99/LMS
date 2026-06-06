<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Admin\CourseMediaController;
use App\Http\Controllers\Lesson\LessonController;

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

        //Course Images
        Route::post('/courses/{course}/images',[CourseMediaController::class, 'uploadImages']);
        Route::put('/courses/{course}/images',[CourseMediaController::class, 'updateImages']);
        Route::delete('/courses/{course}/images/{media}',[CourseMediaController::class, 'deleteImage']);
        //Course PDF
        Route::post('/courses/{course}/pdf',[CourseMediaController::class, 'uploadPdf']);
        Route::put('/courses/{course}/pdf',[CourseMediaController::class, 'updatePdf']);
        Route::delete('/courses/{course}/pdf',[CourseMediaController::class, 'deletePdf']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('courses',CourseController::class);
    Route::apiResource('lessons', LessonController::class);
});

