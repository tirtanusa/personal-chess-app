@props([
    'name' => 'Alexey V.',
    'flag' => '🇮🇩',
    'streak' => 5,
    'memberSince' => 'Feb 2023',
    'totalGames' => '1,482',
    'winRate' => '64.2%',
    'wins' => 952,
    'draws' => 220,
    'losses' => 310,
])

@php
    $ratings = [
        [
            'label' => 'RAPID',
            'value' => '1842',
            'peak' => 'Peak 1865',
            'peakClass' => 'text-primary',
            'icon' => 'clock',
            'iconClass' => 'text-neutral-400',
        ],
        [
            'label' => 'BLITZ',
            'value' => '1720',
            'peak' => 'Peak 1740',
            'peakClass' => 'text-secondary',
            'icon' => 'zap',
            'iconClass' => 'text-secondary',
        ],
        [
            'label' => 'BULLET',
            'value' => '1645',
            'peak' => 'Peak 1660',
            'peakClass' => 'text-neutral-400',
            'icon' => 'rocket',
            'iconClass' => 'text-tertiary-300',
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4">
    <!-- Top Identity Row -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3.5 min-w-0">
            <!-- Avatar with Online Dot -->
            <div class="relative shrink-0">
                <div class="w-14 h-14 rounded-full p-0.5 bg-gradient-to-tr from-primary/50 to-border">
                    <div class="w-full h-full rounded-full bg-surface-elevated flex items-center justify-center overflow-hidden">
                        <x-lucide-user class="w-6 h-6 text-primary" />
                    </div>
                </div>
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-primary-200 border-2 border-surface"></span>
            </div>

            <!-- Name, Streak Pill, Member Meta -->
            <div class="flex flex-col gap-1.5 min-w-0">
                <div class="flex items-center gap-1.5">
                    <h2 class="font-headline text-lg font-bold text-white truncate leading-none">
                        {{ $name }}
                    </h2>
                    <span class="text-sm leading-none shrink-0" aria-hidden="true">{{ $flag }}</span>
                </div>

                <div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary/15 border border-secondary/30 font-label text-[11px] font-bold text-secondary">
                        <x-lucide-flame class="w-3 h-3 text-secondary fill-secondary" />
                        <span>{{ $streak }} Streak</span>
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[11px] text-neutral-400 font-body">
                    <span>Member since {{ $memberSince }}</span>
                    <span>•</span>
                    <span class="text-primary font-semibold">{{ $totalGames }} Games</span>
                </div>
            </div>
        </div>

        <!-- Share Button -->
        <button
            type="button"
            class="w-9 h-9 rounded-full bg-canvas/80 border border-border-subtle hover:border-border text-neutral-300 hover:text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer"
            aria-label="Share Profile"
        >
            <x-lucide-share-2 class="w-4 h-4" />
        </button>
    </div>

    <!-- Overall Performance Bar Card -->
    <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3 flex flex-col gap-2.5">
        <div class="flex items-center justify-between gap-2">
            <span class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
                Overall Performance
            </span>
            <span class="font-label text-xs font-bold text-primary">
                {{ $winRate }} Win Rate
            </span>
        </div>

        <!-- Segmented W/D/L Bar -->
        <div class="h-1.5 w-full rounded-full overflow-hidden flex gap-0.5 bg-surface-elevated">
            <div class="h-full bg-primary rounded-l-full" style="width: 64.2%"></div>
            <div class="h-full bg-secondary" style="width: 14.8%"></div>
            <div class="h-full bg-danger-400 rounded-r-full" style="width: 21.0%"></div>
        </div>

        <!-- W/D/L Counts -->
        <div class="flex items-center justify-between font-label text-[11px] text-neutral-300">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                <span>{{ $wins }} W</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                <span>{{ $draws }} D</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-danger-400"></span>
                <span>{{ $losses }} L</span>
            </span>
        </div>
    </div>

    <!-- 3 Mode Rating Tiles -->
    <div class="grid grid-cols-3 gap-2">
        @foreach ($ratings as $stat)
            <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3 flex flex-col items-center justify-between gap-1 text-center">
                <div class="inline-flex items-center gap-1">
                    <x-dynamic-component :component="'lucide-' . $stat['icon']" class="w-3.5 h-3.5 {{ $stat['iconClass'] }}" />
                    <span class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
                        {{ $stat['label'] }}
                    </span>
                </div>

                <div class="font-headline text-xl font-bold text-white leading-tight">
                    {{ $stat['value'] }}
                </div>

                <div class="font-label text-[10px] font-medium {{ $stat['peakClass'] }}">
                    {{ $stat['peak'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>
