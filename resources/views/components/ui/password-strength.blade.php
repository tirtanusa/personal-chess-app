@props([
    'target' => 'password',
])

<div class="flex flex-col gap-1.5 pt-0.5" data-strength-meter="{{ $target }}">
    <!-- 4-Segment Progress Bars -->
    <div class="grid grid-cols-4 gap-1.5">
        <span data-bar class="h-1 rounded-full bg-border transition-colors duration-200"></span>
        <span data-bar class="h-1 rounded-full bg-border transition-colors duration-200"></span>
        <span data-bar class="h-1 rounded-full bg-border transition-colors duration-200"></span>
        <span data-bar class="h-1 rounded-full bg-border transition-colors duration-200"></span>
    </div>

    <!-- Label & Strength Status -->
    <div class="flex items-center justify-between gap-2">
        <span class="text-[11px] text-neutral-400 font-body">Password Strength</span>
        <span data-strength-label class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-500">
            WEAK
        </span>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-strength-meter]').forEach((meter) => {
                    const inputId = meter.getAttribute('data-strength-meter');
                    const input = document.getElementById(inputId);
                    if (!input) return;

                    const bars = meter.querySelectorAll('[data-bar]');
                    const label = meter.querySelector('[data-strength-label]');

                    const states = [
                        { text: 'WEAK', color: 'bg-danger-500', textClass: 'text-danger-400' },
                        { text: 'FAIR', color: 'bg-secondary-500', textClass: 'text-secondary-400' },
                        { text: 'GOOD', color: 'bg-tertiary-400', textClass: 'text-tertiary-300' },
                        { text: 'STRONG', color: 'bg-primary', textClass: 'text-primary' },
                    ];

                    const updateMeter = () => {
                        const val = input.value;
                        let score = 0;
                        if (val.length >= 8) score++;
                        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
                        if (/\d/.test(val)) score++;
                        if (/[^A-Za-z0-9]/.test(val)) score++;

                        if (val.length === 0) {
                            bars.forEach((bar) => {
                                bar.className = 'h-1 rounded-full bg-border transition-colors duration-200';
                            });
                            label.textContent = 'WEAK';
                            label.className = 'font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-500';
                            return;
                        }

                        const activeCount = Math.max(1, score);
                        const state = states[activeCount - 1];

                        bars.forEach((bar, index) => {
                            bar.className = index < activeCount
                                ? `h-1 rounded-full ${state.color} transition-colors duration-200`
                                : 'h-1 rounded-full bg-border transition-colors duration-200';
                        });

                        label.textContent = state.text;
                        label.className = `font-label text-[10px] font-semibold tracking-wider uppercase ${state.textClass}`;
                    };

                    input.addEventListener('input', updateMeter);
                });
            });
        </script>
    @endpush
@endonce
