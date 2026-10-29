<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FitnessCommunityController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Calendar / Events
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Notes
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::patch('/notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('profile.export');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');

    // Fitness Community
    Route::get('/fitness', [FitnessCommunityController::class, 'index'])->name('fitness.index');
    Route::post('/fitness', [FitnessCommunityController::class, 'store'])->name('fitness.store');
    Route::get('/fitness/{post}/edit', [FitnessCommunityController::class, 'edit'])->name('fitness.edit');
    Route::put('/fitness/{post}', [FitnessCommunityController::class, 'update'])->name('fitness.update');
    Route::post('/fitness/{post}/like', [FitnessCommunityController::class, 'toggleLike'])->name('fitness.like');
    Route::post('/fitness/{post}/comments', [FitnessCommunityController::class, 'storeComment'])->name('fitness.comment');
    Route::post('/fitness/{post}/save', [FitnessCommunityController::class, 'toggleSave'])->name('fitness.save');
    Route::post('/fitness/{post}/hide', [FitnessCommunityController::class, 'hide'])->name('fitness.hide');
    Route::delete('/fitness/{post}/hide', [FitnessCommunityController::class, 'unhide'])->name('fitness.unhide');
    Route::post('/fitness/{post}/report', [FitnessCommunityController::class, 'report'])->name('fitness.report');
    Route::get('/fitness/{post}/share', [FitnessCommunityController::class, 'share'])->name('fitness.share');
    Route::delete('/fitness/{post}', [FitnessCommunityController::class, 'destroy'])->name('fitness.destroy');
    Route::put('/fitness/comments/{comment}', [FitnessCommunityController::class, 'updateComment'])->name('fitness.comment.update');
    Route::delete('/fitness/comments/{comment}', [FitnessCommunityController::class, 'destroyComment'])->name('fitness.comment.destroy');
    Route::post('/fitness/comments/{comment}/hide', [FitnessCommunityController::class, 'hideComment'])->name('fitness.comment.hide');
    Route::delete('/fitness/comments/{comment}/hide', [FitnessCommunityController::class, 'unhideComment'])->name('fitness.comment.unhide');
    Route::post('/fitness/comments/{comment}/report', [FitnessCommunityController::class, 'reportComment'])->name('fitness.comment.report');
    Route::get('/fitness/comments/{comment}/copy', [FitnessCommunityController::class, 'copyComment'])->name('fitness.comment.copy');
    Route::post('/fitness/comments/{comment}/reply', [FitnessCommunityController::class, 'replyComment'])->name('fitness.comment.reply');
    Route::post('/fitness/comments/{comment}/react', [FitnessCommunityController::class, 'toggleCommentReaction'])->name('fitness.comment.react');

    // Mark Fitness Community notifications as read
    Route::post('/fitness/notifications/read', function () {
        request()->user()
            ->unreadNotifications()
            ->where('data->type', 'fitness_activity')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    })->name('fitness.notifications.read');

});

require __DIR__ . '/auth.php';