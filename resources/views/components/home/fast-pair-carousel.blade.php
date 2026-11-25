@php
    $modes = [
        [
            'title' => 'Blitz',
            'icon' => 'zap',
            'featured' => true,
            'iconClass' => 'text-neutral-950 fill-neutral-950',
        ],
        [
            'title' => 'Rapid',
            'icon' => 'timer',
            'featured' => false,
            'iconClass' => 'text-primary',
        ],
        [
            'title' => 'Bullet',
            'icon' => 'rocket',
            'featured' => false,
            'iconClass' => 'text-secondary',
        ],
        [
            'title' => 'Classic',
            'icon' => 'clock',
            'featured' => false,
            'iconClass' => 'text-tertiary-300',
        ],
    ];
@endphp

<section class="w-full flex flex-col gap-2.5">
    <!-- Section Label -->
    <div class="flex items-center justify-between">
        <h3 class="font-label text-[11px] font-semibold tracking-wider uppercase text-neutral-400">
            Fast Pair &amp; Arena
        </h3>
        <span class="font-label text-[10px] text-neutral-500">Scroll →</span>
    </div>

    <!-- Horizontal Scroll Container (stays flush with parent margins) -->
    <div
        class="w-full flex items-stretch gap-3 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth select-none cursor-grab active:cursor-grabbing"
        data-drag-scroll
    >
        @foreach ($modes as $mode)
            <button
                type="button"
                @class([
                    'shrink-0 w-[124px] snap-start rounded-2xl p-3.5 flex flex-col justify-between gap-4 text-left transition-transform active:scale-[0.98] cursor-pointer',
                    'bg-gradient-to-br from-primary-200 via-primary to-primary-800 border border-primary/40 shadow-[0_6px_24px_-6px_rgba(16,185,129,0.45)]' => $mode['featured'],
                    'bg-surface border border-border-subtle hover:border-border' => ! $mode['featured'],
                ])
            >
                <!-- Logo / Icon -->
                <span @class([
                    'w-8 h-8 rounded-xl flex items-center justify-center',
                    'bg-neutral-950/15' => $mode['featured'],
                    'bg-canvas/80 border border-border-subtle' => ! $mode['featured'],
                ])>
                    <x-dynamic-component :component="'lucide-' . $mode['icon']" class="w-4 h-4 {{ $mode['iconClass'] }}" />
                </span>

                <!-- Game Type Classification -->
                <span @class([
                    'font-headline text-sm font-bold leading-snug',
                    'text-neutral-950' => $mode['featured'],
                    'text-white' => ! $mode['featured'],
                ])>
                    {{ $mode['title'] }}
                </span>
            </button>
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
