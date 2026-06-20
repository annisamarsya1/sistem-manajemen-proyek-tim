<div wire:poll.60000ms class="space-y-8">

    {{-- ===================================================================== --}}
    {{-- Flash Notifications --}}
    {{-- ===================================================================== --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 p-4 bg-sky-500/10 border border-sky-500/20 text-sky-400 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium">{{ session('info') }}</p>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- Add Time Log --}}
    {{-- ===================================================================== --}}
    <div class="flex justify-end">
        <livewire:time-log-form />
    </div>

    {{-- ===================================================================== --}}
    {{-- Bagian 1: Analytics Cards --}}
    {{-- ===================================================================== --}}
    <section>
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Ringkasan Minggu Ini</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Card: Total Logged Hours --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Total Logged Hours</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($totalLoggedHours, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">jam</span></p>
                    <p class="text-xs text-slate-600 mt-1">Minggu berjalan</p>
                </div>
            </div>

            {{-- Card: Target Hours --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Target Hours</p>
                    <p class="text-2xl font-bold text-white">40<span class="text-sm font-normal text-slate-400 ml-1">jam/minggu</span></p>
                    <p class="text-xs text-slate-600 mt-1">Standar per minggu</p>
                </div>
            </div>

            {{-- Card: Met Target --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-slate-500 mb-1">Met Target Hari Ini</p>
                    @if (is_int($metTargetCount))
                        <p class="text-2xl font-bold text-white">{{ $metTargetCount }}<span class="text-sm font-normal text-slate-400 ml-1">orang</span></p>
                    @else
                        <p class="text-sm font-semibold text-emerald-400 leading-snug">{{ $metTargetCount }}</p>
                    @endif
                </div>
            </div>

            {{-- Card: Under Target --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-slate-500 mb-1">Under Target Hari Ini</p>
                    @if (is_int($underTargetCount))
                        <p class="text-2xl font-bold text-white">{{ $underTargetCount }}<span class="text-sm font-normal text-slate-400 ml-1">orang</span></p>
                    @else
                        <p class="text-sm font-semibold text-rose-400 leading-snug">{{ $underTargetCount }}</p>
                    @endif
                </div>
            </div>

        </div>
    </section>

    {{-- ===================================================================== --}}
    {{-- Bagian 2: Today's Snapshot (Admin / PM saja) --}}
    {{-- ===================================================================== --}}
    @if (auth()->user()->role !== 'employee')
        <section>
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Today's Snapshot</h2>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                @if ($todaySnapshot->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-3 opacity-40">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm">Tidak ada aktivitas tercatat hari ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-800 text-left">
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Anggota</th>
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek</th>
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tugas</th>
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Durasi Hari Ini</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                @foreach ($todaySnapshot as $log)
                                    <tr wire:key="snapshot-{{ $log->id }}" class="hover:bg-slate-800/30 transition-colors">
                                        <td class="px-5 py-3.5 text-slate-300 font-medium">{{ $log->user?->name ?? '—' }}</td>
                                        <td class="px-5 py-3.5 text-slate-400">{{ $log->project?->title ?? '—' }}</td>
                                        <td class="px-5 py-3.5 text-slate-400">{{ $log->task?->title ?? '—' }}</td>
                                        <td class="px-5 py-3.5 text-slate-300 font-mono text-right">{{ number_format($log->duration_hours, 2) }} jam</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ===================================================================== --}}
    {{-- Bagian 3: Filter + Time Logs Table --}}
    {{-- ===================================================================== --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest">
                {{ auth()->user()->role === 'employee' ? 'My Time Logs' : 'All Time Logs' }}
            </h2>

            {{-- Export Buttons (Admin / PM only) --}}
            @if (auth()->user()->role !== 'employee')
                <div class="flex items-center gap-2">
                    <button wire:click="exportCsv"
                            wire:loading.attr="disabled"
                            wire:target="exportCsv"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-slate-600 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading.remove wire:target="exportCsv" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <svg wire:loading wire:target="exportCsv" class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportCsv">Export CSV</span>
                        <span wire:loading wire:target="exportCsv">Mengekspor...</span>
                    </button>
                    <button wire:click="exportExcel"
                            wire:loading.attr="disabled"
                            wire:target="exportExcel"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-slate-600 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading.remove wire:target="exportExcel" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <svg wire:loading wire:target="exportExcel" class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                        <span wire:loading wire:target="exportExcel">Mengekspor...</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Filter Bar --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" wire:model.live="filterDateStart"
                           class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" wire:model.live="filterDateEnd"
                           class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Proyek</label>
                    <select wire:model.live="filterProjectId"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
                        <option value="">Semua Proyek</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Status</label>
                    <select wire:model.live="filterStatus"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Time Logs Table --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            @if ($timeLogs->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-3 opacity-40">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p class="text-sm">Belum ada log kerja yang tercatat.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-left">
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                                @if (auth()->user()->role !== 'employee')
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                @endif
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tugas</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Mulai</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Selesai</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Durasi</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Catatan</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                @if (auth()->user()->role !== 'employee')
                                    <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($timeLogs as $index => $log)
                                <tr wire:key="log-{{ $log->id }}" class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $timeLogs->firstItem() + $loop->index }}</td>
                                    @if (auth()->user()->role !== 'employee')
                                        <td class="px-5 py-3.5 text-slate-300 font-medium whitespace-nowrap">{{ $log->user?->name ?? '—' }}</td>
                                    @endif
                                    <td class="px-5 py-3.5 text-slate-400 max-w-[160px] truncate">{{ $log->project?->title ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 max-w-[160px] truncate">{{ $log->task?->title ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">{{ $log->start_time?->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">{{ $log->end_time?->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-3.5 text-slate-300 font-mono text-xs text-right whitespace-nowrap">{{ number_format($log->duration_hours, 2) }}j</td>
                                    <td class="px-5 py-3.5 text-slate-500 text-xs max-w-[180px] truncate" title="{{ $log->notes }}">{{ $log->notes ?? '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        @if ($log->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Approved
                                            </span>
                                        @elseif ($log->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    @if (auth()->user()->role !== 'employee')
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            @if ($log->status === 'pending')
                                                <div class="flex items-center justify-center gap-2">
                                                    <button wire:click="approveLog({{ $log->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="approveLog({{ $log->id }})"
                                                            class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 text-emerald-400 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer disabled:opacity-50">
                                                        Approve
                                                    </button>
                                                    <button wire:click="rejectLog({{ $log->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="rejectLog({{ $log->id }})"
                                                            class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 hover:border-rose-500/40 text-rose-400 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer disabled:opacity-50">
                                                        Reject
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-600">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($timeLogs->hasPages())
                    <div class="px-5 py-4 border-t border-slate-800">
                        {{ $timeLogs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

</div>
