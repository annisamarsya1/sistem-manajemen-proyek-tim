<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamProject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kanban Board')]
#[Layout('layouts.app', ['title' => 'Kanban Board'])]
class KanbanBoard extends Component
{
    // ---------------------------------------------------------------------------
    // Filter
    // ---------------------------------------------------------------------------

    public string $filterProjectId = '';

    // ---------------------------------------------------------------------------
    // Task Form (Create / Edit)
    // ---------------------------------------------------------------------------

    public string $taskTitle = '';

    public string $taskDescription = '';

    public string $taskProjectId = '';

    public string $taskAssigneeId = '';

    public string $taskPriority = 'medium';

    public string $taskStartDate = '';

    public string $taskDueDate = '';

    public string $taskStatus = 'todo';

    public int $taskProgressPercent = 0;

    public ?int $editingTaskId = null;

    // ---------------------------------------------------------------------------
    // Task Detail / Comments
    // ---------------------------------------------------------------------------

    public ?int $selectedTaskId = null;

    public string $newComment = '';

    // ---------------------------------------------------------------------------
    // Shared data
    // ---------------------------------------------------------------------------

    /** @var Collection<int, User> */
    public Collection $employees;

    /** @var Collection<int, TeamProject> */
    public Collection $projects;

    // ---------------------------------------------------------------------------
    // Boot
    // ---------------------------------------------------------------------------

    public function mount(): void
    {
        $this->employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->projects = $this->loadProjectsForFilter();
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

    private function isAdminOrPm(): bool
    {
        return in_array($this->currentUser()->role, ['admin', 'project_manager']);
    }

    // ---------------------------------------------------------------------------
    // Projects dropdown
    // ---------------------------------------------------------------------------

    /** @return Collection<int, TeamProject> */
    private function loadProjectsForFilter(): Collection
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

        return TeamProject::orderBy('title')->get(['id', 'title']);
    }

    // ---------------------------------------------------------------------------
    // Kanban data
    // ---------------------------------------------------------------------------

    /**
     * @return array<string, Collection<int, Task>>
     */
    public function getTasksByStatusProperty(): array
    {
        $user = $this->currentUser();

        $query = Task::with(['assignee:id,name', 'project:id,title'])
            ->orderBy('priority', 'desc')
            ->orderBy('due_date');

        if ($user->role === 'employee') {
            $query->where('assignee_id', $user->id);
        }

        if ($this->filterProjectId !== '') {
            $query->where('project_id', $this->filterProjectId);
        }

        $tasks = $query->get();

        $grouped = $tasks->groupBy('status');

        return [
            'todo' => $grouped->get('todo', collect()),
            'in_progress' => $grouped->get('in_progress', collect()),
            'review' => $grouped->get('review', collect()),
            'done' => $grouped->get('done', collect()),
        ];
    }

    // ---------------------------------------------------------------------------
    // Drag-and-drop
    // ---------------------------------------------------------------------------

    public function updateTaskStatus(int $taskId, string $newStatus): void
    {
        $validStatuses = ['todo', 'in_progress', 'review', 'done'];

        if (! in_array($newStatus, $validStatuses)) {
            return;
        }

        $task = Task::findOrFail($taskId);

        if ($this->currentUser()->role === 'employee' && $task->assignee_id !== $this->currentUser()->id) {
            session()->flash('error', 'Anda hanya bisa memindahkan tugas milik Anda sendiri.');

            return;
        }

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'done' ? now() : null,
        ]);
    }

    // ---------------------------------------------------------------------------
    // Create Task
    // ---------------------------------------------------------------------------

    public function openCreateModal(): void
    {
        if (! $this->isAdminOrPm()) {
            abort(403);
        }

        $this->resetTaskForm();
        $this->editingTaskId = null;
        $this->dispatch('open-task-modal');
    }

    public function saveTask(): void
    {
        if (! $this->isAdminOrPm()) {
            abort(403);
        }

        $validated = $this->validate([
            'taskTitle' => ['required', 'string', 'max:200'],
            'taskDescription' => ['nullable', 'string'],
            'taskProjectId' => ['required', 'exists:team_projects,id'],
            'taskAssigneeId' => ['nullable', 'exists:users,id'],
            'taskPriority' => ['required', 'in:low,medium,high'],
            'taskStartDate' => ['nullable', 'date'],
            'taskDueDate' => ['nullable', 'date', 'after_or_equal:taskStartDate'],
        ]);

        // Validate due_date is not before the project's start_date
        if ($validated['taskDueDate'] !== null && $validated['taskDueDate'] !== '') {
            $project = TeamProject::find($validated['taskProjectId']);

            if ($project && $project->start_date && $validated['taskDueDate'] < $project->start_date->format('Y-m-d')) {
                $this->addError('taskDueDate', 'Due date tidak boleh sebelum tanggal mulai proyek ('.$project->start_date->format('d/m/Y').').');

                return;
            }
        }

        Task::create([
            'title' => $validated['taskTitle'],
            'description' => $validated['taskDescription'] ?? null,
            'project_id' => (int) $validated['taskProjectId'],
            'assignee_id' => $validated['taskAssigneeId'] !== '' ? (int) $validated['taskAssigneeId'] : null,
            'priority' => $validated['taskPriority'],
            'start_date' => $validated['taskStartDate'] ?: null,
            'due_date' => $validated['taskDueDate'] ?: null,
            'status' => 'todo',
            'progress_percent' => 0,
        ]);

        $this->resetTaskForm();
        $this->dispatch('close-task-modal');
        session()->flash('success', 'Tugas berhasil dibuat.');
    }

    // ---------------------------------------------------------------------------
    // Edit Task
    // ---------------------------------------------------------------------------

    public function editTask(int $id): void
    {
        if (! $this->isAdminOrPm()) {
            abort(403);
        }

        $task = Task::findOrFail($id);

        $this->editingTaskId = $task->id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description ?? '';
        $this->taskProjectId = (string) $task->project_id;
        $this->taskAssigneeId = $task->assignee_id ? (string) $task->assignee_id : '';
        $this->taskPriority = $task->priority;
        $this->taskStartDate = $task->start_date ? $task->start_date->format('Y-m-d') : '';
        $this->taskDueDate = $task->due_date ? $task->due_date->format('Y-m-d') : '';
        $this->taskStatus = $task->status;
        $this->taskProgressPercent = (int) $task->progress_percent;

        $this->dispatch('open-task-modal');
    }

    public function updateTask(): void
    {
        if (! $this->isAdminOrPm()) {
            abort(403);
        }

        $validated = $this->validate([
            'taskTitle' => ['required', 'string', 'max:200'],
            'taskDescription' => ['nullable', 'string'],
            'taskProjectId' => ['required', 'exists:team_projects,id'],
            'taskAssigneeId' => ['nullable', 'exists:users,id'],
            'taskPriority' => ['required', 'in:low,medium,high'],
            'taskStartDate' => ['nullable', 'date'],
            'taskDueDate' => ['nullable', 'date', 'after_or_equal:taskStartDate'],
            'taskStatus' => ['required', 'in:todo,in_progress,review,done'],
            'taskProgressPercent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // Validate due_date is not before the project's start_date
        if ($validated['taskDueDate'] !== null && $validated['taskDueDate'] !== '') {
            $project = TeamProject::find($validated['taskProjectId']);

            if ($project && $project->start_date && $validated['taskDueDate'] < $project->start_date->format('Y-m-d')) {
                $this->addError('taskDueDate', 'Due date tidak boleh sebelum tanggal mulai proyek ('.$project->start_date->format('d/m/Y').').');

                return;
            }
        }

        $task = Task::findOrFail($this->editingTaskId);

        $task->update([
            'title' => $validated['taskTitle'],
            'description' => $validated['taskDescription'] ?? null,
            'project_id' => (int) $validated['taskProjectId'],
            'assignee_id' => $validated['taskAssigneeId'] !== '' ? (int) $validated['taskAssigneeId'] : null,
            'priority' => $validated['taskPriority'],
            'start_date' => $validated['taskStartDate'] ?: null,
            'due_date' => $validated['taskDueDate'] ?: null,
            'status' => $validated['taskStatus'],
            'progress_percent' => $validated['taskProgressPercent'],
            'completed_at' => $validated['taskStatus'] === 'done' ? ($task->completed_at ?? now()) : null,
        ]);

        $this->resetTaskForm();
        $this->dispatch('close-task-modal');
        session()->flash('success', 'Tugas berhasil diperbarui.');
    }

    // ---------------------------------------------------------------------------
    // Delete Task
    // ---------------------------------------------------------------------------

    public function deleteTask(int $id): void
    {
        if (! $this->isAdminOrPm()) {
            abort(403);
        }

        $task = Task::findOrFail($id);
        $task->comments()->delete();
        $task->delete();

        session()->flash('success', 'Tugas berhasil dihapus.');
    }

    // ---------------------------------------------------------------------------
    // Task Detail & Comments
    // ---------------------------------------------------------------------------

    public function openTask(int $id): void
    {
        $task = Task::findOrFail($id);
        $user = $this->currentUser();

        // Employees can only view tasks assigned to them
        if ($user->role === 'employee' && $task->assignee_id !== $user->id) {
            abort(403);
        }

        $this->selectedTaskId = $id;
        $this->newComment = '';
        $this->dispatch('open-detail-modal');
    }

    public function closeTask(): void
    {
        $this->selectedTaskId = null;
        $this->newComment = '';
    }

    public function addComment(): void
    {
        $this->validate(['newComment' => ['required', 'string', 'min:1', 'max:2000']]);

        TaskComment::create([
            'task_id' => $this->selectedTaskId,
            'user_id' => $this->currentUser()->id,
            'comment' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    private function resetTaskForm(): void
    {
        $this->taskTitle = '';
        $this->taskDescription = '';
        $this->taskProjectId = '';
        $this->taskAssigneeId = '';
        $this->taskPriority = 'medium';
        $this->taskStartDate = '';
        $this->taskDueDate = '';
        $this->taskStatus = 'todo';
        $this->taskProgressPercent = 0;
        $this->editingTaskId = null;
        $this->resetValidation();
    }

    // ---------------------------------------------------------------------------
    // Render
    // ---------------------------------------------------------------------------

    public function render(): View
    {
        $selectedTask = null;
        $taskComments = collect();

        if ($this->selectedTaskId !== null) {
            $selectedTask = Task::with(['assignee:id,name', 'project:id,title'])
                ->findOrFail($this->selectedTaskId);
            $taskComments = $selectedTask->comments()
                ->with('user:id,name')
                ->orderBy('created_at')
                ->get();
        }

        return view('livewire.kanban-board', [
            'tasksByStatus' => $this->tasksByStatus,
            'selectedTask' => $selectedTask,
            'taskComments' => $taskComments,
        ]);
    }
}
