@props([
    'pending' => true,
    'activeMode' => 'blitz',
    'recentMatches' => [],
    'recentMatchesByMode' => null,
])

@php
    $modeLabels = [
        'blitz' => 'Blitz',
        'rapid' => 'Rapid',
        'bullet' => 'Bullet',
        'classic' => 'Classical',
    ];

    $matchesByMode = $recentMatchesByMode ?? [
        'blitz' => $recentMatches,
        'rapid' => [],
        'bullet' => [],
        'classic' => [],
    ];
@endphp

<section
    class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4"
    data-mode-section
>
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-primary/15 border border-primary/25 text-primary flex items-center justify-center">
                <x-lucide-chart-no-axes-combined class="w-4 h-4" />
            </span>
            <h3 class="font-headline text-base font-bold text-white">Game Insights</h3>
        </div>

        @if ($pending)
            <span class="px-2 py-0.5 rounded-full bg-surface-elevated border border-border-subtle font-label text-[10px] font-semibold text-neutral-400">
                Engine Pending
            </span>
        @else
            <span class="font-body text-xs text-neutral-400">Last 10 Games</span>
        @endif
    </div>

    <!-- 3 Mini Metric Cards (Pending Engine Analysis) -->
    <div class="grid grid-cols-3 gap-2 sm:gap-2.5">
        <!-- Avg Accuracy Ring -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="relative w-12 h-12 flex items-center justify-center">
                <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="currentColor" stroke-width="3.2" class="text-surface-elevated" />
                    @if (! $pending)
                        <circle cx="18" cy="18" r="15" fill="none" stroke="currentColor" stroke-width="3.2" stroke-dasharray="94.2" stroke-dashoffset="14.1" stroke-linecap="round" class="text-primary" />
                    @endif
                </svg>
                <span class="absolute font-label text-[11px] font-bold {{ $pending ? 'text-neutral-500' : 'text-white' }}">
                    {{ $pending ? '—' : '85%' }}
                </span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">
                {{ $pending ? 'Accuracy' : 'Avg Accuracy' }}
            </span>
        </div>

        <!-- Brilliant -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 h-12">
                <x-lucide-sparkles class="w-4 h-4 {{ $pending ? 'text-neutral-500' : 'text-secondary' }}" />
                <span class="font-headline text-xl font-bold {{ $pending ? 'text-neutral-500' : 'text-white' }}">
                    {{ $pending ? '—' : '12' }}
                </span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">
                {{ $pending ? 'Brilliant' : 'Brilliant (11)' }}
            </span>
        </div>

        <!-- Best Moves -->
        <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 sm:p-3 flex flex-col items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 h-12">
                <x-lucide-thumbs-up class="w-4 h-4 {{ $pending ? 'text-neutral-500' : 'text-primary' }}" />
                <span class="font-headline text-xl font-bold {{ $pending ? 'text-neutral-500' : 'text-white' }}">
                    {{ $pending ? '—' : '38' }}
                </span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 text-center whitespace-nowrap">Best Moves</span>
        </div>
    </div>

    <!-- Recent Results List (Per Mode) -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center justify-between gap-2">
            <h4 class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
                Recent Results
            </h4>
            <span
                class="px-2 py-0.5 rounded-md bg-primary/10 border border-primary/25 font-label text-[10px] font-semibold text-primary"
                data-active-mode-badge
            >
                {{ $modeLabels[$activeMode] ?? 'Blitz' }}
            </span>
        </div>

        @foreach ($modeLabels as $modeKey => $modeLabel)
            @php
                $modeMatches = $matchesByMode[$modeKey] ?? [];
                $isActive = $modeKey === $activeMode;
            @endphp
            <div
                data-mode-panel="{{ $modeKey }}"
                data-mode-label="{{ $modeLabel }}"
                @class(['flex flex-col gap-2', 'hidden' => ! $isActive])
            >
                @if (count($modeMatches) > 0)
                    @foreach ($modeMatches as $match)
                        <div class="w-full text-left bg-canvas/80 border border-border-subtle rounded-xl px-3 py-2.5 flex items-center justify-between gap-3">
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
                                <span class="font-label text-xs font-bold {{ $match['resultClass'] }}">
                                    {{ $match['resultText'] }}
                                </span>
                                <span class="font-body text-[10px] text-neutral-500">
                                    {{ $match['mode'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-canvas/70 border border-border-subtle rounded-xl px-3 py-4 text-center text-xs text-neutral-400 font-body">
                        No recent {{ $modeLabel }} games found yet.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('home:mode-change', (event) => {
                const mode = event.detail?.mode;
                if (!mode) return;

                document.querySelectorAll('[data-mode-section]').forEach((section) => {
                    const badge = section.querySelector('[data-active-mode-badge]');

                    section.querySelectorAll('[data-mode-panel]').forEach((panel) => {
                        const isMatch = panel.getAttribute('data-mode-panel') === mode;
                        panel.classList.toggle('hidden', !isMatch);

                        if (isMatch && badge) {
                            badge.textContent = panel.getAttribute('data-mode-label') || mode;
                        }

                        if (isMatch) {
                            panel.querySelectorAll('[data-heatmap-scroll]').forEach((scroller) => {
                                scroller.scrollLeft = scroller.scrollWidth;
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
