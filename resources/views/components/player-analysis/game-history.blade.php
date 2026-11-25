@props([
    'firstName' => 'Alireza',
])

@php
    $games = [
        [
            'result' => 'win',
            'format' => 'blitz3',
            'statusLabel' => 'WIN',
            'statusClass' => 'bg-primary/15 text-primary border-primary/30',
            'modeIcon' => 'zap',
            'modeLabel' => 'Blitz 3+0',
            'delta' => '+8.4 ELO',
            'deltaClass' => 'text-primary',
            'accentClass' => 'bg-primary',
            'oppInitials' => 'HN',
            'oppName' => 'vs GM Hikaru_Nakamura',
            'flag' => '🇺🇸',
            'meta' => 'Accuracy: 95.8% · 42 Moves',
            'analysis' => [
                'opponent' => 'GM Hikaru_Nakamura (2875)',
                'youAccuracy' => '95.8%',
                'oppAccuracy' => '91.4%',
                'resultLabel' => 'WIN',
                'resultVariant' => 'win',
                'moves' => '42 Mvs',
                'stats' => [
                    'best' => [24, 19],
                    'excellent' => [11, 13],
                    'good' => [5, 6],
                    'inaccuracy' => [2, 2],
                    'mistake' => [0, 1],
                    'blunder' => [0, 1],
                ],
                'insightBody' => ' Super-GM endgame precision across all 42 moves. Winning tactic emerged on move 34 with ',
                'insightHighlight' => 'Blunder (34... Qd5??).',
            ],
        ],
        [
            'result' => 'win',
            'format' => 'bullet1',
            'statusLabel' => 'WIN',
            'statusClass' => 'bg-primary/15 text-primary border-primary/30',
            'modeIcon' => 'zap',
            'modeLabel' => 'Bullet 1+0',
            'delta' => '+6.2 ELO',
            'deltaClass' => 'text-primary',
            'accentClass' => 'bg-primary',
            'oppInitials' => 'DN',
            'oppName' => 'vs GM Daniel_Naroditsky',
            'flag' => '🇺🇸',
            'meta' => 'Accuracy: 92.1% · 31 Moves',
            'analysis' => [
                'opponent' => 'GM Daniel_Naroditsky (2615)',
                'youAccuracy' => '92.1%',
                'oppAccuracy' => '87.9%',
                'resultLabel' => 'WIN',
                'resultVariant' => 'win',
                'moves' => '31 Mvs',
                'stats' => [
                    'best' => [17, 13],
                    'excellent' => [9, 9],
                    'good' => [4, 5],
                    'inaccuracy' => [1, 2],
                    'mistake' => [0, 1],
                    'blunder' => [0, 1],
                ],
                'insightBody' => ' Relentless kingside attack in the Sicilian Najdorf forced decisive material gain via ',
                'insightHighlight' => 'Brilliant (21. Rxe6!!).',
            ],
        ],
        [
            'result' => 'loss',
            'format' => 'blitz3',
            'statusLabel' => 'LOSS',
            'statusClass' => 'bg-danger/15 text-danger-400 border-danger/30',
            'modeIcon' => 'zap',
            'modeLabel' => 'Blitz 3+2',
            'delta' => '-7.8 ELO',
            'deltaClass' => 'text-danger-400',
            'accentClass' => 'bg-danger-400',
            'oppInitials' => 'MC',
            'oppName' => 'vs GM Magnus_Carlsen',
            'flag' => '🇳🇴',
            'meta' => 'Accuracy: 88.4% · 58 Moves',
            'analysis' => [
                'opponent' => 'GM Magnus_Carlsen (2882)',
                'youAccuracy' => '88.4%',
                'oppAccuracy' => '93.7%',
                'resultLabel' => 'LOSS',
                'resultVariant' => 'loss',
                'moves' => '58 Mvs',
                'stats' => [
                    'best' => [27, 32],
                    'excellent' => [17, 16],
                    'good' => [9, 8],
                    'inaccuracy' => [3, 2],
                    'mistake' => [1, 0],
                    'blunder' => [1, 0],
                ],
                'insightBody' => ' Equal rook endgame until time scramble pressure caused a decisive slip on move 51 with ',
                'insightHighlight' => 'Blunder (51. Rf6??).',
            ],
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-3.5" data-scouting-history>
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex flex-col gap-0.5 min-w-0">
            <h3 class="font-headline text-base font-bold text-white truncate">
                {{ $firstName }}&apos;s Game History
            </h3>
            <span class="font-body text-[11px] text-neutral-400">Recent competitive activity</span>
        </div>

        <x-lucide-history class="w-4 h-4 text-neutral-400 shrink-0" />
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        @foreach ([
            'all' => 'All',
            'win' => 'Win',
            'loss' => 'Loss',
            'blitz3' => 'Blitz 3+0',
            'bullet1' => 'Bullet 1+0',
        ] as $key => $label)
            <button
                type="button"
                data-scout-filter="{{ $key }}"
                @class([
                    'px-3 py-1 rounded-full font-headline text-[11px] whitespace-nowrap transition-colors cursor-pointer shrink-0',
                    'bg-primary-200 text-neutral-950 font-bold' => $key === 'all',
                    'bg-canvas border border-border-subtle text-neutral-300 hover:text-white font-semibold' => $key !== 'all',
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Game Cards -->
    <div class="flex flex-col gap-2.5">
        @foreach ($games as $game)
            <article
                data-scout-game
                data-result="{{ $game['result'] }}"
                data-format="{{ $game['format'] }}"
                data-analysis-payload='@json($game['analysis'])'
                class="relative overflow-hidden bg-canvas/80 border border-border-subtle hover:border-border active:scale-[0.99] rounded-xl p-3 pl-3.5 flex flex-col gap-2.5 transition-all cursor-pointer"
            >
                <span class="absolute left-0 top-2.5 bottom-2.5 w-1 rounded-r-full {{ $game['accentClass'] }}"></span>

                <!-- Top Row: Status + Mode + ELO Delta -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 rounded border font-label text-[9px] font-bold {{ $game['statusClass'] }}">
                            {{ $game['statusLabel'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 font-label text-[10px] text-neutral-400">
                            <x-dynamic-component :component="'lucide-' . $game['modeIcon']" class="w-3 h-3 text-neutral-400" />
                            <span>{{ $game['modeLabel'] }}</span>
                        </span>
                    </div>

                    <span class="font-label text-[11px] font-bold {{ $game['deltaClass'] }}">
                        {{ $game['delta'] }}
                    </span>
                </div>

                <!-- Bottom Row: Opponent + Inspect Game CTA -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-surface-elevated border border-border-subtle font-headline text-[11px] font-bold text-primary flex items-center justify-center shrink-0">
                            {{ $game['oppInitials'] }}
                        </span>

                        <div class="flex flex-col gap-0.5 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-headline text-xs font-bold text-white truncate">
                                    {{ $game['oppName'] }}
                                </span>
                                <span class="text-xs leading-none shrink-0" aria-hidden="true">{{ $game['flag'] }}</span>
                            </div>
                            <span class="font-body text-[10px] text-neutral-400 truncate">
                                {{ $game['meta'] }}
                            </span>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="px-2.5 py-1.5 rounded-lg bg-surface border border-border hover:border-primary/50 hover:bg-primary/10 text-primary font-headline text-[11px] font-semibold whitespace-nowrap transition-colors shrink-0 cursor-pointer"
                    >
                        Inspect Game
                    </button>
                </div>
            </article>
        @endforeach

        <div class="hidden py-4 text-center text-xs text-neutral-400 font-body" data-scout-empty>
            No games match the selected filter.
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-scouting-history]').forEach((section) => {
                    const buttons = section.querySelectorAll('[data-scout-filter]');
                    const items = section.querySelectorAll('[data-scout-game]');
                    const emptyEl = section.querySelector('[data-scout-empty]');

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const filter = btn.getAttribute('data-scout-filter');

                            buttons.forEach((b) => {
                                const active = b === btn;
                                b.className = active
                                    ? 'px-3 py-1 rounded-full font-headline text-[11px] whitespace-nowrap transition-colors cursor-pointer shrink-0 bg-primary-200 text-neutral-950 font-bold'
                                    : 'px-3 py-1 rounded-full font-headline text-[11px] whitespace-nowrap transition-colors cursor-pointer shrink-0 bg-canvas border border-border-subtle text-neutral-300 hover:text-white font-semibold';
                            });

                            let visible = 0;
                            items.forEach((item) => {
                                const show = filter === 'all'
                                    || item.getAttribute('data-result') === filter
                                    || item.getAttribute('data-format') === filter;
                                item.classList.toggle('hidden', !show);
                                if (show) visible++;
                            });

                            if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
