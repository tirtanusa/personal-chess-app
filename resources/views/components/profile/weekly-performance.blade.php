@php
    $days = [
        ['label' => 'MON', 'active' => false],
        ['label' => 'TUE', 'active' => false],
        ['label' => 'WED', 'active' => false],
        ['label' => 'THU', 'active' => false],
        ['label' => 'FRI', 'active' => false],
        ['label' => 'SAT', 'active' => true],
        ['label' => 'SUN', 'active' => false],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <x-lucide-trending-up class="w-4 h-4 text-primary" />
            <h3 class="font-headline text-base font-bold text-white">Last 7 Days Performance</h3>
        </div>
        <span class="font-label text-xs font-bold text-primary">+38 ELO Net</span>
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
                d="M 20 56 C 42 54, 54 38, 74 38 C 94 38, 106 72, 128 72 C 150 72, 162 34, 182 34 C 202 34, 214 54, 236 54 C 258 54, 268 18, 290 18 C 312 18, 324 44, 342 44 L 342 84 L 20 84 Z"
                fill="url(#weeklyAreaGradient)"
            />

            <!-- Glowing Line -->
            <path
                d="M 20 56 C 42 54, 54 38, 74 38 C 94 38, 106 72, 128 72 C 150 72, 162 34, 182 34 C 202 34, 214 54, 236 54 C 258 54, 268 18, 290 18 C 312 18, 324 44, 342 44"
                fill="none"
                stroke="#4CD59D"
                stroke-width="2.5"
                stroke-linecap="round"
                filter="url(#weeklyGlow)"
            />

            <!-- Day Nodes -->
            <circle cx="20" cy="56" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="74" cy="38" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="128" cy="72" r="3.5" fill="#0F1117" stroke="#F43F5E" stroke-width="2" />
            <circle cx="182" cy="34" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
            <circle cx="236" cy="54" r="3.5" fill="#0F1117" stroke="#F59E0B" stroke-width="2" />
            <circle cx="290" cy="18" r="4.5" fill="#6BF1B6" stroke="#10B981" stroke-width="2" />
            <circle cx="342" cy="44" r="3.5" fill="#0F1117" stroke="#4CD59D" stroke-width="2" />
        </svg>

        <!-- Day Axis Labels -->
        <div class="grid grid-cols-7 text-center pt-1 font-label text-[10px]">
            @foreach ($days as $day)
                <span @class([
                    'text-primary font-bold' => $day['active'],
                    'text-neutral-500' => ! $day['active'],
                ])>
                    {{ $day['label'] }}
                </span>
            @endforeach
        </div>
    </div>
</section>
