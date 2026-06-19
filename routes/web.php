<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/projects', function () {
        return view('placeholders.projects');
    })->name('projects')->middleware('admin_or_pm');

    Route::get('/tasks', function () {
        return view('placeholders.tasks');
    })->name('tasks');

    Route::get('/timelogs', function () {
        return view('placeholders.timelogs');
    })->name('timelogs');

    Route::get('/timesheet', function () {
        return view('placeholders.timesheet');
    })->name('timesheet');

    Route::get('/users', function () {
        return view('placeholders.users');
    })->name('users')->middleware('admin_only');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

require __DIR__.'/settings.php';
