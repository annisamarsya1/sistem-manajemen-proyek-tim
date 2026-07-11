<?php

namespace App\Livewire;

use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: PersonalTimesheet
 * 
 * Menampilkan catatan waktu (time logs) khusus untuk pengguna yang sedang login.
 * Memiliki fitur filter berdasarkan rentang tanggal dan export PDF khusus untuk diri sendiri.
 */
#[Layout('components.layouts.app')]
class PersonalTimesheet extends Component
{
    use WithPagination;

    public string $filterStart = '';
    public string $filterEnd = '';

    public function mount(): void
    {
        $this->title = 'Personal Timesheet';
        $this->filterStart = now()->subDays(30)->format('Y-m-d');
        $this->filterEnd = now()->format('Y-m-d');
    }

    /**
     * Ekspor catatan waktu pribadi pengguna ke format PDF.
     */
    public function exportPdf()
    {
        $query = TimeLog::with(['project', 'task', 'user'])
            ->where('user_id', Auth::id());

        if ($this->filterStart) {
            $query->whereDate('start_time', '>=', $this->filterStart);
        }
        if ($this->filterEnd) {
            $query->whereDate('start_time', '<=', $this->filterEnd);
        }

        $logs = $query->orderBy('start_time', 'desc')->get();

        if ($logs->isEmpty()) {
            session()->flash('info', 'Tidak ada data untuk diekspor dengan filter ini.');
            return null;
        }

        $pdf = Pdf::loadView('exports.time-logs', ['logs' => $logs]);
        $filename = 'timesheet_' . str_replace(' ', '_', strtolower(Auth::user()->name)) . '_' . now()->format('Y-m-d') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }

    /**
     * Computed Property: Menghitung total jam kerja dalam minggu dan bulan ini.
     * Hanya menghitung log yang statusnya 'approved'.
     */
    public function getSummaryProperty()
    {
        $userId = Auth::id();
        
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $weekHours = TimeLog::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_time', '>=', $weekStart)
            ->sum('duration_hours');

        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthHours = TimeLog::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_time', '>=', $monthStart)
            ->sum('duration_hours');

        return [
            'weekHours' => $weekHours,
            'monthHours' => $monthHours,
        ];
    }

    public function render()
    {
        $query = TimeLog::with(['project', 'task'])
            ->where('user_id', Auth::id());

        if ($this->filterStart) {
            $query->whereDate('start_time', '>=', $this->filterStart);
        }
        if ($this->filterEnd) {
            $query->whereDate('start_time', '<=', $this->filterEnd);
        }

        $query->orderBy('start_time', 'desc');

        return view('livewire.personal-timesheet', [
            'timeLogs' => $query->paginate(20),
            'summary' => $this->summary,
        ])->title('Personal Timesheet');
    }
}
