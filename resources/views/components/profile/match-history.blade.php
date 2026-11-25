@php
    $matches = [
        [
            'opponent' => 'Viktor_K',
            'rating' => '1890',
            'flag' => '🇷🇺',
            'meta' => '2 hours ago • Blitz 3+2 • 38 moves',
            'result' => 'win',
            'format' => 'blitz',
            'statusLabel' => 'Win',
            'statusClass' => 'bg-primary/15 text-primary border-primary/30',
            'delta' => '+11 ELO',
            'deltaClass' => 'text-primary',
            'accentClass' => 'bg-primary',
            'accuracy' => '91.4%',
            'accuracyIcon' => 'shield-check',
            'accuracyIconClass' => 'text-primary',
            'tag' => '!! 2 BRILLIANT',
            'ctaLabel' => 'Analyze',
            'ctaIcon' => 'text-search',
            'analysis' => [
                'opponent' => 'Viktor_K (1890)',
                'youAccuracy' => '91.4%',
                'oppAccuracy' => '82.6%',
                'resultLabel' => 'WIN',
                'resultVariant' => 'win',
                'moves' => '38 Mvs',
                'stats' => [
                    'best' => [18, 13],
                    'excellent' => [12, 10],
                    'good' => [5, 7],
                    'inaccuracy' => [2, 4],
                    'mistake' => [1, 2],
                    'blunder' => [0, 2],
                ],
                'insightBody' => ' You made zero blunders across all 38 moves. The decisive breakthrough came on move 24 with ',
                'insightHighlight' => 'Brilliant (24. Bxh7+!!).',
            ],
        ],
        [
            'opponent' => 'Elena_R',
            'rating' => '1815',
            'flag' => '🇷🇴',
            'meta' => 'Yesterday • Rapid 10m • 42 moves',
            'result' => 'win',
            'format' => 'rapid',
            'statusLabel' => 'Checkmate',
            'statusClass' => 'bg-primary/15 text-primary border-primary/30',
            'delta' => '+8 ELO',
            'deltaClass' => 'text-primary',
            'accentClass' => 'bg-primary',
            'accuracy' => '88.0% Accuracy',
            'accuracyIcon' => 'shield-check',
            'accuracyIconClass' => 'text-primary',
            'tag' => null,
            'ctaLabel' => 'View Board',
            'ctaIcon' => 'eye',
            'analysis' => [
                'opponent' => 'Elena_R (1815)',
                'youAccuracy' => '88.0%',
                'oppAccuracy' => '79.4%',
                'resultLabel' => 'CHECKMATE',
                'resultVariant' => 'win',
                'moves' => '42 Mvs',
                'stats' => [
                    'best' => [17, 12],
                    'excellent' => [13, 11],
                    'good' => [8, 9],
                    'inaccuracy' => [3, 5],
                    'mistake' => [1, 3],
                    'blunder' => [0, 2],
                ],
                'insightBody' => ' You maintained a clean endgame conversion without a single blunder. Victory was sealed on move 28 after ',
                'insightHighlight' => 'Blunder (28... Bxe5??).',
            ],
        ],
        [
            'opponent' => 'MagnusFan99',
            'rating' => '1850',
            'flag' => '🇳🇴',
            'meta' => '2 days ago • Blitz 3+2 • 56 moves',
            'result' => 'draw',
            'format' => 'blitz',
            'statusLabel' => 'Draw',
            'statusClass' => 'bg-secondary/15 text-secondary border-secondary/30',
            'delta' => '+1 ELO',
            'deltaClass' => 'text-secondary',
            'accentClass' => 'bg-secondary',
            'accuracy' => '84.5% Accuracy',
            'accuracyIcon' => 'scale',
            'accuracyIconClass' => 'text-secondary',
            'tag' => null,
            'ctaLabel' => 'Review',
            'ctaIcon' => 'rotate-ccw',
            'analysis' => [
                'opponent' => 'MagnusFan99 (1850)',
                'youAccuracy' => '84.5%',
                'oppAccuracy' => '84.9%',
                'resultLabel' => 'DRAW',
                'resultVariant' => 'draw',
                'moves' => '56 Mvs',
                'stats' => [
                    'best' => [20, 21],
                    'excellent' => [16, 15],
                    'good' => [12, 11],
                    'inaccuracy' => [5, 6],
                    'mistake' => [2, 2],
                    'blunder' => [1, 1],
                ],
                'insightBody' => ' A razor-close Berlin Defense battle. Both sides defended accurately until perpetual check after ',
                'insightHighlight' => 'Inaccuracy (49. Kf3?!).',
            ],
        ],
        [
            'opponent' => 'KasparovGhost',
            'rating' => '1910',
            'flag' => '🇦🇿',
            'meta' => '3 days ago • Bullet 1+0 • 29 moves',
            'result' => 'loss',
            'format' => 'bullet',
            'statusLabel' => 'Loss',
            'statusClass' => 'bg-danger/15 text-danger-400 border-danger/30',
            'delta' => '-9 ELO',
            'deltaClass' => 'text-danger-400',
            'accentClass' => 'bg-danger-400',
            'accuracy' => '76.2% Accuracy',
            'accuracyIcon' => 'circle-slash',
            'accuracyIconClass' => 'text-danger-400',
            'tag' => null,
            'ctaLabel' => 'Blunder Check',
            'ctaIcon' => 'brain',
            'analysis' => [
                'opponent' => 'KasparovGhost (1910)',
                'youAccuracy' => '76.2%',
                'oppAccuracy' => '87.5%',
                'resultLabel' => 'LOSS',
                'resultVariant' => 'loss',
                'moves' => '29 Mvs',
                'stats' => [
                    'best' => [9, 14],
                    'excellent' => [8, 9],
                    'good' => [5, 4],
                    'inaccuracy' => [3, 1],
                    'mistake' => [2, 1],
                    'blunder' => [2, 0],
                ],
                'insightBody' => ' Time trouble in the middlegame led to a decisive tactical collapse starting on move 22 with ',
                'insightHighlight' => 'Blunder (22. Qe2??).',
            ],
        ],
    ];
@endphp

<section class="w-full flex flex-col gap-3.5" data-match-history>
    <!-- Section Title -->
    <div class="flex items-center justify-between gap-2">
        <h3 class="font-headline text-base font-bold text-white">Match History</h3>
        <span class="font-label text-xs text-neutral-400">1,482 Matches</span>
    </div>

    <!-- Result Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        @foreach ([
            'all' => 'All',
            'win' => 'Win (W)',
            'loss' => 'Loss (L)',
            'draw' => 'Draw (D)',
        ] as $value => $label)
            <button
                type="button"
                data-result-filter="{{ $value }}"
                @class([
                    'px-3.5 py-1.5 rounded-full font-headline text-xs whitespace-nowrap transition-colors cursor-pointer shrink-0',
                    'bg-primary-200 text-neutral-950 font-bold' => $value === 'all',
                    'bg-surface border border-border-subtle text-neutral-300 hover:text-white font-semibold' => $value !== 'all',
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Format Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        <button
            type="button"
            data-format-filter="all"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary/15 border border-primary/35 text-primary font-label text-[11px] font-semibold whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <x-lucide-sliders-horizontal class="w-3 h-3" />
            <span>Format: All</span>
        </button>

        <button
            type="button"
            data-format-filter="blitz"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface border border-border-subtle text-neutral-300 hover:text-white font-label text-[11px] font-medium whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <x-lucide-zap class="w-3 h-3 text-secondary" />
            <span>Blitz</span>
        </button>

        <button
            type="button"
            data-format-filter="rapid"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface border border-border-subtle text-neutral-300 hover:text-white font-label text-[11px] font-medium whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <x-lucide-clock class="w-3 h-3 text-primary" />
            <span>Rapid</span>
        </button>

        <button
            type="button"
            data-format-filter="bullet"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface border border-border-subtle text-neutral-300 hover:text-white font-label text-[11px] font-medium whitespace-nowrap transition-colors cursor-pointer shrink-0"
        >
            <x-lucide-rocket class="w-3 h-3 text-tertiary-300" />
            <span>Bullet</span>
        </button>
    </div>

    <!-- Match Cards -->
    <div class="flex flex-col gap-3">
        @foreach ($matches as $match)
            <article
                data-match-card
                data-result="{{ $match['result'] }}"
                data-format="{{ $match['format'] }}"
                data-analysis-payload='@json($match['analysis'])'
                class="relative overflow-hidden bg-surface border border-border-subtle rounded-2xl p-3.5 pl-4 flex flex-col gap-3 hover:border-border active:scale-[0.99] transition-all cursor-pointer"
            >
                <!-- Left Result Color Accent Bar -->
                <span class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full {{ $match['accentClass'] }}"></span>

                <!-- Top Row: Opponent & Result Badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-canvas border border-border-subtle flex items-center justify-center shrink-0">
                            <x-lucide-user class="w-4 h-4 text-neutral-400" />
                        </div>

                        <div class="flex flex-col gap-0.5 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-headline text-sm font-bold text-white truncate">
                                    {{ $match['opponent'] }}
                                </span>
                                <span class="font-label text-[11px] text-neutral-400">({{ $match['rating'] }})</span>
                                <span class="text-xs leading-none shrink-0" aria-hidden="true">{{ $match['flag'] }}</span>
                            </div>
                            <span class="font-body text-[11px] text-neutral-400 truncate">
                                {{ $match['meta'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-0.5 shrink-0">
                        <span class="px-2 py-0.5 rounded-md border font-label text-[10px] font-bold {{ $match['statusClass'] }}">
                            {{ $match['statusLabel'] }}
                        </span>
                        <span class="font-label text-[11px] font-bold {{ $match['deltaClass'] }}">
                            {{ $match['delta'] }}
                        </span>
                    </div>
                </div>

                <!-- Bottom Row: Accuracy / Highlights & Action Button -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-border-subtle/70">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-canvas/80 border border-border-subtle font-label text-[11px] text-neutral-200">
                            <x-dynamic-component :component="'lucide-' . $match['accuracyIcon']" class="w-3.5 h-3.5 {{ $match['accuracyIconClass'] }}" />
                            <span>{{ $match['accuracy'] }}</span>
                        </span>

                        @if ($match['tag'])
                            <span class="px-2 py-1 rounded-lg bg-surface-elevated border border-border-subtle font-label text-[10px] font-bold text-neutral-200 whitespace-nowrap">
                                {{ $match['tag'] }}
                            </span>
                        @endif
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-canvas/80 border border-border hover:border-border-strong hover:bg-surface-elevated font-headline text-xs font-semibold text-neutral-200 hover:text-white transition-colors shrink-0 cursor-pointer"
                    >
                        <x-dynamic-component :component="'lucide-' . $match['ctaIcon']" class="w-3.5 h-3.5" />
                        <span>{{ $match['ctaLabel'] }}</span>
                    </button>
                </div>
            </article>
        @endforeach

        <!-- Empty State when filters match 0 items -->
        <div class="hidden bg-surface border border-border-subtle rounded-2xl p-6 text-center text-xs text-neutral-400 font-body" data-match-empty>
            No matches found for the selected filter.
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-match-history]').forEach((section) => {
                    const resultBtns = section.querySelectorAll('[data-result-filter]');
                    const formatBtns = section.querySelectorAll('[data-format-filter]');
                    const cards = section.querySelectorAll('[data-match-card]');
                    const emptyEl = section.querySelector('[data-match-empty]');

                    let activeResult = 'all';
                    let activeFormat = 'all';

                    const applyFilters = () => {
                        let visibleCount = 0;
                        cards.forEach((card) => {
                            const matchResult = activeResult === 'all' || card.getAttribute('data-result') === activeResult;
                            const matchFormat = activeFormat === 'all' || card.getAttribute('data-format') === activeFormat;
                            const show = matchResult && matchFormat;
                            card.classList.toggle('hidden', !show);
                            if (show) visibleCount++;
                        });
                        if (emptyEl) {
                            emptyEl.classList.toggle('hidden', visibleCount > 0);
                        }
                    };

                    resultBtns.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            activeResult = btn.getAttribute('data-result-filter');
                            resultBtns.forEach((b) => {
                                const active = b === btn;
                                b.className = active
                                    ? 'px-3.5 py-1.5 rounded-full font-headline text-xs whitespace-nowrap transition-colors cursor-pointer shrink-0 bg-primary-200 text-neutral-950 font-bold'
                                    : 'px-3.5 py-1.5 rounded-full font-headline text-xs whitespace-nowrap transition-colors cursor-pointer shrink-0 bg-surface border border-border-subtle text-neutral-300 hover:text-white font-semibold';
                            });
                            applyFilters();
                        });
                    });

                    formatBtns.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            activeFormat = btn.getAttribute('data-format-filter');
                            formatBtns.forEach((b) => {
                                const active = b === btn;
                                b.className = active
                                    ? 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-primary/15 border border-primary/35 text-primary font-label text-[11px] font-semibold whitespace-nowrap transition-colors cursor-pointer shrink-0'
                                    : 'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-surface border border-border-subtle text-neutral-300 hover:text-white font-label text-[11px] font-medium whitespace-nowrap transition-colors cursor-pointer shrink-0';
                            });
                            applyFilters();
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
