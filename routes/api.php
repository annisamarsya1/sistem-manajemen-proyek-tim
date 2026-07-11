<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\TeamProject;
use App\Models\TimeLog;

/**
 * ========== Public API Endpoints ==========
 * Endpoint publik yang tidak membutuhkan otentikasi.
 * Biasanya digunakan oleh Landing Page untuk menampilkan data statis/umum.
 */
Route::get('/public/stats', function () {
    // Hitung waktu awal dan akhir minggu berjalan
    $weekStart = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
    $weekEnd   = now()->endOfDay(); // Matched with dashboard logic

    return [
        'active_projects'        => TeamProject::whereNotIn('status', ['completed', 'cancelled'])->count(),
        'completed_projects'     => TeamProject::where('status', 'completed')->count(),
        'logged_hours_this_week' => (float) TimeLog::where('start_time', '>=', $weekStart)
                                        ->where('start_time', '<=', $weekEnd)
                                        ->whereIn('status', ['approved', 'pending'])
                                        ->sum('duration_hours'),
    ];
});

/**
 * Endpoint kontak untuk menerima pesan dari form kontak Landing Page.
 */
Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email',
        'message' => 'required|string|max:2000',
    ]);
    
    // Process contact message here (e.g., store to DB or send email)
    // For now, return success directly
    return ['status' => 'success', 'message' => 'Pesan berhasil dikirim.'];
});

/**
 * ========== Protected API Endpoints ==========
 * Endpoint privat yang mewajibkan pengguna untuk login menggunakan token (Sanctum).
 */
Route::get('/user', function (Request $request) {
    // Load relations using eager loading (with) to optimize query performance as per rule
    // Example (if user has relations): return $request->user()->load('tasks');
    return $request->user();
})->middleware('auth:sanctum');
