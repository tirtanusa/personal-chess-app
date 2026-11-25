@php
    $repertoireBySide = [
        'white' => [
            [
                'name' => "1. e4 (King's Pawn Game)",
                'winrate' => '71% Winrate',
                'winrateClass' => 'text-primary',
                'meta' => 'Top Lines: Ruy Lopez, Open Catalan',
                'games' => '482 Games',
                'w' => 71,
                'd' => 17,
                'l' => 12,
            ],
            [
                'name' => 'Italian Game: Giuoco Piano',
                'winrate' => '64% Winrate',
                'winrateClass' => 'text-secondary',
                'meta' => 'Opening Accuracy: 96.8%',
                'games' => '210 Games',
                'w' => 64,
                'd' => 21,
                'l' => 15,
            ],
        ],
        'black' => [
            [
                'name' => 'Sicilian Defense: Najdorf',
                'winrate' => '66% Winrate',
                'winrateClass' => 'text-primary',
                'meta' => 'Top Lines: English Attack, 6. Bg5',
                'games' => '348 Games',
                'w' => 66,
                'd' => 18,
                'l' => 16,
            ],
            [
                'name' => "King's Indian Defense",
                'winrate' => '61% Winrate',
                'winrateClass' => 'text-secondary',
                'meta' => 'Opening Accuracy: 94.1%',
                'games' => '184 Games',
                'w' => 61,
                'd' => 22,
                'l' => 17,
            ],
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4" data-repertoire-card>
    <!-- Header + White/Black Toggle -->
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-col gap-0.5 min-w-0">
            <h3 class="font-headline text-base font-bold text-white leading-tight">Opening Repertoire</h3>
            <span class="font-body text-[11px] text-neutral-400">Frequency and tactical effectiveness</span>
        </div>

        <div class="inline-flex items-center p-0.5 rounded-lg bg-canvas border border-border-subtle shrink-0">
            <button
                type="button"
                data-rep-side="white"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-bold bg-primary-200 text-neutral-950 transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-white border border-neutral-950/30"></span>
                <span>White</span>
            </button>
            <button
                type="button"
                data-rep-side="black"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-semibold text-neutral-400 hover:text-white transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-neutral-700 border border-neutral-500"></span>
                <span>Black</span>
            </button>
        </div>
    </div>

    <!-- Opening Cards -->
    @foreach ($repertoireBySide as $side => $items)
        <div @class(['flex flex-col gap-2.5', 'hidden' => $side !== 'white']) data-rep-list="{{ $side }}">
            @foreach ($items as $item)
                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-3 flex flex-col gap-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-headline text-xs sm:text-sm font-bold text-white truncate">
                            {{ $item['name'] }}
                        </span>
                        <span class="font-label text-[11px] font-bold shrink-0 {{ $item['winrateClass'] }}">
                            {{ $item['winrate'] }}
                        </span>
                    </div>

                    <!-- Segmented Bar -->
                    <div class="h-1.5 w-full rounded-full overflow-hidden flex gap-0.5 bg-surface-elevated">
                        <div class="h-full bg-primary rounded-l-full" style="width: {{ $item['w'] }}%"></div>
                        <div class="h-full bg-neutral-500" style="width: {{ $item['d'] }}%"></div>
                        <div class="h-full bg-danger-400 rounded-r-full" style="width: {{ $item['l'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between gap-2 font-body text-[11px]">
                        <span class="text-neutral-400 truncate">{{ $item['meta'] }}</span>
                        <span class="font-label text-[10px] text-neutral-400 shrink-0">{{ $item['games'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-repertoire-card]').forEach((card) => {
                    const buttons = card.querySelectorAll('[data-rep-side]');
                    const lists = card.querySelectorAll('[data-rep-list]');

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const selected = btn.getAttribute('data-rep-side');
                            buttons.forEach((b) => {
                                const active = b === btn;
                                b.classList.toggle('bg-primary-200', active);
                                b.classList.toggle('text-neutral-950', active);
                                b.classList.toggle('font-bold', active);
                                b.classList.toggle('text-neutral-400', !active);
                                b.classList.toggle('font-semibold', !active);
                            });
                            lists.forEach((list) => {
                                list.classList.toggle('hidden', list.getAttribute('data-rep-list') !== selected);
                            });
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
