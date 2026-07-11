{{-- 
  Komponen: Card
  Membungkus konten dalam kotak (box) dengan gaya background, border, dan shadow.
  Menerima parameter 'padding' kustom.
--}}
@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-surface rounded-xl shadow-card border border-border ' . $padding]) }}>
    {{ $slot }}
</div>
