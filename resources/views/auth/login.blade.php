<x-layouts.auth title="Sign In - Gambit Arena">
    <div class="w-full flex flex-col gap-6">
        <!-- Brand Header & Welcome Banner -->
        <x-auth.header />

        <!-- Login Form Card -->
        <div class="w-full bg-surface border border-border-subtle rounded-2xl p-6 shadow-2xl flex flex-col gap-5">
            <form action="#" method="POST" class="flex flex-col gap-5" onsubmit="event.preventDefault()">
                @csrf

                <!-- Email / Chess Username -->
                <x-ui.input-field
                    label="Email or Chess Username"
                    hint="ID: FIDE / GAMBIT"
                    :hint-accent="true"
                    type="text"
                    name="login"
                    placeholder="grandmaster@gambit.io"
                    icon="at-sign"
                    autocomplete="username"
                />

                <!-- Password -->
                <x-ui.input-field
                    label="Password"
                    hint="MIN. 8 CHARACTERS"
                    type="password"
                    name="password"
                    placeholder="••••••••••••"
                    icon="lock"
                    :password-toggle="true"
                    autocomplete="current-password"
                />

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between gap-2">
                    <x-ui.checkbox
                        name="remember"
                        label="Remember me"
                        :checked="true"
                    />

                    <a href="#" class="text-xs font-medium text-primary hover:text-primary-200 hover:underline transition-colors">
                        Forgot password?
                    </a>
                </div>

                <!-- Primary CTA Submit Button -->
                <button
                    type="submit"
                    class="w-full py-3.5 px-5 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.99] text-neutral-950 font-headline font-bold text-sm shadow-[0_0_24px_-4px_rgba(76,213,157,0.45)] transition-all flex items-center justify-center gap-2.5 cursor-pointer"
                >
                    <x-lucide-gamepad-2 class="w-4 h-4 shrink-0" />
                    <span>Enter the Arena</span>
                    <x-lucide-arrow-right class="w-4 h-4 shrink-0" />
                </button>
            </form>

            <!-- Divider & Social Login -->
            <x-auth.social-buttons />
        </div>

        <!-- Register Prompt & Terms -->
        <x-auth.footer />
    </div>
</x-layouts.auth>
