{{-- 
  View: Dashboard (Livewire Component)
  Halaman ringkasan utama. Menampilkan kartu statistik (Analytics),
  daftar log waktu (Time Logs), dan modal konfirmasi untuk persetujuan (Approve/Reject).
  Dilengkapi polling (wire:poll) untuk memperbarui data otomatis.
--}}
<div x-data="{ showConfirmModal: false, confirmType: '', logId: null }">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-ink">Dashboard Overview</h2>
        
        <div class="flex items-center gap-3 ml-auto md:ml-0">
            @if(auth()->user()->role !== 'employee')
            <x-button variant="danger" wire:click="exportPdf" loadingTarget="exportPdf">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </x-slot>
                Export PDF
            </x-button>
            @endif
            
            <livewire:time-log-form />
        </div>
    </div>

    {{-- Flash Info for Export --}}
    @if(session('info'))
    <div class="mb-6 px-4 py-3 rounded-lg bg-primary-soft border border-primary/20 text-primary text-sm flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- F-B1: Analytics Cards --}}
    <div wire:poll.60000ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card label="Total Logged Hours (Minggu Ini)" :value="number_format($analytics['totalHours'], 1)" suffix="jam" />
        <x-stat-card label="Target Hours" :value="$analytics['targetHours']" />
        <x-stat-card label="Met Target (Hari Ini)" :value="$analytics['metTarget']" tone="success" />
        <x-stat-card label="Under Target (Hari Ini)" :value="$analytics['underTarget']" tone="danger" />
    </div>

    {{-- F-B2: Today's Snapshot (Admin/PM Only) --}}
    @if(auth()->user()->role !== 'employee')
    <x-card padding="p-0" class="mb-8" wire:poll.60000ms>
        <div class="px-6 py-4 border-b border-border bg-subtle/30 rounded-t-xl">
            <h3 class="text-lg font-semibold text-ink">Aktivitas Hari Ini (Snapshot)</h3>
        </div>
        <div class="p-0">
            @if($snapshot->isEmpty())
                <div class="p-6">
                    <p class="text-ink-muted text-sm italic">Tidak ada aktivitas tercatat hari ini.</p>
                </div>
            @else
                <x-table :headers="['Nama Anggota', 'Proyek', 'Tugas', 'Durasi Hari Ini']">
                    @foreach($snapshot as $log)
                    <tr class="hover:bg-subtle transition-colors">
                        <td class="px-6 py-4 font-medium text-ink">{{ $log->user->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $log->project->title ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $log->task->title ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $log->duration_hours }} jam</td>
                    </tr>
                    @endforeach
                </x-table>
            @endif
        </div>
    </x-card>
    @endif

    {{-- F-B3 & F-B4: Time Logs Table with Filters --}}
    <x-card padding="p-0">
        <div class="px-6 py-5 border-b border-border flex flex-col md:flex-row md:items-center justify-between gap-4 bg-surface rounded-t-xl">
            <h3 class="text-lg font-semibold text-ink">Daftar Time Logs</h3>
            
            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                <input type="date" wire:model.live="filterDateStart" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2">
                <span class="text-ink-secondary">-</span>
                <input type="date" wire:model.live="filterDateEnd" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2">
                
                <select wire:model.live="filterProjectId" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2 max-w-[200px] truncate">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus" class="bg-surface border border-border text-ink text-sm rounded-md focus:ring-primary focus:border-primary block px-3 py-2">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="relative">
            <div wire:loading.flex class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 items-center justify-center rounded-b-xl">
                <svg class="w-8 h-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <x-table :headers="['No', 'Nama Karyawan', 'Proyek / Tugas', 'Waktu', 'Durasi', 'Catatan', 'Status', 'Aksi']">
                @forelse($timeLogs as $index => $log)
                    <tr class="hover:bg-subtle transition-colors">
                        <td class="px-6 py-4">{{ $timeLogs->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-medium text-ink">{{ $log->user->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="text-ink font-medium">{{ $log->project->title ?? '-' }}</div>
                            <div class="text-xs text-ink-secondary mt-1">{{ $log->task->title ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>Mulai: {{ \Carbon\Carbon::parse($log->start_time)->format('d M Y H:i') }}</div>
                            <div>Selesai: {{ \Carbon\Carbon::parse($log->end_time)->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $log->duration_hours }}j</td>
                        <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $log->notes }}">{{ $log->notes ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$log->status" />
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(auth()->user()->role !== 'employee' && $log->status === 'pending')
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="showConfirmModal = true; confirmType = 'approve'; logId = {{ $log->id }}" class="p-1.5 text-success hover:bg-success-soft rounded-md transition-colors" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                    <button @click="showConfirmModal = true; confirmType = 'reject'; logId = {{ $log->id }}" class="p-1.5 text-danger hover:bg-danger-soft rounded-md transition-colors" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            @else
                                <span class="text-ink-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-ink-muted italic">
                            Tidak ada aktivitas tercatat hari ini.
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

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showConfirmModal" x-transition.opacity class="fixed inset-0 bg-ink/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="showConfirmModal = false" class="relative transform overflow-hidden rounded-2xl bg-surface border border-border text-left shadow-panel transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-surface px-6 py-5">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="confirmType === 'approve' ? 'bg-success-soft' : 'bg-danger-soft'">
                                <svg x-show="confirmType === 'approve'" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg x-show="confirmType === 'reject'" class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-ink" id="modal-title" x-text="confirmType === 'approve' ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan'"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-ink-secondary" x-text="confirmType === 'approve' ? 'Apakah Anda yakin ingin menyetujui catatan waktu ini?' : 'Apakah Anda yakin ingin menolak catatan waktu ini?'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-subtle/50 px-6 py-4 border-t border-border sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" x-show="confirmType === 'approve'" @click="$wire.approveLog(logId); showConfirmModal = false" class="inline-flex w-full justify-center rounded-xl bg-success px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-success/90 sm:w-auto">
                            Setujui
                        </button>
                        <button type="button" x-show="confirmType === 'reject'" @click="$wire.rejectLog(logId); showConfirmModal = false" class="inline-flex w-full justify-center rounded-xl bg-danger px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-danger/90 sm:w-auto">
                            Tolak
                        </button>
                        <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-surface px-4 py-2.5 text-sm font-semibold text-ink-secondary shadow-sm ring-1 ring-inset ring-border hover:bg-subtle sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
