@props([
    'name' => 'Alexey V.',
    'flag' => '🇷🇴',
    'streak' => 5,
    'form' => ['W', 'W', 'D', 'W', 'W'],
])

@php
    $ratings = [
        [
            'label' => 'RAPID',
            'value' => '1842',
            'sub' => '▲ +18',
            'subClass' => 'text-primary font-semibold',
            'icon' => 'clock',
            'iconClass' => 'text-primary',
        ],
        [
            'label' => 'BLITZ',
            'value' => '1720',
            'sub' => 'Top 4.2%',
            'subClass' => 'text-neutral-400',
            'icon' => 'zap',
            'iconClass' => 'text-secondary',
        ],
        [
            'label' => 'BULLET',
            'value' => '1645',
            'sub' => 'Peak 1690',
            'subClass' => 'text-neutral-400',
            'icon' => 'gauge',
            'iconClass' => 'text-tertiary-300',
        ],
        [
            'label' => 'TACTICS',
            'value' => '2190',
            'sub' => 'Tier Elite',
            'subClass' => 'text-secondary font-medium',
            'icon' => 'brain',
            'iconClass' => 'text-secondary',
        ],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4">
    <!-- Top Row: Avatar, Name & Recent Form -->
    <div class="flex items-center justify-between gap-3">
        <!-- Player Identity -->
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-11 h-11 rounded-xl bg-surface-elevated border border-border flex items-center justify-center shrink-0 overflow-hidden">
                <x-lucide-user class="w-5 h-5 text-primary" />
            </div>

            <div class="flex items-center gap-1.5 min-w-0">
                <h2 class="font-headline text-base font-bold text-white truncate">
                    {{ $name }}
                </h2>
                <span class="text-sm leading-none shrink-0" aria-hidden="true">{{ $flag }}</span>
            </div>
        </div>

        <!-- Form Pills & Unbeaten Streak -->
        <div class="flex flex-col items-end gap-1.5 shrink-0">
            <div class="flex items-center gap-1">
                @foreach ($form as $index => $result)
                    @php
                        $isLatestWin = $loop->last && $result === 'W';
                    @endphp
                    <span @class([
                        'w-5 h-5 rounded-full font-label text-[10px] font-bold flex items-center justify-center',
                        'bg-primary-200 text-neutral-950 shadow-[0_0_10px_rgba(76,213,157,0.4)]' => $isLatestWin,
                        'bg-primary/15 text-primary border border-primary/25' => ! $isLatestWin && $result === 'W',
                        'bg-surface-elevated text-neutral-400 border border-border-subtle' => $result === 'D',
                        'bg-danger/15 text-danger-400 border border-danger/25' => $result === 'L',
                    ])>
                        {{ $result }}
                    </span>
                @endforeach
            </div>

            <div class="inline-flex items-center gap-1 text-primary">
                <x-lucide-flame class="w-3 h-3" />
                <span class="font-body text-[11px] font-semibold">{{ $streak }} Unbeaten</span>
            </div>
        </div>
    </div>

    <!-- Bottom Row: 4 Rating Tiles -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($ratings as $stat)
            <div class="bg-canvas/80 border border-border-subtle rounded-xl p-2.5 flex flex-col gap-1">
                <div class="flex items-center justify-between gap-1">
                    <span class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-400">
                        {{ $stat['label'] }}
                    </span>
                    <x-dynamic-component :component="'lucide-' . $stat['icon']" class="w-3.5 h-3.5 {{ $stat['iconClass'] }}" />
                </div>

                <div class="font-headline text-lg font-bold text-white leading-tight">
                    {{ $stat['value'] }}
                </div>

                <div class="font-label text-[10px] {{ $stat['subClass'] }}">
                    {{ $stat['sub'] }}
                </div>
            </div>
        @endforeach
    </div>
</section>
