<section class="w-full flex flex-col gap-3">
    <h2 class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
        Alternative Analysis Options
    </h2>

    <div class="grid grid-cols-2 gap-3">
        <!-- Scan Physical Board -->
        <button
            type="button"
            class="bg-surface border border-border-subtle hover:border-border active:scale-[0.99] rounded-2xl p-4 flex flex-col items-start gap-3 text-left transition-all cursor-pointer group"
        >
            <span class="w-9 h-9 rounded-xl bg-primary/15 border border-primary/25 text-primary flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                <x-lucide-camera class="w-4 h-4" />
            </span>

            <div class="flex flex-col gap-0.5">
                <span class="font-headline text-sm font-bold text-white leading-snug">
                    Scan Physical Board
                </span>
                <span class="font-body text-[11px] text-neutral-400">
                    Detect real board
                </span>
            </div>
        </button>

        <!-- Paste Notation -->
        <button
            type="button"
            class="bg-surface border border-border-subtle hover:border-border active:scale-[0.99] rounded-2xl p-4 flex flex-col items-start gap-3 text-left transition-all cursor-pointer group"
        >
            <span class="w-9 h-9 rounded-xl bg-secondary/15 border border-secondary/25 text-secondary flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                <x-lucide-clipboard-list class="w-4 h-4" />
            </span>

            <div class="flex flex-col gap-0.5">
                <span class="font-headline text-sm font-bold text-white leading-snug">
                    Paste Notation
                </span>
                <span class="font-body text-[11px] text-neutral-400">
                    PGN / FEN input
                </span>
            </div>
        </button>
    </div>
</section>
