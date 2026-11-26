@props([
    'activeMode' => 'blitz',
])

@php
    $modes = [
        [
            'key' => 'blitz',
            'title' => 'Blitz',
            'icon' => 'zap',
            'inactiveIconClass' => 'text-secondary',
        ],
        [
            'key' => 'rapid',
            'title' => 'Rapid',
            'icon' => 'timer',
            'inactiveIconClass' => 'text-primary',
        ],
        [
            'key' => 'bullet',
            'title' => 'Bullet',
            'icon' => 'rocket',
            'inactiveIconClass' => 'text-tertiary-300',
        ],
        [
            'key' => 'classic',
            'title' => 'Classical',
            'icon' => 'clock',
            'inactiveIconClass' => 'text-neutral-300',
        ],
    ];
@endphp

<section class="w-full flex flex-col gap-2.5" data-mode-selector data-active-mode="{{ $activeMode }}">
    <!-- Section Label -->
    <div class="flex items-center justify-between">
        <h3 class="font-label text-[11px] font-semibold tracking-wider uppercase text-neutral-400">
            MODE
        </h3>
        <span class="font-label text-[10px] text-neutral-500">Scroll →</span>
    </div>

    <!-- Horizontal Scroll Container (stays flush with parent margins) -->
    <div
        class="w-full flex items-stretch gap-3 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth select-none cursor-grab active:cursor-grabbing"
        data-drag-scroll
    >
        @foreach ($modes as $mode)
            @php
                $isActive = $mode['key'] === $activeMode;
            @endphp
            <button
                type="button"
                data-mode-btn="{{ $mode['key'] }}"
                data-inactive-icon-class="{{ $mode['inactiveIconClass'] }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                @class([
                    'shrink-0 w-[124px] snap-start rounded-2xl p-3.5 flex flex-col justify-between gap-4 text-left transition-all active:scale-[0.98] cursor-pointer',
                    'bg-gradient-to-br from-primary-200 via-primary to-primary-800 border border-primary/40 shadow-[0_6px_24px_-6px_rgba(16,185,129,0.45)]' => $isActive,
                    'bg-surface border border-border-subtle hover:border-border' => ! $isActive,
                ])
            >
                <!-- Logo / Icon -->
                <span
                    data-mode-icon-box
                    @class([
                        'w-8 h-8 rounded-xl flex items-center justify-center',
                        'bg-neutral-950/15' => $isActive,
                        'bg-canvas/80 border border-border-subtle' => ! $isActive,
                    ])
                >
                    <x-dynamic-component
                        :component="'lucide-' . $mode['icon']"
                        data-mode-icon
                        @class([
                            'w-4 h-4',
                            'text-neutral-950 fill-neutral-950' => $isActive,
                            $mode['inactiveIconClass'] => ! $isActive,
                        ])
                    />
                </span>

                <!-- Game Type Classification -->
                <span
                    data-mode-title
                    @class([
                        'font-headline text-sm font-bold leading-snug',
                        'text-neutral-950' => $isActive,
                        'text-white' => ! $isActive,
                    ])
                >
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
                const activeCardClasses = [
                    'bg-gradient-to-br',
                    'from-primary-200',
                    'via-primary',
                    'to-primary-800',
                    'border-primary/40',
                    'shadow-[0_6px_24px_-6px_rgba(16,185,129,0.45)]',
                ];
                const inactiveCardClasses = [
                    'bg-surface',
                    'border-border-subtle',
                    'hover:border-border',
                ];

                document.querySelectorAll('[data-mode-selector]').forEach((selector) => {
                    const buttons = selector.querySelectorAll('[data-mode-btn]');
                    const scroller = selector.querySelector('[data-drag-scroll]');
                    let hasDragged = false;

                    if (scroller) {
                        let isDown = false;
                        let startX = 0;
                        let scrollLeft = 0;

                        scroller.addEventListener('mousedown', (e) => {
                            isDown = true;
                            hasDragged = false;
                            startX = e.pageX - scroller.offsetLeft;
                            scrollLeft = scroller.scrollLeft;
                        });

                        scroller.addEventListener('mouseleave', () => { isDown = false; });
                        scroller.addEventListener('mouseup', () => { isDown = false; });

                        scroller.addEventListener('mousemove', (e) => {
                            if (!isDown) return;
                            const x = e.pageX - scroller.offsetLeft;
                            if (Math.abs(x - startX) > 6) {
                                hasDragged = true;
                            }
                            e.preventDefault();
                            const walk = (x - startX) * 1.3;
                            scroller.scrollLeft = scrollLeft - walk;
                        });
                    }

                    const setActiveMode = (mode) => {
                        selector.setAttribute('data-active-mode', mode);

                        buttons.forEach((btn) => {
                            const isMatch = btn.getAttribute('data-mode-btn') === mode;
                            const iconBox = btn.querySelector('[data-mode-icon-box]');
                            const icon = btn.querySelector('[data-mode-icon]');
                            const title = btn.querySelector('[data-mode-title]');
                            const inactiveIconClass = btn.getAttribute('data-inactive-icon-class') || 'text-primary';

                            btn.setAttribute('aria-pressed', isMatch ? 'true' : 'false');

                            activeCardClasses.forEach((cls) => btn.classList.toggle(cls, isMatch));
                            inactiveCardClasses.forEach((cls) => btn.classList.toggle(cls, !isMatch));

                            if (iconBox) {
                                iconBox.classList.toggle('bg-neutral-950/15', isMatch);
                                iconBox.classList.toggle('bg-canvas/80', !isMatch);
                                iconBox.classList.toggle('border', !isMatch);
                                iconBox.classList.toggle('border-border-subtle', !isMatch);
                            }

                            if (icon) {
                                icon.classList.toggle('text-neutral-950', isMatch);
                                icon.classList.toggle('fill-neutral-950', isMatch);
                                icon.classList.toggle(inactiveIconClass, !isMatch);
                            }

                            if (title) {
                                title.classList.toggle('text-neutral-950', isMatch);
                                title.classList.toggle('text-white', !isMatch);
                            }
                        });

                        document.dispatchEvent(new CustomEvent('home:mode-change', {
                            detail: { mode },
                        }));
                    };

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            if (hasDragged) {
                                hasDragged = false;
                                return;
                            }

                            const mode = btn.getAttribute('data-mode-btn');
                            if (mode) {
                                setActiveMode(mode);
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
