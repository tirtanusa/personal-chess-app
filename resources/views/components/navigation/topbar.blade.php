@props([
    'brand' => 'Gambit',
    'avatarUrl' => null,
    'variant' => 'default',
    'pageTitle' => '',
    'backUrl' => null,
])

<header class="sticky top-0 z-30 w-full bg-canvas/90 backdrop-blur-md border-b border-border-subtle/60">
    <div class="w-full max-w-[480px] mx-auto px-4 h-14 flex items-center justify-between gap-3">
        @if ($variant === 'page')
            <!-- Subpage Left: Back Button + Optional Title -->
            <div class="flex items-center gap-3 min-w-0">
                <a
                    href="{{ $backUrl ?? route('home') }}"
                    class="inline-flex items-center justify-center w-8 h-8 -ml-1.5 rounded-lg text-neutral-300 hover:text-white hover:bg-surface/80 transition-colors shrink-0"
                    aria-label="Back"
                >
                    <x-lucide-arrow-left class="w-5 h-5" />
                </a>
                @if ($pageTitle !== '')
                    <h1 class="font-headline text-lg font-bold text-white tracking-tight truncate">
                        {{ $pageTitle }}
                    </h1>
                @endif
            </div>

            <!-- Subpage Right: Settings Action -->
            <button
                type="button"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-neutral-300 hover:text-white hover:bg-surface/80 transition-colors cursor-pointer shrink-0"
                aria-label="Settings"
            >
                <x-lucide-settings class="w-5 h-5" />
            </button>
        @else
            <!-- Main Tab Navbar (Home, Analysis, Profile) -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-surface border border-border flex items-center justify-center shadow-sm group-hover:border-primary/50 transition-colors">
                    <x-lucide-crown class="w-4 h-4 text-primary" />
                </div>
                <span class="font-headline text-lg font-bold text-white tracking-tight">
                    {{ $brand }}
                </span>
            </a>

            <!-- User Avatar -->
            <a
                href="{{ route('profile') }}"
                class="w-9 h-9 rounded-full p-0.5 bg-gradient-to-tr from-primary/60 via-secondary/40 to-tertiary/50 hover:scale-105 transition-transform"
                aria-label="User Profile"
            >
                <div class="w-full h-full rounded-full bg-surface-elevated overflow-hidden flex items-center justify-center">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <x-lucide-user class="w-4 h-4 text-neutral-200" />
                    @endif
                </div>
            </a>
        @endif
    </div>
</header>
