<div class="space-y-8">

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

    {{-- ===================================================================== --}}
    {{-- Page Header --}}
    {{-- ===================================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Personal Timesheet</h1>
            <p class="text-sm text-slate-500 mt-0.5">Riwayat log kerja pribadi Anda</p>
        </div>
        <livewire:time-log-form />
    </div>

    {{-- ===================================================================== --}}
    {{-- Summary Cards --}}
    {{-- ===================================================================== --}}
    <section>
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Ringkasan (Approved)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Card: Total Jam Minggu Ini --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Total Jam Minggu Ini</p>
                    <p class="text-2xl font-bold text-white">
                        {{ number_format($weekHours, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">jam</span>
                    </p>
                    <p class="text-xs text-slate-600 mt-1">Senin s/d hari ini</p>
                </div>
            </div>

            {{-- Card: Total Jam Bulan Ini --}}
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Total Jam Bulan Ini</p>
                    <p class="text-2xl font-bold text-white">
                        {{ number_format($monthHours, 1) }}<span class="text-sm font-normal text-slate-400 ml-1">jam</span>
                    </p>
                    <p class="text-xs text-slate-600 mt-1">{{ now()->format('F Y') }}</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ===================================================================== --}}
    {{-- Filter + Tabel --}}
    {{-- ===================================================================== --}}
    <section>
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-4">Log Kerja Pribadi</h2>

        {{-- Filter Bar --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Dari Tanggal</label>
                    <input
                        type="date"
                        wire:model.live="filterStart"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Sampai Tanggal</label>
                    <input
                        type="date"
                        wire:model.live="filterEnd"
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all [color-scheme:dark]">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            @if ($timeLogs->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-3 opacity-40">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm">Belum ada log kerja di periode ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-left">
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tugas</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Mulai</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Selesai</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Durasi</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Catatan</th>
                                <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach ($timeLogs as $log)
                                <tr wire:key="ts-{{ $log->id }}" class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $timeLogs->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">{{ $log->start_time?->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 max-w-[140px] truncate">{{ $log->project?->title ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 max-w-[140px] truncate">{{ $log->task?->title ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">{{ $log->start_time?->format('H:i') }}</td>
                                    <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">{{ $log->end_time?->format('H:i') }}</td>
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
