{{-- 
  View: User Management (Livewire Component)
  Halaman khusus Admin untuk mengelola akun pengguna (CRUD & Aktifasi).
  Menampilkan tabel daftar pengguna beserta form modal manajemen akun.
--}}
<div x-data="{ openModal: @entangle('showModal') }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-ink">User Management</h2>
        
        <x-button wire:click="createUser">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </x-slot>
            Buat Pengguna Baru
        </x-button>
    </div>

    {{-- Filter/Search --}}
    <x-card padding="p-4" class="mb-6 flex flex-wrap gap-4 items-center">
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block pl-10 px-4 py-2.5" placeholder="Cari nama atau email...">
            
            <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg class="w-4 h-4 animate-spin text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </div>
    </x-card>

    {{-- Users Table --}}
    <x-card padding="p-0" class="relative">
        <div wire:loading.flex wire:target="search, toggleActive" class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 hidden items-center justify-center rounded-xl"></div>
        
        <x-table :headers="['No', 'Nama', 'Email', 'Role', 'Status', 'Tanggal Dibuat', 'Aksi']">
            @forelse($users as $index => $user)
                <tr class="hover:bg-subtle transition-colors">
                    <td class="px-6 py-4">{{ $users->firstItem() + $index }}</td>
                    <td class="px-6 py-4 font-medium text-ink">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    
                    {{-- Role Badge --}}
                    <td class="px-6 py-4">
                        @if($user->role === 'admin')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-soft text-danger border border-danger/20">Admin</span>
                        @elseif($user->role === 'project_manager')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-soft text-primary border border-primary/20">Project Manager</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-soft text-success border border-success/20">Employee</span>
                        @endif
                    </td>
                    
                    {{-- Status Badge --}}
                    <td class="px-6 py-4">
                        <x-status-badge :status="$user->is_active ? 'active' : 'on_hold'" />
                    </td>

                    <td class="px-6 py-4">{{ $user->created_at->format('d M Y') }}</td>
                    
                    {{-- Actions --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="editUser({{ $user->id }})" class="p-1.5 text-ink-secondary hover:text-primary hover:bg-primary-soft rounded-md transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            
                            @if($user->id !== auth()->id())
                                <button wire:click="toggleActive({{ $user->id }})" 
                                        wire:confirm="{{ $user->is_active ? 'Nonaktifkan akun ini? Pengguna tidak akan bisa login kembali.' : 'Aktifkan kembali akun ini?' }}"
                                        class="p-1.5 {{ $user->is_active ? 'text-ink-secondary hover:text-danger hover:bg-danger-soft' : 'text-ink-secondary hover:text-success hover:bg-success-soft' }} rounded-md transition-colors" 
                                        title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    @if($user->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    @endif
                                </button>
                            @else
                                <span class="w-8"></span> {{-- Spacer for alignment --}}
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-ink-muted italic">
                        Tidak ada pengguna yang cocok dengan pencarian.
                    </td>
                </tr>
            @endforelse
        </x-table>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-border bg-subtle/30 rounded-b-xl">
            {{ $users->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </x-card>

    {{-- Create/Edit User Modal --}}
    <div x-show="openModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="openModal = false; $wire.closeModal()" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    <form wire:submit="save">
                        <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-ink" id="modal-title">
                                @if($editingId) Edit Pengguna @else Buat Pengguna Baru @endif
                            </h3>
                            <button type="button" @click="openModal = false; $wire.closeModal()" class="text-ink-secondary hover:text-ink transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="space-y-4">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Nama <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('name') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Email <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="email" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('email') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Role --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Role <span class="text-danger">*</span></label>
                                    <select wire:model="role" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="employee">Employee</option>
                                        <option value="project_manager">Project Manager</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    @error('role') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>

                                <hr class="border-border my-2">

                                {{-- Password --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">
                                        Password @if(!$editingId) <span class="text-danger">*</span> @else <span class="text-ink-muted font-normal">(Opsional - isi hanya jika ingin ganti)</span> @endif
                                    </label>
                                    <input type="password" wire:model="password" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('password') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Password Confirmation --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Konfirmasi Password</label>
                                    <input type="password" wire:model="passwordConfirmation" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('passwordConfirmation') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 border-t border-border bg-subtle/50 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="openModal = false; $wire.closeModal()" class="px-4 py-2 text-sm font-medium text-ink-secondary hover:text-ink transition-colors">
                                Batal
                            </button>
                            <x-button type="submit" loadingTarget="save">
                                Simpan
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
