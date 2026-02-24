<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protected routes (only logged-in users)
Route::middleware(['auth'])->group(function () {
    // Categories routes
    Route::resource('categories', CategoryController::class);
    
    // Tasks routes
    Route::resource('tasks', TaskController::class);
    
    // Custom route for restoring tasks
    Route::post('tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
});