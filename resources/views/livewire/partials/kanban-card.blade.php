@php
    $priorityConfig = match($task->priority) {
        'high'   => ['label' => 'High',   'class' => 'bg-rose-900/50 text-rose-300 border border-rose-500/20'],
        'medium' => ['label' => 'Med',    'class' => 'bg-amber-900/50 text-amber-300 border border-amber-500/20'],
        default  => ['label' => 'Low',    'class' => 'bg-slate-700 text-slate-400 border border-slate-600/20'],
    };

    $isDue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
@endphp

<div
    data-task-id="{{ $task->id }}"
    wire:key="task-{{ $task->id }}"
    class="bg-slate-800 border border-slate-700/50 hover:border-slate-600 rounded-xl p-3.5 cursor-pointer select-none transition-all duration-150 group">

    {{-- Priority badge + action buttons --}}
    <div class="flex items-center justify-between mb-2.5">
        <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-md {{ $priorityConfig['class'] }}">
            {{ $priorityConfig['label'] }}
        </span>

        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
            {{-- Detail button --}}
            <button wire:click.stop="openTask({{ $task->id }})"
                    class="p-1 text-slate-400 hover:text-indigo-400 transition-colors"
                    title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.58-3.007-9.964-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </button>

            @if(auth()->user()->role !== 'employee')
                {{-- Edit button --}}
                <button wire:click.stop="editTask({{ $task->id }})"
                        class="p-1 text-slate-400 hover:text-amber-400 transition-colors"
                        title="Edit Tugas">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                    </svg>
                </button>

                {{-- Delete button --}}
                <button wire:click.stop="deleteTask({{ $task->id }})"
                        wire:confirm="Hapus tugas ini? Komentar terkait juga akan ikut terhapus."
                        class="p-1 text-slate-400 hover:text-rose-400 transition-colors"
                        title="Hapus Tugas">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Title --}}
    <button wire:click="openTask({{ $task->id }})"
            class="text-left w-full text-sm font-semibold text-white hover:text-indigo-300 transition-colors duration-150 leading-snug mb-3">
        {{ $task->title }}
    </button>

    {{-- Progress bar --}}
    @if((float) $task->progress_percent > 0)
        <div class="mb-3">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] text-slate-500">Progress</span>
                <span class="text-[10px] text-slate-400">{{ (int) $task->progress_percent }}%</span>
            </div>
            <div class="bg-slate-700 rounded-full h-1">
                <div class="bg-indigo-500 h-1 rounded-full transition-all"
                     style="width: {{ $task->progress_percent }}%"></div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between gap-2">
        {{-- Assignee --}}
        <div class="flex items-center gap-1.5 min-w-0">
            @if($task->assignee)
                <div class="w-5 h-5 rounded-md bg-slate-700 border border-slate-600 flex items-center justify-center text-[9px] font-bold text-indigo-400 flex-shrink-0">
                    {{ Str::of($task->assignee->name)->explode(' ')->take(2)->map(fn($w) => Str::substr($w, 0, 1))->implode('') }}
                </div>
                <span class="text-[11px] text-slate-400 truncate">{{ $task->assignee->name }}</span>
            @else
                <span class="text-[11px] text-slate-600">Unassigned</span>
            @endif
        </div>

        {{-- Due Date --}}
        @if($task->due_date)
            <span class="text-[11px] font-medium flex-shrink-0 {{ $isDue ? 'text-rose-400' : 'text-slate-500' }}">
                {{ $task->due_date->format('d M') }}
                @if($isDue)
                    <span class="text-[9px]">⚠</span>
                @endif
            </span>
        @endif
    </div>
</div>
