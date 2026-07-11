<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Daftar semua route atau URL yang bisa diakses via browser.
| Route ini secara default sudah termasuk proteksi CSRF.
*/

// Root redirect: Jika buka URL root (/), arahkan langsung ke halaman login.
Route::get('/', function () {
    return redirect()->route('login');
});

// ========== Guest Only ==========
// Route yang HANYA bisa diakses saat pengguna BELUM login (guest).
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login'); // Halaman Login
});

// ========== Authenticated ==========
// Route yang HANYA bisa diakses SETELAH pengguna berhasil login (auth).
Route::middleware('auth')->group(function () {

    // Halaman Dashboard utama (Dapat diakses oleh semua role)
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Project Studio — admin & project_manager (Fase 4)
    Route::get('/projects', \App\Livewire\ProjectStudio::class)
        ->middleware('admin_or_pm')
        ->name('projects');

    // Kanban Board — semua role (Fase 5)
    Route::get('/tasks', \App\Livewire\KanbanBoard::class)->name('tasks');


    // Timesheet — semua role
    Route::get('/timesheet', \App\Livewire\PersonalTimesheet::class)->name('timesheet');

    // Halaman Manajemen Pengguna — Dibatasi hanya untuk Admin
    Route::get('/users', \App\Livewire\UserManagement::class)
        ->middleware('admin_only')
        ->name('users');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
