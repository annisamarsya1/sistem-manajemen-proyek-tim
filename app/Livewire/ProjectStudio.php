<?php

namespace App\Livewire;

use App\Models\TeamProject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Project Studio')]
#[Layout('layouts.app', ['title' => 'Project Studio'])]
class ProjectStudio extends Component
{
    use WithPagination;

    // ---------------------------------------------------------------------------
    // Filters
    // ---------------------------------------------------------------------------

    public string $filterStatus = '';

    public string $filterPriority = '';

    // ---------------------------------------------------------------------------
    // Form fields (shared for create & edit)
    // ---------------------------------------------------------------------------

    public string $title = '';

    public string $description = '';

    public string $clientName = '';

    public string $budget = '';

    public string $startDate = '';

    public string $deadline = '';

    public string $priority = 'medium';

    public string $status = 'planning';

    // ---------------------------------------------------------------------------
    // Modal / edit state
    // ---------------------------------------------------------------------------

    public bool $showModal = false;

    public ?int $editingId = null;

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
    // Validation rules
    // ---------------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    protected function validationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'clientName' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'startDate' => ['nullable', 'date'],
            'deadline' => [
                'required',
                'date',
                $this->startDate !== '' ? 'after_or_equal:startDate' : '',
            ],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:planning,active,on_hold,completed,cancelled'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'title' => 'judul',
            'clientName' => 'nama klien',
            'budget' => 'anggaran',
            'startDate' => 'tanggal mulai',
            'deadline' => 'deadline',
            'priority' => 'prioritas',
            'status' => 'status',
        ];
    }

    // ---------------------------------------------------------------------------
    // Open / close modal helpers
    // ---------------------------------------------------------------------------

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'title', 'description', 'clientName', 'budget',
            'startDate', 'deadline', 'editingId',
        ]);

        $this->priority = 'medium';
        $this->status = 'planning';
        $this->resetValidation();
    }

    // ---------------------------------------------------------------------------
    // Create
    // ---------------------------------------------------------------------------

    public function save(): void
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            abort(403, 'Anda tidak memiliki akses untuk membuat proyek.');
        }

        $validated = $this->validate(
            $this->validationRules(),
            [],
            $this->validationAttributes()
        );

        TeamProject::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'client_name' => $validated['clientName'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'start_date' => $validated['startDate'] !== '' ? $validated['startDate'] : null,
            'deadline' => $validated['deadline'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'created_by' => $user->id,
        ]);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Proyek berhasil dibuat.');
    }

    // ---------------------------------------------------------------------------
    // Edit
    // ---------------------------------------------------------------------------

    public function editProject(int $id): void
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit proyek.');
        }

        $project = TeamProject::findOrFail($id);

        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description ?? '';
        $this->clientName = $project->client_name ?? '';
        $this->budget = $project->budget !== null ? (string) $project->budget : '';
        $this->startDate = $project->start_date?->format('Y-m-d') ?? '';
        $this->deadline = $project->deadline->format('Y-m-d');
        $this->priority = $project->priority;
        $this->status = $project->status;

        $this->showModal = true;
    }

    public function update(): void
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui proyek.');
        }

        $project = TeamProject::findOrFail($this->editingId);

        $validated = $this->validate(
            $this->validationRules(),
            [],
            $this->validationAttributes()
        );

        $project->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'client_name' => $validated['clientName'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'start_date' => $validated['startDate'] !== '' ? $validated['startDate'] : null,
            'deadline' => $validated['deadline'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
        ]);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Proyek berhasil diperbarui.');
    }

    // ---------------------------------------------------------------------------
    // Delete
    // ---------------------------------------------------------------------------

    public function deleteProject(int $id): void
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            abort(403, 'Anda tidak memiliki akses untuk menghapus proyek.');
        }

        $project = TeamProject::findOrFail($id);
        $project->delete();

        session()->flash('success', 'Proyek berhasil dihapus.');
    }

    // ---------------------------------------------------------------------------
    // Reset pagination on filter change
    // ---------------------------------------------------------------------------

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPriority(): void
    {
        $this->resetPage();
    }

    // ---------------------------------------------------------------------------
    // Render
    // ---------------------------------------------------------------------------

    public function render(): View
    {
        $query = TeamProject::with('creator')
            ->orderByDesc('created_at');

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority !== '') {
            $query->where('priority', $this->filterPriority);
        }

        return view('livewire.project-studio', [
            'projects' => $query->paginate(15),
        ]);
    }
}
