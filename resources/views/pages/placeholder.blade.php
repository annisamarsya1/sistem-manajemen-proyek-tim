<x-layouts.app :title="$title ?? 'Placeholder'">
    <div class="flex flex-col items-center justify-center min-h-[60vh]">
        <x-card class="text-center max-w-md w-full">
            <div class="w-14 h-14 rounded-2xl bg-subtle flex items-center justify-center mx-auto mb-4 border border-border">
                <svg class="w-7 h-7 text-ink-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-ink mb-2">{{ $title ?? 'Segera Hadir' }}</h2>
            <p class="text-ink-secondary text-sm">Halaman ini sedang dalam pengembangan dan akan tersedia di fase berikutnya.</p>
        </x-card>
    </div>
</x-layouts.app>
