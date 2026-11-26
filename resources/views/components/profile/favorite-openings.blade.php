@props([
    'openings' => null,
])

@php
    $openingsBySide = $openings ?? [
        'white' => [],
        'black' => [],
    ];
@endphp

<section class="w-full bg-surface border border-border-subtle rounded-2xl p-4 shadow-xl flex flex-col gap-4" data-openings-card>
    <!-- Header + Side Toggle -->
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <x-lucide-book-open class="w-4 h-4 text-primary shrink-0" />
            <h3 class="font-headline text-base font-bold text-white truncate">Favorite Openings</h3>
        </div>

        <div class="inline-flex items-center p-0.5 rounded-lg bg-canvas border border-border-subtle shrink-0">
            <button
                type="button"
                data-side-btn="white"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-bold bg-primary-200 text-neutral-950 transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-white border border-neutral-950/30"></span>
                <span>White</span>
            </button>
            <button
                type="button"
                data-side-btn="black"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md font-headline text-[11px] font-semibold text-neutral-400 hover:text-white transition-colors cursor-pointer"
            >
                <span class="w-2 h-2 rounded-full bg-neutral-700 border border-neutral-500"></span>
                <span>Black</span>
            </button>
        </div>
    </div>

    <!-- Openings List -->
    @foreach (['white', 'black'] as $side)
        @php
            $sideOpenings = $openingsBySide[$side] ?? [];
        @endphp
        <div @class(['flex flex-col gap-2.5', 'hidden' => $side !== 'white']) data-side-list="{{ $side }}">
            @if (count($sideOpenings) > 0)
                @foreach ($sideOpenings as $opening)
                    <div class="bg-canvas/80 border border-border-subtle rounded-xl p-3 flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $opening['dotClass'] }}"></span>
                                <span class="font-headline text-xs sm:text-sm font-bold text-white truncate">
                                    {{ $opening['name'] }}
                                </span>
                            </div>
                            <span class="font-label text-[11px] font-bold text-primary shrink-0">
                                {{ $opening['winRate'] }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="font-label text-[11px] text-primary truncate">
                                {{ $opening['moves'] }}
                            </span>
                            <span class="font-label text-[10px] text-neutral-400 shrink-0">
                                {{ $opening['games'] }}
                            </span>
                        </div>

                        <!-- Segmented Bar -->
                        <div class="h-1 w-full rounded-full overflow-hidden flex gap-0.5 bg-surface-elevated">
                            @if ($opening['w'] > 0)
                                <div class="h-full bg-primary rounded-l-full" style="width: {{ $opening['w'] }}%"></div>
                            @endif
                            @if ($opening['d'] > 0)
                                <div class="h-full bg-secondary" style="width: {{ $opening['d'] }}%"></div>
                            @endif
                            @if ($opening['l'] > 0)
                                <div class="h-full bg-danger-400 rounded-r-full" style="width: {{ $opening['l'] }}%"></div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-2 font-label text-[10px]">
                            <span class="text-neutral-400">{{ $opening['record'] }}</span>
                            <span class="text-neutral-500 font-semibold">{{ $opening['accuracy'] }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-canvas/70 border border-border-subtle rounded-xl p-4 text-center text-xs text-neutral-400 font-body">
                    No {{ ucfirst($side) }} opening stats stored yet.
                </div>
            @endif
        </div>
    @endforeach
</section>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-openings-card]').forEach((card) => {
                    const buttons = card.querySelectorAll('[data-side-btn]');
                    const lists = card.querySelectorAll('[data-side-list]');

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const selected = btn.getAttribute('data-side-btn');

                            buttons.forEach((b) => {
                                const active = b === btn;
                                b.classList.toggle('bg-primary-200', active);
                                b.classList.toggle('text-neutral-950', active);
                                b.classList.toggle('font-bold', active);
                                b.classList.toggle('text-neutral-400', !active);
                                b.classList.toggle('font-semibold', !active);
                            });

                            lists.forEach((list) => {
                                list.classList.toggle('hidden', list.getAttribute('data-side-list') !== selected);
                            });
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
