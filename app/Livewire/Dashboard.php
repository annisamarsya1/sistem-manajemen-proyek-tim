<?php

namespace App\Livewire;

use App\Exports\TimeLogsExport;
use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Dashboard')]
#[Layout('layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    use WithPagination;

    public string $filterDateStart = '';

    public string $filterDateEnd = '';

    public string $filterProjectId = '';

    public string $filterStatus = '';

    /** @var Collection<int, TeamProject> */
    public Collection $projects;

    public function mount(): void
    {
        $this->projects = $this->loadProjectsForFilter();
    }

    // ---------------------------------------------------------------------------
    // Typed auth helper
    // ---------------------------------------------------------------------------

    private function currentUser(): User
    {
        $user = Auth::user();

        assert($user instanceof User);

        return $user;
    }

    // ---------------------------------------------------------------------------
    // Filter projects dropdown
    // ---------------------------------------------------------------------------

    /** @return Collection<int, TeamProject> */
    private function loadProjectsForFilter(): Collection
    {
        $user = $this->currentUser();

        if ($user->role === 'employee') {
            $projectIds = TimeLog::where('user_id', $user->id)
                ->distinct()
                ->pluck('project_id');

            return TeamProject::whereIn('id', $projectIds)
                ->orderBy('title')
                ->get(['id', 'title']);
        }

        return TeamProject::orderBy('title')->get(['id', 'title']);
    }

    // ---------------------------------------------------------------------------
    // Analytics: Total Logged Hours (minggu berjalan, approved + pending)
    // ---------------------------------------------------------------------------

    public function getTotalLoggedHoursProperty(): float
    {
        $user = $this->currentUser();
        $weekStart = Carbon::now()->startOfWeek();
        $today = Carbon::today()->endOfDay();

        $query = TimeLog::whereBetween('start_time', [$weekStart, $today])
            ->whereIn('status', ['approved', 'pending']);

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        return (float) $query->sum('duration_hours');
    }

    // ---------------------------------------------------------------------------
    // Analytics: Met Target (approved hours hari ini >= 8 jam)
    // ---------------------------------------------------------------------------

    public function getMetTargetCountProperty(): int|string
    {
        $user = $this->currentUser();
        $today = Carbon::today();

        if ($user->role === 'employee') {
            $hours = TimeLog::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->where('status', 'approved')
                ->sum('duration_hours');

            return $hours >= 8
                ? 'Anda memenuhi target hari ini'
                : 'Anda belum memenuhi target';
        }

        // Admin / PM: count distinct users who logged >= 8 approved hours today
        $metUserIds = TimeLog::whereDate('start_time', $today)
            ->where('status', 'approved')
            ->selectRaw('user_id, SUM(duration_hours) as total_hours')
            ->groupBy('user_id')
            ->havingRaw('total_hours >= 8')
            ->pluck('user_id');

        return $metUserIds->count();
    }

    // ---------------------------------------------------------------------------
    // Analytics: Under Target
    // ---------------------------------------------------------------------------

    public function getUnderTargetCountProperty(): int|string
    {
        $user = $this->currentUser();
        $today = Carbon::today();

        if ($user->role === 'employee') {
            $hours = TimeLog::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->where('status', 'approved')
                ->sum('duration_hours');

            return $hours < 8
                ? 'Anda belum memenuhi target hari ini'
                : 'Anda sudah memenuhi target';
        }

        $metUserIds = TimeLog::whereDate('start_time', $today)
            ->where('status', 'approved')
            ->selectRaw('user_id, SUM(duration_hours) as total_hours')
            ->groupBy('user_id')
            ->havingRaw('total_hours >= 8')
            ->pluck('user_id');

        $allActiveUserCount = User::where('is_active', true)->count();

        return max(0, $allActiveUserCount - $metUserIds->count());
    }

    // ---------------------------------------------------------------------------
    // Today's Snapshot (Admin / PM only)
    // ---------------------------------------------------------------------------

    /** @return Collection<int, TimeLog> */
    public function getTodaySnapshotProperty(): Collection
    {
        if ($this->currentUser()->role === 'employee') {
            return collect();
        }

        return TimeLog::with(['user', 'project', 'task'])
            ->whereDate('start_time', Carbon::today())
            ->orderByDesc('start_time')
            ->get();
    }

    // ---------------------------------------------------------------------------
    // Time Logs Table with filters
    // ---------------------------------------------------------------------------

    public function getTimeLogsProperty(): LengthAwarePaginator
    {
        $user = $this->currentUser();

        $query = TimeLog::with(['user', 'project', 'task'])
            ->orderByDesc('start_time');

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        if ($this->filterDateStart !== '') {
            $query->where('start_time', '>=', Carbon::parse($this->filterDateStart)->startOfDay());
        }

        if ($this->filterDateEnd !== '') {
            $query->where('start_time', '<=', Carbon::parse($this->filterDateEnd)->endOfDay());
        }

        if ($this->filterProjectId !== '') {
            $query->where('project_id', $this->filterProjectId);
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(20);
    }

    // ---------------------------------------------------------------------------
    // Actions: Approve / Reject
    // ---------------------------------------------------------------------------

    public function approveLog(int $id): void
    {
        $user = $this->currentUser();

        if (! in_array($user->role, ['admin', 'project_manager'])) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui time log.');
        }

        $log = TimeLog::findOrFail($id);

        if ($log->status !== 'pending') {
            session()->flash('error', 'Log ini sudah diproses sebelumnya.');

            return;
        }

        $log->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        session()->flash('success', 'Time log disetujui.');
    }

    public function rejectLog(int $id): void
    {
        $user = $this->currentUser();

        if (! in_array($user->role, ['admin', 'project_manager'])) {
            abort(403, 'Anda tidak memiliki akses untuk menolak time log.');
        }

        $log = TimeLog::findOrFail($id);

        if ($log->status !== 'pending') {
            session()->flash('error', 'Log ini sudah diproses sebelumnya.');

            return;
        }

        $log->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        session()->flash('error', 'Time log ditolak.');
    }

    // ---------------------------------------------------------------------------
    // Export
    // ---------------------------------------------------------------------------

    public function exportCsv(): ?BinaryFileResponse
    {
        if (! in_array($this->currentUser()->role, ['admin', 'project_manager'])) {
            abort(403);
        }

        $filters = $this->buildExportFilters();
        $export = new TimeLogsExport($filters);

        if ($export->query()->count() === 0) {
            session()->flash('info', 'Tidak ada data untuk diekspor dengan filter ini.');

            return null;
        }

        $filename = 'time_logs_'.now()->format('Y-m-d').'.csv';

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportExcel(): ?BinaryFileResponse
    {
        if (! in_array($this->currentUser()->role, ['admin', 'project_manager'])) {
            abort(403);
        }

        $filters = $this->buildExportFilters();
        $export = new TimeLogsExport($filters);

        if ($export->query()->count() === 0) {
            session()->flash('info', 'Tidak ada data untuk diekspor dengan filter ini.');

            return null;
        }

        $filename = 'time_logs_'.now()->format('Y-m-d').'.xlsx';

        return Excel::download($export, $filename);
    }

    /** @return array{start: string, end: string, project_id: string, status: string} */
    private function buildExportFilters(): array
    {
        return [
            'start' => $this->filterDateStart,
            'end' => $this->filterDateEnd,
            'project_id' => $this->filterProjectId,
            'status' => $this->filterStatus,
        ];
    }

    // ---------------------------------------------------------------------------
    // Reset pagination on filter change
    // ---------------------------------------------------------------------------

    public function updatedFilterDateStart(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateEnd(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProjectId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.dashboard', [
            'totalLoggedHours' => $this->totalLoggedHours,
            'metTargetCount' => $this->metTargetCount,
            'underTargetCount' => $this->underTargetCount,
            'todaySnapshot' => $this->todaySnapshot,
            'timeLogs' => $this->timeLogs,
        ]);
    }
}
