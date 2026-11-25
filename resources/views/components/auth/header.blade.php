@props([
    'brand' => 'GAMBIT',
    'badge' => 'MASTER',
    'elo' => '2850+',
    'tagline' => 'ENTER THE ARENA & CLIMB YOUR ELO',
    'heading' => 'Welcome Back',
    'subheading' => 'Continue sharpening your tactics and analyzing your best games',
])

<div class="w-full flex flex-col items-center text-center gap-4">
    <x-auth.emblem :elo="$elo" />

    <!-- Brand & Tagline -->
    <div class="flex flex-col items-center gap-1.5 mt-1">
        <div class="flex items-center gap-2">
            <h1 class="font-headline text-3xl font-extrabold tracking-wider text-white uppercase leading-none">
                {{ $brand }}
            </h1>
            <span class="px-2 py-0.5 rounded-md bg-primary/15 border border-primary/30 font-label text-[11px] font-bold tracking-wider text-primary uppercase">
                {{ $badge }}
            </span>
        </div>

        <p class="font-label text-[11px] font-semibold tracking-[0.1em] text-primary uppercase">
            {{ $tagline }}
        </p>
    </div>

    <!-- Welcome Banner Box -->
    <div class="w-full bg-surface/65 border border-border-subtle rounded-2xl px-5 py-3.5 flex flex-col gap-1">
        <h2 class="font-headline text-xl font-bold text-white tracking-tight">
            {{ $heading }}
        </h2>
        <p class="text-xs text-neutral-400 font-body">
            {{ $subheading }}
        </p>
    </div>
</div>
