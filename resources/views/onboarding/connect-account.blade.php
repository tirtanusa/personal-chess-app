<x-layouts.auth title="Connect Chess Account - Gambit Arena" :back-url="route('login')" :centered="false">
    <div class="w-full flex flex-col gap-5" data-onboarding-card>
        <!-- Onboarding Card -->
        <div class="w-full bg-surface border border-border-subtle rounded-2xl p-5 shadow-2xl flex flex-col gap-4">
            <form action="#" method="POST" class="flex flex-col gap-4" onsubmit="event.preventDefault()">
                @csrf

                <!-- Platform Toggle (Chess.com / Lichess) -->
                <x-onboarding.platform-toggle name="platform" default="chesscom" />

                <!-- Username Input + Inline Check Button -->
                <x-onboarding.username-lookup
                    name="username"
                    placeholder="Enter username / profile"
                />

                <!-- Primary Submit CTA -->
                <button
                    type="submit"
                    class="w-full py-3.5 px-5 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.99] text-neutral-950 font-headline font-bold text-sm shadow-[0_0_24px_-4px_rgba(76,213,157,0.45)] transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <x-lucide-badge-check class="w-4 h-4 shrink-0" />
                    <span>Submit</span>
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.querySelector('[data-onboarding-card]');
                if (!container) return;

                container.addEventListener('platform-changed', (event) => {
                    const platform = event.detail.platform;
                    const chessIcon = container.querySelector('[data-icon="chesscom"]');
                    const lichessIcon = container.querySelector('[data-icon="lichess"]');

                    if (chessIcon && lichessIcon) {
                        chessIcon.classList.toggle('hidden', platform !== 'chesscom');
                        lichessIcon.classList.toggle('hidden', platform !== 'lichess');
                    }
                });
            });
        </script>
    @endpush
</x-layouts.auth>
