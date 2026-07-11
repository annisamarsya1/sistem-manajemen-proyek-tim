{{-- 
  View: Kanban Board (Livewire Component)
  Antarmuka visual manajemen tugas menggunakan sistem kolom (Todo, In Progress, Review, Done).
  Mendukung fungsi Drag and Drop (SortableJS) untuk memindahkan status tugas.
--}}
<div>
    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-ink">Kanban Board</h2>
        
        <div class="flex items-center gap-3">
            <select wire:model.live="filterProjectId" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-4 py-2 min-w-[200px]">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                @endforeach
            </select>

            @if(in_array(auth()->user()->role, ['admin', 'project_manager']))
            <x-button wire:click="createTask">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </x-slot>
                Tambah Tugas
            </x-button>
            @endif
        </div>
    </div>

    {{-- Kanban Columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 pb-4 min-h-[calc(100vh-200px)]">
        @php
            $columns = [
                'todo' => ['title' => 'To Do', 'color' => 'slate'],
                'in_progress' => ['title' => 'In Progress', 'color' => 'blue'],
                'review' => ['title' => 'Review', 'color' => 'amber'],
                'done' => ['title' => 'Done', 'color' => 'emerald']
            ];
        @endphp

        @foreach($columns as $status => $col)
        <div class="flex flex-col bg-subtle/30 border border-border rounded-xl">
            {{-- Column Header --}}
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-{{ $col['color'] }}-500"></span>
                    <h3 class="font-semibold text-ink">{{ $col['title'] }}</h3>
                </div>
                <span class="bg-surface text-ink-secondary text-xs font-medium px-2 py-1 rounded-md">{{ count($tasksByStatus[$status]) }}</span>
            </div>

            {{-- Column Body (Sortable) --}}
            <div class="p-3 h-full overflow-y-auto" style="min-height: 200px;" 
                 x-data x-init="
                 Sortable.create($el, {
                     group: 'kanban',
                     animation: 150,
                     ghostClass: 'opacity-50',
                     onEnd: function(evt) {
                         const taskId = evt.item.dataset.taskId;
                         const toStatus = evt.to.dataset.status;
                         if (taskId && toStatus) {
                             $wire.updateTaskStatus(taskId, toStatus);
                         }
                     }
                 })" data-status="{{ $status }}">
                
                @if(count($tasksByStatus[$status]) === 0)
                    <div class="p-4 border-2 border-dashed border-border rounded-xl text-center text-ink-muted text-sm">
                        Tidak ada tugas di sini.
                    </div>
                @else
                    @foreach($tasksByStatus[$status] as $task)
                    <div data-task-id="{{ $task->id }}" wire:key="task-{{ $task->id }}" 
                         class="bg-surface border border-border hover:border-primary-soft rounded-xl p-4 mb-3 cursor-grab active:cursor-grabbing shadow-sm transition-colors group">
                        <div class="flex justify-between items-start mb-2 gap-2">
                            <h4 wire:click="openTask({{ $task->id }})" class="text-ink font-medium text-sm leading-tight hover:text-primary cursor-pointer transition-colors">{{ $task->title }}</h4>
                            
                            {{-- Priority Badge --}}
                            <div class="flex-shrink-0">
                                @if($task->priority === 'low')
                                    <span class="block w-2.5 h-2.5 rounded-full bg-ink-muted" title="Low"></span>
                                @elseif($task->priority === 'medium')
                                    <span class="block w-2.5 h-2.5 rounded-full bg-primary" title="Medium"></span>
                                @elseif($task->priority === 'high')
                                    <span class="block w-2.5 h-2.5 rounded-full bg-danger" title="High"></span>
                                @else
                                    <span class="block w-2.5 h-2.5 rounded-full bg-danger-hover" title="Urgent"></span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-2">
                                @if($task->assignee)
                                    <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white shadow-sm" title="{{ $task->assignee->name }}">
                                        {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full bg-subtle flex items-center justify-center text-ink-secondary shadow-sm" title="Unassigned">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                @endif
                                <span class="text-xs text-ink-secondary max-w-[120px] truncate">{{ $task->project->title ?? 'N/A' }}</span>
                            </div>
                            
                            @if($task->due_date)
                                <div class="flex items-center gap-1 text-xs {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'done' ? 'text-danger font-medium' : 'text-ink-secondary' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Create/Edit Task Modal --}}
    <div x-data="{ open: @entangle('showTaskModal') }" x-show="open" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="open = false; $wire.closeTaskModal()" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                    <form wire:submit="saveTask">
                        <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-ink">
                                @if($editingTaskId) Edit Tugas @else Tambah Tugas Baru @endif
                            </h3>
                            <button type="button" @click="open = false; $wire.closeTaskModal()" class="text-ink-secondary hover:text-ink transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-5 max-h-[70vh] overflow-y-auto space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Judul Tugas <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="taskTitle" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    @error('taskTitle') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Proyek <span class="text-danger">*</span></label>
                                    <select wire:model="taskProjectId" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="">Pilih Proyek...</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('taskProjectId') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Assignee</label>
                                    <select wire:model="taskAssigneeId" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="">(Tanpa Assignee)</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Prioritas <span class="text-danger">*</span></label>
                                    <select wire:model="taskPriority" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                    @error('taskPriority') <span class="text-xs text-danger mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Due Date</label>
                                    <input type="date" wire:model="taskDueDate" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                </div>
                                
                                @if($editingTaskId)
                                    <div>
                                        <label class="block text-sm font-medium text-ink-secondary mb-1.5">Status</label>
                                        <select wire:model="taskStatus" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                            <option value="todo">To Do</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="review">Review</option>
                                            <option value="done">Done</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-ink-secondary mb-1.5">Progress (%)</label>
                                        <input type="number" wire:model="taskProgress" min="0" max="100" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5">
                                    </div>
                                @endif

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-ink-secondary mb-1.5">Deskripsi</label>
                                    <textarea wire:model="taskDescription" rows="4" class="w-full bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary px-4 py-2.5"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 border-t border-border bg-subtle/50 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="open = false; $wire.closeTaskModal()" class="px-4 py-2 text-sm font-medium text-ink-secondary hover:text-ink">Batal</button>
                            <x-button type="submit" loadingTarget="saveTask">Simpan Tugas</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail & Comments Modal --}}
    <div x-data="{ openDetail: @entangle('showDetailModal') }" x-show="openDetail" x-cloak class="relative z-50">
        <div x-show="openDetail" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="openDetail" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="openDetail = false; $wire.closeDetailModal()" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-4xl flex flex-col md:flex-row h-[80vh]">
                    
                    @if($selectedTask)
                    {{-- Task Details Sidebar --}}
                    <div class="w-full md:w-1/2 lg:w-5/12 bg-subtle/30 border-r border-border flex flex-col h-full">
                        <div class="px-6 py-5 border-b border-border flex justify-between items-start bg-surface">
                            <div>
                                <div class="text-xs font-semibold text-primary mb-1 uppercase tracking-wider">{{ $selectedTask->project->title ?? 'Tanpa Proyek' }}</div>
                                <h3 class="text-xl font-bold text-ink leading-tight">{{ $selectedTask->title }}</h3>
                            </div>
                            <button type="button" @click="openDetail = false; $wire.closeDetailModal()" class="text-ink-secondary hover:text-ink md:hidden">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="p-6 overflow-y-auto flex-1 space-y-6">
                            @if(in_array(auth()->user()->role, ['admin', 'project_manager']))
                                <div class="flex gap-2">
                                    <button wire:click="editTask({{ $selectedTask->id }})" class="px-3 py-1.5 text-xs font-medium bg-primary-soft text-primary rounded-lg hover:bg-primary/20 transition-colors">Edit Tugas</button>
                                    <button wire:click="deleteTask({{ $selectedTask->id }})" wire:confirm="Hapus tugas ini? Komentar terkait juga akan ikut terhapus." class="px-3 py-1.5 text-xs font-medium bg-danger-soft text-danger rounded-lg hover:bg-danger/20 transition-colors">Hapus Tugas</button>
                                </div>
                            @endif
                        
                            <div>
                                <h4 class="text-xs font-medium text-ink-secondary uppercase mb-2">Deskripsi</h4>
                                <p class="text-sm text-ink whitespace-pre-line">{{ $selectedTask->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 bg-surface p-4 rounded-xl border border-border">
                                <div>
                                    <div class="text-xs text-ink-secondary mb-1">Status</div>
                                    <div class="text-sm font-medium text-ink capitalize">{{ str_replace('_', ' ', $selectedTask->status) }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-ink-secondary mb-1">Progress</div>
                                    <div class="text-sm font-medium text-ink">{{ number_format($selectedTask->progress_percent, 0) }}%</div>
                                </div>
                                <div>
                                    <div class="text-xs text-ink-secondary mb-1">Prioritas</div>
                                    <div class="text-sm font-medium text-ink capitalize">{{ $selectedTask->priority }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-ink-secondary mb-1">Due Date</div>
                                    <div class="text-sm font-medium text-ink">{{ $selectedTask->due_date ? \Carbon\Carbon::parse($selectedTask->due_date)->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="col-span-2 mt-2">
                                    <div class="text-xs text-ink-secondary mb-2">Assignee</div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white shadow-sm">
                                            {{ $selectedTask->assignee_id ? strtoupper(substr($selectedTask->assignee->name, 0, 1)) : '?' }}
                                        </div>
                                        <span class="text-sm font-medium text-ink">{{ $selectedTask->assignee->name ?? 'Unassigned' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Comments Area --}}
                    <div class="w-full md:w-1/2 lg:w-7/12 flex flex-col h-full bg-surface">
                        <div class="px-6 py-5 border-b border-border flex justify-between items-center bg-subtle/50 hidden md:flex">
                            <h3 class="font-semibold text-ink">Diskusi & Komentar</h3>
                            <button type="button" @click="openDetail = false; $wire.closeDetailModal()" class="text-ink-secondary hover:text-ink transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        {{-- Comments List --}}
                        <div class="flex-1 p-6 overflow-y-auto space-y-6 bg-surface">
                            @forelse($comments as $comment)
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-subtle border border-border flex items-center justify-center text-xs font-bold text-ink-secondary">
                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-baseline gap-2 mb-1">
                                            <span class="font-medium text-sm text-ink">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-ink-secondary">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="bg-subtle/50 border border-border rounded-2xl rounded-tl-none px-4 py-3 text-sm text-ink whitespace-pre-line">{{ $comment->comment }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="h-full flex flex-col items-center justify-center text-ink-secondary opacity-60">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    <p class="text-sm">Belum ada diskusi untuk tugas ini.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Add Comment Form --}}
                        <div class="p-4 border-t border-border bg-subtle/30">
                            <form wire:submit="addComment" class="relative flex items-center gap-3">
                                <textarea wire:model="newComment" rows="1" placeholder="Tulis komentar..." class="w-full bg-surface border border-border text-ink text-sm rounded-full focus:ring-primary focus:border-primary px-4 py-2.5 resize-none h-10 min-h-[40px]"></textarea>
                                <button type="submit" class="p-2.5 bg-primary hover:bg-primary-hover text-white rounded-full transition-colors flex-shrink-0" title="Kirim">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                </button>
                            </form>
                            @error('newComment') <span class="text-xs text-danger mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
