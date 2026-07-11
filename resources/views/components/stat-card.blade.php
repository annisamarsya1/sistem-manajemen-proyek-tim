{{-- 
  Komponen: Stat Card
  Menampilkan informasi ringkasan statistik tunggal (misal: Total Jam)
  Menerima parameter label, value, tone (warna: success, danger, primary, dll), dan suffix.
--}}
@props(['label', 'value', 'tone' => 'default', 'suffix' => null])

@php
$toneClasses = match($tone) {
    'success' => 'text-success',
    'danger' => 'text-danger',
    'primary' => 'text-primary',
    default => 'text-ink',
};

$bgCard = match($tone) {
    'success' => 'bg-success-soft',
    'danger' => 'bg-danger-soft',
    default => 'bg-surface',
};
@endphp

<div class="{{ $bgCard }} rounded-lg shadow-card border border-border p-5">
    <p class="text-sm font-medium text-ink-secondary mb-2">{{ $label }}</p>
    <div class="text-3xl font-bold {{ $toneClasses }}">
        {{ $value }}
        @if($suffix)
            <span class="text-lg font-normal text-ink-secondary ml-1">{{ $suffix }}</span>
        @endif
    </div>
</div>
