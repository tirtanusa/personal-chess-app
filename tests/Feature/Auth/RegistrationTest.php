<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class);

test('registration screen can be rendered', function (): void {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Create Account');
});

test('registration validates required fields and terms', function (): void {
    $response = $this->post(route('register'), []);

    $response->assertSessionHasErrors(['email', 'username', 'password', 'terms']);
});

test('registration validates password confirmation', function (): void {
    $response = $this->post(route('register'), [
        'password' => 'secret1234',
        'password_confirmation' => 'different1234',
        'terms' => 'on',
    ]);

    $response->assertSessionHasErrors(['password']);

    $request = new RegisterRequest;
    expect($request->authorize())->toBeTrue()
        ->and(array_keys($request->rules()))->toBe(['email', 'username', 'password', 'terms']);
});

test('registered user is redirected to login without automatic sign-in', function (): void {
    $user = new User(['name' => 'magnus_wannabe', 'email' => 'magnus@gambit.io']);

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('register')
        ->once()
        ->with([
            'email' => 'magnus@gambit.io',
            'username' => 'magnus_wannabe',
            'password' => 'secret1234',
        ])
        ->andReturn($user);

    $request = Mockery::mock(RegisterRequest::class);
    $request->shouldReceive('validated')
        ->once()
        ->andReturn([
            'email' => 'magnus@gambit.io',
            'username' => 'magnus_wannabe',
            'password' => 'secret1234',
        ]);

    $controller = new RegisteredUserController($authService);
    $response = $controller->store($request);

    expect($response->getTargetUrl())->toBe(route('login'))
        ->and($response->getSession()->get('status'))->toBe('Account created successfully. Please sign in to continue.')
        ->and(Auth::check())->toBeFalse();
});

test('login page renders registration status banner', function (): void {
    $response = $this->withSession([
        'status' => 'Account created successfully. Please sign in to continue.',
    ])->get(route('login'));

    $response->assertOk()
        ->assertSee('Account created successfully. Please sign in to continue.');
});
