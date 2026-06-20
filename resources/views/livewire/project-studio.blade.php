<div x-data="{ open: $wire.entangle('showModal') }" class="space-y-6">

    {{-- ===================================================================== --}}
    {{-- Flash Notifications --}}
    {{-- ===================================================================== --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center justify-between gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- Header: Judul + Tombol Buat --}}
    {{-- ===================================================================== --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Project Studio</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola semua proyek tim Anda.</p>
        </div>
        <button @click="$wire.openCreateModal()"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-lg shadow-indigo-600/20 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Proyek
        </button>
    </div>

    {{-- ===================================================================== --}}
    {{-- Filter Bar --}}
    {{-- ===================================================================== --}}
    <div class="flex flex-wrap items-center gap-3">
        <select wire:model.live="filterStatus"
                class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
            <option value="">Semua Status</option>
            <option value="planning">Planning</option>
            <option value="active">Active</option>
            <option value="on_hold">On Hold</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <select wire:model.live="filterPriority"
                class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
            <option value="">Semua Prioritas</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>
        @if ($filterStatus !== '' || $filterPriority !== '')
            <button wire:click="$set('filterStatus', ''); $set('filterPriority', '')"
                    class="px-3 py-2 text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-xl transition-all cursor-pointer">
                Reset Filter
            </button>
        @endif
    </div>

    {{-- ===================================================================== --}}
    {{-- Tabel Proyek --}}
    {{-- ===================================================================== --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        @if ($projects->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-3 opacity-40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
                <p class="text-sm">Belum ada proyek. Klik '+ Buat Proyek' untuk memulai.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-left">
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Klien</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Deadline</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Prioritas</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach ($projects as $project)
                            <tr wire:key="project-{{ $project->id }}" class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $projects->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-white">{{ $project->title }}</p>
                                    @if ($project->description)
                                        <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $project->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap">{{ $project->client_name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">
                                    {{ $project->deadline->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $priorityMap = [
                                            'low'    => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                            'medium' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                                            'high'   => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                            'urgent' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                        ];
                                        $priorityClass = $priorityMap[$project->priority] ?? $priorityMap['medium'];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $priorityClass }} capitalize">
                                        {{ $project->priority }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusMap = [
                                            'planning'  => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                            'active'    => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            'on_hold'   => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'completed' => 'bg-violet-500/10 text-violet-400 border-violet-500/20',
                                            'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                        ];
                                        $statusClass = $statusMap[$project->status] ?? $statusMap['planning'];
                                        $statusLabel = str_replace('_', ' ', $project->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $statusClass }} capitalize">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="editProject({{ $project->id }})"
                                                class="p-1.5 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-all cursor-pointer"
                                                title="Edit proyek">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteProject({{ $project->id }})"
                                                wire:confirm="Hapus proyek ini? Semua tugas terkait akan ikut terhapus."
                                                wire:loading.attr="disabled"
                                                wire:target="deleteProject({{ $project->id }})"
                                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all cursor-pointer disabled:opacity-50"
                                                title="Hapus proyek">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="px-5 py-4 border-t border-slate-800">
                    {{ $projects->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ===================================================================== --}}
    {{-- Modal Create / Edit --}}
    {{-- ===================================================================== --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="$wire.closeModal()"></div>

        {{-- Modal Panel --}}
        <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop>

            {{-- Top accent --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800">
                <h2 class="text-lg font-bold text-white">
                    {{ $editingId ? 'Edit Proyek' : 'Buat Proyek Baru' }}
                </h2>
                <button @click="$wire.closeModal()" class="text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form wire:submit="{{ $editingId ? 'update' : 'save' }}" class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">

                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Judul Proyek <span class="text-rose-400">*</span></label>
                    <input wire:model="title" type="text" placeholder="Nama proyek..."
                           class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('title') border-rose-500/50 @enderror">
                    @error('title')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Deskripsi</label>
                    <textarea wire:model="description" rows="3" placeholder="Deskripsi singkat proyek..."
                              class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all resize-none"></textarea>
                </div>

                {{-- Klien + Anggaran --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nama Klien</label>
                        <input wire:model="clientName" type="text" placeholder="Nama perusahaan klien..."
                               class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Anggaran (Rp)</label>
                        <input wire:model="budget" type="number" min="0" step="1000" placeholder="0"
                               class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('budget') border-rose-500/50 @enderror">
                        @error('budget')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Mulai + Deadline --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tanggal Mulai</label>
                        <input wire:model="startDate" type="date"
                               class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Deadline <span class="text-rose-400">*</span></label>
                        <input wire:model="deadline" type="date"
                               class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark] @error('deadline') border-rose-500/50 @enderror">
                        @error('deadline')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Prioritas + Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Prioritas <span class="text-rose-400">*</span></label>
                        <select wire:model="priority"
                                class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status <span class="text-rose-400">*</span></label>
                        <select wire:model="status"
                                class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
                            <option value="planning">Planning</option>
                            <option value="active">Active</option>
                            <option value="on_hold">On Hold</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="$wire.closeModal()"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="{{ $editingId ? 'update' : 'save' }}"
                            class="flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-600/20 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading wire:target="{{ $editingId ? 'update' : 'save' }}" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="{{ $editingId ? 'update' : 'save' }}">
                            {{ $editingId ? 'Simpan Perubahan' : 'Buat Proyek' }}
                        </span>
                        <span wire:loading wire:target="{{ $editingId ? 'update' : 'save' }}">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
