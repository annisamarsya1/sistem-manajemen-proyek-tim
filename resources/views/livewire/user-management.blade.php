<div x-data="{ open: $wire.entangle('showModal') }" class="space-y-6">

    {{-- ===================================================================== --}}
    {{-- Flash Notifications --}}
    {{-- ===================================================================== --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center justify-between gap-3 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center justify-between gap-3 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-6.25a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-semibold">{{ session('error') }}</p>
            </div>
            <button @click="show = false" class="text-rose-400 hover:text-rose-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif


    {{-- ===================================================================== --}}
    {{-- Header: Judul + Search + Tombol Buat --}}
    {{-- ===================================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">User Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola akun dan role seluruh anggota tim.</p>
        </div>
        <button @click="$wire.openCreateModal()"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-lg shadow-indigo-600/20 cursor-pointer self-start sm:self-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Pengguna
        </button>
    </div>

    {{-- ===================================================================== --}}
    {{-- Search Bar --}}
    {{-- ===================================================================== --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <input wire:model.live="search" type="search"
               placeholder="Cari nama atau email pengguna..."
               class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
    </div>


    {{-- ===================================================================== --}}
    {{-- Tabel Pengguna --}}
    {{-- ===================================================================== --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        @if ($users->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-3 opacity-40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <p class="text-sm">Tidak ada pengguna yang cocok dengan pencarian.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-left">
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $users->firstItem() + $loop->index }}</td>

                                {{-- Nama --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs font-bold flex-shrink-0">
                                            {{ $user->initials() }}
                                        </div>
                                        <span class="font-medium text-white">{{ $user->name }}</span>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-5 py-3.5 text-slate-400">{{ $user->email }}</td>

                                {{-- Badge Role --}}
                                <td class="px-5 py-3.5">
                                    @php
                                        $roleMap = [
                                            'admin'           => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            'project_manager' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                                            'employee'        => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        ];
                                        $roleLabel = [
                                            'admin'           => 'Admin',
                                            'project_manager' => 'Project Manager',
                                            'employee'        => 'Employee',
                                        ];
                                        $roleClass = $roleMap[$user->role] ?? 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $roleClass }}">
                                        {{ $roleLabel[$user->role] ?? $user->role }}
                                    </span>
                                </td>

                                {{-- Badge Status --}}
                                <td class="px-5 py-3.5">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal Dibuat --}}
                                <td class="px-5 py-3.5 text-slate-400 whitespace-nowrap font-mono text-xs">
                                    {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                                </td>


                                {{-- Kolom Aksi --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Tombol Edit --}}
                                        <button wire:click="editUser({{ $user->id }})"
                                                class="p-1.5 text-slate-400 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-lg transition-all cursor-pointer"
                                                title="Edit pengguna">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        {{-- Tombol Toggle Aktif (tidak tampil untuk diri sendiri) --}}
                                        @if ($user->id !== auth()->id())
                                            <button wire:click="toggleActive({{ $user->id }})"
                                                    wire:confirm="{{ $user->is_active ? 'Nonaktifkan akun ini?' : 'Aktifkan kembali akun ini?' }}"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleActive({{ $user->id }})"
                                                    class="p-1.5 rounded-lg transition-all cursor-pointer disabled:opacity-50 {{ $user->is_active ? 'text-slate-400 hover:text-amber-400 hover:bg-amber-500/10' : 'text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10' }}"
                                                    title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                @if ($user->is_active)
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @else
                                            <div class="w-7 h-7"></div>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-5 py-4 border-t border-slate-800">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>


    {{-- ===================================================================== --}}
    {{-- Modal Create / Edit --}}
    {{-- ===================================================================== --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="$wire.closeModal()"></div>

        {{-- Modal Panel --}}
        <div class="relative w-full max-w-lg bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop>

            {{-- Top accent --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800">
                <h2 class="text-lg font-bold text-white">
                    {{ $editingId ? 'Edit Pengguna' : 'Buat Pengguna Baru' }}
                </h2>
                <button @click="$wire.closeModal()" class="text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <form wire:submit="{{ $editingId ? 'update' : 'save' }}" class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Nama Lengkap <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="name" type="text" placeholder="Nama lengkap pengguna..."
                           class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('name') border-rose-500/50 @enderror">
                    @error('name')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Email <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="email" type="email" placeholder="email@contoh.com"
                           class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('email') border-rose-500/50 @enderror">
                    @error('email')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Role <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="role"
                            class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('role') border-rose-500/50 @enderror">
                        <option value="employee">Employee</option>
                        <option value="project_manager">Project Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        {{ $editingId ? 'Ganti Password (opsional)' : 'Password' }}
                        @if (! $editingId) <span class="text-rose-400">*</span> @endif
                    </label>
                    <input wire:model="password" type="password"
                           placeholder="{{ $editingId ? 'Kosongkan jika tidak ingin mengganti...' : 'Minimal 8 karakter...' }}"
                           class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('password') border-rose-500/50 @enderror">
                    @error('password')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Konfirmasi Password
                        @if (! $editingId) <span class="text-rose-400">*</span> @endif
                    </label>
                    <input wire:model="passwordConfirmation" type="password"
                           placeholder="Ulangi password..."
                           class="w-full px-3.5 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all @error('passwordConfirmation') border-rose-500/50 @enderror">
                    @error('passwordConfirmation')
                        <p class="text-rose-400 text-xs mt-1.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm.75-10a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 0 1.5 0V5Zm-.75 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="$wire.closeModal()"
                            class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="{{ $editingId ? 'update' : 'save' }}"
                            class="flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-600/20 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <svg wire:loading wire:target="{{ $editingId ? 'update' : 'save' }}" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="{{ $editingId ? 'update' : 'save' }}">
                            {{ $editingId ? 'Simpan Perubahan' : 'Buat Pengguna' }}
                        </span>
                        <span wire:loading wire:target="{{ $editingId ? 'update' : 'save' }}">Menyimpan...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
