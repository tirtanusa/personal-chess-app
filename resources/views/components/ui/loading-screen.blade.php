@props([
    'overlay' => false,
    'id' => 'gambit-loading-screen',
    'title' => 'Loading...',
    'stage' => 'INITIALIZING',
    'message' => 'Welcome back, Grandmaster.',
    'progress' => 100,
    'animate' => true,
    'tag' => '#TACTIC_402',
    'quote' => 'A knight on the rim is dim.',
    'author' => 'Dr. Siegbert Tarrasch',
])

@php
    $quotes = [
        [
            'tag' => '#TACTIC_402',
            'quote' => 'A knight on the rim is dim.',
            'author' => 'Dr. Siegbert Tarrasch',
        ],
        [
            'tag' => '#STRATEGY_108',
            'quote' => 'Passed pawns must be pushed.',
            'author' => 'Aron Nimzowitsch',
        ],
        [
            'tag' => '#ENDGAME_215',
            'quote' => 'In order to improve your game, you must study the endgame before everything else.',
            'author' => 'José Raúl Capablanca',
        ],
        [
            'tag' => '#ATTACK_309',
            'quote' => 'You must take your opponent into a deep dark forest where 2+2=5.',
            'author' => 'Mikhail Tal',
        ],
    ];
@endphp

<div
    id="{{ $id }}"
    data-loading-screen
    data-animate="{{ $animate ? 'true' : 'false' }}"
    @class([
        'w-full bg-canvas text-neutral-100 antialiased font-body flex items-center justify-center p-5 selection:bg-primary/30 selection:text-primary-50',
        'fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200' => $overlay,
        'min-h-screen relative overflow-hidden' => ! $overlay,
    ])
>
    <!-- Subtle Ambient Emerald Glow -->
    <div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden">
        <div class="h-80 w-80 rounded-full bg-primary/12 blur-[110px]"></div>
    </div>

    <!-- Centered Loading Card -->
    <div class="relative z-10 w-full max-w-[400px] bg-surface border border-border-subtle rounded-2xl p-5 shadow-2xl flex flex-col gap-5">
        <!-- Progress Header & Bar -->
        <div class="flex flex-col gap-2.5">
            <div class="flex items-center justify-between gap-2">
                <span class="font-headline text-base font-bold text-white tracking-tight">
                    {{ $title }}
                </span>
                <span
                    data-loading-stage
                    class="font-label text-[10px] font-bold tracking-wider uppercase text-primary"
                >
                    {{ $stage }}
                </span>
            </div>

            <!-- Glowing Progress Track -->
            <div class="w-full h-1.5 rounded-full bg-canvas overflow-hidden">
                <div
                    data-loading-bar
                    class="h-full rounded-full bg-gradient-to-r from-primary-600 via-primary to-primary-100 shadow-[0_0_12px_rgba(16,185,129,0.55)] transition-all duration-300 ease-out"
                    style="width: {{ max(0, min(100, (int) $progress)) }}%"
                ></div>
            </div>

            <!-- Status Message & Percentage -->
            <div class="flex items-center justify-between gap-2">
                <span data-loading-message class="font-body text-xs text-neutral-400 truncate">
                    {{ $message }}
                </span>
                <span data-loading-percent class="font-label text-xs font-bold text-primary shrink-0">
                    {{ max(0, min(100, (int) $progress)) }}%
                </span>
            </div>
        </div>

        <!-- Grandmaster Wisdom Quote Card -->
        <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3.5 flex items-start gap-3">
            <span class="w-8 h-8 rounded-lg bg-surface-elevated border border-border text-secondary flex items-center justify-center shrink-0">
                <x-lucide-brain class="w-4 h-4" />
            </span>

            <div class="flex-1 min-w-0 flex flex-col gap-1.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-label text-[10px] font-bold tracking-wider uppercase text-secondary">
                        Grandmaster Wisdom
                    </span>
                    <span data-quote-tag class="font-label text-[10px] text-neutral-500 shrink-0">
                        {{ $tag }}
                    </span>
                </div>

                <p data-quote-text class="font-body text-xs italic leading-relaxed text-neutral-200">
                    &ldquo;{{ $quote }}&rdquo;
                </p>

                <span data-quote-author class="font-label text-[11px] text-neutral-400">
                    — {{ $author }}
                </span>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const quotes = @json($quotes);

                document.querySelectorAll('[data-loading-screen]').forEach((screen) => {
                    if (screen.getAttribute('data-animate') !== 'true') {
                        return;
                    }

                    const stageEl = screen.querySelector('[data-loading-stage]');
                    const barEl = screen.querySelector('[data-loading-bar]');
                    const messageEl = screen.querySelector('[data-loading-message]');
                    const percentEl = screen.querySelector('[data-loading-percent]');
                    const quoteTagEl = screen.querySelector('[data-quote-tag]');
                    const quoteTextEl = screen.querySelector('[data-quote-text]');
                    const quoteAuthorEl = screen.querySelector('[data-quote-author]');

                    const steps = [
                        { pct: 25, stage: 'CONNECTING', msg: 'Syncing chess profile...' },
                        { pct: 55, stage: 'ANALYZING', msg: 'Loading Stockfish 16.1 engine...' },
                        { pct: 85, stage: 'PREPARING', msg: 'Building tactical insights...' },
                        { pct: 100, stage: 'INITIALIZING', msg: 'Welcome back, Grandmaster.' },
                    ];

                    // Pick a random Grandmaster Wisdom quote on load
                    const chosen = quotes[Math.floor(Math.random() * quotes.length)];
                    if (quoteTagEl && quoteTextEl && quoteAuthorEl && chosen) {
                        quoteTagEl.textContent = chosen.tag;
                        quoteTextEl.textContent = `“${chosen.quote}”`;
                        quoteAuthorEl.textContent = `— ${chosen.author}`;
                    }

                    // Smooth step progression from 0% -> 100%
                    if (barEl && percentEl) {
                        barEl.style.width = '12%';
                        percentEl.textContent = '12%';

                        steps.forEach((step, idx) => {
                            setTimeout(() => {
                                barEl.style.width = `${step.pct}%`;
                                percentEl.textContent = `${step.pct}%`;
                                if (stageEl) stageEl.textContent = step.stage;
                                if (messageEl) messageEl.textContent = step.msg;
                            }, (idx + 1) * 450);
                        });
                    }
                });
            });
        </script>
    @endpush
@endonce
