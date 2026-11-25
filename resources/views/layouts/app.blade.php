<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Gambit Arena') }}</title>

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full bg-canvas text-neutral-100 antialiased font-body flex flex-col selection:bg-primary/30 selection:text-primary-50">
    <x-navigation.topbar
        :variant="$headerVariant ?? 'default'"
        :page-title="$headerTitle ?? ''"
        :back-url="$backUrl ?? null"
    />

    <main @class([
        'flex-1 w-full max-w-[480px] mx-auto px-4 pt-4',
        'pb-24' => $showBottomNav ?? true,
        'pb-10' => ! ($showBottomNav ?? true),
    ])>
        @yield('content')
    </main>

    @if ($showBottomNav ?? true)
        <x-navigation.bottom-nav :active="$activeNav ?? 'home'" />
    @endif

    <x-analysis.game-report-modal />

    @stack('scripts')
</body>
</html>
