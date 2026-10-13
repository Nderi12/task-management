<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks')->name('home');

// Task manager
Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');
Route::resource('tasks', TaskController::class)->except('show');

// Starter kit (auth + settings) - not used by the task manager itself
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
