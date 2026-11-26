<x-layouts.auth title="Connect Chess Account - Gambit Arena" :back-url="route('login')">
    <div class="w-full flex flex-col gap-5" data-onboarding-card data-check-url="{{ route('onboarding.connect.check') }}">
        <!-- Onboarding Card -->
        <div class="w-full bg-surface border border-border-subtle rounded-2xl p-5 shadow-2xl flex flex-col gap-4">
            <form action="{{ route('onboarding.connect.store') }}" method="POST" class="flex flex-col gap-4" data-onboarding-form>
                @csrf

                <!-- Platform Toggle (Chess.com / Lichess) -->
                <x-onboarding.platform-toggle name="platform" :default="old('platform', 'chesscom')" />

                <!-- Username Input + Inline Check Button -->
                <x-onboarding.username-lookup
                    name="username"
                    placeholder="Enter username / profile"
                />

                <!-- Dynamic Verification Feedback -->
                <div
                    data-verified-card
                    class="hidden bg-primary/10 border border-primary/30 rounded-xl px-3.5 py-2.5 items-center justify-between gap-3"
                >
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-primary/20 border border-primary/30 text-primary font-headline text-xs font-bold flex items-center justify-center shrink-0" data-verified-initials></span>
                        <div class="flex flex-col min-w-0">
                            <span class="font-headline text-xs font-bold text-white truncate" data-verified-name></span>
                            <span class="font-label text-[10px] text-primary truncate" data-verified-meta></span>
                        </div>
                    </div>
                    <x-lucide-circle-check class="w-4 h-4 text-primary shrink-0" />
                </div>

                <div
                    data-check-error
                    @class([
                        'bg-danger/10 border border-danger/30 rounded-xl px-3.5 py-2.5 items-center gap-2 text-xs text-danger-400 font-body',
                        'flex' => $errors->has('username') || $errors->has('platform'),
                        'hidden' => ! $errors->has('username') && ! $errors->has('platform'),
                    ])
                >
                    <x-lucide-circle-alert class="w-4 h-4 shrink-0" />
                    <span data-check-error-text>{{ $errors->first('username') ?: $errors->first('platform') }}</span>
                </div>

                <!-- Primary Submit CTA (disabled & greyed out until checked & verified) -->
                <button
                    type="submit"
                    disabled
                    data-submit-btn
                    class="w-full py-3.5 px-5 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.99] text-neutral-950 font-headline font-bold text-sm shadow-[0_0_24px_-4px_rgba(76,213,157,0.45)] disabled:bg-surface-elevated disabled:hover:bg-surface-elevated disabled:text-neutral-500 disabled:border disabled:border-border disabled:shadow-none disabled:cursor-not-allowed disabled:active:scale-100 transition-all flex items-center justify-center gap-2 cursor-pointer"
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

                const checkUrl = container.getAttribute('data-check-url');
                const form = container.querySelector('[data-onboarding-form]');
                const csrfInput = form.querySelector('input[name="_token"]');
                const platformInput = container.querySelector('[data-platform-input]');
                const usernameInput = container.querySelector('[data-username-input]');
                const lookupBox = container.querySelector('[data-lookup-box]');
                const checkBtn = container.querySelector('[data-check-btn]');
                const checkIcon = container.querySelector('[data-check-icon]');
                const checkSpinner = container.querySelector('[data-check-spinner]');
                const checkLabel = container.querySelector('[data-check-label]');
                const submitBtn = container.querySelector('[data-submit-btn]');
                const verifiedCard = container.querySelector('[data-verified-card]');
                const verifiedInitials = container.querySelector('[data-verified-initials]');
                const verifiedName = container.querySelector('[data-verified-name]');
                const verifiedMeta = container.querySelector('[data-verified-meta]');
                const errorBox = container.querySelector('[data-check-error]');
                const errorText = container.querySelector('[data-check-error-text]');

                let isVerified = false;
                let verifiedSignature = '';

                const syncPlatformIcons = (platform) => {
                    const chessIcon = container.querySelector('[data-icon="chesscom"]');
                    const lichessIcon = container.querySelector('[data-icon="lichess"]');

                    if (chessIcon && lichessIcon) {
                        chessIcon.classList.toggle('hidden', platform !== 'chesscom');
                        lichessIcon.classList.toggle('hidden', platform !== 'lichess');
                    }
                };

                const resetVerification = (clearError = true) => {
                    isVerified = false;
                    verifiedSignature = '';
                    submitBtn.disabled = true;

                    verifiedCard.classList.add('hidden');
                    verifiedCard.classList.remove('flex');

                    lookupBox.classList.remove('border-primary', 'border-danger-400');
                    lookupBox.classList.add('border-border');

                    if (clearError) {
                        errorBox.classList.add('hidden');
                        errorBox.classList.remove('flex');
                        errorText.textContent = '';
                    }
                };

                const showError = (message) => {
                    resetVerification(false);
                    errorText.textContent = message;
                    errorBox.classList.remove('hidden');
                    errorBox.classList.add('flex');
                    lookupBox.classList.remove('border-border', 'border-primary');
                    lookupBox.classList.add('border-danger-400');
                };

                const showVerified = (player) => {
                    const currentSig = `${platformInput.value}:${usernameInput.value.trim().toLowerCase()}`;
                    isVerified = true;
                    verifiedSignature = currentSig;
                    submitBtn.disabled = false;

                    errorBox.classList.add('hidden');
                    errorBox.classList.remove('flex');

                    const initials = (player.display_name || player.username || '?').slice(0, 2).toUpperCase();
                    const platformName = player.platform === 'chesscom' ? 'Chess.com' : 'Lichess';
                    const titlePrefix = player.title ? `${player.title} ` : '';

                    verifiedInitials.textContent = initials;
                    verifiedName.textContent = `${titlePrefix}${player.display_name || player.username}`;
                    verifiedMeta.textContent = `@${player.username} • Verified on ${platformName}`;

                    verifiedCard.classList.remove('hidden');
                    verifiedCard.classList.add('flex');

                    lookupBox.classList.remove('border-border', 'border-danger-400');
                    lookupBox.classList.add('border-primary');
                };

                const setChecking = (loading) => {
                    checkBtn.disabled = loading;
                    checkIcon.classList.toggle('hidden', loading);
                    checkIcon.classList.toggle('flex', !loading);
                    checkSpinner.classList.toggle('hidden', !loading);
                    checkSpinner.classList.toggle('flex', loading);
                    checkLabel.textContent = loading ? 'Checking...' : 'Check';
                };

                const runCheck = async () => {
                    const username = usernameInput.value.trim();
                    const platform = platformInput.value;

                    if (username.length < 2) {
                        showError('Please enter a valid username first.');
                        return;
                    }

                    setChecking(true);

                    try {
                        const res = await fetch(checkUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfInput ? csrfInput.value : '',
                            },
                            body: JSON.stringify({ platform, username }),
                        });

                        const data = await res.json();

                        if (res.ok && data.found && data.player) {
                            showVerified(data.player);
                        } else {
                            const fallbackMsg = data.errors?.username?.[0]
                                || data.errors?.platform?.[0]
                                || data.message
                                || 'Username not found on the selected platform.';
                            showError(fallbackMsg);
                        }
                    } catch (err) {
                        showError('Network error while checking username. Please try again.');
                    } finally {
                        setChecking(false);
                    }
                };

                syncPlatformIcons(platformInput.value);

                container.addEventListener('platform-changed', (event) => {
                    syncPlatformIcons(event.detail.platform);
                    resetVerification(true);
                });

                usernameInput.addEventListener('input', () => {
                    const sig = `${platformInput.value}:${usernameInput.value.trim().toLowerCase()}`;
                    if (sig !== verifiedSignature) {
                        resetVerification(true);
                    }
                });

                usernameInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !isVerified) {
                        e.preventDefault();
                        runCheck();
                    }
                });

                checkBtn.addEventListener('click', runCheck);

                form.addEventListener('submit', (e) => {
                    const sig = `${platformInput.value}:${usernameInput.value.trim().toLowerCase()}`;
                    if (!isVerified || sig !== verifiedSignature) {
                        e.preventDefault();
                        showError('Please verify your username using the Check button first.');
                    }
                });
            });
        </script>
    @endpush
</x-layouts.auth>
