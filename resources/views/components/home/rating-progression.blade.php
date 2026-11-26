@props([
    'activeMode' => 'blitz',
    'byMode' => null,
])

@php
    $modeLabels = [
        'blitz' => 'Blitz',
        'rapid' => 'Rapid',
        'bullet' => 'Bullet',
        'classic' => 'Classical',
    ];

    $defaultGeometry = [
        'linePath' => 'M 40 56 L 352 56',
        'areaPath' => 'M 40 56 L 352 56 L 352 104 L 40 104 Z',
        'yLabels' => ['high' => '—', 'mid' => '—', 'low' => '—'],
        'xLabels' => ['start' => 'Start', 'mid' => 'Mid', 'end' => 'Today'],
        'dots' => [],
        'hasCurve' => false,
    ];

    $normalizedSeries = [];
    foreach ($modeLabels as $modeKey => $modeLabel) {
        $rawMode = $byMode[$modeKey] ?? [
            'mode' => $modeKey,
            'label' => $modeLabel,
            'rating' => '—',
            'peak' => '—',
            'deltaText' => 'No data yet',
            'deltaClass' => 'text-neutral-500',
        ];

        $periods = $rawMode['periods'] ?? null;
        if (! is_array($periods)) {
            $singlePeriod = array_merge($defaultGeometry, [
                'rating' => $rawMode['rating'] ?? '—',
                'peak' => $rawMode['peak'] ?? '—',
                'deltaText' => $rawMode['deltaText'] ?? 'No data yet',
                'deltaClass' => $rawMode['deltaClass'] ?? 'text-neutral-500',
            ]);
            $periods = [
                '7D' => $singlePeriod,
                '30D' => $singlePeriod,
                '90D' => $singlePeriod,
            ];
        }

        $normalizedSeries[$modeKey] = [
            'mode' => $modeKey,
            'label' => $rawMode['label'] ?? $modeLabel,
            'periods' => $periods,
        ];
    }

    $initialModeData = $normalizedSeries[$activeMode] ?? $normalizedSeries['blitz'];
    $initialPeriod = '30D';
    $initial = $initialModeData['periods'][$initialPeriod] ?? $defaultGeometry;
@endphp

<section
    class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4"
    data-rating-chart
    data-active-mode="{{ $activeMode }}"
    data-active-period="{{ $initialPeriod }}"
    data-mode-section
>
    <!-- Header & Period Filter -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-lucide-trending-up class="w-4 h-4 text-primary" />
            <h3 class="font-headline text-base font-bold text-white">Rating Progression</h3>
            <span
                class="px-2 py-0.5 rounded-md bg-primary/10 border border-primary/25 font-label text-[10px] font-semibold text-primary"
                data-active-mode-badge
            >
                {{ $initialModeData['label'] }}
            </span>
        </div>

        <div class="inline-flex items-center p-0.5 rounded-lg bg-canvas border border-border-subtle">
            @foreach (['7D', '30D', '90D'] as $period)
                <button
                    type="button"
                    data-period-btn="{{ $period }}"
                    @class([
                        'px-2 py-1 rounded-md font-label text-[10px] font-bold transition-colors cursor-pointer',
                        'bg-primary-200 text-neutral-950' => $period === $initialPeriod,
                        'text-neutral-400 hover:text-white' => $period !== $initialPeriod,
                    ])
                >
                    {{ $period }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Rating, Delta & Peak Row -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-baseline gap-2 min-w-0">
            <span class="font-headline text-2xl font-bold text-white" data-rating-current>
                {{ $initial['rating'] }}
            </span>
            <span class="font-label text-[11px] font-semibold truncate {{ $initial['deltaClass'] }}" data-rating-delta>
                {{ $initial['deltaText'] }}
            </span>
        </div>

        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-canvas border border-border-subtle shrink-0">
            <x-lucide-award class="w-3.5 h-3.5 text-secondary" />
            <span class="font-label text-[11px] text-neutral-300">Peak: <strong class="text-white" data-rating-peak>{{ $initial['peak'] }}</strong></span>
        </div>
    </div>

    <!-- Glowing SVG Chart -->
    <div class="relative w-full pt-1" data-rating-canvas>
        <svg viewBox="0 0 360 120" class="w-full h-32 overflow-visible" aria-label="Rating Progression Chart">
            <defs>
                <linearGradient id="ratingAreaGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.32" />
                    <stop offset="100%" stop-color="#10B981" stop-opacity="0" />
                </linearGradient>
                <filter id="ratingGlow" x="-10%" y="-20%" width="120%" height="150%">
                    <feGaussianBlur stdDeviation="3" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
            </defs>

            <!-- Horizontal Dashed Gridlines & Y Labels -->
            <g class="font-label text-[9px] fill-neutral-500">
                <line x1="36" y1="22" x2="352" y2="22" stroke="#2E2E3A" stroke-dasharray="3 3" stroke-width="1" />
                <text x="0" y="25" data-y-high>{{ $initial['yLabels']['high'] }}</text>

                <line x1="36" y1="56" x2="352" y2="56" stroke="#2E2E3A" stroke-dasharray="3 3" stroke-width="1" />
                <text x="0" y="59" data-y-mid>{{ $initial['yLabels']['mid'] }}</text>

                <line x1="36" y1="90" x2="352" y2="90" stroke="#2E2E3A" stroke-dasharray="3 3" stroke-width="1" />
                <text x="0" y="93" data-y-low>{{ $initial['yLabels']['low'] }}</text>
            </g>

            <!-- Area Fill -->
            <path
                data-chart-area
                d="{{ $initial['areaPath'] }}"
                fill="url(#ratingAreaGradient)"
                @class(['opacity-0' => ! $initial['hasCurve']])
            />

            <!-- Glowing Line -->
            <path
                data-chart-line
                d="{{ $initial['linePath'] }}"
                fill="none"
                stroke="#4CD59D"
                stroke-width="2.5"
                stroke-linecap="round"
                @if ($initial['hasCurve']) filter="url(#ratingGlow)" @else stroke-dasharray="4 4" opacity="0.35" @endif
            />

            <!-- Data Point Dots -->
            <g data-chart-dots>
                @foreach ($initial['dots'] as $dot)
                    @php
                        $isLast = $loop->last;
                    @endphp
                    <circle
                        cx="{{ $dot['x'] }}"
                        cy="{{ $dot['y'] }}"
                        r="{{ $isLast ? '4' : '3.5' }}"
                        fill="{{ $isLast ? '#6BF1B6' : '#0F1117' }}"
                        stroke="{{ $isLast ? '#10B981' : '#4CD59D' }}"
                        stroke-width="2"
                        class="cursor-pointer"
                        data-rating-dot
                        data-dot-label="{{ $dot['label'] }}"
                        data-dot-rating="{{ $dot['rating'] }}"
                    />
                @endforeach
            </g>
        </svg>

        <!-- Floating Chart Tooltip -->
        <div
            data-rating-tooltip
            class="pointer-events-none absolute z-30 hidden px-2.5 py-1.5 rounded-lg bg-neutral-950 border border-border-strong text-[11px] font-body text-neutral-200 shadow-2xl whitespace-nowrap"
        ></div>

        <!-- X-Axis Date Labels -->
        <div class="flex items-center justify-between pl-9 pt-1 font-label text-[10px] text-neutral-500">
            <span data-x-start>{{ $initial['xLabels']['start'] }}</span>
            <span data-x-mid>{{ $initial['xLabels']['mid'] }}</span>
            <span data-x-end class="text-primary font-semibold">{{ $initial['xLabels']['end'] }}</span>
        </div>
    </div>

    <script type="application/json" data-rating-series>@json($normalizedSeries)</script>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-rating-chart]').forEach((container) => {
                    const seriesEl = container.querySelector('[data-rating-series]');
                    if (!seriesEl) return;

                    let seriesData = {};
                    try {
                        seriesData = JSON.parse(seriesEl.textContent || '{}');
                    } catch {
                        return;
                    }

                    const modeBadge = container.querySelector('[data-active-mode-badge]');
                    const currentEl = container.querySelector('[data-rating-current]');
                    const deltaEl = container.querySelector('[data-rating-delta]');
                    const peakEl = container.querySelector('[data-rating-peak]');
                    const yHighEl = container.querySelector('[data-y-high]');
                    const yMidEl = container.querySelector('[data-y-mid]');
                    const yLowEl = container.querySelector('[data-y-low]');
                    const xStartEl = container.querySelector('[data-x-start]');
                    const xMidEl = container.querySelector('[data-x-mid]');
                    const xEndEl = container.querySelector('[data-x-end]');
                    const areaEl = container.querySelector('[data-chart-area]');
                    const lineEl = container.querySelector('[data-chart-line]');
                    const dotsGroup = container.querySelector('[data-chart-dots]');
                    const tooltip = container.querySelector('[data-rating-tooltip]');
                    const canvas = container.querySelector('[data-rating-canvas]');
                    const periodButtons = container.querySelectorAll('[data-period-btn]');

                    const hideTooltip = () => {
                        if (tooltip) tooltip.classList.add('hidden');
                    };

                    const showDotTooltip = (dot) => {
                        if (!tooltip || !canvas) return;

                        const label = dot.getAttribute('data-dot-label') || '';
                        const rating = dot.getAttribute('data-dot-rating') || '';
                        if (!rating) return;

                        const strong = document.createElement('strong');
                        strong.className = 'text-white font-semibold';
                        strong.textContent = `${rating} ELO`;

                        const dateSpan = document.createElement('span');
                        dateSpan.className = 'text-neutral-400';
                        dateSpan.textContent = ` on ${label}`;

                        tooltip.replaceChildren(strong, dateSpan);
                        tooltip.classList.remove('hidden');

                        const wrapRect = canvas.getBoundingClientRect();
                        const dotRect = dot.getBoundingClientRect();
                        const tipWidth = tooltip.offsetWidth;
                        const tipHeight = tooltip.offsetHeight;

                        let left = dotRect.left - wrapRect.left + (dotRect.width / 2) - (tipWidth / 2);
                        left = Math.max(4, Math.min(left, wrapRect.width - tipWidth - 4));

                        let top = dotRect.top - wrapRect.top - tipHeight - 8;
                        if (top < 0) top = dotRect.bottom - wrapRect.top + 8;

                        tooltip.style.left = `${left}px`;
                        tooltip.style.top = `${top}px`;
                    };

                    const bindDotTooltips = () => {
                        if (!dotsGroup) return;
                        dotsGroup.querySelectorAll('[data-rating-dot]').forEach((dot) => {
                            dot.addEventListener('mouseenter', () => showDotTooltip(dot));
                            dot.addEventListener('mouseleave', hideTooltip);
                            dot.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showDotTooltip(dot);
                            });
                        });
                    };

                    const renderChart = () => {
                        const mode = container.getAttribute('data-active-mode') || 'blitz';
                        const period = container.getAttribute('data-active-period') || '30D';
                        const modeEntry = seriesData[mode];
                        const periodEntry = modeEntry?.periods?.[period];
                        if (!modeEntry || !periodEntry) return;

                        hideTooltip();

                        if (modeBadge) modeBadge.textContent = modeEntry.label || mode;
                        if (currentEl) currentEl.textContent = periodEntry.rating ?? '—';
                        if (peakEl) peakEl.textContent = periodEntry.peak ?? '—';

                        if (deltaEl) {
                            deltaEl.textContent = periodEntry.deltaText ?? '';
                            deltaEl.className = `font-label text-[11px] font-semibold truncate ${periodEntry.deltaClass || 'text-neutral-400'}`;
                        }

                        if (yHighEl) yHighEl.textContent = periodEntry.yLabels?.high ?? '—';
                        if (yMidEl) yMidEl.textContent = periodEntry.yLabels?.mid ?? '—';
                        if (yLowEl) yLowEl.textContent = periodEntry.yLabels?.low ?? '—';

                        if (xStartEl) xStartEl.textContent = periodEntry.xLabels?.start ?? '';
                        if (xMidEl) xMidEl.textContent = periodEntry.xLabels?.mid ?? '';
                        if (xEndEl) xEndEl.textContent = periodEntry.xLabels?.end ?? 'Today';

                        if (areaEl) {
                            areaEl.setAttribute('d', periodEntry.areaPath || '');
                            areaEl.classList.toggle('opacity-0', !periodEntry.hasCurve);
                        }

                        if (lineEl) {
                            lineEl.setAttribute('d', periodEntry.linePath || '');
                            if (periodEntry.hasCurve) {
                                lineEl.setAttribute('filter', 'url(#ratingGlow)');
                                lineEl.removeAttribute('stroke-dasharray');
                                lineEl.removeAttribute('opacity');
                            } else {
                                lineEl.removeAttribute('filter');
                                lineEl.setAttribute('stroke-dasharray', '4 4');
                                lineEl.setAttribute('opacity', '0.35');
                            }
                        }

                        if (dotsGroup) {
                            const svgNs = 'http://www.w3.org/2000/svg';
                            const dots = Array.isArray(periodEntry.dots) ? periodEntry.dots : [];
                            const nodes = dots.map((dot, index) => {
                                const isLast = index === dots.length - 1;
                                const circle = document.createElementNS(svgNs, 'circle');
                                circle.setAttribute('cx', String(dot.x));
                                circle.setAttribute('cy', String(dot.y));
                                circle.setAttribute('r', isLast ? '4' : '3.5');
                                circle.setAttribute('fill', isLast ? '#6BF1B6' : '#0F1117');
                                circle.setAttribute('stroke', isLast ? '#10B981' : '#4CD59D');
                                circle.setAttribute('stroke-width', '2');
                                circle.setAttribute('class', 'cursor-pointer');
                                circle.setAttribute('data-rating-dot', '');
                                circle.setAttribute('data-dot-label', String(dot.label || ''));
                                circle.setAttribute('data-dot-rating', String(dot.rating || ''));
                                return circle;
                            });
                            dotsGroup.replaceChildren(...nodes);
                            bindDotTooltips();
                        }
                    };

                    periodButtons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const period = btn.getAttribute('data-period-btn');
                            if (!period) return;

                            container.setAttribute('data-active-period', period);

                            periodButtons.forEach((b) => {
                                const active = b === btn;
                                b.classList.toggle('bg-primary-200', active);
                                b.classList.toggle('text-neutral-950', active);
                                b.classList.toggle('text-neutral-400', !active);
                            });

                            renderChart();
                        });
                    });

                    document.addEventListener('home:mode-change', (event) => {
                        const mode = event.detail?.mode;
                        if (!mode) return;
                        container.setAttribute('data-active-mode', mode);
                        renderChart();
                    });

                    document.addEventListener('click', hideTooltip);
                    bindDotTooltips();
                });
            });
        </script>
    @endpush
@endonce
