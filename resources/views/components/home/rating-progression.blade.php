<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4" data-rating-chart>
    <!-- Header & Period Filter -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-lucide-trending-up class="w-4 h-4 text-primary" />
            <h3 class="font-headline text-base font-bold text-white">Rating Progression</h3>
        </div>

        <div class="inline-flex items-center p-0.5 rounded-lg bg-canvas border border-border-subtle">
            @foreach (['7D', '30D', '90D'] as $period)
                <button
                    type="button"
                    data-period-btn="{{ $period }}"
                    @class([
                        'px-2 py-1 rounded-md font-label text-[10px] font-bold transition-colors cursor-pointer',
                        'bg-primary-200 text-neutral-950' => $period === '30D',
                        'text-neutral-400 hover:text-white' => $period !== '30D',
                    ])
                >
                    {{ $period }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Rating & Peak Row -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-baseline gap-2">
            <span class="font-headline text-2xl font-bold text-white">1842</span>
            <span class="font-label text-[11px] font-semibold text-primary" data-rating-delta>
                ↑ +38 ELO (30D)
            </span>
        </div>

        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-canvas border border-border-subtle">
            <x-lucide-award class="w-3.5 h-3.5 text-secondary" />
            <span class="font-label text-[11px] text-neutral-300">Peak: <strong class="text-white">1842</strong></span>
        </div>
    </div>

    <!-- Glowing SVG Chart -->
    <div class="relative w-full pt-1">
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
                <text x="0" y="25">1840</text>

                <line x1="36" y1="56" x2="352" y2="56" stroke="#2E2E3A" stroke-dasharray="3 3" stroke-width="1" />
                <text x="0" y="59">1820</text>

                <line x1="36" y1="90" x2="352" y2="90" stroke="#2E2E3A" stroke-dasharray="3 3" stroke-width="1" />
                <text x="0" y="93">1800</text>
            </g>

            <!-- Area Fill -->
            <path
                data-chart-area
                d="M 40 96 C 72 96, 86 78, 114 78 C 142 78, 152 84, 178 72 C 204 60, 224 44, 246 34 C 272 22, 294 56, 316 52 C 334 48, 344 26, 352 16 L 352 104 L 40 104 Z"
                fill="url(#ratingAreaGradient)"
            />

            <!-- Glowing Line -->
            <path
                data-chart-line
                d="M 40 96 C 72 96, 86 78, 114 78 C 142 78, 152 84, 178 72 C 204 60, 224 44, 246 34 C 272 22, 294 56, 316 52 C 334 48, 344 26, 352 16"
                fill="none"
                stroke="#4CD59D"
                stroke-width="2.5"
                stroke-linecap="round"
                filter="url(#ratingGlow)"
            />

            <!-- Data Point Dots -->
            <g data-chart-dots>
                <circle cx="104" cy="79" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
                <circle cx="164" cy="79" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
                <circle cx="230" cy="43" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
                <circle cx="292" cy="40" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
                <circle cx="352" cy="16" r="4" fill="#6BF1B6" stroke="#10B981" stroke-width="2" />
            </g>
        </svg>

        <!-- X-Axis Date Labels -->
        <div class="flex items-center justify-between pl-9 pt-1 font-label text-[10px] text-neutral-500">
            <span>Nov 1</span>
            <span>Nov 8</span>
            <span>Nov 15</span>
            <span>Nov 22</span>
            <span class="text-primary font-semibold">Today</span>
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.querySelector('[data-rating-chart]');
                if (!container) return;

                const deltaEl = container.querySelector('[data-rating-delta]');
                const areaEl = container.querySelector('[data-chart-area]');
                const lineEl = container.querySelector('[data-chart-line]');
                const buttons = container.querySelectorAll('[data-period-btn]');

                const datasets = {
                    '7D': {
                        delta: '↑ +14 ELO (7D)',
                        line: 'M 40 76 C 85 74, 125 60, 168 62 C 212 64, 252 44, 296 36 C 324 30, 340 22, 352 16',
                    },
                    '30D': {
                        delta: '↑ +38 ELO (30D)',
                        line: 'M 40 96 C 72 96, 86 78, 114 78 C 142 78, 152 84, 178 72 C 204 60, 224 44, 246 34 C 272 22, 294 56, 316 52 C 334 48, 344 26, 352 16',
                    },
                    '90D': {
                        delta: '↑ +96 ELO (90D)',
                        line: 'M 40 100 C 80 94, 110 88, 150 76 C 190 64, 220 70, 260 48 C 295 30, 325 32, 352 16',
                    },
                };

                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const period = btn.getAttribute('data-period-btn');
                        const data = datasets[period];
                        if (!data) return;

                        buttons.forEach((b) => {
                            const active = b === btn;
                            b.classList.toggle('bg-primary-200', active);
                            b.classList.toggle('text-neutral-950', active);
                            b.classList.toggle('text-neutral-400', !active);
                        });

                        deltaEl.textContent = data.delta;
                        lineEl.setAttribute('d', data.line);
                        areaEl.setAttribute('d', `${data.line} L 352 104 L 40 104 Z`);
                    });
                });
            });
        </script>
    @endpush
@endonce
