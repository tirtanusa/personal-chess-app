@php
    $openingsBySide = [
        'white' => [
            [
                'name' => 'Ruy Lopez (Spanish Game)',
                'moves' => '1. e4 e5 2. Nf3 Nc6 3. Bb5',
                'winRate' => '68% Win',
                'games' => '209 games',
                'record' => '142W • 45D • 22L',
                'accuracy' => '89.4% Accuracy',
                'w' => 68,
                'd' => 21,
                'l' => 11,
                'dotClass' => 'bg-primary',
            ],
            [
                'name' => "Queen's Gambit",
                'moves' => '1. d4 d5 2. c4',
                'winRate' => '62% Win',
                'games' => '157 games',
                'record' => '98W • 38D • 21L',
                'accuracy' => '87.1% Accuracy',
                'w' => 62,
                'd' => 24,
                'l' => 14,
                'dotClass' => 'bg-primary',
            ],
            [
                'name' => 'Italian Game (Giuoco Piano)',
                'moves' => '1. e4 e5 2. Nf3 Nc6 3. Bc4',
                'winRate' => '59% Win',
                'games' => '108 games',
                'record' => '64W • 26D • 18L',
                'accuracy' => '85.5% Accuracy',
                'w' => 59,
                'd' => 24,
                'l' => 17,
                'dotClass' => 'bg-primary',
            ],
        ],
        'black' => [
            [
                'name' => 'Sicilian Defense: Najdorf',
                'moves' => '1. e4 c5 2. Nf3 d6 3. d4 cxd4',
                'winRate' => '64% Win',
                'games' => '184 games',
                'record' => '118W • 36D • 30L',
                'accuracy' => '88.2% Accuracy',
                'w' => 64,
                'd' => 20,
                'l' => 16,
                'dotClass' => 'bg-secondary',
            ],
            [
                'name' => "King's Indian Defense",
                'moves' => '1. d4 Nf6 2. c4 g6 3. Nc3 Bg7',
                'winRate' => '58% Win',
                'games' => '132 games',
                'record' => '76W • 30D • 26L',
                'accuracy' => '84.9% Accuracy',
                'w' => 58,
                'd' => 23,
                'l' => 19,
                'dotClass' => 'bg-secondary',
            ],
            [
                'name' => 'French Defense: Winawer',
                'moves' => '1. e4 e6 2. d4 d5 3. Nc3 Bb4',
                'winRate' => '56% Win',
                'games' => '96 games',
                'record' => '54W • 22D • 20L',
                'accuracy' => '83.7% Accuracy',
                'w' => 56,
                'd' => 23,
                'l' => 21,
                'dotClass' => 'bg-secondary',
            ],
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4" data-openings-card>
    <!-- Header + Side Toggle -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <x-lucide-book-open class="w-4 h-4 text-primary shrink-0" />
            <h3 class="font-headline text-base font-bold text-white truncate">Favorite Openings</h3>
        </div>

        <div class="inline-flex items-center p-0.5 rounded-lg bg-canvas border border-border-subtle shrink-0">
            <button
                type="button"
                data-side-btn="white"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-bold bg-primary-200 text-neutral-950 transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-white border border-neutral-950/30"></span>
                <span>White</span>
            </button>
            <button
                type="button"
                data-side-btn="black"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-semibold text-neutral-400 hover:text-white transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-neutral-700 border border-neutral-500"></span>
                <span>Black</span>
            </button>
        </div>
    </div>

    <!-- Openings List -->
    @foreach ($openingsBySide as $side => $openings)
        <div @class(['flex flex-col gap-2.5', 'hidden' => $side !== 'white']) data-side-list="{{ $side }}">
            @foreach ($openings as $opening)
                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-3 flex flex-col gap-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full shrink-0 {{ $opening['dotClass'] }}"></span>
                            <span class="font-headline text-xs sm:text-sm font-bold text-white truncate">
                                {{ $opening['name'] }}
                            </span>
                        </div>
                        <span class="font-label text-[11px] font-bold text-primary shrink-0">
                            {{ $opening['winRate'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <span class="font-label text-[11px] text-primary truncate">
                            {{ $opening['moves'] }}
                        </span>
                        <span class="font-label text-[10px] text-neutral-400 shrink-0">
                            {{ $opening['games'] }}
                        </span>
                    </div>

                    <!-- Segmented Bar -->
                    <div class="h-1 w-full rounded-full overflow-hidden flex gap-0.5 bg-surface-elevated">
                        <div class="h-full bg-primary rounded-l-full" style="width: {{ $opening['w'] }}%"></div>
                        <div class="h-full bg-secondary" style="width: {{ $opening['d'] }}%"></div>
                        <div class="h-full bg-danger-400 rounded-r-full" style="width: {{ $opening['l'] }}%"></div>
                    </div>

                    <div class="flex items-center justify-between gap-2 font-label text-[10px]">
                        <span class="text-neutral-400">{{ $opening['record'] }}</span>
                        <span class="text-primary font-semibold">{{ $opening['accuracy'] }}</span>
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
                document.querySelectorAll('[data-openings-card]').forEach((card) => {
                    const buttons = card.querySelectorAll('[data-side-btn]');
                    const lists = card.querySelectorAll('[data-side-list]');

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const selected = btn.getAttribute('data-side-btn');

                            buttons.forEach((b) => {
                                const active = b === btn;
                                b.classList.toggle('bg-primary-200', active);
                                b.classList.toggle('text-neutral-950', active);
                                b.classList.toggle('font-bold', active);
                                b.classList.toggle('text-neutral-400', !active);
                                b.classList.toggle('font-semibold', !active);
                            });

                            lists.forEach((list) => {
                                list.classList.toggle('hidden', list.getAttribute('data-side-list') !== selected);
                            });
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
