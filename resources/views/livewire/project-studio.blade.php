{{-- 
  View: Project Studio (Livewire Component)
  Halaman manajemen proyek (CRUD) untuk Admin dan Project Manager.
  Menampilkan daftar proyek dalam tabel beserta modal form tambah/edit.
--}}
<div x-data="{ openModal: @entangle('showModal') }">
    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-ink">Project Studio</h2>
        
        <x-button wire:click="createProject">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </x-slot>
            Buat Proyek
        </x-button>
    </div>

    {{-- Filters --}}
    <x-card padding="p-4" class="mb-6 flex flex-wrap gap-4 items-center">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-ink-secondary">Status:</label>
            <select wire:model.live="filterStatus" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-1.5">
                <option value="">Semua</option>
                <option value="planning">Planning</option>
                <option value="active">Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-ink-secondary">Prioritas:</label>
            <select wire:model.live="filterPriority" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-1.5">
                <option value="">Semua</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
        
        <div wire:loading.delay.longest wire:target="filterStatus, filterPriority" class="text-sm text-primary flex items-center gap-2 ml-auto">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Memuat...
        </div>
    </x-card>

    {{-- Projects Table --}}
    <x-card padding="p-0" class="relative">
        <div wire:loading.flex wire:target="filterStatus, filterPriority" class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 hidden items-center justify-center rounded-xl"></div>
        
        <x-table :headers="['No', 'Judul', 'Klien', 'Deadline', 'Prioritas', 'Status', 'Aksi']">
            @forelse($projects as $index => $project)
                <tr class="hover:bg-subtle transition-colors">
                    <td class="px-6 py-4">{{ $projects->firstItem() + $index }}</td>
                    <td class="px-6 py-4 font-medium text-ink max-w-[200px] truncate" title="{{ $project->title }}">{{ $project->title }}</td>
                    <td class="px-6 py-4">{{ $project->client_name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</td>
                    
                    {{-- Priority Badge --}}
                    <td class="px-6 py-4">
                        <x-status-badge :status="$project->priority" />
                    </td>
                    
                    {{-- Status Badge --}}
                    <td class="px-6 py-4">
                        <x-status-badge :status="$project->status" />
                    </td>
                    
                    {{-- Actions --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="editProject({{ $project->id }})" class="p-1.5 text-ink-secondary hover:text-primary hover:bg-primary-soft rounded-md transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <button wire:click="deleteProject({{ $project->id }})" wire:confirm="Hapus proyek ini? Semua tugas terkait akan ikut terhapus." class="p-1.5 text-ink-secondary hover:text-danger hover:bg-danger-soft rounded-md transition-colors" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-ink-muted italic">
                        @if(in_array(auth()->user()->role, ['admin', 'project_manager']))
                            Belum ada proyek. Klik 'Buat Proyek' untuk memulai.
                        @else
                            Belum ada proyek.
                        @endif
                    </td>
                </tr>
            @endforelse
        </x-table>
        
        @if($projects->hasPages())
        <div class="px-6 py-4 border-t border-border bg-subtle/30 rounded-b-xl">
            {{ $projects->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </x-card>

    {{-- Create/Edit Modal --}}
    <div x-show="openModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="openModal = false; $wire.closeModal()" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <form wire:submit="save">
                        <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-ink" id="modal-title">
                                @if($editingId) Edit Proyek @else Buat Proyek Baru @endif
                            </h3>
                            <button type="button" @click="openModal = false; $wire.closeModal()" class="text-ink-secondary hover:text-ink transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Title --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Judul Proyek <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="title" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('title') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Client Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Nama Klien</label>
                                    <input type="text" wire:model="clientName" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                </div>
                                
                                {{-- Budget --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Anggaran (opsional)</label>
                                    <input type="number" wire:model="budget" step="0.01" min="0" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('budget') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Start Date --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Tanggal Mulai</label>
                                    <input type="date" wire:model="startDate" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                </div>
                                
                                {{-- Deadline --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Deadline <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="deadline" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('deadline') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Priority --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Prioritas <span class="text-danger">*</span></label>
                                    <select wire:model="priority" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                    @error('priority') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Status --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Status <span class="text-danger">*</span></label>
                                    <select wire:model="status" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="planning">Planning</option>
                                        <option value="active">Active</option>
                                        <option value="on_hold">On Hold</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @error('status') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Description --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Deskripsi</label>
                                    <textarea wire:model="description" rows="3" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5"></textarea>
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
