@props([
    'active' => 'home',
])

<nav class="fixed bottom-0 inset-x-0 z-30 bg-surface/95 backdrop-blur-md border-t border-border-subtle">
    <div class="w-full max-w-[480px] mx-auto px-6 h-16 grid grid-cols-3 items-center">
        <!-- Analysis -->
        <a
            href="{{ route('analysis') }}"
            @class([
                'flex flex-col items-center justify-center gap-1 py-1.5 transition-colors relative',
                'text-primary' => $active === 'analysis',
                'text-neutral-400 hover:text-neutral-200' => $active !== 'analysis',
            ])
        >
            @if ($active === 'analysis')
                <span class="absolute top-1 w-8 h-8 rounded-full bg-primary/15 blur-md pointer-events-none"></span>
            @endif
            <x-lucide-text-search class="w-5 h-5 relative z-10" />
            <span @class(['text-[11px] font-body relative z-10', 'font-semibold' => $active === 'analysis', 'font-medium' => $active !== 'analysis'])>Analysis</span>
        </a>

        <!-- Home -->
        <a
            href="{{ route('home') }}"
            @class([
                'flex flex-col items-center justify-center gap-1 py-1.5 transition-colors relative',
                'text-primary' => $active === 'home',
                'text-neutral-400 hover:text-neutral-200' => $active !== 'home',
            ])
        >
            @if ($active === 'home')
                <span class="absolute top-1 w-8 h-8 rounded-full bg-primary/15 blur-md pointer-events-none"></span>
            @endif
            <x-lucide-layout-grid class="w-5 h-5 relative z-10" />
            <span @class(['text-[11px] font-body relative z-10', 'font-semibold' => $active === 'home', 'font-medium' => $active !== 'home'])>Home</span>
        </a>

        <!-- Profile -->
        <a
            href="{{ route('profile') }}"
            @class([
                'flex flex-col items-center justify-center gap-1 py-1.5 transition-colors relative',
                'text-primary' => $active === 'profile',
                'text-neutral-400 hover:text-neutral-200' => $active !== 'profile',
            ])
        >
            @if ($active === 'profile')
                <span class="absolute top-1 w-8 h-8 rounded-full bg-primary/15 blur-md pointer-events-none"></span>
            @endif
            <x-lucide-user class="w-5 h-5 relative z-10" />
            <span @class(['text-[11px] font-body relative z-10', 'font-semibold' => $active === 'profile', 'font-medium' => $active !== 'profile'])>Profile</span>
        </a>
    </div>
</nav>
