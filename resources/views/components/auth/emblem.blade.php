@props([
    'elo' => null,
])

<div class="relative inline-flex">
    <div class="w-20 h-20 rounded-2xl bg-surface/90 border border-border-subtle shadow-[0_0_35px_-5px_rgba(16,185,129,0.2)] flex flex-col items-center justify-center p-2">
        <x-lucide-crown class="w-8 h-8 text-primary drop-shadow-[0_0_10px_rgba(16,185,129,0.5)]" />
        <span class="font-label text-[9px] tracking-widest uppercase text-neutral-400 mt-1">GAMBIT</span>
    </div>

    @if ($elo)
        <!-- ELO Text Pill Badge -->
        <div class="absolute -bottom-2 -right-3 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-surface-elevated border border-border shadow-md">
            <x-lucide-zap class="w-3 h-3 text-primary fill-primary" />
            <span class="font-label text-[10px] font-bold text-primary">{{ $elo }}</span>
        </div>
    @else
        <!-- Compact Circular Zap Badge -->
        <div class="absolute -bottom-1.5 -right-1.5 w-6 h-6 rounded-full bg-primary border-2 border-canvas shadow-md flex items-center justify-center">
            <x-lucide-zap class="w-3 h-3 text-neutral-950 fill-neutral-950" />
        </div>
    @endif
</div>
