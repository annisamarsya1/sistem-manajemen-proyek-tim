<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class TeamProject
 * 
 * Model untuk merepresentasikan tabel team_projects.
 * Menyimpan data tentang sebuah proyek, termasuk pembuat, tugas-tugas di dalamnya,
 * dan log waktu yang dihabiskan untuk proyek tersebut.
 */
class TeamProject extends Model
{
    protected $guarded = [];

    /**
     * Relasi ke model User (pembuat).
     * Proyek dibuat oleh satu orang pengguna (created_by).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke model Task.
     * Sebuah proyek dapat memiliki banyak tugas di dalamnya.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /**
     * Relasi ke model TimeLog.
     * Total waktu kerja (time logs) yang dihabiskan untuk semua tugas dalam proyek ini.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'project_id');
    }
}
