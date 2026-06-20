<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimeLogForm extends Component
{
    public string $projectId = '';

    public string $taskId = '';

    public string $startTime = '';

    public string $endTime = '';

    public string $notes = '';

    public bool $showModal = false;

    /** @var Collection<int, TeamProject> */
    public Collection $availableProjects;

    /** @var Collection<int, Task> */
    public Collection $availableTasks;

    public function mount(): void
    {
        $this->availableProjects = $this->loadProjects();
        $this->availableTasks = collect();
    }

    // ---------------------------------------------------------------------------
    // Typed auth helper
    // ---------------------------------------------------------------------------

    private function currentUser(): User
    {
        $user = Auth::user();

        assert($user instanceof User);

        return $user;
    }

    // ---------------------------------------------------------------------------
    // Load projects based on role
    // ---------------------------------------------------------------------------

    /** @return Collection<int, TeamProject> */
    private function loadProjects(): Collection
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            $projectIds = Task::where('assignee_id', $user->id)
                ->distinct()
                ->pluck('project_id');

            return TeamProject::whereIn('id', $projectIds)
                ->orderBy('title')
                ->get(['id', 'title']);
        }

        return TeamProject::orderBy('title')
            ->get(['id', 'title']);
    }

    // ---------------------------------------------------------------------------
    // Lifecycle hook: filter tasks when project changes
    // ---------------------------------------------------------------------------

    public function updatedProjectId(): void
    {
        $this->taskId = '';
        $this->availableTasks = collect();

        if ($this->projectId === '') {
            return;
        }

        $this->availableTasks = Task::where('project_id', $this->projectId)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    // ---------------------------------------------------------------------------
    // Save time log
    // ---------------------------------------------------------------------------

    public function saveTimeLog(): void
    {
        $this->validate([
            'projectId' => 'required|exists:team_projects,id',
            'taskId' => 'required|exists:tasks,id',
            'startTime' => 'required|date',
            'endTime' => 'required|date|after:startTime',
        ]);

        // Jam mulai tidak boleh lebih dari 5 menit ke depan
        if (Carbon::parse($this->startTime)->isAfter(now()->addMinutes(5))) {
            $this->addError('startTime', 'Jam mulai tidak boleh di masa depan.');

            return;
        }

        // Durasi maksimal 12 jam
        $durationMinutes = Carbon::parse($this->startTime)->diffInMinutes(Carbon::parse($this->endTime));

        if ($durationMinutes > 720) {
            $this->addError('endTime', 'Durasi log tidak boleh lebih dari 12 jam.');

            return;
        }

        // Validasi overlap (server-side)
        $overlap = TimeLog::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('start_time', '<', $this->endTime)
                        ->where('end_time', '>', $this->startTime);
                });
            })
            ->first();

        if ($overlap) {
            $start = Carbon::parse($overlap->start_time)->format('H:i');
            $end = Carbon::parse($overlap->end_time)->format('H:i');
            $this->addError('startTime', "Waktu ini bertabrakan dengan log Anda yang lain ({$start} - {$end}).");

            return;
        }

        $durationHours = round($durationMinutes / 60, 2);

        TimeLog::create([
            'user_id' => Auth::id(),
            'project_id' => $this->projectId,
            'task_id' => $this->taskId ?: null,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'duration_hours' => $durationHours,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Time log berhasil ditambahkan dan menunggu persetujuan.');
    }

    // ---------------------------------------------------------------------------
    // Reset form fields
    // ---------------------------------------------------------------------------

    public function resetForm(): void
    {
        $this->projectId = '';
        $this->taskId = '';
        $this->startTime = '';
        $this->endTime = '';
        $this->notes = '';
        $this->availableTasks = collect();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.time-log-form');
    }
}
