<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

test('login screen can be rendered', function (): void {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('Enter the Arena');
});

test('login validates required credentials', function (): void {
    $response = $this->post(route('login'), []);

    $response->assertSessionHasErrors(['login', 'password']);
});

test('login accepts checkbox remember value on and redirects to onboarding or home', function (): void {
    $user = new User(['name' => 'Magnus', 'email' => 'magnus@gambit.io']);
    $user->id = 1;

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('login')
        ->once()
        ->with('magnus@gambit.io', 'secret1234', true, '127.0.0.1')
        ->andReturn($user);
    $authService->shouldReceive('needsOnboarding')
        ->once()
        ->with($user)
        ->andReturn(true);

    $this->app->instance(AuthService::class, $authService);

    $response = $this->post(route('login'), [
        'login' => 'magnus@gambit.io',
        'password' => 'secret1234',
        'remember' => 'on',
    ]);

    $response->assertRedirect(route('onboarding.connect'));
});

test('login redirects onboarded user to home', function (): void {
    $user = new User(['name' => 'Magnus', 'email' => 'magnus@gambit.io']);
    $user->id = 1;

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('login')
        ->once()
        ->with('magnus@gambit.io', 'secret1234', false, '127.0.0.1')
        ->andReturn($user);
    $authService->shouldReceive('needsOnboarding')
        ->once()
        ->with($user)
        ->andReturn(false);

    $this->app->instance(AuthService::class, $authService);

    $response = $this->post(route('login'), [
        'login' => 'magnus@gambit.io',
        'password' => 'secret1234',
    ]);

    $response->assertRedirect(route('home', absolute: false));
});

test('authenticated user can log out', function (): void {
    $user = new User(['name' => 'Magnus', 'email' => 'magnus@gambit.io']);
    $user->id = 1;

    $this->actingAs($user);

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('login'));
    expect(Auth::check())->toBeFalse();
});

test('guest cannot access logout endpoint', function (): void {
    $response = $this->post(route('logout'));

    $response->assertRedirect(route('login'));
});

test('login request contains validation rules only', function (): void {
    $request = new LoginRequest;
    $validator = Validator::make([
        'login' => 'magnus',
        'password' => 'secret1234',
        'remember' => true,
    ], $request->rules());

    expect($request->authorize())->toBeTrue()
        ->and($validator->passes())->toBeTrue()
        ->and($request->rules())->toBe([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ])
        ->and(method_exists(LoginRequest::class, 'authenticate'))->toBeFalse()
        ->and(method_exists(AuthService::class, 'login'))->toBeTrue()
        ->and(method_exists(AuthService::class, 'logout'))->toBeTrue()
        ->and(method_exists(AuthenticatedSessionController::class, 'store'))->toBeTrue()
        ->and(method_exists(AuthenticatedSessionController::class, 'destroy'))->toBeTrue();
});
