@props([
    'hasPlayer' => false,
])

<section class="w-full flex flex-col gap-3">
    <!-- Primary Export CTA -->
    @if ($hasPlayer)
        <a
            href="{{ route('profile.export-pgn') }}"
            class="w-full py-3 px-4 rounded-xl bg-primary/10 hover:bg-primary/20 border border-primary/35 text-primary font-headline font-bold text-sm transition-colors flex items-center justify-center gap-2"
        >
            <x-lucide-download class="w-4 h-4 shrink-0" />
            <span>Download PGN / Export Match History</span>
        </a>
    @else
        <a
            href="{{ route('onboarding.connect') }}"
            class="w-full py-3 px-4 rounded-xl bg-primary/10 hover:bg-primary/20 border border-primary/35 text-primary font-headline font-bold text-sm transition-colors flex items-center justify-center gap-2"
        >
            <x-lucide-link class="w-4 h-4 shrink-0" />
            <span>Connect Chess.com / Lichess Account</span>
        </a>
    @endif

    <!-- Archive / Switch Account Link Row -->
    <a
        href="{{ route('onboarding.connect') }}"
        class="w-full px-2 py-1.5 flex items-center justify-between gap-2 text-xs text-neutral-400 hover:text-neutral-200 transition-colors"
    >
        <span class="inline-flex items-center gap-2 min-w-0">
            <x-lucide-file-text class="w-4 h-4 text-neutral-500 shrink-0" />
            <span class="truncate">Import Games from Lichess / Chess.com</span>
        </span>
        <span class="inline-flex items-center gap-1 text-primary font-semibold shrink-0">
            <span>Connect Account</span>
            <x-lucide-chevron-right class="w-3.5 h-3.5" />
        </span>
    </a>

    @auth
        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button
                type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-danger/10 hover:bg-danger/20 border border-danger/30 text-danger-400 font-headline font-semibold text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer"
            >
                <x-lucide-log-out class="w-4 h-4 shrink-0" />
                <span>Sign Out</span>
            </button>
        </form>
    @endauth
</section>
