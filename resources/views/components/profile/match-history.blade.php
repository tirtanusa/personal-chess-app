@props([
    'history' => null,
])

@php
    $totalLabel = $history['totalLabel'] ?? '0 Matches';
    $matches = $history['matches'] ?? [];
    $filters = $history['filters'] ?? ['result' => 'all', 'format' => 'all'];
    $activeResult = $filters['result'] ?? 'all';
    $activeFormat = $filters['format'] ?? 'all';

    $pagination = $history['pagination'] ?? [
        'perPage' => 10,
        'perPageOptions' => [5, 10, 100],
        'currentPage' => 1,
        'totalPages' => 1,
        'totalItems' => count($matches),
        'from' => count($matches) > 0 ? 1 : 0,
        'to' => count($matches),
        'hasPrev' => false,
        'hasNext' => false,
    ];

    $perPage = (int) ($pagination['perPage'] ?? 10);
    $perPageOptions = $pagination['perPageOptions'] ?? [5, 10, 100];
    $currentPage = (int) ($pagination['currentPage'] ?? 1);
    $totalPages = (int) ($pagination['totalPages'] ?? 1);
    $totalItems = (int) ($pagination['totalItems'] ?? count($matches));
    $from = (int) ($pagination['from'] ?? 0);
    $to = (int) ($pagination['to'] ?? 0);
    $hasPrev = (bool) ($pagination['hasPrev'] ?? false);
    $hasNext = (bool) ($pagination['hasNext'] ?? false);

    $buildUrl = fn (array $overrides = []): string => route('profile', array_merge([
        'result' => $activeResult,
        'format' => $activeFormat,
        'per_page' => $perPage,
        'page' => $currentPage,
    ], $overrides)).'#match-history';
@endphp

<section id="match-history" class="w-full flex flex-col gap-3.5" data-match-history>
    <!-- Section Title & Per-Page Selector -->
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <h3 class="font-headline text-base font-bold text-white">Match History</h3>
            <span class="font-label text-xs text-neutral-400">{{ $totalLabel }}</span>
        </div>

        <div class="inline-flex items-center gap-1 p-0.5 rounded-lg bg-surface border border-border-subtle" data-per-page-group>
            <span class="px-1.5 font-label text-[10px] text-neutral-400">Show</span>
            @foreach ($perPageOptions as $option)
                <a
                    href="{{ $buildUrl(['per_page' => $option, 'page' => 1]) }}"
                    data-per-page-option="{{ $option }}"
                    @class([
                        'px-2 py-0.5 rounded-md font-label text-[10px] font-bold transition-colors',
                        'bg-primary-200 text-neutral-950' => $perPage === $option,
                        'text-neutral-300 hover:text-white' => $perPage !== $option,
                    ])
                >
                    {{ $option }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Result Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        @foreach ([
            'all' => 'All',
            'win' => 'Win (W)',
            'loss' => 'Loss (L)',
            'draw' => 'Draw (D)',
        ] as $value => $label)
            <a
                href="{{ $buildUrl(['result' => $value, 'page' => 1]) }}"
                data-result-filter="{{ $value }}"
                @class([
                    'px-3.5 py-1.5 rounded-full font-headline text-xs whitespace-nowrap transition-colors shrink-0',
                    'bg-primary-200 text-neutral-950 font-bold' => $activeResult === $value,
                    'bg-surface border border-border-subtle text-neutral-300 hover:text-white font-semibold' => $activeResult !== $value,
                ])
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Format Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        <a
            href="{{ $buildUrl(['format' => 'all', 'page' => 1]) }}"
            data-format-filter="all"
            @class([
                'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg font-label text-[11px] whitespace-nowrap transition-colors shrink-0',
                'bg-primary/15 border border-primary/35 text-primary font-semibold' => $activeFormat === 'all',
                'bg-surface border border-border-subtle text-neutral-300 hover:text-white font-medium' => $activeFormat !== 'all',
            ])
        >
            <x-lucide-sliders-horizontal class="w-3 h-3" />
            <span>Format: All</span>
        </a>

        @foreach ([
            'blitz' => ['label' => 'Blitz', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
            'rapid' => ['label' => 'Rapid', 'icon' => 'clock', 'iconClass' => 'text-primary'],
            'bullet' => ['label' => 'Bullet', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
            'classic' => ['label' => 'Classical', 'icon' => 'crown', 'iconClass' => 'text-primary'],
        ] as $formatKey => $meta)
            <a
                href="{{ $buildUrl(['format' => $formatKey, 'page' => 1]) }}"
                data-format-filter="{{ $formatKey }}"
                @class([
                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg font-label text-[11px] whitespace-nowrap transition-colors shrink-0',
                    'bg-primary/15 border border-primary/35 text-primary font-semibold' => $activeFormat === $formatKey,
                    'bg-surface border border-border-subtle text-neutral-300 hover:text-white font-medium' => $activeFormat !== $formatKey,
                ])
            >
                <x-dynamic-component :component="'lucide-' . $meta['icon']" class="w-3 h-3 {{ $meta['iconClass'] }}" />
                <span>{{ $meta['label'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Match Cards -->
    <div class="flex flex-col gap-3">
        @forelse ($matches as $match)
            <article
                data-match-card
                data-result="{{ $match['result'] }}"
                data-format="{{ $match['format'] }}"
                class="relative overflow-hidden bg-surface border border-border-subtle rounded-2xl p-3.5 pl-4 flex flex-col gap-3 hover:border-border transition-all"
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
                            </div>
                            <span class="font-body text-[11px] text-neutral-400 truncate">
                                {{ $match['opening'] }} • {{ $match['meta'] }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-0.5 shrink-0">
                        <span class="px-2 py-0.5 rounded-md border font-label text-[10px] font-bold {{ $match['statusClass'] }}">
                            {{ $match['statusLabel'] }}
                        </span>
                        <span class="font-label text-[10px] text-neutral-400">
                            You {{ $match['playerRating'] }}
                        </span>
                    </div>
                </div>

                <!-- Bottom Row: Pending Engine Status & External Game Link -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-border-subtle/70">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-canvas/80 border border-border-subtle font-label text-[10px] text-neutral-400">
                        <x-lucide-cpu class="w-3.5 h-3.5 text-neutral-500" />
                        <span>Engine Pending</span>
                    </span>

                    @if (! empty($match['url']))
                        <a
                            href="{{ $match['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-canvas/80 border border-border hover:border-border-strong hover:bg-surface-elevated font-headline text-xs font-semibold text-neutral-200 hover:text-white transition-colors shrink-0"
                        >
                            <x-lucide-external-link class="w-3.5 h-3.5" />
                            <span>View Game</span>
                        </a>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-surface border border-border-subtle rounded-2xl p-6 text-center text-xs text-neutral-400 font-body" data-match-empty>
                No matches found for the selected filter.
            </div>
        @endforelse
    </div>

    <!-- Pagination Bar -->
    @if ($totalItems > 0)
        <div
            class="bg-surface border border-border-subtle rounded-xl px-3 py-2.5 flex items-center justify-between gap-2"
            data-match-pagination
        >
            <span class="font-label text-[11px] text-neutral-400">
                Showing <strong class="text-white">{{ $from }}–{{ $to }}</strong> of {{ number_format($totalItems) }}
            </span>

            <div class="inline-flex items-center gap-1.5">
                @if ($hasPrev)
                    <a
                        href="{{ $buildUrl(['page' => $currentPage - 1]) }}"
                        data-page-prev
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-canvas border border-border-subtle hover:border-border text-neutral-200 hover:text-white font-label text-[11px] font-semibold transition-colors"
                    >
                        <x-lucide-chevron-left class="w-3.5 h-3.5" />
                        <span>Prev</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-canvas/40 border border-border-subtle/50 text-neutral-600 font-label text-[11px] font-semibold select-none">
                        <x-lucide-chevron-left class="w-3.5 h-3.5" />
                        <span>Prev</span>
                    </span>
                @endif

                <span class="px-2 font-label text-[11px] font-semibold text-neutral-300" data-page-indicator>
                    {{ $currentPage }} / {{ $totalPages }}
                </span>

                @if ($hasNext)
                    <a
                        href="{{ $buildUrl(['page' => $currentPage + 1]) }}"
                        data-page-next
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-canvas border border-border-subtle hover:border-border text-neutral-200 hover:text-white font-label text-[11px] font-semibold transition-colors"
                    >
                        <span>Next</span>
                        <x-lucide-chevron-right class="w-3.5 h-3.5" />
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-canvas/40 border border-border-subtle/50 text-neutral-600 font-label text-[11px] font-semibold select-none">
                        <span>Next</span>
                        <x-lucide-chevron-right class="w-3.5 h-3.5" />
                    </span>
                @endif
            </div>
        </div>
    @endif
</section>
