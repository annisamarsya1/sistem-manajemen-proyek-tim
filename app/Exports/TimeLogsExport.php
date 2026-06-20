<?php

namespace App\Exports;

use App\Models\TimeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimeLogsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    private int $rowNumber = 0;

    /**
     * @param  array{start?: string, end?: string, project_id?: string|int, status?: string}  $filters
     */
    public function __construct(
        private readonly array $filters,
        private readonly ?int $userId = null
    ) {}

    public function query(): Builder
    {
        return TimeLog::with(['user', 'project', 'task', 'reviewer'])
            ->when($this->userId, fn (Builder $q) => $q->where('user_id', $this->userId))
            ->when($this->filters['start'] ?? null, fn (Builder $q) => $q->whereDate('start_time', '>=', $this->filters['start']))
            ->when($this->filters['end'] ?? null, fn (Builder $q) => $q->whereDate('start_time', '<=', $this->filters['end']))
            ->when($this->filters['project_id'] ?? null, fn (Builder $q) => $q->where('project_id', $this->filters['project_id']))
            ->when($this->filters['status'] ?? null, fn (Builder $q) => $q->where('status', $this->filters['status']))
            ->orderBy('start_time', 'desc');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Proyek',
            'Tugas',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Durasi (jam)',
            'Catatan',
            'Status',
            'Di-review Oleh',
            'Waktu Review',
        ];
    }

    /**
     * @param  TimeLog  $log
     * @return array<int, mixed>
     */
    public function map($log): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $log->user->name ?? '-',
            $log->project->title ?? '-',
            $log->task->title ?? '-',
            Carbon::parse($log->start_time)->format('d/m/Y'),
            Carbon::parse($log->start_time)->format('H:i'),
            $log->end_time ? Carbon::parse($log->end_time)->format('H:i') : '-',
            number_format((float) $log->duration_hours, 2),
            $log->notes ?? '-',
            ucfirst($log->status),
            $log->reviewer->name ?? '-',
            $log->reviewed_at ? Carbon::parse($log->reviewed_at)->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        // Bold the heading row
        $sheet->getStyle('1')->getFont()->setBold(true);
    }
}
