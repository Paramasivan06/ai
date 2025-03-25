<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;



Route::get('/chatbot', function () {
    return view('chatbot');
});

Route::post('/chatbot', [App\Http\Controllers\ChatbotController::class, 'chat']);

Route::get('/', function () {
    return view('welcome');
});
