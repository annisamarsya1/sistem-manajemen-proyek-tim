{{-- 
  Layout: App (Utama)
  Digunakan oleh semua halaman setelah login (Dashboard, Kanban, dsb).
  Berisi struktur HTML utama, integrasi Tailwind CSS, AlpineJS, sidebar navigasi, 
  sistem notifikasi (toast), dan slot untuk konten (Livewire).
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TaskSync — Kelola proyek, tugas, dan pencatatan jam kerja tim Anda.">
    <title>TaskSync - {{ $title ?? 'Dashboard' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com" data-navigate-track></script>
    <script data-navigate-track>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        canvas: '#EBEDF0',
                        surface: '#FFFFFF',
                        subtle: '#F5F7FA',
                        border: '#E7EAEF',
                        primary: { DEFAULT: '#2F6BFF', hover: '#2559D8', soft: '#EAF0FF' },
                        ink: { DEFAULT: '#1A1D29', secondary: '#6B7280', muted: '#9AA3B2' },
                        success: { DEFAULT: '#16A34A', soft: '#E7F6EC' },
                        danger:  { DEFAULT: '#DC2626', soft: '#FDECEC' },
                    },
                    borderRadius: { sm: '8px', md: '12px', lg: '16px', xl: '24px' },
                    boxShadow: {
                        card: '0 1px 3px rgba(16,24,40,0.06), 0 1px 2px rgba(16,24,40,0.04)',
                        panel: '0 8px 24px rgba(16,24,40,0.08)',
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                }
            }
        }
    </script>
    <!-- Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js" data-navigate-track></script>

    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js" data-navigate-track></script>

    {{-- Alpine.js sudah di-inject otomatis oleh Livewire 4 --}}


    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #EBEDF0; }
        ::-webkit-scrollbar-thumb { background: #9AA3B2; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #6B7280; }

        /* Sidebar transition */
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: #F5F7FA;
            color: #1A1D29;
        }
        .sidebar-link.active {
            background: #EAF0FF;
            color: #2F6BFF;
        }
        .sidebar-link.active svg {
            color: #2F6BFF;
        }
    </style>
</head>
<body class="bg-canvas text-ink antialiased font-sans">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">

        {{-- ========== SIDEBAR ========== --}}
        <aside
            :class="sidebarOpen ? 'w-52' : 'w-20'"
            class="flex flex-col bg-surface border-r border-border transition-all duration-300 ease-in-out"
        >
            {{-- Logo / Brand (Toggle Sidebar) --}}
            <button @click="sidebarOpen = !sidebarOpen" class="flex items-center gap-2.5 px-5 h-16 border-b border-border hover:bg-subtle transition-colors focus:outline-none w-full text-left group">
                <div class="flex-shrink-0 p-2 rounded-xl bg-blue-500/10 border border-blue-500/25 group-hover:bg-blue-500/20 group-hover:border-blue-500/40 transition-all">
                    <svg class="w-5 h-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                        <polyline points="2 12 12 17 22 12"/>
                        <polyline points="2 17 12 22 22 17"/>
                    </svg>
                </div>
                <span x-show="sidebarOpen" x-cloak class="font-bold text-xl text-ink tracking-tight truncate">
                    Task<span class="text-blue-500 font-normal">Sync</span>
                </span>
            </button>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : 'text-ink-secondary' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                </a>

                {{-- Project Studio (admin & project_manager only) --}}
                @if(in_array(auth()->user()->role, ['admin', 'project_manager']))
                <a href="{{ route('projects') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium {{ request()->routeIs('projects') ? 'active' : 'text-ink-secondary' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Project Studio</span>
                </a>
                @endif

                {{-- Kanban Board --}}
                <a href="{{ route('tasks') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium {{ request()->routeIs('tasks') ? 'active' : 'text-ink-secondary' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Kanban Board</span>
                </a>


                {{-- Timesheet --}}
                <a href="{{ route('timesheet') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium {{ request()->routeIs('timesheet') ? 'active' : 'text-ink-secondary' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak>Timesheet</span>
                </a>

                {{-- User Management (admin only) --}}
                @if(auth()->user()->role === 'admin')
                <div class="pt-4 mt-4 border-t border-border">
                    <p x-show="sidebarOpen" x-cloak class="px-3 mb-2 text-xs font-semibold uppercase tracking-wider text-ink-muted">Administrasi</p>
                    <a href="{{ route('users') }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium {{ request()->routeIs('users') ? 'active' : 'text-ink-secondary' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="sidebarOpen" x-cloak>User Management</span>
                    </a>
                </div>
                @endif
            </nav>

            {{-- User Info & Logout (Bottom Sidebar) --}}
            <div class="border-t border-border p-3 bg-subtle/50">
                <div class="flex items-center gap-3 mb-3 px-2">
                    <div class="w-8 h-8 flex-shrink-0 rounded-full bg-primary flex items-center justify-center text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div x-show="sidebarOpen" x-cloak class="truncate">
                        <p class="text-sm font-medium text-ink leading-none truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-ink-secondary mt-0.5 capitalize truncate">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-cloak>
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-3 py-2 text-sm font-medium text-ink-secondary hover:text-danger rounded-md hover:bg-danger-soft transition-all duration-200"
                            title="Logout">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
                
                {{-- Logout Icon Only for Collapsed Sidebar --}}
                <form method="POST" action="{{ route('logout') }}" x-show="!sidebarOpen" x-cloak>
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center w-full py-2 text-ink-secondary hover:text-danger rounded-md hover:bg-danger-soft transition-all duration-200"
                            title="Logout">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ========== MAIN CONTENT ========== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Topbar --}}
            <header class="flex items-center justify-between h-16 px-6 bg-surface border-b border-border">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-semibold text-ink">
                        {{ $title ?? 'Dashboard' }}
                    </h1>
                </div>
            </header>



            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- Toast Notification -->
    <div
        x-data="{
            notifications: [],
            add(type, msg) {
                const id = Date.now();
                this.notifications.push({ id, type, msg });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }"
        x-init="
            @if(session('success')) add('success', '{{ session('success') }}') @endif
            @if(session('error')) add('error', '{{ session('error') }}') @endif
            @if(session('info')) add('info', '{{ session('info') }}') @endif
        "
        @notify.window="add($event.detail[0].type, $event.detail[0].msg)"
        class="fixed bottom-4 right-4 z-50 space-y-2"
    >
        <template x-for="n in notifications" :key="n.id">
            <div x-show="true" x-transition
                 :class="{ 'bg-success-soft text-success border-success/20': n.type === 'success', 'bg-danger-soft text-danger border-danger/20': n.type === 'error', 'bg-primary-soft text-primary border-primary/20': n.type === 'info' }"
                 class="px-4 py-3 rounded-lg shadow-panel border flex items-center gap-3 min-w-[250px]">
                <span x-text="n.msg" class="text-sm font-medium"></span>
                <button @click="remove(n.id)" class="ml-auto opacity-70 hover:opacity-100 transition-opacity">✕</button>
            </div>
        </template>
    </div>
</body>
</html>
