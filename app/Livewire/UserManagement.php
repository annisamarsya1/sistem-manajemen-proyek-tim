<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: UserManagement
 * 
 * Mengelola daftar pengguna aplikasi. 
 * Menyediakan fungsi CRUD (Create, Read, Update) dan pengaktifan/penonaktifan akun.
 * Hanya bisa diakses oleh pengguna dengan role 'admin'.
 */
#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form properties
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $role = 'employee';
    public bool $showModal = false;

    public function mount()
    {
        $this->title = 'User Management';
        $this->checkAdmin();
    }

    private function checkAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Admin.');
        }
    }

    public function createUser(): void
    {
        $this->checkAdmin();
        $this->resetForm();
        $this->showModal = true;
    }

    public function editUser(int $id): void
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->passwordConfirmation = '';

        $this->showModal = true;
    }

    /**
     * Menyimpan data pengguna.
     * Menangani logika pembuatan password (jika pengguna baru) dan memastikan
     * validasi email tidak bentrok saat proses pembaruan (update).
     */
    public function save(): void
    {
        $this->checkAdmin();

        if ($this->editingId) {
            $rules = [
                'name'  => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($this->editingId)],
                'role'  => ['required', 'in:admin,project_manager,employee'],
            ];

            if ($this->password !== '') {
                $rules['password'] = ['min:8', 'same:passwordConfirmation'];
                $rules['passwordConfirmation'] = ['required'];
            }

            $this->validate($rules, [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email sudah digunakan.',
                'password.same' => 'Konfirmasi password tidak cocok.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ];

            if ($this->password !== '') {
                $data['password'] = bcrypt($this->password);
            }

            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Pengguna berhasil diperbarui.');

        } else {
            $this->validate([
                'name'                 => 'required|string|max:100',
                'email'                => 'required|email|unique:users,email',
                'password'             => 'required|min:8|same:passwordConfirmation',
                'passwordConfirmation' => 'required',
                'role'                 => 'required|in:admin,project_manager,employee',
            ], [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email sudah digunakan.',
                'password.required' => 'Password wajib diisi.',
                'password.same' => 'Konfirmasi password tidak cocok.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => bcrypt($this->password),
                'role'      => $this->role,
                'is_active' => true,
            ]);

            session()->flash('success', 'Pengguna berhasil dibuat.');
        }

        $this->resetForm();
    }

    /**
     * Mengubah status aktif (is_active) pengguna.
     * Mencegah admin menonaktifkan akun miliknya sendiri.
     */
    public function toggleActive(int $userId): void
    {
        $this->checkAdmin();

        if ($userId === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function closeModal(): void
    {
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
        $this->showModal = false;
        $this->resetValidation();
    }
    
    // reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $query->orderBy('name');

        return view('livewire.user-management', [
            'users' => $query->paginate(15),
        ])->title('User Management');
    }
}
