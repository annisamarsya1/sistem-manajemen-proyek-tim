<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TimeLog
 * 
 * Model untuk merepresentasikan pencatatan waktu kerja (time logs).
 * Melacak waktu mulai, waktu selesai, deskripsi pekerjaan, 
 * dan status persetujuan (approval) oleh reviewer.
 */
class TimeLog extends Model
{
    protected $guarded = [];

    // Casts digunakan untuk memastikan atribut waktu di-cast secara otomatis ke object Carbon (datetime)
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Relasi ke model User (pekerja).
     * Log waktu ini milik satu pengguna yang mengerjakannya.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model TeamProject.
     * Log waktu ini terkait dengan satu proyek tertentu.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(TeamProject::class, 'project_id');
    }

    /**
     * Relasi ke model Task.
     * Log waktu ini terkait dengan satu tugas tertentu dalam proyek.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relasi ke model User (reviewer).
     * Log waktu ini disetujui (approved) atau ditolak oleh satu reviewer (misal: PM).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
