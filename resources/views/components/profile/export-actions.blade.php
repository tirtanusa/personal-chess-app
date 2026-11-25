<section class="w-full flex flex-col gap-3">
    <!-- Primary Export CTA -->
    <button
        type="button"
        class="w-full py-3 px-4 rounded-xl bg-primary/10 hover:bg-primary/20 border border-primary/35 text-primary font-headline font-bold text-sm transition-colors flex items-center justify-center gap-2 cursor-pointer"
    >
        <x-lucide-download class="w-4 h-4 shrink-0" />
        <span>Download PGN / Export Match History</span>
    </button>

    <!-- Archive Link Row -->
    <a
        href="#"
        class="w-full px-2 py-1.5 flex items-center justify-between gap-2 text-xs text-neutral-400 hover:text-neutral-200 transition-colors"
    >
        <span class="inline-flex items-center gap-2 min-w-0">
            <x-lucide-file-text class="w-4 h-4 text-neutral-500 shrink-0" />
            <span class="truncate">Import Games from Lichess / Chess.com</span>
        </span>
        <span class="inline-flex items-center gap-1 text-primary font-semibold shrink-0">
            <span>Full Archive</span>
            <x-lucide-chevron-right class="w-3.5 h-3.5" />
        </span>
    </a>
</section>
