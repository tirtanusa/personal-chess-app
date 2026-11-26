@props([
    'summary' => null,
])

@php
    $data = $summary ?? [
        'name' => 'Player',
        'username' => 'player',
        'platform' => null,
        'avatarUrl' => null,
        'flag' => '',
        'streak' => 0,
        'memberSince' => 'New',
        'totalGames' => '0',
        'winRate' => '—',
        'wins' => 0,
        'draws' => 0,
        'losses' => 0,
        'winPct' => 0.0,
        'drawPct' => 0.0,
        'lossPct' => 0.0,
        'ratings' => [
            ['label' => 'RAPID', 'value' => '—', 'peak' => 'Unrated', 'peakClass' => 'text-neutral-500', 'icon' => 'clock', 'iconClass' => 'text-neutral-400'],
            ['label' => 'BLITZ', 'value' => '—', 'peak' => 'Unrated', 'peakClass' => 'text-neutral-500', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
            ['label' => 'BULLET', 'value' => '—', 'peak' => 'Unrated', 'peakClass' => 'text-neutral-500', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
            ['label' => 'CLASSIC', 'value' => '—', 'peak' => 'Unrated', 'peakClass' => 'text-neutral-500', 'icon' => 'crown', 'iconClass' => 'text-primary'],
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
                        @if (! empty($data['avatarUrl']))
                            <img src="{{ $data['avatarUrl'] }}" alt="{{ $data['name'] }}" class="w-full h-full object-cover">
                        @else
                            <x-lucide-user class="w-6 h-6 text-primary" />
                        @endif
                    </div>
                </div>
                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full bg-primary-200 border-2 border-surface"></span>
            </div>

            <!-- Name, Streak Pill, Member Meta -->
            <div class="flex flex-col gap-1.5 min-w-0">
                <div class="flex items-center gap-1.5 min-w-0">
                    <h2 class="font-headline text-lg font-bold text-white truncate leading-none">
                        {{ $data['name'] }}
                    </h2>
                    @if (! empty($data['flag']))
                        <span class="text-sm leading-none shrink-0" aria-hidden="true">{{ $data['flag'] }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary/15 border border-secondary/30 font-label text-[11px] font-bold text-secondary">
                        <x-lucide-flame class="w-3 h-3 text-secondary fill-secondary" />
                        <span>{{ $data['streak'] }} {{ $data['streak'] === 1 ? 'Day' : 'Days' }} Streak</span>
                    </span>

                    @if (! empty($data['platform']))
                        <span class="px-2 py-0.5 rounded-full bg-canvas border border-border-subtle font-label text-[10px] font-semibold text-neutral-300">
                            {{ $data['platform'] }} • {{ '@' . $data['username'] }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[11px] text-neutral-400 font-body">
                    <span>Synced {{ $data['memberSince'] }}</span>
                    <span>•</span>
                    <span class="text-primary font-semibold">{{ $data['totalGames'] }} Games (1Y)</span>
                </div>
            </div>
        </div>

        <!-- Share Button -->
        <button
            type="button"
            data-share-profile
            data-share-title="{{ $data['name'] }} - Gambit Arena"
            class="w-9 h-9 rounded-full bg-canvas/80 border border-border-subtle hover:border-border text-neutral-300 hover:text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer"
            aria-label="Share Profile"
            title="Share Profile"
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
                {{ $data['winRate'] }} Win Rate
            </span>
        </div>

        <!-- Segmented W/D/L Bar -->
        <div class="h-1.5 w-full rounded-full overflow-hidden flex gap-0.5 bg-surface-elevated">
            @if ($data['winPct'] > 0)
                <div class="h-full bg-primary rounded-l-full" style="width: {{ $data['winPct'] }}%"></div>
            @endif
            @if ($data['drawPct'] > 0)
                <div class="h-full bg-secondary" style="width: {{ $data['drawPct'] }}%"></div>
            @endif
            @if ($data['lossPct'] > 0)
                <div class="h-full bg-danger-400 rounded-r-full" style="width: {{ $data['lossPct'] }}%"></div>
            @endif
        </div>

        <!-- W/D/L Counts -->
        <div class="flex items-center justify-between font-label text-[11px] text-neutral-300">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                <span>{{ number_format($data['wins']) }} W</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                <span>{{ number_format($data['draws']) }} D</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-danger-400"></span>
                <span>{{ number_format($data['losses']) }} L</span>
            </span>
        </div>
    </div>

    <!-- 4 Mode Rating Tiles -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($data['ratings'] as $stat)
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

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-share-profile]').forEach((btn) => {
                    btn.addEventListener('click', async () => {
                        const title = btn.getAttribute('data-share-title') || document.title;
                        const url = window.location.href;
                        if (navigator.share) {
                            try {
                                await navigator.share({ title, url });
                                return;
                            } catch {
                                // Ignore cancel
                            }
                        }

                        if (navigator.clipboard?.writeText) {
                            try {
                                await navigator.clipboard.writeText(url);
                                btn.classList.add('border-primary', 'text-primary');
                                setTimeout(() => btn.classList.remove('border-primary', 'text-primary'), 1400);
                            } catch {
                                // Ignore clipboard permission issue
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
