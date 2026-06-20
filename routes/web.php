<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\KanbanBoard;
use App\Livewire\PersonalTimesheet;
use App\Livewire\ProjectStudio;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/projects', ProjectStudio::class)->name('projects')->middleware('admin_or_pm');

    Route::get('/tasks', KanbanBoard::class)->name('tasks');

    Route::get('/timelogs', Dashboard::class)->name('timelogs');

    Route::get('/timesheet', PersonalTimesheet::class)->name('timesheet');

    Route::get('/users', UserManagement::class)->name('users')->middleware('admin_only');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

require __DIR__.'/settings.php';
