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
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center font-sans antialiased p-4 relative overflow-hidden">
    <!-- Background subtle gradient highlights -->
    <div class="absolute top-[-20%] left-[-20%] w-[60%] h-[60%] rounded-full bg-brand-500/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-20%] w-[60%] h-[60%] rounded-full bg-secondary-300/10 blur-[120px] pointer-events-none"></div>

    {{ $slot }}

    @livewireScripts
</body>
</html>
