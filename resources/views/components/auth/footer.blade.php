@props([
    'registerUrl' => null,
    'termsUrl' => '#',
    'privacyUrl' => '#',
])

@php
    $targetRegisterUrl = $registerUrl ?? (\Illuminate\Support\Facades\Route::has('register') ? route('register') : '#');
@endphp

<div class="w-full flex flex-col items-center text-center gap-3 pt-1">
    <p class="text-sm text-neutral-400 font-body">
        Don't have an account?
        <a href="{{ $targetRegisterUrl }}" class="text-primary font-semibold hover:text-primary-200 hover:underline transition-colors">
            Sign Up Now
        </a>
    </p>

    <p class="text-[11px] leading-relaxed text-neutral-500 max-w-xs font-body">
        By signing in, you agree to the
        <a href="{{ $termsUrl }}" class="text-neutral-400 underline underline-offset-2 hover:text-neutral-200 transition-colors">Terms of Service</a>
        &amp;
        <a href="{{ $privacyUrl }}" class="text-neutral-400 underline underline-offset-2 hover:text-neutral-200 transition-colors">Privacy Policy</a>
        of Gambit Arena.
    </p>
</div>
