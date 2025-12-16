<?php

use App\Http\Controllers\Admin\MessageModerationController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'nickname'])->name('chat.nickname');
Route::post('/enter', [ChatController::class, 'enter'])->name('chat.enter');
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/messages', [ChatController::class, 'store'])->name('messages.store');
Route::get('/logout', [ChatController::class, 'logout'])->name('chat.logout');

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [MessageModerationController::class, 'index'])->name('dashboard');
    Route::post('/messages/{message}/approve', [MessageModerationController::class, 'approve'])->name('messages.approve');
    Route::post('/messages/{message}/reject', [MessageModerationController::class, 'reject'])->name('messages.reject');
});

require __DIR__.'/auth.php';
