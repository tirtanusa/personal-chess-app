@props([
    'name' => 'username',
    'id' => 'chess-username',
    'placeholder' => 'Enter username / profile',
    'value' => '',
])

<div class="w-full bg-canvas border border-border rounded-xl p-1.5 pl-3.5 flex items-center gap-2.5 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/40 transition-colors">
    <!-- Platform Emblem Icon -->
    <span class="shrink-0 flex items-center justify-center" data-platform-icon>
        <!-- Chess.com Pawn Icon (default) -->
        <svg data-icon="chesscom" class="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 2a3.5 3.5 0 0 0-3.5 3.5c0 1.24.64 2.33 1.61 2.95L8.6 10.5H7.5a1 1 0 0 0 0 2h1.07L7 17.5h10l-1.57-5H16.5a1 1 0 0 0 0-2h-1.1l-1.51-2.05A3.49 3.49 0 0 0 15.5 5.5 3.5 3.5 0 0 0 12 2Zm-6 17a1 1 0 0 0-1 1v1.5a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V20a1 1 0 0 0-1-1H6Z"/>
        </svg>

        <!-- Lichess Knight Icon (hidden by default) -->
        <svg data-icon="lichess" class="w-4 h-4 text-neutral-100 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 3a9 9 0 1 0 9 9" />
            <path d="M19 5c-2.5-1.5-6-.5-7.5 2-.5.8-.5 2 0 3l2 1.5-2.5 2c-1 .8-2 2-2 3.5" />
            <path d="M17 3l4 4" />
        </svg>
    </span>

    <!-- Username Input -->
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        class="flex-1 min-w-0 bg-transparent text-sm text-neutral-100 placeholder:text-neutral-500 font-label focus:outline-none"
    >

    <!-- Inline Check Button -->
    <button
        type="button"
        class="px-3.5 py-2 rounded-lg bg-primary-200 hover:bg-primary-100 active:scale-[0.98] text-neutral-950 font-headline font-bold text-xs flex items-center gap-1.5 shrink-0 transition-all cursor-pointer"
    >
        <x-lucide-search class="w-3.5 h-3.5 stroke-[2.5]" />
        <span>Check</span>
    </button>
</div>
