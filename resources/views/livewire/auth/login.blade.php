{{-- 
  View: Login (Livewire Component)
  Menampilkan form otentikasi pengguna. Menggunakan komponen card kustom 
  dan binding property dari Livewire (wire:model, wire:submit).
--}}
<div class="w-full max-w-md">
    {{-- Logo & Branding --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary mb-4 shadow-sm">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-ink">TaskSync</h1>
        <p class="text-ink-secondary text-sm mt-1">Masuk ke akun Anda</p>
    </div>

    {{-- Login Card --}}
    <x-card padding="p-8" class="shadow-card">
        <form wire:submit="login" class="space-y-5">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-ink-secondary mb-1.5">Email</label>
                <input
                    wire:model="email"
                    type="email"
                    id="email"
                    placeholder="nama@email.com"
                    autocomplete="email"
                    class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl text-ink placeholder-ink-muted text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                >
                @error('email')
                    <p class="mt-1.5 text-sm text-danger flex items-start gap-1.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-ink-secondary mb-1.5">Password</label>
                <div x-data="{ showPassword: false }" class="relative">
                    <input
                        wire:model="password"
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 pr-11 bg-surface border border-border rounded-xl text-ink placeholder-ink-muted text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                    >
                    <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-secondary hover:text-ink transition-colors">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-sm text-danger flex items-start gap-1.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Submit --}}
            <x-button type="submit" loadingTarget="login" class="w-full py-2.5">
                Masuk
            </x-button>
        </form>
    </x-card>

    {{-- Footer --}}
    <p class="text-center text-xs text-ink-muted mt-6">
        &copy; {{ date('Y') }} TaskSync &mdash; Sistem Manajemen Proyek Tim
    </p>
</div>
