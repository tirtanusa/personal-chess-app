<x-layouts.auth title="Create Account - Gambit Arena" :back-url="route('login')">
    <div class="w-full flex flex-col gap-6">
        <!-- Brand Emblem & Register Header -->
        <x-auth.register-header />

        <!-- Register Form Card -->
        <div class="w-full bg-surface border border-border-subtle rounded-2xl p-6 shadow-2xl flex flex-col gap-5">
            <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <!-- Email Address -->
                <x-ui.input-field
                    label="Email Address"
                    type="email"
                    name="email"
                    placeholder="name@email.com"
                    icon="mail"
                    autocomplete="email"
                />

                <!-- Grandmaster Username -->
                <x-ui.input-field
                    label="Grandmaster Username"
                    hint="ELO 1200+"
                    :hint-accent="true"
                    type="text"
                    name="username"
                    placeholder="e.g. magnus_wannabe"
                    icon="user"
                    autocomplete="username"
                >
                    <div class="flex items-start gap-2 pt-0.5 text-neutral-400">
                        <x-lucide-info class="w-3.5 h-3.5 shrink-0 mt-0.5 text-neutral-400" />
                        <p class="text-[11px] leading-relaxed font-body">
                            Your public identity on tournament boards &amp; analysis history.
                        </p>
                    </div>
                </x-ui.input-field>

                <!-- Password + Strength Meter -->
                <x-ui.input-field
                    label="Password"
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Minimum 8 characters"
                    icon="lock"
                    :password-toggle="true"
                    autocomplete="new-password"
                >
                    <x-ui.password-strength target="password" />
                </x-ui.input-field>

                <!-- Confirm Password -->
                <x-ui.input-field
                    label="Confirm Password"
                    type="password"
                    name="password_confirmation"
                    placeholder="Re-enter password"
                    icon="shield-check"
                    autocomplete="new-password"
                />

                <!-- Terms Agreement Checkbox -->
                <div class="flex flex-col gap-1.5">
                    <x-ui.checkbox name="terms" :checked="(bool) old('terms')">
                        I agree to the
                        <a href="#" class="text-primary hover:text-primary-200 hover:underline transition-colors">Terms of Service</a>
                        and
                        <a href="#" class="text-primary hover:text-primary-200 hover:underline transition-colors">Privacy Policy</a>
                        of Gambit Chess.
                    </x-ui.checkbox>

                    @error('terms')
                        <p class="text-[11px] text-danger-400 font-body">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Primary Submit CTA -->
                <button
                    type="submit"
                    class="w-full py-3.5 px-5 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.99] text-neutral-950 font-headline font-bold text-sm shadow-[0_0_24px_-4px_rgba(76,213,157,0.45)] transition-all flex items-center justify-center gap-2 cursor-pointer"
                >
                    <span>Create Account</span>
                    <x-lucide-arrow-right class="w-4 h-4 shrink-0" />
                </button>
            </form>

            <!-- Divider & Social Register -->
            <x-auth.social-buttons divider-text="Or Sign Up With" />
        </div>

        <!-- Login Redirect Footer -->
        <p class="text-center text-sm text-neutral-400 font-body pb-2">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary font-semibold underline underline-offset-4 hover:text-primary-200 transition-colors">
                Sign in here
            </a>
        </p>
    </div>
</x-layouts.auth>
