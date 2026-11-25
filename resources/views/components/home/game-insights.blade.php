@php
    $recentMatches = [
        [
            'opponent' => 'vs Eleni_K',
            'rating' => '1835',
            'opening' => 'Sicilian: Najdorf',
            'moves' => '32 moves',
            'delta' => '+8 ELO',
            'mode' => 'Rapid 10m',
            'accentClass' => 'bg-primary',
            'deltaClass' => 'text-primary',
            'analysis' => [
                'opponent' => 'Eleni_K (1835)',
                'youAccuracy' => '89.4%',
                'oppAccuracy' => '81.2%',
                'resultLabel' => 'WIN',
                'resultVariant' => 'win',
                'moves' => '32 Mvs',
                'stats' => [
                    'best' => [15, 11],
                    'excellent' => [10, 9],
                    'good' => [5, 7],
                    'inaccuracy' => [2, 3],
                    'mistake' => [0, 1],
                    'blunder' => [0, 1],
                ],
                'insightBody' => ' Clean Sicilian Najdorf execution with zero mistakes. Decisive tactical blow occurred on move 26 with ',
                'insightHighlight' => 'Blunder (26... Rxe4??).',
            ],
        ],
        [
            'opponent' => 'vs MagnusFan99',
            'rating' => '1850',
            'opening' => 'Ruy Lopez: Berlin',
            'moves' => '44 moves',
            'delta' => '+1 ELO',
            'mode' => 'Blitz 3+2',
            'accentClass' => 'bg-secondary',
            'deltaClass' => 'text-secondary',
            'analysis' => [
                'opponent' => 'MagnusFan99 (1850)',
                'youAccuracy' => '84.5%',
                'oppAccuracy' => '84.9%',
                'resultLabel' => 'DRAW',
                'resultVariant' => 'draw',
                'moves' => '44 Mvs',
                'stats' => [
                    'best' => [18, 19],
                    'excellent' => [12, 11],
                    'good' => [9, 9],
                    'inaccuracy' => [3, 3],
                    'mistake' => [1, 1],
                    'blunder' => [1, 1],
                ],
                'insightBody' => ' Balanced Berlin endgame where both sides neutralized counterplay until perpetual check on ',
                'insightHighlight' => 'Inaccuracy (39. Kf3?!).',
            ],
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-primary/15 border border-primary/25 text-primary flex items-center justify-center">
                <x-lucide-chart-no-axes-combined class="w-4 h-4" />
            </span>
            <h3 class="font-headline text-base font-bold text-white">Game Insights</h3>
        </div>

        <span class="font-body text-xs text-neutral-400">Last 10 Games</span>
    </div>

    <!-- 3 Mini Metric Cards -->
    <div class="grid grid-cols-3 gap-2 sm:gap-2.5">
        <!-- Avg Accuracy Ring -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="relative w-12 h-12 flex items-center justify-center">
                <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="currentColor" stroke-width="3.2" class="text-surface-elevated" />
                    <circle cx="18" cy="18" r="15" fill="none" stroke="currentColor" stroke-width="3.2" stroke-dasharray="94.2" stroke-dashoffset="14.1" stroke-linecap="round" class="text-primary" />
                </svg>
                <span class="absolute font-label text-[11px] font-bold text-white">85%</span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">Avg Accuracy</span>
        </div>

        <!-- Brilliant -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 h-12">
                <x-lucide-sparkles class="w-4 h-4 text-secondary" />
                <span class="font-headline text-xl font-bold text-white">12</span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">Brilliant (11)</span>
        </div>

        <!-- Best Moves -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 h-12">
                <x-lucide-thumbs-up class="w-4 h-4 text-primary" />
                <span class="font-headline text-xl font-bold text-white">38</span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">Best Moves</span>
        </div>
    </div>

    <!-- Recent Results List -->
    <div class="flex flex-col gap-2">
        <h4 class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
            Recent Results
        </h4>

        <div class="flex flex-col gap-2">
            @foreach ($recentMatches as $match)
                <button
                    type="button"
                    data-analysis-payload='@json($match['analysis'])'
                    class="w-full text-left bg-canvas/80 border border-border-subtle rounded-xl px-3 py-2.5 flex items-center justify-between gap-3 hover:border-border active:scale-[0.99] transition-all cursor-pointer"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-1 h-8 rounded-full shrink-0 {{ $match['accentClass'] }}"></span>

                        <div class="flex flex-col gap-0.5 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-headline text-xs font-bold text-white truncate">
                                    {{ $match['opponent'] }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded bg-surface-elevated border border-border-subtle font-label text-[10px] text-neutral-300">
                                    {{ $match['rating'] }}
                                </span>
                            </div>

                            <span class="font-body text-[11px] text-neutral-400 truncate">
                                {{ $match['opening'] }} • {{ $match['moves'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-0.5 shrink-0">
                        <span class="font-label text-xs font-bold {{ $match['deltaClass'] }}">
                            {{ $match['delta'] }}
                        </span>
                        <span class="font-body text-[10px] text-neutral-500">
                            {{ $match['mode'] }}
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</section>
