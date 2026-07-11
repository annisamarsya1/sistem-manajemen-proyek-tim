<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Task
 * 
 * Model untuk merepresentasikan tabel tasks.
 * Menyimpan informasi tugas individu dalam sebuah proyek.
 */
class Task extends Model
{
    // Mengizinkan mass assignment untuk semua atribut
    protected $guarded = [];

    /**
     * Relasi ke model TeamProject.
     * Setiap tugas dimiliki oleh satu proyek (project_id).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(TeamProject::class, 'project_id');
    }

    /**
     * Relasi ke model User (assignee).
     * Setiap tugas dapat ditugaskan ke satu pengguna (assignee_id).
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Relasi ke model TimeLog.
     * Setiap tugas dapat memiliki banyak catatan waktu kerja (time logs).
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'task_id');
    }
}
