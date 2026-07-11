{{-- 
  View: Time Log Form (Livewire Component)
  Menampilkan tombol dan modal popup untuk mencatat waktu kerja.
  Menggunakan Alpine.js dan Flatpickr untuk input tanggal/waktu.
--}}
<div>
    {{-- Button to trigger modal --}}
    <x-button wire:click="openModal">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        </x-slot>
        Add Time Log
    </x-button>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak class="relative z-50">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="if ($event.target.closest('.flatpickr-calendar')) return; open = false; $wire.resetForm()" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-xl">
                    <form wire:submit="saveTimeLog">
                        <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-ink">Catat Waktu (Time Log)</h3>
                            <button type="button" @click="open = false; $wire.resetForm()" class="text-ink-secondary hover:text-ink transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="space-y-4">
                                {{-- Project --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Proyek <span class="text-danger">*</span></label>
                                    <select wire:model.live="projectId" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="">Pilih Proyek...</option>
                                        @foreach($availableProjects as $project)
                                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('projectId') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Task --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Tugas <span class="text-danger">*</span></label>
                                    <select wire:model="taskId" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="">Pilih Tugas...</option>
                                        @foreach($availableTasks as $task)
                                            <option value="{{ $task->id }}">{{ $task->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskId') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    {{-- Start Time --}}
                                    <div x-data="{ 
                                            value: @entangle('startTime'), 
                                            init() { 
                                                let picker = flatpickr(this.$refs.input, { 
                                                    enableTime: true, 
                                                    time_24hr: true, 
                                                    dateFormat: 'Y-m-d H:i', 
                                                    allowInput: false,
                                                    clickOpens: true,
                                                    minuteIncrement: 5,
                                                    onChange: (dates, dateStr) => this.value = dateStr 
                                                });
                                                this.$watch('value', () => picker.setDate(this.value));
                                            } 
                                        }">
                                        <label class="block text-sm font-medium text-ink-secondary mb-1.5">Waktu Mulai <span class="text-danger">*</span></label>
                                        <div wire:ignore>
                                            <input x-ref="input" type="text" placeholder="Pilih tanggal & jam..." class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5 cursor-pointer">
                                        </div>
                                        @error('startTime') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    {{-- End Time --}}
                                    <div x-data="{ 
                                            value: @entangle('endTime'), 
                                            init() { 
                                                let picker = flatpickr(this.$refs.input, { 
                                                    enableTime: true, 
                                                    time_24hr: true, 
                                                    dateFormat: 'Y-m-d H:i', 
                                                    allowInput: false,
                                                    clickOpens: true,
                                                    minuteIncrement: 5,
                                                    onChange: (dates, dateStr) => this.value = dateStr 
                                                });
                                                this.$watch('value', () => picker.setDate(this.value));
                                            } 
                                        }">
                                        <label class="block text-sm font-medium text-ink-secondary mb-1.5">Waktu Selesai <span class="text-danger">*</span></label>
                                        <div wire:ignore>
                                            <input x-ref="input" type="text" placeholder="Pilih tanggal & jam..." class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5 cursor-pointer">
                                        </div>
                                        @error('endTime') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                {{-- Notes --}}
                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Catatan Pekerjaan</label>
                                    <textarea wire:model="notes" rows="3" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5" placeholder="Jelaskan apa yang Anda kerjakan..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 border-t border-border bg-subtle/50 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="open = false; $wire.resetForm()" class="px-4 py-2 text-sm font-medium text-ink-secondary hover:text-ink transition-colors">
                                Batal
                            </button>
                            <x-button type="submit" loadingTarget="saveTimeLog">
                                Simpan Log
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
