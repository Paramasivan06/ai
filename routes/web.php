<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController; // Add this line
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('/chatbot', function () {
        return view('chatbot');
    })->name('chatbot.view');
    
    Route::post('/chatbot', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
});

require __DIR__.'/auth.php';