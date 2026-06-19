<div>
    {{-- ===================================================================== --}}
    {{-- TOOLBAR                                                                --}}
    {{-- ===================================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Kanban Board</h2>
            <p class="text-sm text-slate-400 mt-0.5">Kelola dan pantau progres tugas tim</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Filter Proyek --}}
            <select wire:model.live="filterProjectId"
                    class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent min-w-[180px]">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                @endforeach
            </select>

            {{-- Tambah Tugas (Admin/PM only) --}}
            @if(auth()->user()->role !== 'employee')
                <button
                    x-data
                    @click="$dispatch('open-task-modal')"
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-colors duration-150 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Tugas
                </button>
            @endif
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- KANBAN COLUMNS                                                         --}}
    {{-- ===================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        @php
            $columns = [
                'todo'        => ['label' => 'Todo',        'color' => 'bg-slate-500',  'ring' => 'ring-slate-500/30',  'badge' => 'bg-slate-700 text-slate-300'],
                'in_progress' => ['label' => 'In Progress', 'color' => 'bg-blue-500',   'ring' => 'ring-blue-500/30',   'badge' => 'bg-blue-900/50 text-blue-300'],
                'review'      => ['label' => 'Review',      'color' => 'bg-amber-500',  'ring' => 'ring-amber-500/30',  'badge' => 'bg-amber-900/50 text-amber-300'],
                'done'        => ['label' => 'Done',        'color' => 'bg-emerald-500','ring' => 'ring-emerald-500/30','badge' => 'bg-emerald-900/50 text-emerald-300'],
            ];
        @endphp

        @foreach($columns as $statusKey => $col)
        <div
            x-data="{
                init() {
                    Sortable.create(this.$el.querySelector('[data-sortable]'), {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'opacity-40',
                        dragClass: 'rotate-1 scale-105 shadow-2xl',
                        onEnd: function(evt) {
                            $wire.updateTaskStatus(
                                parseInt(evt.item.dataset.taskId),
                                evt.to.dataset.status
                            )
                        }
                    })
                }
            }"
            class="flex flex-col bg-slate-900 rounded-2xl border border-slate-800 min-h-[400px]">

            {{-- Column Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full {{ $col['color'] }}"></span>
                    <span class="text-sm font-semibold text-white">{{ $col['label'] }}</span>
                </div>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $col['badge'] }}">
                    {{ $tasksByStatus[$statusKey]->count() }}
                </span>
            </div>

            {{-- Card Drop Zone --}}
            <div data-sortable data-status="{{ $statusKey }}"
                 class="flex-1 p-3 flex flex-col gap-2 min-h-[300px]">

                @foreach($tasksByStatus[$statusKey] as $task)
                    @include('livewire.partials.kanban-card', ['task' => $task])
                @endforeach

                {{-- Empty state --}}
                @if($tasksByStatus[$statusKey]->isEmpty())
                    <div class="flex-1 flex items-center justify-center text-slate-600 text-xs select-none pointer-events-none py-8">
                        Seret tugas ke sini
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL: CREATE / EDIT TASK                                              --}}
    {{-- ===================================================================== --}}
    <div
        x-data="{ open: false }"
        x-on:open-task-modal.window="open = true"
        x-on:close-task-modal.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="open = false"></div>

        {{-- Dialog --}}
        <div class="relative w-full max-w-lg bg-slate-900 rounded-2xl border border-slate-800 shadow-2xl overflow-hidden"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                <h3 class="text-base font-semibold text-white">
                    {{ $editingTaskId ? 'Edit Tugas' : 'Tambah Tugas Baru' }}
                </h3>
                <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="{{ $editingTaskId ? 'updateTask' : 'saveTask' }}" class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Judul Tugas <span class="text-rose-400">*</span></label>
                    <input wire:model="taskTitle" type="text" maxlength="200"
                           class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-xl px-3 py-2.5 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Masukkan judul tugas...">
                    @error('taskTitle') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Deskripsi</label>
                    <textarea wire:model="taskDescription" rows="3"
                              class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-xl px-3 py-2.5 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                              placeholder="Deskripsi tugas (opsional)..."></textarea>
                    @error('taskDescription') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Proyek & Assignee --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Proyek <span class="text-rose-400">*</span></label>
                        <select wire:model="taskProjectId"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Pilih proyek...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                        @error('taskProjectId') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Assignee</label>
                        <select wire:model="taskAssigneeId"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Tanpa assignee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('taskAssigneeId') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Prioritas & Status (edit only) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Prioritas <span class="text-rose-400">*</span></label>
                        <select wire:model="taskPriority"
                                class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('taskPriority') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($editingTaskId)
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status</label>
                            <select wire:model="taskStatus"
                                    class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="todo">Todo</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="done">Done</option>
                            </select>
                            @error('taskStatus') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                {{-- Progress (edit only) --}}
                @if($editingTaskId)
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                            Progress: <span class="text-indigo-400">{{ $taskProgressPercent }}%</span>
                        </label>
                        <input wire:model.live="taskProgressPercent" type="range" min="0" max="100" step="5"
                               class="w-full accent-indigo-500">
                        @error('taskProgressPercent') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Tanggal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tanggal Mulai</label>
                        <input wire:model="taskStartDate" type="date"
                               class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('taskStartDate') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Due Date</label>
                        <input wire:model="taskDueDate" type="date"
                               class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('taskDueDate') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition-colors duration-150">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl transition-colors duration-150">
                        <span wire:loading.remove wire:target="{{ $editingTaskId ? 'updateTask' : 'saveTask' }}">
                            {{ $editingTaskId ? 'Simpan Perubahan' : 'Buat Tugas' }}
                        </span>
                        <span wire:loading wire:target="{{ $editingTaskId ? 'updateTask' : 'saveTask' }}">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL: TASK DETAIL & KOMENTAR                                          --}}
    {{-- ===================================================================== --}}
    <div
        x-data="{ open: false }"
        x-on:open-detail-modal.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"
             @click="open = false; $wire.closeTask()"></div>

        <div class="relative w-full max-w-2xl bg-slate-900 rounded-2xl border border-slate-800 shadow-2xl overflow-hidden"
             @click.stop>

            @if($selectedTask)
                {{-- Header --}}
                <div class="flex items-start justify-between px-6 py-4 border-b border-slate-800">
                    <div class="flex-1 min-w-0 pr-4">
                        <div class="flex items-center gap-2 mb-1">
                            @php
                                $priorityBadge = match($selectedTask->priority) {
                                    'high'   => 'bg-rose-900/50 text-rose-300 border border-rose-500/20',
                                    'medium' => 'bg-amber-900/50 text-amber-300 border border-amber-500/20',
                                    default  => 'bg-slate-700 text-slate-300 border border-slate-600/20',
                                };
                            @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $priorityBadge }}">
                                {{ ucfirst($selectedTask->priority) }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white leading-snug">{{ $selectedTask->title }}</h3>
                        @if($selectedTask->project)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $selectedTask->project->title }}</p>
                        @endif
                    </div>
                    <button wire:click="closeTask" @click="open = false"
                            class="text-slate-400 hover:text-white transition-colors flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="overflow-y-auto max-h-[60vh]">

                    {{-- Task Fields --}}
                    <div class="px-6 py-5 grid grid-cols-2 gap-x-6 gap-y-4 border-b border-slate-800">
                        @if($selectedTask->description)
                            <div class="col-span-2">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Deskripsi</p>
                                <p class="text-sm text-slate-300 leading-relaxed">{{ $selectedTask->description }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Assignee</p>
                            <p class="text-sm text-white">{{ $selectedTask->assignee?->name ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</p>
                            @php
                                $statusLabel = match($selectedTask->status) {
                                    'todo'        => ['label' => 'Todo',        'class' => 'bg-slate-700 text-slate-300'],
                                    'in_progress' => ['label' => 'In Progress', 'class' => 'bg-blue-900/50 text-blue-300'],
                                    'review'      => ['label' => 'Review',      'class' => 'bg-amber-900/50 text-amber-300'],
                                    'done'        => ['label' => 'Done',        'class' => 'bg-emerald-900/50 text-emerald-300'],
                                    default       => ['label' => ucfirst($selectedTask->status), 'class' => 'bg-slate-700 text-slate-300'],
                                };
                            @endphp
                            <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusLabel['class'] }}">
                                {{ $statusLabel['label'] }}
                            </span>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Due Date</p>
                            <p class="text-sm text-white">{{ $selectedTask->due_date?->format('d M Y') ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Progress</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all"
                                         style="width: {{ $selectedTask->progress_percent }}%"></div>
                                </div>
                                <span class="text-xs text-slate-400 w-8 text-right">{{ (int) $selectedTask->progress_percent }}%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Comments --}}
                    <div class="px-6 py-5">
                        <h4 class="text-sm font-semibold text-white mb-4">
                            Komentar
                            <span class="ml-1 text-xs text-slate-500">({{ $taskComments->count() }})</span>
                        </h4>

                        @if($taskComments->isEmpty())
                            <p class="text-sm text-slate-500 text-center py-4">Belum ada komentar.</p>
                        @else
                            <div class="space-y-4 mb-5">
                                @foreach($taskComments as $comment)
                                    <div class="flex gap-3" wire:key="comment-{{ $comment->id }}">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-indigo-400 flex-shrink-0">
                                            {{ Str::of($comment->user->name)->explode(' ')->take(2)->map(fn($w) => Str::substr($w, 0, 1))->implode('') }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-baseline gap-2 mb-1">
                                                <span class="text-sm font-semibold text-white">{{ $comment->user->name }}</span>
                                                <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-slate-300 leading-relaxed break-words">{{ $comment->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- New Comment Form --}}
                        <form wire:submit="addComment" class="flex gap-2">
                            <textarea wire:model="newComment"
                                      rows="2"
                                      placeholder="Tulis komentar..."
                                      class="flex-1 bg-slate-800 border border-slate-700 text-white text-sm rounded-xl px-3 py-2.5 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
                            <button type="submit"
                                    class="self-end px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-colors duration-150 flex-shrink-0">
                                <span wire:loading.remove wire:target="addComment">Kirim</span>
                                <span wire:loading wire:target="addComment">...</span>
                            </button>
                        </form>
                        @error('newComment') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
