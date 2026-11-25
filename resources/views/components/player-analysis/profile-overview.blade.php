@props([
    'player',
])

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4">
    <!-- Top Identity Row -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Initials Avatar + GM Badge -->
            <div class="relative shrink-0">
                <div class="w-13 h-13 rounded-2xl bg-primary/15 border border-primary/25 text-primary font-headline text-lg font-bold flex items-center justify-center">
                    {{ $player['initials'] }}
                </div>
                <span class="absolute -bottom-1 -right-1 px-1.5 py-0.5 rounded bg-secondary text-neutral-950 font-label text-[9px] font-extrabold leading-none shadow">
                    {{ $player['title'] }}
                </span>
            </div>

            <!-- Name, Flag, FIDE Rating & Last Seen -->
            <div class="flex flex-col gap-1.5 min-w-0">
                <div class="flex items-center gap-1.5">
                    <h2 class="font-headline text-base sm:text-lg font-bold text-white leading-tight truncate">
                        {{ $player['name'] }}
                    </h2>
                    <span class="text-sm leading-none shrink-0" aria-hidden="true">{{ $player['flag'] }}</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-primary/15 border border-primary/30 font-label text-[10px] font-bold text-primary">
                        {{ $player['fide'] }}
                    </span>
                    <span class="font-body text-[11px] text-neutral-400">
                        • {{ $player['lastSeen'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Saved Toggle Pill -->
        <button
            type="button"
            data-save-toggle
            data-saved="true"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-secondary/15 border border-secondary/35 text-secondary font-headline text-xs font-semibold transition-colors shrink-0 cursor-pointer"
        >
            <x-lucide-bookmark class="w-3.5 h-3.5 fill-secondary" data-save-icon />
            <span data-save-label>Saved</span>
        </button>
    </div>

    <!-- Overall Stats (3 Columns) -->
    <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3 grid grid-cols-3 gap-2 text-center divide-x divide-border-subtle">
        <div class="flex flex-col items-center justify-between gap-1 px-1">
            <span class="font-label text-[9px] font-semibold tracking-wider uppercase text-neutral-400">
                Total Winrate
            </span>
            <span class="font-headline text-lg font-bold text-primary leading-tight">
                {{ $player['totalWinrate'] }}
            </span>
            <span class="font-body text-[10px] text-neutral-400">
                {{ $player['totalGames'] }}
            </span>
        </div>

        <div class="flex flex-col items-center justify-between gap-1 px-1">
            <span class="font-label text-[9px] font-semibold tracking-wider uppercase text-neutral-400">
                Game Results
            </span>
            <span class="font-label text-[11px] font-bold text-white leading-tight">
                {{ $player['winsDraws'] }}
            </span>
            <span class="font-body text-[10px] text-danger-400">
                {{ $player['losses'] }}
            </span>
        </div>

        <div class="flex flex-col items-center justify-between gap-1 px-1">
            <span class="font-label text-[9px] font-semibold tracking-wider uppercase text-neutral-400">
                Avg Accuracy
            </span>
            <span class="font-headline text-lg font-bold text-secondary leading-tight">
                {{ $player['avgAccuracy'] }}
            </span>
            <span class="font-body text-[10px] text-primary font-medium">
                {{ $player['accuracyTier'] }}
            </span>
        </div>
    </div>

    <!-- Mode Ratings (Rapid / Blitz / Bullet) -->
    <div class="grid grid-cols-3 gap-2">
        <div class="bg-canvas/75 border border-border-subtle rounded-xl p-2.5 flex flex-col items-center gap-1 text-center">
            <span class="inline-flex items-center gap-1 font-label text-[10px] font-semibold uppercase text-neutral-400">
                <x-lucide-clock class="w-3 h-3" />
                <span>Rapid</span>
            </span>
            <span class="font-headline text-lg font-bold text-white leading-tight">{{ $player['rapid']['value'] }}</span>
            <span class="font-label text-[10px] text-neutral-400">{{ $player['rapid']['peak'] }}</span>
        </div>

        <div class="bg-canvas/75 border border-border-subtle rounded-xl p-2.5 flex flex-col items-center gap-1 text-center">
            <span class="inline-flex items-center gap-1 font-label text-[10px] font-semibold uppercase text-secondary">
                <x-lucide-zap class="w-3 h-3" />
                <span>Blitz</span>
            </span>
            <span class="font-headline text-lg font-bold text-secondary leading-tight">{{ $player['blitz']['value'] }}</span>
            <span class="font-label text-[10px] text-neutral-400">{{ $player['blitz']['peak'] }}</span>
        </div>

        <div class="bg-canvas/75 border border-border-subtle rounded-xl p-2.5 flex flex-col items-center gap-1 text-center">
            <span class="inline-flex items-center gap-1 font-label text-[10px] font-semibold uppercase text-primary">
                <x-lucide-rocket class="w-3 h-3" />
                <span>Bullet</span>
            </span>
            <span class="font-headline text-lg font-bold text-primary leading-tight">{{ $player['bullet']['value'] }}</span>
            <span class="font-label text-[10px] text-primary/80">{{ $player['bullet']['peak'] }}</span>
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-save-toggle]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const isSaved = btn.getAttribute('data-saved') === 'true';
                        const next = !isSaved;
                        btn.setAttribute('data-saved', next ? 'true' : 'false');

                        const label = btn.querySelector('[data-save-label]');
                        const icon = btn.querySelector('[data-save-icon]');

                        if (next) {
                            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-secondary/15 border border-secondary/35 text-secondary font-headline text-xs font-semibold transition-colors shrink-0 cursor-pointer';
                            icon?.classList.add('fill-secondary');
                            if (label) label.textContent = 'Saved';
                        } else {
                            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-canvas border border-border text-neutral-300 hover:text-white font-headline text-xs font-semibold transition-colors shrink-0 cursor-pointer';
                            icon?.classList.remove('fill-secondary');
                            if (label) label.textContent = 'Save';
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
