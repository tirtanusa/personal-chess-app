@props([
    'heading' => 'Create Gambit Account',
    'subheading' => 'Start your chess journey, run accurate engine analysis, and sharpen daily tactics.',
])

<div class="w-full flex flex-col items-center text-center gap-4">
    <x-auth.emblem />

    <div class="flex flex-col items-center gap-2 mt-1">
        <h1 class="font-headline text-2xl sm:text-[26px] font-bold text-white tracking-tight leading-tight">
            {{ $heading }}
        </h1>
        <p class="text-xs text-neutral-400 font-body leading-relaxed max-w-xs">
            {{ $subheading }}
        </p>
    </div>
</div>
