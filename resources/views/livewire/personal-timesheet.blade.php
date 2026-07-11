{{-- 
  View: Personal Timesheet (Livewire Component)
  Halaman untuk melihat riwayat log kerja pribadi.
  Dilengkapi fitur filter tanggal dan tombol Export PDF.
--}}
<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-ink">Personal Timesheet</h2>
        <div class="flex gap-3">
            <x-button variant="danger" wire:click="exportPdf" loadingTarget="exportPdf">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </x-slot>
                Export PDF
            </x-button>
        </div>
    </div>

    {{-- Flash Info for Export --}}
    @if(session('info'))
    <div class="mb-6 px-4 py-3 rounded-lg bg-primary-soft border border-primary/20 text-primary text-sm flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <x-stat-card label="Total Jam Minggu Ini (Approved)" :value="number_format($summary['weekHours'], 1)" suffix="jam" tone="success" />
        <x-stat-card label="Total Jam Bulan Ini (Approved)" :value="number_format($summary['monthHours'], 1)" suffix="jam" tone="primary" />
    </div>

    {{-- Filter & Table --}}
    <x-card padding="p-0">
        <div class="px-6 py-5 border-b border-border flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface rounded-t-xl">
            <h3 class="text-lg font-semibold text-ink">Riwayat Waktu Anda</h3>
            
            <div class="flex items-center gap-3">
                <input type="date" wire:model.live="filterStart" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2">
                <span class="text-ink-secondary">-</span>
                <input type="date" wire:model.live="filterEnd" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2">
            </div>
        </div>

        <div class="relative">
            <div wire:loading.flex wire:target="filterStart, filterEnd" class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 items-center justify-center rounded-b-xl">
                <svg class="w-8 h-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <x-table :headers="['No', 'Tanggal', 'Proyek', 'Tugas', 'Jam Mulai', 'Jam Selesai', 'Durasi', 'Catatan', 'Status']">
                @forelse($timeLogs as $index => $log)
                    <tr class="hover:bg-subtle transition-colors">
                        <td class="px-6 py-4">{{ $timeLogs->firstItem() + $index }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($log->start_time)->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-medium text-ink">{{ $log->project->title ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $log->task->title ?? '-' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-ink">{{ $log->duration_hours }}j</td>
                        <td class="px-6 py-4 text-sm max-w-[200px] truncate" title="{{ $log->notes }}">{{ $log->notes ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$log->status" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-ink-muted italic">
                            Belum ada log kerja di periode yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>
        
        @if($timeLogs->hasPages())
        <div class="px-6 py-4 border-t border-border bg-subtle/30 rounded-b-xl">
            {{ $timeLogs->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </x-card>
</div>
