@props([
    'title' => config('app.name', 'Gambit Arena'),
    'backUrl' => url('/'),
    'centered' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full bg-canvas text-neutral-100 antialiased font-body flex flex-col relative overflow-x-hidden selection:bg-primary/30 selection:text-primary-50">
    <!-- Subtle Ambient Emerald Glow -->
    <div class="pointer-events-none fixed inset-x-0 top-0 flex justify-center overflow-hidden">
        <div class="h-72 w-72 rounded-full bg-primary/15 blur-[100px] -translate-y-12"></div>
    </div>

    <!-- Top Navigation / Back Action -->
    <header class="relative z-10 w-full max-w-[460px] mx-auto px-5 pt-6">
        <a
            href="{{ $backUrl }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-neutral-300 hover:text-white hover:bg-surface/80 transition-colors"
            aria-label="Back"
        >
            <x-lucide-arrow-left class="w-5 h-5" />
        </a>
    </header>

    <!-- Main Centered Content -->
    <main @class([
        'relative z-10 flex-1 flex flex-col items-center w-full max-w-[460px] mx-auto px-5 pb-10',
        'justify-center pt-2' => $centered,
        'justify-start pt-6' => ! $centered,
    ])>
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
