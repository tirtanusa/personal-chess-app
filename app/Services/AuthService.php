<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user account without logging them in.
     *
     * @param  array{email: string, username: string, password: string}  $data
     */
    public function register(array $data): User
    {
        $user = DB::transaction(function () use ($data): User {
            return User::create([
                'name' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
        });

        event(new Registered($user));

        return $user;
    }

    /**
     * Authenticate a user using email or username with rate limiting.
     *
     * @throws ValidationException
     */
    public function login(string $login, string $password, bool $remember = false, string $ip = ''): User
    {
        $throttleKey = $this->throttleKey($login, $ip);

        $this->ensureIsNotRateLimited($throttleKey, $login, $ip);

        $identifier = trim($login);
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $authenticated = Auth::attempt([
            $field => $identifier,
            'password' => $password,
        ], $remember);

        if (! $authenticated) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * Check whether the user still needs first-time chess account onboarding.
     */
    public function needsOnboarding(User $user): bool
    {
        return $user->players()->doesntExist();
    }

    /**
     * Log out the currently authenticated user.
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();
    }

    /**
     * Ensure the login attempt has not exceeded rate limits.
     *
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(string $throttleKey, string $login, string $ip): void
    {
        if (! RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return;
        }

        event(new Lockout(Request::create('/', 'POST', ['login' => $login], server: ['REMOTE_ADDR' => $ip])));

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Build the rate limiter key for a login attempt.
     */
    private function throttleKey(string $login, string $ip): string
    {
        return Str::transliterate(Str::lower($login).'|'.$ip);
    }
}
