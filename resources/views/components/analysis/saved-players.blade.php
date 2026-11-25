@php
    $savedPlayers = [
        [
            'slug' => 'alireza-firouzja',
            'initials' => 'AF',
            'avatarClass' => 'bg-primary/15 border-primary/25 text-primary',
            'name' => 'Alireza Firouzja',
            'flag' => '🇫🇷',
            'title' => 'GM',
            'rating' => '2805 FIDE',
            'tag' => 'Favorite Rival',
            'tagDot' => true,
            'tagIcon' => null,
            'tagClass' => 'bg-primary/15 border-primary/25 text-primary',
            'status' => 'Online 15m ago',
            'winrate' => '45%',
            'winrateClass' => 'text-primary',
            'blunder' => '0.8',
            'opening' => 'Sicilian Najdorf',
        ],
        [
            'slug' => 'daniel-naroditsky',
            'initials' => 'DN',
            'avatarClass' => 'bg-secondary/15 border-secondary/25 text-secondary',
            'name' => 'Daniel Naroditsky',
            'flag' => '🇺🇸',
            'title' => 'GM',
            'rating' => '2615 FIDE',
            'tag' => 'Blitz Specialist',
            'tagDot' => false,
            'tagIcon' => 'zap',
            'tagClass' => 'bg-secondary/15 border-secondary/25 text-secondary',
            'status' => 'Online 2h ago',
            'winrate' => '38%',
            'winrateClass' => 'text-secondary',
            'blunder' => '0.6',
            'opening' => "King's Indian",
        ],
        [
            'slug' => 'hikaru-nakamura',
            'initials' => 'HN',
            'avatarClass' => 'bg-tertiary/15 border-tertiary/25 text-tertiary-300',
            'name' => 'Hikaru Nakamura',
            'flag' => '🇺🇸',
            'title' => 'GM',
            'rating' => '2875 FIDE',
            'tag' => 'Speed Demon',
            'tagDot' => false,
            'tagIcon' => 'flame',
            'tagClass' => 'bg-tertiary/15 border-tertiary/25 text-tertiary-200',
            'status' => 'Active now',
            'winrate' => '32%',
            'winrateClass' => 'text-tertiary-200',
            'blunder' => '0.5',
            'opening' => 'Ruy Lopez',
        ],
        [
            'slug' => 'magnus-carlsen',
            'initials' => 'MC',
            'avatarClass' => 'bg-primary/15 border-primary/25 text-primary',
            'name' => 'Magnus Carlsen',
            'flag' => '🇳🇴',
            'title' => 'GM',
            'rating' => '2882 FIDE',
            'tag' => 'World #1',
            'tagDot' => true,
            'tagIcon' => null,
            'tagClass' => 'bg-primary/15 border-primary/25 text-primary',
            'status' => 'Online 1d ago',
            'winrate' => '21%',
            'winrateClass' => 'text-primary',
            'blunder' => '0.3',
            'opening' => "Queen's Gambit",
        ],
    ];
@endphp

<section class="w-full flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-lg bg-primary/15 border border-primary/25 text-primary flex items-center justify-center shrink-0">
                <x-lucide-bookmark class="w-3.5 h-3.5" />
            </span>
            <h2 class="font-headline text-base font-bold text-white truncate">Saved Players</h2>
            <span class="px-2 py-0.5 rounded-full bg-surface border border-border-subtle font-label text-[10px] text-neutral-400 shrink-0">
                4 Players
            </span>
        </div>

        <a href="#" class="inline-flex items-center gap-0.5 font-headline text-xs font-semibold text-primary hover:text-primary-200 transition-colors shrink-0">
            <span>Manage</span>
            <x-lucide-chevron-right class="w-3.5 h-3.5" />
        </a>
    </div>

    <!-- Horizontal Scroll Carousel -->
    <div
        class="w-full flex items-stretch gap-3 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth select-none cursor-grab active:cursor-grabbing"
        data-drag-scroll
    >
        @foreach ($savedPlayers as $player)
            <article class="shrink-0 w-[264px] snap-start bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-3.5">
                <!-- Player Header -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-xl border flex items-center justify-center font-headline text-sm font-bold {{ $player['avatarClass'] }}">
                            {{ $player['initials'] }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 text-xs leading-none" aria-hidden="true">{{ $player['flag'] }}</span>
                    </div>

                    <div class="flex flex-col gap-1 min-w-0">
                        <h3 class="font-headline text-sm font-bold text-white truncate leading-tight">
                            {{ $player['name'] }}
                        </h3>
                        <div class="flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded bg-secondary/15 border border-secondary/30 font-label text-[9px] font-bold text-secondary leading-none">
                                {{ $player['title'] }}
                            </span>
                            <span class="font-label text-[11px] text-neutral-400">
                                {{ $player['rating'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tag & Last Seen -->
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full border font-label text-[10px] font-semibold {{ $player['tagClass'] }}">
                        @if ($player['tagDot'])
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        @elseif ($player['tagIcon'])
                            <x-dynamic-component :component="'lucide-' . $player['tagIcon']" class="w-2.5 h-2.5" />
                        @endif
                        <span>{{ $player['tag'] }}</span>
                    </span>

                    <span class="font-body text-[10px] text-neutral-500 truncate">
                        {{ $player['status'] }}
                    </span>
                </div>

                <!-- Stats Box -->
                <div class="bg-canvas/80 border border-border-subtle rounded-xl p-3 flex flex-col gap-2.5">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-label text-[9px] tracking-wider uppercase text-neutral-500">
                                Winrate vs You
                            </span>
                            <span class="font-headline text-base font-bold {{ $player['winrateClass'] }}">
                                {{ $player['winrate'] }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-0.5">
                            <span class="font-label text-[9px] tracking-wider uppercase text-neutral-500">
                                Avg Blunder
                            </span>
                            <span class="font-headline text-base font-bold text-white">
                                {{ $player['blunder'] }}<span class="font-label text-[11px] font-normal text-neutral-400">/gm</span>
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-border-subtle/70 flex items-center justify-between gap-2 text-[11px]">
                        <span class="text-neutral-500 font-body">Fav Opening:</span>
                        <span class="text-neutral-200 font-semibold font-body truncate">{{ $player['opening'] }}</span>
                    </div>
                </div>

                <!-- Inspect Account CTA -->
                <a
                    href="{{ route('analysis.player', $player['slug']) }}"
                    class="w-full py-2.5 px-4 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.98] text-neutral-950 font-headline font-bold text-xs shadow-[0_0_18px_-4px_rgba(76,213,157,0.4)] transition-all flex items-center justify-center gap-1.5"
                >
                    <x-lucide-text-search class="w-3.5 h-3.5" />
                    <span>Inspect Account</span>
                </a>
            </article>
        @endforeach
    </div>
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-drag-scroll]').forEach((el) => {
                    let isDown = false;
                    let startX = 0;
                    let scrollLeft = 0;

                    el.addEventListener('mousedown', (e) => {
                        isDown = true;
                        startX = e.pageX - el.offsetLeft;
                        scrollLeft = el.scrollLeft;
                    });

                    el.addEventListener('mouseleave', () => { isDown = false; });
                    el.addEventListener('mouseup', () => { isDown = false; });

                    el.addEventListener('mousemove', (e) => {
                        if (!isDown) return;
                        e.preventDefault();
                        const x = e.pageX - el.offsetLeft;
                        const walk = (x - startX) * 1.3;
                        el.scrollLeft = scrollLeft - walk;
                    });
                });
            });
        </script>
    @endpush
@endonce
