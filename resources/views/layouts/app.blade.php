<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Manajemen Proyek' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef2fb',
                            100: '#d6e1f5',
                            200: '#adc3eb',
                            300: '#84a5e1',
                            400: '#5b87d7',
                            500: '#4274D9',
                            600: '#2f5cb8',
                            700: '#244593',
                            800: '#1a2f6e',
                            900: '#101c49',
                            950: '#080e25',
                        },
                        secondary: {
                            50:  '#f0f9fc',
                            100: '#d9eff5',
                            200: '#b3dfeb',
                            300: '#95CCDD',
                            400: '#6ab5cc',
                            500: '#4a9db8',
                            600: '#37809a',
                            700: '#27647c',
                            800: '#1a475e',
                            900: '#0d2a40',
                            950: '#061521',
                        },
                        tertiary: {
                            50:  '#f7fbfb',
                            100: '#e8f4f3',
                            200: '#D0E7E6',
                            300: '#b0d3d2',
                            400: '#85b8b7',
                            500: '#5e9e9d',
                            600: '#447e7d',
                            700: '#2e5e5d',
                            800: '#1c3e3d',
                            900: '#0d1f1e',
                            950: '#060f0f',
                        },
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body class="h-full text-slate-100 font-sans antialiased" x-data="{ mobileSidebarOpen: false }">

    <!-- Toast Notifications (Alpine.js auto-dismiss) -->
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
            @if(session('success')) add('success', @js(session('success'))) @endif
            @if(session('error'))   add('error',   @js(session('error')))   @endif
            @if(session('info'))    add('info',    @js(session('info')))    @endif
        "
        class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]"
    >
        <template x-for="n in notifications" :key="n.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2 translate-x-2"
                x-transition:enter-end="opacity-100 translate-y-0 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-x-4"
                :class="{
                    'bg-emerald-500/10 border-emerald-500/20 text-emerald-400': n.type === 'success',
                    'bg-rose-500/10 border-rose-500/20 text-rose-400':         n.type === 'error',
                    'bg-secondary-300/10 border-secondary-300/30 text-secondary-400':             n.type === 'info'
                }"
                class="flex items-center justify-between gap-3 px-4 py-3 border rounded-xl shadow-lg backdrop-blur-md"
            >
                {{-- Icon --}}
                <div class="flex items-center gap-2.5 min-w-0">
                    <template x-if="n.type === 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template x-if="n.type === 'error'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5a.75.75 0 0 1 .75-.75Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template x-if="n.type === 'info'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <p class="text-sm font-semibold leading-snug" x-text="n.msg"></p>
                </div>
                <button @click="remove(n.id)" class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity ml-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Layout container -->
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar (Desktop) -->
        <aside class="hidden lg:flex lg:flex-shrink-0 lg:w-64 lg:flex-col border-r border-slate-800 bg-slate-900">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-800">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18a2.25 2.25 0 0 1 2.25 2.25v4.5A2.25 2.25 0 0 1 21.75 22.5H2.25A2.25 2.25 0 0 1 0 20.25v-4.5A2.25 2.25 0 0 1 2.25 13.5Zm0-3h18A2.25 2.25 0 0 0 22.5 8.25v-4.5A2.25 2.25 0 0 0 20.25 1.5H3.75A2.25 2.25 0 0 0 1.5 3.75v4.5a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <span class="font-bold text-white tracking-tight">Proyek Tim</span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard (Semua Role) -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>

                <!-- Project Studio (Admin atau PM) -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'project_manager')
                    <a href="{{ route('projects') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('projects') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-.778.099-1.533.284-2.253" />
                        </svg>
                        Project Studio
                    </a>
                @endif

                <!-- Kanban Board (Semua Role) -->
                <a href="{{ route('tasks') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tasks') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.5-6h15M3 8.25h18m-18 7.5h18M3 5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25v13.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V5.25Z" />
                    </svg>
                    Kanban Board
                </a>

                <!-- Time Logs (Semua Role - Label Berbeda) -->
                <a href="{{ route('timelogs') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('timelogs') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ (auth()->user()->role === 'admin' || auth()->user()->role === 'project_manager') ? 'Time Logs' : 'My Logs' }}
                </a>

                <!-- Timesheet (Semua Role) -->
                <a href="{{ route('timesheet') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('timesheet') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    Timesheet
                </a>

                <!-- User Management (Admin saja) -->
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('users') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('users') ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/10' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.97 5.97 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                        User Management
                    </a>
                @endif
            </nav>

            <!-- Bottom sidebar profile / menu -->
            <div class="p-4 border-t border-slate-800 bg-slate-900/50 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-sm font-bold text-brand-400 flex-shrink-0">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize truncate">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Sidebar (Mobile Overlay) -->
        <div x-show="mobileSidebarOpen" 
             class="fixed inset-0 z-40 lg:hidden" 
             style="display: none;" 
             x-ref="dialog" 
             role="dialog" 
             aria-modal="true">
            <!-- Backdrop overlay -->
            <div x-show="mobileSidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" 
                 @click="mobileSidebarOpen = false"></div>

            <!-- Drawer Container -->
            <div class="fixed inset-y-0 left-0 flex w-full max-w-xs bg-slate-900 border-r border-slate-800">
                <div class="flex flex-col flex-1 pb-4">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between px-6 h-16 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18a2.25 2.25 0 0 1 2.25 2.25v4.5A2.25 2.25 0 0 1 21.75 22.5H2.25A2.25 2.25 0 0 1 0 20.25v-4.5A2.25 2.25 0 0 1 2.25 13.5Zm0-3h18A2.25 2.25 0 0 0 22.5 8.25v-4.5A2.25 2.25 0 0 0 20.25 1.5H3.75A2.25 2.25 0 0 0 1.5 3.75v4.5a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <span class="font-bold text-white tracking-tight">Proyek Tim</span>
                        </div>
                        <button type="button" @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Dashboard
                        </a>

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'project_manager')
                            <a href="{{ route('projects') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('projects') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                Project Studio
                            </a>
                        @endif

                        <a href="{{ route('tasks') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tasks') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Kanban Board
                        </a>

                        <a href="{{ route('timelogs') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('timelogs') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            {{ (auth()->user()->role === 'admin' || auth()->user()->role === 'project_manager') ? 'Time Logs' : 'My Logs' }}
                        </a>

                        <a href="{{ route('timesheet') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('timesheet') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Timesheet
                        </a>

                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('users') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('users') ? 'bg-brand-500 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                                User Management
                            </a>
                        @endif
                    </nav>

                    <div class="px-6 py-4 border-t border-slate-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-sm font-bold text-brand-400">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 capitalize truncate">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Topbar / Header -->
            <header class="flex items-center justify-between px-6 lg:px-8 h-16 bg-slate-900 border-b border-slate-800 flex-shrink-0">
                <!-- Hamburger Button for Mobile -->
                <button type="button" @click="mobileSidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="hidden lg:block">
                    <h1 class="text-lg font-semibold text-white">{{ $title ?? 'Workspace' }}</h1>
                </div>

                <!-- Right Topbar Controls -->
                <div class="flex items-center gap-4 ml-auto">
                    <!-- User Info & Logout Form -->
                    <div class="flex items-center gap-3 border-r border-slate-800 pr-4">
                        <span class="text-sm font-medium text-slate-300">{{ auth()->user()->name }}</span>
                        <div class="w-8 h-8 rounded-lg bg-brand-500/10 border border-brand-500/20 text-brand-400 flex items-center justify-center text-xs font-bold">
                            {{ auth()->user()->initials() }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="flex items-center gap-2 px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700/80 border border-slate-700/60 hover:border-slate-600 rounded-xl text-xs font-semibold text-slate-300 hover:text-white transition-all duration-150 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Workspace (Slot) -->
            <main class="flex-1 overflow-y-auto bg-slate-950 p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    @livewireScripts
</body>
</html>
