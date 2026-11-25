@props([
    'label',
    'hint' => null,
    'hintAccent' => false,
    'type' => 'text',
    'name' => '',
    'id' => null,
    'placeholder' => '',
    'value' => '',
    'icon' => null,
    'passwordToggle' => false,
])

@php
    $inputId = $id ?? ($name ?: \Illuminate\Support\Str::slug($label) . '-' . uniqid());
@endphp

<div class="flex flex-col gap-2">
    <!-- Label & Right Hint -->
    <div class="flex items-center justify-between gap-2">
        <label for="{{ $inputId }}" class="font-label text-[11px] font-semibold tracking-wider uppercase text-neutral-200">
            {{ $label }}
        </label>

        @if ($hint)
            <span @class([
                'font-label text-[11px] font-medium tracking-wider uppercase',
                'text-primary' => $hintAccent,
                'text-neutral-500' => ! $hintAccent,
            ])>
                {{ $hint }}
            </span>
        @endif
    </div>

    <!-- Input Container -->
    <div class="relative flex items-center">
        @if ($icon)
            <span class="pointer-events-none absolute left-3.5 text-neutral-500 flex items-center">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
            </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'w-full bg-canvas border border-border rounded-xl py-3 text-sm text-neutral-100 placeholder:text-neutral-500 font-body focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/40 transition-colors ' .
                    ($icon ? 'pl-10 ' : 'pl-4 ') .
                    ($passwordToggle ? 'pr-11' : 'pr-4'),
            ]) }}
        >

        @if ($passwordToggle)
            <button
                type="button"
                onclick="(function(btn){
                    const input = document.getElementById('{{ $inputId }}');
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    btn.querySelector('[data-eye-open]').classList.toggle('hidden', isHidden);
                    btn.querySelector('[data-eye-off]').classList.toggle('hidden', !isHidden);
                })(this)"
                class="absolute right-3.5 text-neutral-400 hover:text-neutral-200 transition-colors flex items-center cursor-pointer"
                aria-label="Show or hide password"
            >
                <span data-eye-open class="flex items-center">
                    <x-lucide-eye class="w-4 h-4" />
                </span>
                <span data-eye-off class="hidden items-center">
                    <x-lucide-eye-off class="w-4 h-4" />
                </span>
            </button>
        @endif
    </div>

    {{ $slot }}
</div>
