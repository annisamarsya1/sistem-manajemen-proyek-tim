{{-- 
  Komponen: Button
  Membungkus elemen <button> HTML dengan styling Tailwind kustom.
  Mendukung berbagai varian (primary, success, ghost, danger) dan state loading.
--}}
@props(['variant' => 'primary', 'icon' => null, 'loadingTarget' => null])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'primary' => 'bg-primary hover:bg-primary-hover text-white shadow-sm',
    'success' => 'bg-success hover:bg-success/90 text-white shadow-sm',
    'ghost' => 'bg-surface border border-border text-primary hover:bg-subtle shadow-sm',
    'danger' => 'bg-danger hover:bg-danger/90 text-white shadow-sm',
    default => 'bg-primary hover:bg-primary-hover text-white shadow-sm',
};
@endphp

<button {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses]) }}>
    @if($loadingTarget)
        <span wire:loading.remove wire:target="{{ $loadingTarget }}" class="flex items-center gap-2">
            @if($icon) {!! $icon !!} @endif
            {{ $slot }}
        </span>
        <span wire:loading wire:target="{{ $loadingTarget }}" class="flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="sr-only">Loading...</span>
        </span>
    @else
        @if($icon) {!! $icon !!} @endif
        {{ $slot }}
    @endif
</button>
