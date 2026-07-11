<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TeamProject;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Livewire Component: TimeLogForm
 * 
 * Komponen modal untuk mencatat waktu kerja (time log) harian.
 * Terintegrasi ke layout utama sehingga bisa dipanggil dari halaman manapun
 * melalui event 'openTimeLogModal'. Menangani konversi zona waktu dari klien (WIB/Lokal) ke UTC.
 */
class TimeLogForm extends Component
{
    public string $projectId = '';
    public string $taskId = '';
    public string $startTime = '';
    public string $endTime = '';
    public string $notes = '';
    public bool $showModal = false;
    public $availableProjects = [];
    public $availableTasks = [];

    protected $listeners = ['openTimeLogModal' => 'openModal'];

    public function mount()
    {
        $this->loadProjects();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->loadProjects();
        $this->showModal = true;
    }

    private function loadProjects()
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'project_manager'])) {
            $this->availableProjects = TeamProject::where('status', 'active')->get();
        } else {
            $projectIds = Task::where('assignee_id', $user->id)->pluck('project_id')->unique();
            $this->availableProjects = TeamProject::whereIn('id', $projectIds)->where('status', 'active')->get();
        }
    }

    public function updatedProjectId()
    {
        $this->taskId = '';
        $this->availableTasks = [];

        if (!$this->projectId) {
            return;
        }

        $user = Auth::user();
        if (in_array($user->role, ['admin', 'project_manager'])) {
            $this->availableTasks = Task::where('project_id', $this->projectId)->get();
        } else {
            $this->availableTasks = Task::where('project_id', $this->projectId)
                ->where('assignee_id', $user->id)
                ->get();
        }
    }

    /**
     * Validasi dan simpan log waktu kerja ke database.
     * Mengatur logika perlindungan terhadap tumpang tindih waktu (overlap),
     * konversi zona waktu ke UTC, dan batasan maksimal durasi kerja.
     */
    public function saveTimeLog()
    {
        $this->resetValidation();

        // 1. Parse input WIB (Asia/Jakarta) ke UTC agar bisa divalidasi dengan `before_or_equal:now` (karena now() server = UTC).
        $startUtc = $this->startTime ? Carbon::parse($this->startTime, 'Asia/Jakarta')->setTimezone('UTC')->toDateTimeString() : null;
        $endUtc = $this->endTime ? Carbon::parse($this->endTime, 'Asia/Jakarta')->setTimezone('UTC')->toDateTimeString() : null;

        // 2. Validasi manual menggunakan Validator untuk menimpa nilai yang di-validate dengan versi UTC
        $validator = \Illuminate\Support\Facades\Validator::make([
            'projectId' => $this->projectId,
            'taskId' => $this->taskId,
            'startTime' => $startUtc,
            'endTime' => $endUtc,
        ], [
            'projectId' => 'required|exists:team_projects,id',
            'taskId' => 'required|exists:tasks,id',
            'startTime' => 'required|date|before_or_equal:now',
            'endTime' => 'required|date|after:startTime',
        ], [
            'projectId.required' => 'Proyek wajib dipilih.',
            'taskId.required' => 'Tugas wajib dipilih.',
            'startTime.required' => 'Waktu mulai wajib diisi.',
            'startTime.before_or_equal' => 'Waktu mulai tidak boleh di masa depan.',
            'endTime.required' => 'Waktu selesai wajib diisi.',
            'endTime.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        if ($validator->fails()) {
            // Mapping error ke property aslinya
            foreach ($validator->errors()->toArray() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
            return;
        }

        $start = Carbon::parse($startUtc);
        $end = Carbon::parse($endUtc);

        $durationMinutes = $start->diffInMinutes($end);
        if ($durationMinutes > 720) {
            $this->addError('endTime', 'Durasi log tidak boleh lebih dari 12 jam.');
            return;
        }

        // Cek tabrakan waktu (overlap) di database yang juga menggunakan format UTC
        $overlap = TimeLog::where('user_id', Auth::id())
            ->where(function ($query) use ($startUtc, $endUtc) {
                $query->where(function ($q) use ($startUtc, $endUtc) {
                    $q
                        ->where('start_time', '<', $endUtc)
                        ->where('end_time', '>', $startUtc);
                });
            })
            ->first();

        if ($overlap) {
            // Tampilkan kembali ke user dalam WIB
            $overlapStart = $overlap->start_time->timezone('Asia/Jakarta')->format('d/m H:i');
            $overlapEnd = $overlap->end_time->timezone('Asia/Jakarta')->format('d/m H:i');
            $this->addError('startTime', "Waktu ini bertabrakan dengan log Anda yang lain ({$overlapStart} - {$overlapEnd} WIB).");
            return;
        }

        $durationHours = round($durationMinutes / 60, 2);

        TimeLog::create([
            'user_id' => Auth::id(),
            'project_id' => $this->projectId,
            'task_id' => $this->taskId ?: null,
            'start_time' => $startUtc,  // Simpan ke DB dalam UTC
            'end_time' => $endUtc,  // Simpan ke DB dalam UTC
            'duration_hours' => $durationHours,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        $this->resetForm();
        session()->flash('success', 'Time log berhasil ditambahkan dan menunggu persetujuan.');

        // Refresh component via event for parent
        $this->dispatch('timeLogAdded');
    }

    public function resetForm()
    {
        $this->projectId = '';
        $this->taskId = '';
        $this->startTime = '';
        $this->endTime = '';
        $this->notes = '';
        $this->showModal = false;
        $this->availableTasks = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.time-log-form');
    }
}
