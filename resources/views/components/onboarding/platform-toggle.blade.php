@props([
    'name' => 'platform',
    'default' => 'chesscom',
])

<div
    class="inline-flex items-center p-1 rounded-xl bg-canvas border border-border-subtle self-start"
    data-platform-toggle="{{ $name }}"
>
    <input type="hidden" name="{{ $name }}" value="{{ $default }}" data-platform-input>

    <button
        type="button"
        data-platform-option="chesscom"
        @class([
            'px-3.5 py-1.5 rounded-lg font-headline text-xs transition-all cursor-pointer',
            'bg-primary-200 text-neutral-950 font-bold shadow-sm' => $default === 'chesscom',
            'text-neutral-200 hover:text-white font-semibold' => $default !== 'chesscom',
        ])
    >
        Chess.com
    </button>

    <button
        type="button"
        data-platform-option="lichess"
        @class([
            'px-3.5 py-1.5 rounded-lg font-headline text-xs transition-all cursor-pointer',
            'bg-primary-200 text-neutral-950 font-bold shadow-sm' => $default === 'lichess',
            'text-neutral-200 hover:text-white font-semibold' => $default !== 'lichess',
        ])
    >
        Lichess
    </button>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-platform-toggle]').forEach((toggle) => {
                    const hiddenInput = toggle.querySelector('[data-platform-input]');
                    const buttons = toggle.querySelectorAll('[data-platform-option]');

                    const activeClasses = ['bg-primary-200', 'text-neutral-950', 'font-bold', 'shadow-sm'];
                    const inactiveClasses = ['text-neutral-200', 'hover:text-white', 'font-semibold'];

                    buttons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const selected = btn.getAttribute('data-platform-option');
                            hiddenInput.value = selected;

                            buttons.forEach((b) => {
                                const isActive = b === btn;
                                b.classList.remove(...(isActive ? inactiveClasses : activeClasses));
                                b.classList.add(...(isActive ? activeClasses : inactiveClasses));
                            });

                            toggle.dispatchEvent(new CustomEvent('platform-changed', {
                                bubbles: true,
                                detail: { platform: selected },
                            }));
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
