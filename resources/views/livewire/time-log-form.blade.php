<div>
    {{-- Trigger Button --}}
    <button
        wire:click="$set('showModal', true)"
        class="flex items-center gap-2 px-4 py-2 bg-brand-500 hover:bg-brand-400 text-white text-sm font-semibold rounded-xl transition-all duration-150 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Add Time Log
    </button>

    {{-- Modal --}}
    @if ($showModal)
        <div
            x-data
            x-init="$nextTick(() => document.body.classList.add('overflow-hidden'))"
            x-on:keydown.escape.window="$wire.set('showModal', false)"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            wire:transition>

            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                wire:click="$set('showModal', false)"
                x-init="$el.addEventListener('click', () => { document.body.classList.remove('overflow-hidden') })">
            </div>

            {{-- Panel --}}
            <div class="relative w-full max-w-lg bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-white">Tambah Time Log</h2>
                    </div>
                    <button
                        wire:click="$set('showModal', false)"
                        x-on:click="document.body.classList.remove('overflow-hidden')"
                        class="text-slate-500 hover:text-slate-300 transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="saveTimeLog" class="px-6 py-5 space-y-4">

                    {{-- Proyek --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Proyek <span class="text-rose-400">*</span>
                        </label>
                        <select
                            wire:model.live="projectId"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all @error('projectId') border-rose-500/60 @enderror">
                            <option value="">— Pilih Proyek —</option>
                            @foreach ($availableProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                        @error('projectId')
                            <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tugas --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">
                            Tugas <span class="text-rose-400">*</span>
                        </label>
                        <select
                            wire:model="taskId"
                            @disabled($projectId === '')
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all disabled:opacity-40 disabled:cursor-not-allowed @error('taskId') border-rose-500/60 @enderror">
                            <option value="">— Pilih Tugas —</option>
                            @foreach ($availableTasks as $task)
                                <option value="{{ $task->id }}">{{ $task->title }}</option>
                            @endforeach
                        </select>
                        @error('taskId')
                            <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jam Mulai & Selesai --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                Jam Mulai <span class="text-rose-400">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model.blur="startTime"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all [color-scheme:dark] @error('startTime') border-rose-500/60 @enderror">
                            @error('startTime')
                                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                Jam Selesai <span class="text-rose-400">*</span>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model.blur="endTime"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all [color-scheme:dark] @error('endTime') border-rose-500/60 @enderror">
                            @error('endTime')
                                <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Catatan</label>
                        <textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Deskripsikan pekerjaan yang dilakukan..."
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all resize-none"></textarea>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-1">
                        <button
                            type="button"
                            wire:click="resetForm(); $set('showModal', false)"
                            x-on:click="document.body.classList.remove('overflow-hidden')"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg transition-all cursor-pointer">
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveTimeLog"
                            class="flex items-center gap-2 px-4 py-2 bg-brand-500 hover:bg-brand-400 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition-all cursor-pointer">
                            <span wire:loading.remove wire:target="saveTimeLog">Simpan Log</span>
                            <span wire:loading wire:target="saveTimeLog" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
