<?php

namespace App\Livewire;

use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

/**
 * Livewire Component: Dashboard
 * 
 * Merupakan halaman utama setelah pengguna berhasil login.
 * Menampilkan ringkasan statistik (analytics), tabel time logs terkini, 
 * fitur persetujuan (approve/reject) untuk log waktu (bagi Admin/PM),
 * serta fitur export ke PDF.
 */
#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public string $filterDateStart = '';
    public string $filterDateEnd = '';
    public string $filterProjectId = '';
    public string $filterStatus = '';

    public function mount()
    {
        $this->title = 'Dashboard';
    }

    /**
     * Menyetujui (Approve) catatan waktu kerja karyawan.
     * Hanya bisa diakses oleh Admin atau PM.
     */
    public function approveLog(int $id): void
    {
        $user = Auth::user();
        if ($user->role === 'employee') {
            abort(403, 'Akses ditolak.');
        }

        $log = TimeLog::findOrFail($id);
        if ($log->status === 'pending') {
            $log->update([
                'status' => 'approved',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
            session()->flash('success', 'Time log disetujui.');
        }
    }

    /**
     * Menolak (Reject) catatan waktu kerja karyawan.
     * Hanya bisa diakses oleh Admin atau PM.
     */
    public function rejectLog(int $id): void
    {
        $user = Auth::user();
        if ($user->role === 'employee') {
            abort(403, 'Akses ditolak.');
        }

        $log = TimeLog::findOrFail($id);
        if ($log->status === 'pending') {
            $log->update([
                'status' => 'rejected',
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);
            session()->flash('error', 'Time log ditolak.');
        }
    }

    /**
     * Mengekspor data catatan waktu ke format PDF menggunakan domPDF.
     * Menggunakan filter yang sedang aktif.
     */
    public function exportPdf()
    {
        $user = Auth::user();
        if ($user->role === 'employee') {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengekspor data.');
            return null;
        }

        $query = TimeLog::with(['user', 'project', 'task']);

        if ($this->filterDateStart) {
            $query->whereDate('start_time', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $query->whereDate('start_time', '<=', $this->filterDateEnd);
        }
        if ($this->filterProjectId) {
            $query->where('project_id', $this->filterProjectId);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $logs = $query->orderBy('start_time', 'desc')->get();

        if ($logs->isEmpty()) {
            session()->flash('info', 'Tidak ada data untuk diekspor dengan filter ini.');
            return null;
        }

        $pdf = Pdf::loadView('exports.time-logs', ['logs' => $logs]);
        $filename = 'time_logs_' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }


    /**
     * Mendapatkan data analitik (total jam kerja, karyawan memenuhi target, dll).
     * Dihitung secara dinamis berdasarkan role pengguna (Admin/PM vs Employee).
     */
    public function getAnalyticsProperty()
    {
        $user = Auth::user();
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endOfWeek = now()->endOfDay(); // or endOfWeek

        $totalHours = 0;
        $targetHoursStr = "40 jam/minggu";
        $metTarget = "";
        $underTarget = "";

        if ($user->role === 'admin' || $user->role === 'project_manager') {
            $totalHours = TimeLog::where('start_time', '>=', $startOfWeek)
                ->where('start_time', '<=', $endOfWeek)
                ->whereIn('status', ['approved', 'pending'])
                ->sum('duration_hours');

            // Count distinct users meeting/under target today
            $today = today()->toDateString();
            $userStats = TimeLog::select('user_id', DB::raw('SUM(duration_hours) as total_hours'))
                ->whereDate('start_time', $today)
                ->where('status', 'approved')
                ->groupBy('user_id')
                ->get();

            $metCount = $userStats->where('total_hours', '>=', 8)->count();
            // Total active users minus metCount could be underTarget, or just those who logged something < 8.
            // Let's count those who logged < 8
            $underCount = $userStats->where('total_hours', '<', 8)->count();
            
            // To be more accurate for "Under Target" as all users not meeting it:
            $totalActiveUsers = User::where('is_active', true)->where('role', 'employee')->count();
            $underTargetCount = max(0, $totalActiveUsers - $metCount); // assuming everyone should meet 8h

            $metTarget = $metCount . " Karyawan";
            $underTarget = $underTargetCount . " Karyawan";

        } else {
            $totalHours = TimeLog::where('user_id', $user->id)
                ->where('start_time', '>=', $startOfWeek)
                ->where('start_time', '<=', $endOfWeek)
                ->sum('duration_hours'); // any status or only approved+pending? 

            $today = today()->toDateString();
            $todayHours = TimeLog::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->where('status', 'approved')
                ->sum('duration_hours');

            if ($todayHours >= 8) {
                $metTarget = "Anda memenuhi target hari ini";
                $underTarget = "-";
            } else {
                $metTarget = "-";
                $underTarget = "Anda belum memenuhi target";
            }
        }

        return [
            'totalHours' => $totalHours,
            'targetHours' => $targetHoursStr,
            'metTarget' => $metTarget,
            'underTarget' => $underTarget,
        ];
    }

    public function getSnapshotProperty()
    {
        if (Auth::user()->role === 'employee') {
            return collect();
        }
        return TimeLog::with(['user', 'project', 'task'])
            ->whereDate('start_time', today()->toDateString())
            ->get();
    }

    public function getProjectsProperty()
    {
        $user = Auth::user();
        if ($user->role === 'admin' || $user->role === 'project_manager') {
            return TeamProject::all();
        }

        $projectIds = TimeLog::where('user_id', $user->id)->pluck('project_id')->unique();
        return TeamProject::whereIn('id', $projectIds)->get();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = TimeLog::with(['user', 'project', 'task']);

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        if ($this->filterDateStart) {
            $query->whereDate('start_time', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd) {
            $query->whereDate('start_time', '<=', $this->filterDateEnd);
        }
        if ($this->filterProjectId) {
            $query->where('project_id', $this->filterProjectId);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $query->orderBy('start_time', 'desc');

        return view('livewire.dashboard', [
            'timeLogs' => $query->paginate(20),
            'analytics' => $this->analytics,
            'snapshot' => $this->snapshot,
            'projects' => $this->projects,
        ])->title('Dashboard');
    }
}
