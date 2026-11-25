@props([
    'dividerText' => 'Or Continue With',
])

<div class="flex flex-col gap-4">
    <!-- Divider -->
    <div class="relative flex items-center justify-center">
        <div class="w-full border-t border-border-subtle"></div>
        <span class="absolute bg-surface px-3 font-label text-[10px] font-medium tracking-[0.14em] uppercase text-neutral-400">
            {{ $dividerText }}
        </span>
    </div>

    <!-- Social Buttons Grid -->
    <div class="grid grid-cols-2 gap-3 pt-1">
        <!-- Google Button -->
        <button
            type="button"
            class="flex items-center justify-center gap-2.5 py-2.5 px-4 rounded-xl bg-canvas/80 border border-border hover:border-border-strong hover:bg-surface-elevated text-sm font-headline font-semibold text-neutral-100 transition-all cursor-pointer"
        >
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#EA4335" d="M12 5c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 1.09 14.97 0 12 0 7.31 0 3.25 2.69 1.25 6.61l3.68 2.86C5.8 6.86 8.65 5 12 5z"/>
                <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58l3.71 2.88c2.17-2 3.71-4.94 3.71-8.7z"/>
                <path fill="#FBBC05" d="M4.93 14.53A7.22 7.22 0 0 1 4.55 12c0-.88.15-1.73.38-2.53L1.25 6.61A11.98 11.98 0 0 0 0 12c0 1.94.46 3.77 1.25 5.39l3.68-2.86z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.71-2.88c-1.07.72-2.44 1.15-4.22 1.15-3.35 0-6.2-1.86-7.07-4.83l-3.68 2.86C3.25 21.31 7.31 24 12 24z"/>
            </svg>
            <span>Google</span>
        </button>

        <!-- Facebook Button -->
        <button
            type="button"
            class="flex items-center justify-center gap-2.5 py-2.5 px-4 rounded-xl bg-canvas/80 border border-border hover:border-border-strong hover:bg-surface-elevated text-sm font-headline font-semibold text-neutral-100 transition-all cursor-pointer"
        >
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="12" fill="#1877F2"/>
                <path fill="#FFFFFF" d="M16.67 15.47l.53-3.47h-3.33V9.75c0-.95.46-1.87 1.95-1.87h1.51V4.92s-1.37-.23-2.68-.23c-2.74 0-4.53 1.66-4.53 4.67V12H7.08v3.47h3.04V24h3.75v-8.53h2.8z"/>
            </svg>
            <span>Facebook</span>
        </button>
    </div>
</div>
