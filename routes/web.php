<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/user-profile', function () {
        return view('profile.user-profile');
    })->name('user.profile');
    
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages');

    Route::prefix('api')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
        Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('/messages/{user}', [MessageController::class, 'getMessages']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::post('/messages/{user}/read', [MessageController::class, 'markAsRead']);
        Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
    });
});

Broadcast::routes(['middleware' => ['web', 'auth']]);

require __DIR__.'/auth.php';