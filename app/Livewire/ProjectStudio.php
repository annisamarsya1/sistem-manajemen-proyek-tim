<?php

namespace App\Livewire;

use App\Models\TeamProject;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: ProjectStudio
 * 
 * Mengelola (CRUD) proyek dalam aplikasi.
 * Komponen ini hanya bisa diakses oleh Admin atau Project Manager.
 */
#[Layout('components.layouts.app')]
class ProjectStudio extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $filterPriority = '';

    // Form properties
    public ?int $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $clientName = '';
    public string $budget = '';
    public string $startDate = '';
    public string $deadline = '';
    public string $priority = 'medium';
    public string $status = 'planning';
    public bool $showModal = false;

    public function mount()
    {
        $this->title = 'Project Studio';
        $this->checkAccess();
    }

    private function checkAccess(): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'project_manager'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function createProject(): void
    {
        $this->checkAccess();
        $this->resetForm();
        $this->showModal = true;
    }

    public function editProject(int $id): void
    {
        $this->checkAccess();
        $project = TeamProject::findOrFail($id);
        
        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description ?? '';
        $this->clientName = $project->client_name ?? '';
        $this->budget = $project->budget ? (string)$project->budget : '';
        $this->startDate = $project->start_date ?? '';
        $this->deadline = $project->deadline;
        $this->priority = $project->priority;
        $this->status = $project->status;

        $this->showModal = true;
    }

    /**
     * Menyimpan data proyek (Create baru atau Update data lama).
     * Melakukan validasi input sesuai aturan yang ditentukan.
     */
    public function save(): void
    {
        $this->checkAccess();

        $rules = [
            'title' => ['required', 'max:200'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:planning,active,on_hold,completed,cancelled'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($this->startDate) {
            $rules['deadline'] = ['required', 'date', 'after_or_equal:startDate'];
        } else {
            $rules['deadline'] = ['required', 'date'];
        }

        $this->validate($rules, [
            'title.required' => 'Judul proyek wajib diisi.',
            'title.max' => 'Judul proyek maksimal 200 karakter.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.date' => 'Format deadline tidak valid.',
            'deadline.after_or_equal' => 'Deadline harus setelah atau sama dengan tanggal mulai.',
            'priority.in' => 'Prioritas tidak valid.',
            'status.in' => 'Status tidak valid.',
            'budget.numeric' => 'Anggaran harus berupa angka.',
            'budget.min' => 'Anggaran tidak boleh negatif.',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'client_name' => $this->clientName ?: null,
            'budget' => $this->budget !== '' ? $this->budget : 0,
            'start_date' => $this->startDate ?: null,
            'deadline' => $this->deadline,
            'priority' => $this->priority,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            $project = TeamProject::findOrFail($this->editingId);
            $project->update($data);
            session()->flash('success', 'Proyek berhasil diperbarui.');
        } else {
            $data['created_by'] = Auth::id();
            TeamProject::create($data);
            session()->flash('success', 'Proyek berhasil dibuat.');
        }

        $this->resetForm();
    }

    /**
     * Menghapus data proyek beserta semua relasinya (via cascade/events).
     */
    public function deleteProject(int $id): void
    {
        $this->checkAccess();
        
        $project = TeamProject::findOrFail($id);
        $project->delete();
        
        session()->flash('success', 'Proyek berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->description = '';
        $this->clientName = '';
        $this->budget = '';
        $this->startDate = '';
        $this->deadline = '';
        $this->priority = 'medium';
        $this->status = 'planning';
        $this->showModal = false;
        
        // Reset validation errors
        $this->resetValidation();
    }

    public function render()
    {
        $query = TeamProject::query();

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        $query->orderBy('created_at', 'desc');

        return view('livewire.project-studio', [
            'projects' => $query->paginate(15),
        ])->title('Project Studio');
    }
}
