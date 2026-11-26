@props([
    'activeMode' => 'blitz',
    'heatmap' => null,
    'heatmapsByMode' => null,
])

@php
    $modeLabels = [
        'blitz' => 'Blitz',
        'rapid' => 'Rapid',
        'bullet' => 'Bullet',
        'classic' => 'Classical',
    ];

    $buildEmptyGrid = function (): array {
        $today = \Carbon\CarbonImmutable::today();
        $startDate = $today->startOfWeek(\Carbon\CarbonInterface::SUNDAY)->subWeeks(52);
        $weeks = [];
        $monthLabels = [];
        $lastMonthIndex = -5;

        for ($w = 0; $w < 53; $w++) {
            $weekStart = $startDate->addWeeks($w);
            $weekDays = [];
            $monthLabelForWeek = '';

            for ($d = 0; $d < 7; $d++) {
                $cellDate = $weekStart->addDays($d);
                $isFuture = $cellDate->isAfter($today);

                if (! $isFuture && $cellDate->day === 1 && ($w - $lastMonthIndex) >= 3) {
                    $monthLabelForWeek = $cellDate->format('M');
                    $lastMonthIndex = $w;
                } elseif ($w === 0 && $d === 0) {
                    $monthLabelForWeek = $cellDate->format('M');
                    $lastMonthIndex = 0;
                }

                $weekDays[] = $isFuture
                    ? ['future' => true]
                    : [
                        'future' => false,
                        'date' => $cellDate->format('M j, Y'),
                        'games' => 0,
                        'level' => 0,
                    ];
            }

            $weeks[] = $weekDays;
            $monthLabels[] = $monthLabelForWeek;
        }

        return [
            'currentStreak' => 0,
            'totalGamesThisYear' => 0,
            'winrate' => '—',
            'mostActiveLabel' => 'No activity yet',
            'weeks' => $weeks,
            'monthLabels' => $monthLabels,
        ];
    };

    $fallbackGrid = $heatmap ?? $buildEmptyGrid();
    $panels = $heatmapsByMode ?? [
        'blitz' => $fallbackGrid,
        'rapid' => $buildEmptyGrid(),
        'bullet' => $buildEmptyGrid(),
        'classic' => $buildEmptyGrid(),
    ];

    $intensityClasses = [
        0 => 'bg-surface-elevated/80',
        1 => 'bg-primary-900',
        2 => 'bg-primary-700',
        3 => 'bg-primary-500',
        4 => 'bg-primary-200',
    ];

    $dayNames = [
        0 => '',
        1 => 'Mon',
        2 => '',
        3 => 'Wed',
        4 => '',
        5 => 'Fri',
        6 => '',
    ];
@endphp

<section
    class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4"
    data-mode-section
>
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-lucide-flame class="w-4 h-4 text-secondary" />
            <h3 class="font-headline text-base font-bold text-white">Activity Streak &amp; Heatmap</h3>
            <span
                class="px-2 py-0.5 rounded-md bg-primary/10 border border-primary/25 font-label text-[10px] font-semibold text-primary"
                data-active-mode-badge
            >
                {{ $modeLabels[$activeMode] ?? 'Blitz' }}
            </span>
        </div>
        <span class="font-label text-[10px] text-neutral-500">1 Year</span>
    </div>

    @foreach ($modeLabels as $modeKey => $modeLabel)
        @php
            $panel = $panels[$modeKey] ?? $fallbackGrid;
            $weeks = $panel['weeks'];
            $monthLabels = $panel['monthLabels'];
            $currentStreak = $panel['currentStreak'];
            $totalGamesThisYear = $panel['totalGamesThisYear'];
            $winrate = $panel['winrate'];
            $mostActiveLabel = $panel['mostActiveLabel'];
            $isActive = $modeKey === $activeMode;
        @endphp

        <div
            data-mode-panel="{{ $modeKey }}"
            data-mode-label="{{ $modeLabel }}"
            @class(['flex flex-col gap-4', 'hidden' => ! $isActive])
        >
            <!-- 3 Summary KPI Tiles -->
            <div class="grid grid-cols-3 gap-2 sm:gap-2.5">
                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 flex flex-col justify-between gap-2">
                    <span class="font-label text-[9px] font-semibold leading-tight tracking-normal sm:tracking-wider uppercase text-neutral-400">
                        Current Streak
                    </span>
                    <div class="flex items-center gap-1 font-headline text-sm sm:text-base font-bold text-white leading-none whitespace-nowrap">
                        <x-lucide-flame class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-secondary shrink-0" />
                        <span>{{ $currentStreak }} {{ $currentStreak === 1 ? 'Day' : 'Days' }}</span>
                    </div>
                </div>

                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 flex flex-col justify-between gap-2">
                    <span class="font-label text-[9px] font-semibold leading-tight tracking-normal sm:tracking-wider uppercase text-neutral-400">
                        This Year
                    </span>
                    <div class="font-headline text-sm sm:text-base font-bold text-white leading-none whitespace-nowrap">
                        {{ number_format($totalGamesThisYear) }} <span class="text-[11px] sm:text-xs font-normal text-neutral-300">Games</span>
                    </div>
                </div>

                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 flex flex-col justify-between gap-2">
                    <span class="font-label text-[9px] font-semibold leading-tight tracking-normal sm:tracking-wider uppercase text-neutral-400">
                        Winrate
                    </span>
                    <div class="font-headline text-sm sm:text-base font-bold text-primary leading-none whitespace-nowrap">
                        {{ $winrate }}
                    </div>
                </div>
            </div>

            <!-- GitHub-Style 1-Year Heatmap -->
            <div class="relative w-full" data-heatmap-wrapper>
                <div class="w-full overflow-x-auto no-scrollbar pb-1" data-heatmap-scroll>
                    <table class="border-separate border-spacing-[3px] w-max pr-3 select-none">
                        <thead>
                            <tr>
                                <th class="sticky left-0 z-10 bg-surface w-7 p-0 h-4"></th>
                                @foreach ($monthLabels as $month)
                                    <th class="w-[10px] min-w-[10px] max-w-[10px] h-4 p-0 text-left font-label text-[9px] font-normal text-neutral-400 relative">
                                        @if ($month !== '')
                                            <span class="absolute bottom-0.5 left-0 whitespace-nowrap leading-none">{{ $month }}</span>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for ($d = 0; $d < 7; $d++)
                                <tr class="h-[10px]">
                                    <th class="sticky left-0 z-10 bg-surface w-7 h-[10px] p-0 pr-2 text-left align-middle font-label text-[9px] font-normal leading-none text-neutral-500">
                                        {{ $dayNames[$d] }}
                                    </th>

                                    @for ($w = 0; $w < 53; $w++)
                                        @php $day = $weeks[$w][$d]; @endphp
                                        <td class="w-[10px] h-[10px] p-0 leading-none">
                                            @if (! $day['future'])
                                                <button
                                                    type="button"
                                                    data-heatmap-cell
                                                    data-games="{{ $day['games'] }}"
                                                    data-date="{{ $day['date'] }}"
                                                    aria-label="{{ $day['games'] }} games on {{ $day['date'] }}"
                                                    class="block w-[10px] h-[10px] rounded-[2px] {{ $intensityClasses[$day['level']] }} hover:ring-1 hover:ring-white/75 focus:outline-none focus:ring-1 focus:ring-primary-200 transition-shadow cursor-pointer"
                                                ></button>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- Floating GitHub-Style Tooltip -->
                <div
                    data-heatmap-tooltip
                    class="pointer-events-none absolute z-30 hidden px-2.5 py-1.5 rounded-lg bg-neutral-950 border border-border-strong text-[11px] font-body text-neutral-200 shadow-2xl whitespace-nowrap"
                ></div>
            </div>

            <!-- Footer Legend -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-border-subtle">
                <div class="flex items-center gap-1.5 text-[11px] text-neutral-400 font-body">
                    <x-lucide-calendar class="w-3.5 h-3.5 text-primary shrink-0" />
                    <span>Most Active: <strong class="text-white font-semibold">{{ $mostActiveLabel }}</strong></span>
                </div>

                <div class="flex items-center gap-1 font-label text-[9px] text-neutral-500 shrink-0">
                    <span>Less</span>
                    @foreach ($intensityClasses as $cls)
                        <span class="w-2.5 h-2.5 rounded-[2px] {{ $cls }}"></span>
                    @endforeach
                    <span>More</span>
                </div>
            </div>
        </div>
    @endforeach
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-heatmap-wrapper]').forEach((wrapper) => {
                    const scroller = wrapper.querySelector('[data-heatmap-scroll]');
                    const tooltip = wrapper.querySelector('[data-heatmap-tooltip]');
                    if (!scroller || !tooltip) return;

                    scroller.scrollLeft = scroller.scrollWidth;

                    const showTooltip = (cell) => {
                        const games = parseInt(cell.getAttribute('data-games'), 10);
                        const date = cell.getAttribute('data-date') || '';
                        const countLabel = games === 0
                            ? 'No games'
                            : `${games} ${games === 1 ? 'game' : 'games'}`;

                        const strong = document.createElement('strong');
                        strong.className = 'text-white font-semibold';
                        strong.textContent = countLabel;

                        const dateSpan = document.createElement('span');
                        dateSpan.className = 'text-neutral-400';
                        dateSpan.textContent = ` on ${date}`;

                        tooltip.replaceChildren(strong, dateSpan);
                        tooltip.classList.remove('hidden');

                        const wrapRect = wrapper.getBoundingClientRect();
                        const cellRect = cell.getBoundingClientRect();
                        const tipWidth = tooltip.offsetWidth;
                        const tipHeight = tooltip.offsetHeight;

                        let left = cellRect.left - wrapRect.left + (cellRect.width / 2) - (tipWidth / 2);
                        left = Math.max(4, Math.min(left, wrapRect.width - tipWidth - 4));

                        let top = cellRect.top - wrapRect.top - tipHeight - 8;
                        if (top < -wrapRect.top + 4) {
                            top = cellRect.bottom - wrapRect.top + 8;
                        }

                        tooltip.style.left = `${left}px`;
                        tooltip.style.top = `${top}px`;
                    };

                    const hideTooltip = () => {
                        tooltip.classList.add('hidden');
                    };

                    wrapper.querySelectorAll('[data-heatmap-cell]').forEach((cell) => {
                        cell.addEventListener('mouseenter', () => showTooltip(cell));
                        cell.addEventListener('mouseleave', hideTooltip);
                        cell.addEventListener('focus', () => showTooltip(cell));
                        cell.addEventListener('blur', hideTooltip);
                        cell.addEventListener('click', (e) => {
                            e.stopPropagation();
                            showTooltip(cell);
                        });
                    });

                    scroller.addEventListener('scroll', hideTooltip, { passive: true });
                    document.addEventListener('click', hideTooltip);
                });
            });
        </script>
    @endpush
@endonce
