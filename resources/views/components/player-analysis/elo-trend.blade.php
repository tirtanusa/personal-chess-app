@props([
    'net' => '+24 Net',
    'matches' => '18 Matches',
])

@php
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Today'];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-start justify-between gap-2">
        <div class="flex flex-col gap-0.5">
            <h3 class="font-headline text-base font-bold text-white">ELO Rating Trend</h3>
            <span class="font-body text-[11px] text-neutral-400">Last 7 Days Activity</span>
        </div>

        <div class="flex flex-col items-end gap-0.5">
            <span class="font-label text-xs font-bold text-primary">{{ $net }}</span>
            <span class="font-body text-[11px] text-neutral-400">{{ $matches }}</span>
        </div>
    </div>

    <!-- Trend Line SVG -->
    <div class="w-full pt-1">
        <svg viewBox="0 0 360 96" class="w-full h-24 overflow-visible" aria-label="ELO Rating Trend Chart">
            <defs>
                <linearGradient id="playerTrendGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.28" />
                    <stop offset="100%" stop-color="#10B981" stop-opacity="0" />
                </linearGradient>
                <filter id="playerTrendGlow" x="-10%" y="-20%" width="120%" height="150%">
                    <feGaussianBlur stdDeviation="3" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
            </defs>

            <!-- Dashed Guide Line -->
            <line x1="16" y1="78" x2="344" y2="78" stroke="#262630" stroke-dasharray="3 3" stroke-width="1" />

            <!-- Area Fill -->
            <path
                d="M 20 68 L 74 52 L 128 58 L 182 40 L 236 44 L 290 26 L 342 16 L 342 82 L 20 82 Z"
                fill="url(#playerTrendGradient)"
            />

            <!-- Glowing Trend Line -->
            <path
                d="M 20 68 L 74 52 L 128 58 L 182 40 L 236 44 L 290 26 L 342 16"
                fill="none"
                stroke="#4CD59D"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                filter="url(#playerTrendGlow)"
            />

            <!-- Nodes -->
            <circle cx="20" cy="68" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="74" cy="52" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="128" cy="58" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="182" cy="40" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="236" cy="44" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="290" cy="26" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="342" cy="16" r="4" fill="#6BF1B6" stroke="#10B981" stroke-width="2" />
        </svg>

        <!-- Day Labels -->
        <div class="grid grid-cols-7 text-center pt-1 font-label text-[10px]">
            @foreach ($days as $day)
                <span @class([
                    'text-primary font-bold' => $loop->last,
                    'text-neutral-500' => ! $loop->last,
                ])>
                    {{ $day }}
                </span>
            @endforeach
        </div>
    </div>
</section>
