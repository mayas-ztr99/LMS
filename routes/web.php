<?php

//use App\Http\Controllers\Api\FcmTokenController;
//use App\Http\Controllers\FcmController;

use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
});
//Route::post('/api/fcm-token', [FcmTokenController::class,'store'])->middleware('auth');
Route::get('/chat/{conversation?}', function ($conversationId = null) {
    return view('chat', ['conversationId' => $conversationId]);
})->middleware('auth')->name('chat');
