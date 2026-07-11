{{-- 
  Komponen: Table
  Membungkus tabel data dengan styling Tailwind yang konsisten.
  Menerima array 'headers' untuk menyusun thead secara dinamis.
--}}
@props(['headers' => []])

<div class="overflow-x-auto relative rounded-b-xl">
    <table class="w-full text-left text-sm text-ink">
        <thead class="text-xs text-ink-secondary uppercase bg-subtle/50 tracking-wide border-b border-border">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 font-medium">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            {{ $slot }}
        </tbody>
    </table>
</div>
