<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskComment
 * 
 * Model untuk merepresentasikan tabel task_comments.
 * Menyimpan data komentar atau pesan diskusi pada suatu tugas.
 */
class TaskComment extends Model
{
    protected $guarded = [];

    /**
     * Relasi ke model Task.
     * Komentar terikat pada satu tugas.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relasi ke model User.
     * Mengetahui siapa penulis/pengguna yang membuat komentar tersebut.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
