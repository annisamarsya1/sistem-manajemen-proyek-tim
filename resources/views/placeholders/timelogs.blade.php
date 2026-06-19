<x-layouts::app :title="auth()->user()->role === 'employee' ? __('My Logs') : __('Time Logs')">
    <div class="p-6 bg-slate-900 border border-slate-800 rounded-2xl shadow-xl">
        <h2 class="text-2xl font-bold text-white mb-4">
            {{ auth()->user()->role === 'employee' ? 'My Logs' : 'Time Logs' }}
        </h2>
        <p class="text-slate-400">Halaman ini sedang dalam pengembangan (Placeholder Fase 6).</p>
    </div>
</x-layouts::app>
