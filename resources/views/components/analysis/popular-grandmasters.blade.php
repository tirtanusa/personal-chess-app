@php
    $grandmasters = [
        [
            'slug' => 'magnus-carlsen',
            'initials' => 'MC',
            'avatarClass' => 'bg-secondary/15 border-secondary/25 text-secondary',
            'name' => 'Magnus Carlsen',
            'flag' => '🇳🇴',
            'title' => 'GM',
            'rating' => '2882 FIDE',
            'rank' => '#1 World',
            'rankClass' => 'text-primary',
        ],
        [
            'slug' => 'hikaru-nakamura',
            'initials' => 'HN',
            'avatarClass' => 'bg-primary/15 border-primary/25 text-primary',
            'name' => 'Hikaru Nakamura',
            'flag' => '🇺🇸',
            'title' => 'GM',
            'rating' => '2875 FIDE',
            'rank' => '#2 World',
            'rankClass' => 'text-primary',
        ],
        [
            'slug' => 'dommaraju-gukesh',
            'initials' => 'DG',
            'avatarClass' => 'bg-tertiary/15 border-tertiary/25 text-tertiary-300',
            'name' => 'Dommaraju Gukesh',
            'flag' => '🇮🇳',
            'title' => 'GM',
            'rating' => '2794 FIDE',
            'rank' => 'Challenger',
            'rankClass' => 'text-tertiary-300',
        ],
        [
            'slug' => 'susanto-megaranto',
            'initials' => 'SM',
            'avatarClass' => 'bg-danger/15 border-danger/25 text-danger-400',
            'name' => 'Susanto Megaranto',
            'flag' => '🇮🇩',
            'title' => 'GM',
            'rating' => '2505 FIDE',
            'rank' => '#1 IDN',
            'rankClass' => 'text-danger-400',
        ],
    ];
@endphp

<section class="w-full flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <x-lucide-award class="w-4 h-4 text-secondary shrink-0" />
            <h2 class="font-headline text-base font-bold text-white truncate">Popular Players &amp; Grandmasters</h2>
        </div>
        <span class="font-label text-[11px] text-neutral-400 shrink-0">Top FIDE</span>
    </div>

    <!-- Grandmasters List -->
    <div class="flex flex-col gap-2.5">
        @foreach ($grandmasters as $gm)
            <div class="bg-surface border border-border-subtle rounded-2xl p-3 flex items-center justify-between gap-3 hover:border-border transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-10 h-10 rounded-xl border flex items-center justify-center font-headline text-xs font-bold {{ $gm['avatarClass'] }}">
                            {{ $gm['initials'] }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 text-[11px] leading-none" aria-hidden="true">{{ $gm['flag'] }}</span>
                    </div>

                    <div class="flex flex-col gap-0.5 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-headline text-sm font-bold text-white truncate">
                                {{ $gm['name'] }}
                            </span>
                            <span class="px-1.5 py-0.5 rounded bg-secondary/15 border border-secondary/30 font-label text-[9px] font-bold text-secondary leading-none shrink-0">
                                {{ $gm['title'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5 font-label text-[11px]">
                            <span class="text-neutral-400">{{ $gm['rating'] }}</span>
                            <span class="text-neutral-600">•</span>
                            <span class="font-semibold {{ $gm['rankClass'] }}">{{ $gm['rank'] }}</span>
                        </div>
                    </div>
                </div>

                <a
                    href="{{ route('analysis.player', $gm['slug']) }}"
                    class="px-3.5 py-1.5 rounded-xl bg-canvas/80 border border-border hover:border-primary/50 hover:bg-primary/10 text-primary font-headline text-xs font-semibold transition-colors shrink-0"
                >
                    Analyze
                </a>
            </div>
        @endforeach
    </div>
</section>
