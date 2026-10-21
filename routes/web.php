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

    // Fitness Community
    Route::get('/fitness', [FitnessCommunityController::class, 'index'])->name('fitness.index');
    Route::post('/fitness', [FitnessCommunityController::class, 'store'])->name('fitness.store');
    Route::post('/fitness/{post}/like', [FitnessCommunityController::class, 'toggleLike'])->name('fitness.like');
    Route::post('/fitness/{post}/comments', [FitnessCommunityController::class, 'storeComment'])->name('fitness.comment');
    Route::delete('/fitness/{post}', [FitnessCommunityController::class, 'destroy'])->name('fitness.destroy');
    Route::delete('/fitness/comments/{comment}', [FitnessCommunityController::class, 'destroyComment'])->name('fitness.comment.destroy');
});

require __DIR__.'/auth.php';
