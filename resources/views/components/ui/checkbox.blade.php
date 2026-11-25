@props([
    'name' => 'remember',
    'id' => null,
    'label' => '',
    'checked' => false,
])

@php
    $checkboxId = $id ?? $name;
@endphp

<label for="{{ $checkboxId }}" class="inline-flex items-start gap-2.5 cursor-pointer select-none group">
    <span class="relative inline-flex items-center justify-center mt-0.5 shrink-0">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $checkboxId }}"
            @checked($checked)
            class="peer sr-only"
        >
        <span class="w-4 h-4 rounded-[5px] border border-border bg-canvas peer-checked:bg-primary-200 peer-checked:border-primary-200 peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40 transition-colors"></span>
        <x-lucide-check class="w-3 h-3 text-neutral-950 stroke-[3] pointer-events-none absolute opacity-0 peer-checked:opacity-100 transition-opacity" />
    </span>

    @if ($label)
        <span class="text-xs text-neutral-300 group-hover:text-white transition-colors font-body leading-relaxed">
            {{ $label }}
        </span>
    @elseif ($slot->isNotEmpty())
        <span class="text-xs text-neutral-400 font-body leading-relaxed">
            {{ $slot }}
        </span>
    @endif
</label>
