<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Aktivitas Pekerjaan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 8px; }
        .mt-4 { margin-top: 16px; }
    </style>
</head>
<body>
    <h2 class="text-center mb-2">Laporan Aktivitas Pekerjaan (Time Logs)</h2>
    <p class="text-center">Diekspor pada: {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Proyek</th>
                <th>Tugas</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Durasi (Jam)</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->user->name ?? '-' }}</td>
                <td>{{ $log->project->title ?? '-' }}</td>
                <td>{{ $log->task->title ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($log->start_time)->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($log->start_time)->timezone('Asia/Jakarta')->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($log->end_time)->timezone('Asia/Jakarta')->format('H:i') }}
                </td>
                <td>{{ number_format($log->duration_hours, 2) }}</td>
                <td>{{ ucfirst($log->status) }}</td>
                <td>{{ $log->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
