<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
#[Layout('layouts.app', ['title' => 'User Management'])]
class UserManagement extends Component
{
    use WithPagination;

    // ---------------------------------------------------------------------------
    // Search
    // ---------------------------------------------------------------------------

    public string $search = '';

    // ---------------------------------------------------------------------------
    // Form properties
    // ---------------------------------------------------------------------------

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public string $role = 'employee';

    public bool $showModal = false;

    public ?int $editingId = null;

    // ---------------------------------------------------------------------------
    // Auth guard
    // ---------------------------------------------------------------------------

    private function currentUser(): User
    {
        $user = Auth::user();

        assert($user instanceof User);

        return $user;
    }

    private function authorizeAdmin(): void
    {
        if ($this->currentUser()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    // ---------------------------------------------------------------------------
    // Query
    // ---------------------------------------------------------------------------

    public function getUsersProperty(): LengthAwarePaginator
    {
        return User::when(
            $this->search,
            fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
        )
            ->orderBy('name')
            ->paginate(15);
    }

    // ---------------------------------------------------------------------------
    // Modal helpers
    // ---------------------------------------------------------------------------

    public function openCreateModal(): void
    {
        $this->authorizeAdmin();

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
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->role = 'employee';
        $this->resetValidation();
    }

    // ---------------------------------------------------------------------------
    // Bagian 2 — Buat Pengguna Baru
    // ---------------------------------------------------------------------------

    public function save(): void
    {
        $this->authorizeAdmin();

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|same:passwordConfirmation',
            'passwordConfirmation' => 'required',
            'role' => 'required|in:admin,project_manager,employee',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'role' => $this->role,
            'is_active' => true,
        ]);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Pengguna berhasil dibuat.');
    }

    // ---------------------------------------------------------------------------
    // Bagian 3 — Edit Pengguna
    // ---------------------------------------------------------------------------

    public function editUser(int $id): void
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);

        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function update(): void
    {
        $this->authorizeAdmin();

        $rules = [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->editingId)],
            'role' => 'required|in:admin,project_manager,employee',
        ];

        if ($this->password !== '') {
            $rules['password'] = 'min:8|same:passwordConfirmation';
            $rules['passwordConfirmation'] = 'required';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password !== '') {
            $data['password'] = bcrypt($this->password);
        }

        User::findOrFail($this->editingId)->update($data);

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', 'Data pengguna berhasil diperbarui.');
    }

    // ---------------------------------------------------------------------------
    // Bagian 4 — Toggle aktif / nonaktif
    // ---------------------------------------------------------------------------

    public function toggleActive(int $userId): void
    {
        $this->authorizeAdmin();

        if ($userId === auth()->id()) {
            session()->flash('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');

            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        session()->flash('success', "Akun {$user->name} berhasil {$status}.");
    }

    // ---------------------------------------------------------------------------
    // Pagination reset
    // ---------------------------------------------------------------------------

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ---------------------------------------------------------------------------
    // Render
    // ---------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.user-management', [
            'users' => $this->users,
        ]);
    }
}
