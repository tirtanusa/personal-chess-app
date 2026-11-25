@props([
    'key',
    'title',
    'badge',
    'description',
    'icon' => null,
    'symbol' => null,
    'iconBoxClass' => 'bg-primary/15 text-primary border-primary/25',
    'badgeClass' => 'bg-primary/15 text-primary',
    'youPillClass' => 'bg-primary/20 text-primary',
    'defaultYou' => 0,
    'defaultOpp' => 0,
])

<div class="bg-canvas/80 border border-border-subtle rounded-xl px-2.5 py-2 flex items-center justify-between gap-2.5">
    <div class="flex items-center gap-2.5 min-w-0">
        <!-- Left Icon / Symbol Square -->
        <span class="w-7 h-7 rounded-lg border flex items-center justify-center shrink-0 {{ $iconBoxClass }}">
            @if ($icon)
                <x-dynamic-component :component="'lucide-' . $icon" class="w-3.5 h-3.5" />
            @else
                <span class="font-label text-[11px] font-bold leading-none">{{ $symbol }}</span>
            @endif
        </span>

        <!-- Title, Badge & Description -->
        <div class="flex flex-col min-w-0">
            <div class="flex items-center gap-1.5">
                <span class="font-headline text-xs sm:text-[13px] font-bold text-white leading-tight">
                    {{ $title }}
                </span>
                <span class="px-1.5 py-0.5 rounded font-label text-[9px] font-bold tracking-wide leading-none {{ $badgeClass }}">
                    {{ $badge }}
                </span>
            </div>
            <span class="font-body text-[10px] sm:text-[11px] text-neutral-400 truncate leading-snug">
                {{ $description }}
            </span>
        </div>
    </div>

    <!-- You vs Opponent Count Pills -->
    <div class="flex items-center gap-1.5 shrink-0 font-label text-[11px] font-bold">
        <span
            data-stat-you="{{ $key }}"
            class="min-w-[24px] h-5 px-1.5 rounded-md flex items-center justify-center {{ $youPillClass }}"
        >
            {{ $defaultYou }}
        </span>
        <span class="text-neutral-600 text-[11px] font-normal">/</span>
        <span
            data-stat-opp="{{ $key }}"
            class="min-w-[24px] h-5 px-1.5 rounded-md bg-surface-elevated text-neutral-300 flex items-center justify-center"
        >
            {{ $defaultOpp }}
        </span>
    </div>
</div>
