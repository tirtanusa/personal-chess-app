@props([
    'name' => 'Alexey V.',
    'avatarUrl' => null,
    'flag' => '',
    'streak' => 0,
    'form' => [],
    'ratings' => null,
    'lastSynced' => null,
    'canRefresh' => false,
])

@php
    $ratingCards = $ratings ?? [
        [
            'label' => 'RAPID',
            'value' => '—',
            'sub' => 'Unrated',
            'subClass' => 'text-neutral-500',
            'icon' => 'clock',
            'iconClass' => 'text-primary',
        ],
        [
            'label' => 'BLITZ',
            'value' => '—',
            'sub' => 'Unrated',
            'subClass' => 'text-neutral-500',
            'icon' => 'zap',
            'iconClass' => 'text-secondary',
        ],
        [
            'label' => 'BULLET',
            'value' => '—',
            'sub' => 'Unrated',
            'subClass' => 'text-neutral-500',
            'icon' => 'rocket',
            'iconClass' => 'text-tertiary-300',
        ],
        [
            'label' => 'CLASSIC',
            'value' => '—',
            'sub' => 'Unrated',
            'subClass' => 'text-neutral-500',
            'icon' => 'crown',
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
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="w-full h-full object-cover">
                @else
                    <x-lucide-user class="w-5 h-5 text-primary" />
                @endif
            </div>

            <div class="flex items-center gap-1.5 min-w-0">
                <h2 class="font-headline text-base font-bold text-white truncate">
                    {{ $name }}
                </h2>
                @if ($flag !== '')
                    <span class="text-sm leading-none shrink-0" aria-hidden="true">{{ $flag }}</span>
                @endif
            </div>
        </div>

        <!-- Form Pills & Unbeaten Streak -->
        <div class="flex flex-col items-end gap-1.5 shrink-0">
            @if (count($form) > 0)
                <div class="flex items-center gap-1">
                    @foreach ($form as $result)
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
            @else
                <span class="font-label text-[10px] text-neutral-500">No recent form</span>
            @endif
        </div>
    </div>

    <!-- Bottom Row: 4 Rating Tiles (Rapid, Blitz, Bullet, Classic) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach ($ratingCards as $stat)
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

    @if ($canRefresh || $lastSynced)
        <div class="flex items-center justify-between gap-2 pt-2 border-t border-border-subtle/70">
            <span class="font-label text-[10px] text-neutral-400">
                @if ($lastSynced)
                    Last synced: <span class="text-neutral-200">{{ $lastSynced }}</span>
                @else
                    Manual sync available
                @endif
            </span>

            @if ($canRefresh)
                <form method="POST" action="{{ route('home.refresh') }}" data-refresh-form>
                    @csrf
                    <button
                        type="submit"
                        data-refresh-btn
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-surface-elevated/90 px-2.5 py-1.5 font-label text-[11px] font-semibold text-neutral-200 hover:text-white hover:border-primary/50 hover:bg-surface-elevated transition-colors cursor-pointer disabled:opacity-60 disabled:cursor-wait"
                    >
                        <x-lucide-refresh-cw class="w-3.5 h-3.5 text-primary" data-refresh-icon />
                        <span data-refresh-label>Refresh Data</span>
                    </button>
                </form>
            @endif
        </div>
    @endif
</section>

@if ($canRefresh)
    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('[data-refresh-form]').forEach((form) => {
                        form.addEventListener('submit', () => {
                            const btn = form.querySelector('[data-refresh-btn]');
                            const icon = form.querySelector('[data-refresh-icon]');
                            const label = form.querySelector('[data-refresh-label]');
                            if (btn) btn.disabled = true;
                            if (icon) icon.classList.add('animate-spin');
                            if (label) label.textContent = 'Syncing...';
                        });
                    });
                });
            </script>
        @endpush
    @endonce
@endif
