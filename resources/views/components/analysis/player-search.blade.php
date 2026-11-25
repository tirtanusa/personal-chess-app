<section class="w-full flex flex-col gap-3" data-player-search>
    <!-- Search Bar -->
    <form
        action="#"
        method="GET"
        onsubmit="event.preventDefault()"
        class="w-full bg-surface border border-border rounded-2xl p-1.5 pl-4 flex items-center gap-2.5 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/35 transition-colors shadow-lg"
    >
        <x-lucide-search class="w-4 h-4 text-neutral-400 shrink-0" />

        <input
            type="text"
            name="q"
            placeholder="Enter player username (e.g. Hikaru, M...)"
            autocomplete="off"
            class="flex-1 min-w-0 bg-transparent text-sm text-neutral-100 placeholder:text-neutral-500 font-body focus:outline-none"
        >

        <button
            type="submit"
            aria-label="Analyze Player"
            class="w-9 h-9 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.97] text-neutral-950 flex items-center justify-center shrink-0 transition-all cursor-pointer shadow-[0_0_18px_-4px_rgba(76,213,157,0.45)]"
        >
            <x-lucide-arrow-right class="w-4 h-4 stroke-[2.5]" />
        </button>
    </form>

    <!-- Server Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        <button
            type="button"
            data-server-filter="all"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-primary-200 text-neutral-950 font-headline text-xs font-bold whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <x-lucide-globe class="w-3.5 h-3.5" />
            <span>All Servers</span>
        </button>

        <button
            type="button"
            data-server-filter="chesscom"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface border border-border-subtle text-neutral-300 hover:text-white font-headline text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <span class="w-2 h-2 rounded-full bg-primary"></span>
            <span>Chess.com</span>
        </button>

        <button
            type="button"
            data-server-filter="lichess"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface border border-border-subtle text-neutral-300 hover:text-white font-headline text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <span class="w-2 h-2 rounded-full bg-neutral-200"></span>
            <span>Lichess</span>
        </button>
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-player-search]').forEach((block) => {
                    const buttons = block.querySelectorAll('[data-server-filter]');
                    const activeCls = 'bg-primary-200 text-neutral-950 font-bold';
                    const inactiveCls = 'bg-surface border border-border-subtle text-neutral-300 hover:text-white font-semibold';

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            buttons.forEach((b) => {
                                const isActive = b === btn;
                                b.className = `inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-headline text-xs whitespace-nowrap transition-colors cursor-pointer shrink-0 ${isActive ? activeCls : inactiveCls}`;
                            });
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
