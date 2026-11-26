@props([
    'avatarUrl' => null,
    'name' => null,
    'handle' => null,
    'rating' => '1842',
    'activeAccounts' => 2,
])

@php
    $displayName = $name ?? auth()->user()?->name ?? 'Alexey V.';
    $displayHandle = $handle ?? (auth()->check()
        ? '@' . \Illuminate\Support\Str::slug(auth()->user()->name, '_')
        : '@alexey_v');
@endphp

<div class="relative" data-profile-dropdown>
    <!-- Circular Avatar Trigger -->
    <button
        type="button"
        data-profile-trigger
        aria-expanded="false"
        aria-haspopup="menu"
        aria-label="User Profile Menu"
        class="w-9 h-9 rounded-full p-0.5 bg-gradient-to-tr from-primary/60 via-secondary/40 to-tertiary/50 hover:scale-105 transition-all cursor-pointer flex items-center justify-center focus:outline-none"
    >
        <div class="w-full h-full rounded-full bg-surface-elevated overflow-hidden flex items-center justify-center">
            @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <x-lucide-user class="w-4 h-4 text-neutral-200" />
            @endif
        </div>
    </button>

    <!-- Dropdown Menu Panel -->
    <div
        role="menu"
        data-profile-menu
        class="hidden opacity-0 scale-95 origin-top-right transition-all duration-150 absolute right-0 top-[calc(100%+0.625rem)] z-50 w-[276px] sm:w-[292px] bg-surface border border-border rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.65)] p-2 flex flex-col gap-1"
    >
        <!-- Profile Summary Header -->
        <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3 flex items-center justify-between gap-2.5 mb-1">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-full p-0.5 bg-gradient-to-tr from-primary/60 via-secondary/40 to-tertiary/50">
                        <div class="w-full h-full rounded-full bg-surface-elevated overflow-hidden flex items-center justify-center">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <x-lucide-user class="w-4 h-4 text-primary" />
                            @endif
                        </div>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-primary-200 border-2 border-surface"></span>
                </div>

                <div class="flex flex-col min-w-0">
                    <span class="font-headline text-sm font-bold text-white truncate leading-tight">
                        {{ $displayName }}
                    </span>
                    <span class="font-label text-[11px] text-neutral-400 truncate">
                        {{ $displayHandle }}
                    </span>
                </div>
            </div>

            <span class="px-2 py-0.5 rounded-md bg-primary/15 border border-primary/30 font-label text-[11px] font-bold text-primary shrink-0">
                {{ $rating }}
            </span>
        </div>

        <!-- Menu Links -->
        <a
            href="{{ route('profile') }}"
            role="menuitem"
            class="px-3 py-2.5 rounded-xl hover:bg-surface-elevated/80 flex items-center gap-3 text-sm font-body text-neutral-200 hover:text-white transition-colors"
        >
            <x-lucide-user class="w-4 h-4 text-neutral-400 shrink-0" />
            <span>My Profile</span>
        </a>

        <a
            href="#"
            role="menuitem"
            class="px-3 py-2.5 rounded-xl hover:bg-surface-elevated/80 flex items-center gap-3 text-sm font-body text-neutral-200 hover:text-white transition-colors"
        >
            <x-lucide-settings class="w-4 h-4 text-neutral-400 shrink-0" />
            <span>Account Settings</span>
        </a>

        <a
            href="{{ route('onboarding.connect') }}"
            role="menuitem"
            class="px-3 py-2.5 rounded-xl hover:bg-surface-elevated/80 flex items-center justify-between gap-2 text-sm font-body text-neutral-200 hover:text-white transition-colors"
        >
            <span class="flex items-center gap-3 min-w-0">
                <x-lucide-arrow-left-right class="w-4 h-4 text-secondary shrink-0" />
                <span class="truncate">Switch Chess Account</span>
            </span>

            <span class="px-2 py-0.5 rounded bg-secondary/15 border border-secondary/30 font-label text-[9px] font-bold tracking-wider uppercase text-secondary shrink-0">
                {{ $activeAccounts }} ACTIVE
            </span>
        </a>

        <a
            href="{{ route('theme') }}"
            role="menuitem"
            class="px-3 py-2.5 rounded-xl hover:bg-surface-elevated/80 flex items-center gap-3 text-sm font-body text-neutral-200 hover:text-white transition-colors"
        >
            <x-lucide-palette class="w-4 h-4 text-neutral-400 shrink-0" />
            <span>Theme &amp; Appearance</span>
        </a>

        <a
            href="#"
            role="menuitem"
            class="px-3 py-2.5 rounded-xl hover:bg-surface-elevated/80 flex items-center gap-3 text-sm font-body text-neutral-200 hover:text-white transition-colors"
        >
            <x-lucide-circle-help class="w-4 h-4 text-neutral-400 shrink-0" />
            <span>Help &amp; FAQ</span>
        </a>

        <!-- Divider + Sign Out -->
        <div class="border-t border-border-subtle mt-1 pt-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    role="menuitem"
                    class="w-full px-3 py-2.5 rounded-xl hover:bg-danger/10 flex items-center gap-3 text-sm font-body font-medium text-danger-400 hover:text-danger-300 transition-colors cursor-pointer text-left"
                >
                    <x-lucide-log-out class="w-4 h-4 shrink-0" />
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-profile-dropdown]').forEach((wrapper) => {
                    const trigger = wrapper.querySelector('[data-profile-trigger]');
                    const menu = wrapper.querySelector('[data-profile-menu]');
                    if (!trigger || !menu) return;

                    const openMenu = () => {
                        menu.classList.remove('hidden');
                        trigger.setAttribute('aria-expanded', 'true');
                        trigger.classList.add('ring-2', 'ring-primary/70', 'ring-offset-2', 'ring-offset-canvas');

                        requestAnimationFrame(() => {
                            menu.classList.remove('opacity-0', 'scale-95');
                        });
                    };

                    const closeMenu = () => {
                        if (menu.classList.contains('hidden')) return;
                        menu.classList.add('opacity-0', 'scale-95');
                        trigger.setAttribute('aria-expanded', 'false');
                        trigger.classList.remove('ring-2', 'ring-primary/70', 'ring-offset-2', 'ring-offset-canvas');

                        setTimeout(() => {
                            menu.classList.add('hidden');
                        }, 150);
                    };

                    trigger.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (menu.classList.contains('hidden')) {
                            openMenu();
                        } else {
                            closeMenu();
                        }
                    });

                    document.addEventListener('click', (e) => {
                        if (!wrapper.contains(e.target)) {
                            closeMenu();
                        }
                    });

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            closeMenu();
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
