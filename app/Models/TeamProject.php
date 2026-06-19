<?php

namespace App\Models;

use Database\Factories\TeamProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $client_name
 * @property Carbon|null $start_date
 * @property Carbon $deadline
 * @property float $budget
 * @property string $priority
 * @property string $status
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TeamProject extends Model
{
    /** @use HasFactory<TeamProjectFactory> */
    use HasFactory;

    protected $table = 'team_projects';

    protected $fillable = [
        'title',
        'description',
        'client_name',
        'start_date',
        'deadline',
        'budget',
        'priority',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'project_id');
    }
}
