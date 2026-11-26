@props([
    'weekly' => null,
])

@php
    $data = $weekly ?? [
        'modeLabel' => 'Blitz',
        'netLabel' => 'No games (7D)',
        'netClass' => 'text-neutral-500',
        'days' => collect(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'])
            ->map(fn (string $label): array => ['label' => $label, 'active' => false, 'isToday' => false])
            ->all(),
        'linePath' => 'M 20 56 L 342 56',
        'areaPath' => 'M 20 56 L 342 56 L 342 84 L 20 84 Z',
        'dots' => [],
        'hasCurve' => false,
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-lucide-trending-up class="w-4 h-4 text-primary" />
            <h3 class="font-headline text-base font-bold text-white">Last 7 Days Performance</h3>
        </div>
        <span class="font-label text-xs font-bold {{ $data['netClass'] }}">
            {{ $data['netLabel'] }}
        </span>
    </div>

    <!-- 7-Day Glowing Curve -->
    <div class="w-full pt-1">
        <svg viewBox="0 0 360 96" class="w-full h-24 overflow-visible" aria-label="Last 7 Days Performance Chart">
            <defs>
                <linearGradient id="weeklyAreaGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.28" />
                    <stop offset="100%" stop-color="#10B981" stop-opacity="0" />
                </linearGradient>
                <filter id="weeklyGlow" x="-10%" y="-20%" width="120%" height="150%">
                    <feGaussianBlur stdDeviation="3" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
            </defs>

            <!-- Baseline -->
            <line x1="16" y1="84" x2="344" y2="84" stroke="#262630" stroke-width="1" />

            <!-- Area Fill -->
            <path
                d="{{ $data['areaPath'] }}"
                fill="url(#weeklyAreaGradient)"
                @class(['opacity-0' => ! $data['hasCurve']])
            />

            <!-- Glowing Line -->
            <path
                d="{{ $data['linePath'] }}"
                fill="none"
                stroke="#4CD59D"
                stroke-width="2.5"
                stroke-linecap="round"
                @if ($data['hasCurve']) filter="url(#weeklyGlow)" @else stroke-dasharray="4 4" opacity="0.35" @endif
            />

            <!-- Day Nodes -->
            @foreach ($data['dots'] as $dot)
                <circle
                    cx="{{ $dot['cx'] }}"
                    cy="{{ $dot['cy'] }}"
                    r="{{ $dot['isHighlight'] ? '4.5' : '3.5' }}"
                    fill="{{ $dot['isHighlight'] ? '#6BF1B6' : '#0F1117' }}"
                    stroke="{{ $dot['stroke'] }}"
                    stroke-width="2"
                >
                    @if (! empty($dot['title']))
                        <title>{{ $dot['title'] }}</title>
                    @endif
                </circle>
            @endforeach
        </svg>

        <!-- Day Axis Labels -->
        <div class="grid grid-cols-7 text-center pt-1 font-label text-[10px]">
            @foreach ($data['days'] as $day)
                <span @class([
                    'text-primary font-bold' => $day['active'] || $day['isToday'],
                    'text-neutral-500' => ! $day['active'] && ! $day['isToday'],
                ])>
                    {{ $day['label'] }}
                </span>
            @endforeach
        </div>
    </div>
</section>
