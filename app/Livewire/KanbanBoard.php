<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TeamProject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Livewire Component: KanbanBoard
 * 
 * Mengelola antarmuka drag-and-drop atau perpindahan status untuk Tugas (Tasks).
 * Memiliki fitur pemisahan tugas berdasarkan status (todo, in_progress, review, done),
 * filter per proyek, pembuatan tugas baru, komentar diskusi tugas, dan update status realtime.
 */
#[Layout('components.layouts.app')]
class KanbanBoard extends Component
{
    public string $filterProjectId = '';
    
    // Properties for creating/editing a task
    public bool $showTaskModal = false;
    public ?int $editingTaskId = null;
    
    public string $taskTitle = '';
    public string $taskDescription = '';
    public string $taskProjectId = '';
    public string $taskAssigneeId = '';
    public string $taskPriority = 'medium';
    public string $taskStartDate = '';
    public string $taskDueDate = '';
    public string $taskStatus = 'todo';
    public string $taskProgress = '0';
    
    public $employees = [];

    // Properties for task detail & comments
    public bool $showDetailModal = false;
    public ?int $selectedTaskId = null;
    public string $newComment = '';
    public $selectedTask = null;

    public function mount()
    {
        $this->title = 'Kanban Board';
        $this->employees = User::where('role', 'employee')->where('is_active', true)->get();
    }

    public function getProjectsProperty()
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'project_manager'])) {
            return TeamProject::all();
        }

        // For employee, fetch projects that have tasks assigned to them
        $projectIds = Task::where('assignee_id', $user->id)->pluck('project_id')->unique();
        return TeamProject::whereIn('id', $projectIds)->get();
    }

    /**
     * Computed Property: Mengelompokkan tugas berdasarkan kolom status.
     * Menerapkan filter proyek yang sedang dipilih.
     */
    public function getTasksByStatusProperty()
    {
        $query = Task::with(['project', 'assignee']);

        if ($this->filterProjectId) {
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

    /**
     * Memperbarui status tugas (misal dipindah dari 'todo' ke 'in_progress').
     * Memastikan hak akses, di mana karyawan hanya bisa memindah tugas mereka sendiri.
     */
    public function updateTaskStatus(int $taskId, string $newStatus): void
    {
        $validStatuses = ['todo', 'in_progress', 'review', 'done'];
        if (!in_array($newStatus, $validStatuses)) {
            return;
        }

        $task = Task::findOrFail($taskId);

        if (Auth::user()->role === 'employee' && $task->assignee_id !== Auth::id()) {
            session()->flash('error', 'Anda hanya bisa memindahkan tugas milik Anda sendiri.');
            return;
        }

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'done' ? now() : null,
        ]);
    }

    public function createTask(): void
    {
        $this->checkAdminOrPm();
        $this->resetTaskForm();
        $this->showTaskModal = true;
    }

    public function editTask(int $id): void
    {
        $this->checkAdminOrPm();
        $task = Task::findOrFail($id);

        $this->editingTaskId = $task->id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description ?? '';
        $this->taskProjectId = (string)$task->project_id;
        $this->taskAssigneeId = $task->assignee_id ? (string)$task->assignee_id : '';
        $this->taskPriority = $task->priority;
        $this->taskStartDate = $task->start_date ?? '';
        $this->taskDueDate = $task->due_date ?? '';
        $this->taskStatus = $task->status;
        $this->taskProgress = (string)$task->progress_percent;

        $this->showTaskModal = true;
    }

    /**
     * Menyimpan (Create / Update) tugas dari form modal.
     * Melakukan validasi, terutama mengecek batas tanggal tugas terhadap durasi proyek.
     */
    public function saveTask(): void
    {
        $this->checkAdminOrPm();

        $this->validate([
            'taskTitle' => ['required', 'max:200'],
            'taskProjectId' => ['required', 'exists:team_projects,id'],
            'taskPriority' => ['required', 'in:low,medium,high'],
            'taskDueDate' => ['nullable', 'date'],
            'taskAssigneeId' => ['nullable', 'exists:users,id'],
        ], [
            'taskTitle.required' => 'Judul tugas wajib diisi.',
            'taskProjectId.required' => 'Proyek wajib dipilih.',
            'taskPriority.in' => 'Prioritas tidak valid.',
        ]);

        if ($this->taskDueDate) {
            $project = \App\Models\TeamProject::find($this->taskProjectId);
            if ($project && $project->start_date) {
                if (\Carbon\Carbon::parse($this->taskDueDate)->startOfDay()->lt(\Carbon\Carbon::parse($project->start_date)->startOfDay())) {
                    $this->addError('taskDueDate', 'Tenggat waktu tidak boleh sebelum tanggal mulai proyek (' . \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') . ').');
                    return;
                }
            }
        }

        $data = [
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
            'project_id' => $this->taskProjectId,
            'assignee_id' => $this->taskAssigneeId ?: null,
            'priority' => $this->taskPriority,
            'start_date' => $this->taskStartDate ?: null,
            'due_date' => $this->taskDueDate ?: null,
        ];

        if ($this->editingTaskId) {
            $data['status'] = $this->taskStatus;
            $data['progress_percent'] = $this->taskProgress !== '' ? $this->taskProgress : 0;
            if ($this->taskStatus === 'done' && !Task::find($this->editingTaskId)->completed_at) {
                $data['completed_at'] = now();
            } elseif ($this->taskStatus !== 'done') {
                $data['completed_at'] = null;
            }

            $task = Task::findOrFail($this->editingTaskId);
            $task->update($data);
            session()->flash('success', 'Tugas berhasil diperbarui.');
        } else {
            Task::create($data);
            session()->flash('success', 'Tugas berhasil dibuat.');
        }

        $this->closeTaskModal();
    }

    public function deleteTask(int $id): void
    {
        $this->checkAdminOrPm();
        $task = Task::findOrFail($id);
        $task->delete();
        
        session()->flash('success', 'Tugas berhasil dihapus.');
        if ($this->selectedTaskId === $id) {
            $this->closeDetailModal();
        }
    }

    public function openTask(int $id): void
    {
        $this->selectedTaskId = $id;
        $this->loadSelectedTask();
        $this->showDetailModal = true;
    }
    
    private function loadSelectedTask(): void
    {
        if ($this->selectedTaskId) {
            $this->selectedTask = Task::with(['project', 'assignee'])->findOrFail($this->selectedTaskId);
        }
    }

    public function addComment(): void
    {
        $this->validate(['newComment' => 'required|min:1|max:2000'], [
            'newComment.required' => 'Komentar tidak boleh kosong.',
            'newComment.max' => 'Komentar terlalu panjang.',
        ]);
        
        TaskComment::create([
            'task_id' => $this->selectedTaskId,
            'user_id' => Auth::id(),
            'comment' => $this->newComment,
        ]);
        
        $this->newComment = '';
        $this->loadSelectedTask(); // refresh the task data if needed, though comments are queried in view
    }

    public function closeTaskModal(): void
    {
        $this->resetTaskForm();
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedTaskId = null;
        $this->selectedTask = null;
        $this->newComment = '';
    }

    private function resetTaskForm(): void
    {
        $this->editingTaskId = null;
        $this->taskTitle = '';
        $this->taskDescription = '';
        $this->taskProjectId = '';
        $this->taskAssigneeId = '';
        $this->taskPriority = 'medium';
        $this->taskStartDate = '';
        $this->taskDueDate = '';
        $this->taskStatus = 'todo';
        $this->taskProgress = '0';
        $this->showTaskModal = false;
        $this->resetValidation();
    }

    private function checkAdminOrPm(): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'project_manager'])) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Admin dan Project Manager.');
        }
    }

    public function render()
    {
        $comments = collect();
        if ($this->selectedTaskId) {
            $comments = TaskComment::with('user')
                ->where('task_id', $this->selectedTaskId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.kanban-board', [
            'projects' => $this->projects,
            'tasksByStatus' => $this->tasksByStatus,
            'comments' => $comments,
        ])->title('Kanban Board');
    }
}
