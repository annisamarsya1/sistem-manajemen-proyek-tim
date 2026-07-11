{{-- 
  Layout: Guest
  Digunakan oleh halaman yang dapat diakses tanpa login (misal: Halaman Login).
  Berisi struktur HTML dasar yang bersih tanpa sidebar/navbar.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login — TaskSync">
    <title>TaskSync - {{ $title ?? 'Login' }}</title>
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
                        danger: { DEFAULT: '#DC2626', hover: '#B91C1C', soft: '#FEE2E2' },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 2px 8px -2px rgba(26, 29, 41, 0.05), 0 4px 16px -4px rgba(26, 29, 41, 0.02)',
                        'panel': '0 12px 32px -4px rgba(26, 29, 41, 0.08), 0 4px 12px -2px rgba(26, 29, 41, 0.04)',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js sudah di-inject otomatis oleh Livewire 4 --}}


    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Light Theme */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F5F7FA;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-canvas text-ink antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
