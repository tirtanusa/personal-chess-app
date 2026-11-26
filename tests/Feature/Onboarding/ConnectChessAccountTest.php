<?php

use App\Http\Controllers\Onboarding\ConnectChessAccountController;
use App\Http\Requests\Onboarding\CheckChessAccountRequest;
use App\Models\Player;
use App\Models\User;
use App\Services\ChessPlatformService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

test('onboarding screen renders with disabled submit button by default', function (): void {
    $response = $this->get(route('onboarding.connect'));

    $response->assertOk()
        ->assertSee('data-submit-btn', false)
        ->assertSee('disabled', false)
        ->assertSee('Chess.com')
        ->assertSee('Lichess');
});

test('check endpoint verifies existing chesscom account', function (): void {
    Http::fake([
        'https://api.chess.com/pub/player/hikaru' => Http::response([
            'username' => 'Hikaru',
            'name' => 'Hikaru Nakamura',
            'avatar' => 'https://example.com/hikaru.jpg',
            'title' => 'GM',
            'url' => 'https://www.chess.com/member/hikaru',
        ], 200),
    ]);

    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'chesscom',
        'username' => 'Hikaru',
    ]);

    $response->assertOk()
        ->assertJson([
            'found' => true,
            'player' => [
                'platform' => 'chesscom',
                'username' => 'Hikaru',
                'display_name' => 'Hikaru Nakamura',
                'title' => 'GM',
            ],
        ]);
});

test('check endpoint verifies existing lichess account', function (): void {
    Http::fake([
        'https://lichess.org/api/user/drnykterstein' => Http::response([
            'id' => 'drnykterstein',
            'username' => 'DrNykterstein',
            'title' => 'GM',
            'profile' => [
                'firstName' => 'Magnus',
                'lastName' => 'Carlsen',
            ],
            'perfs' => [
                'rapid' => ['rating' => 2810],
                'blitz' => ['rating' => 2890],
                'bullet' => ['rating' => 2950],
                'classical' => ['rating' => 2830],
            ],
            'url' => 'https://lichess.org/@/DrNykterstein',
        ], 200),
    ]);

    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'lichess',
        'username' => 'DrNykterstein',
    ]);

    $response->assertOk()
        ->assertJson([
            'found' => true,
            'player' => [
                'platform' => 'lichess',
                'username' => 'DrNykterstein',
                'display_name' => 'Magnus Carlsen',
                'title' => 'GM',
                'ratings' => [
                    'rapid' => 2810,
                    'blitz' => 2890,
                    'bullet' => 2950,
                    'classic' => 2830,
                ],
            ],
        ]);
});

test('check endpoint returns 404 when username is not found on platform', function (): void {
    Http::fake([
        'https://api.chess.com/pub/player/unknown_user_9999' => Http::response([], 404),
    ]);

    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'chesscom',
        'username' => 'unknown_user_9999',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'found' => false,
        ]);
});

test('check endpoint returns 503 when external platform api fails', function (): void {
    Http::fake([
        'https://lichess.org/api/user/*' => Http::response([], 500),
    ]);

    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'lichess',
        'username' => 'magnus',
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'found' => false,
        ]);
});

test('check endpoint validates platform and username format', function (): void {
    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'invalid_site',
        'username' => 'a',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['platform', 'username']);
});

test('store endpoint connects player via service and redirects to home', function (): void {
    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $service = Mockery::mock(ChessPlatformService::class);
    $service->shouldReceive('connectPlayerToUser')
        ->once()
        ->with($user, 'chesscom', 'Hikaru')
        ->andReturn(new Player(['chesscom_username' => 'hikaru']));

    $this->app->instance(ChessPlatformService::class, $service);

    $this->actingAs($user);

    $response = $this->post(route('onboarding.connect.store'), [
        'platform' => 'chesscom',
        'username' => 'Hikaru',
    ]);

    $response->assertRedirect(route('home'));
    expect(method_exists(CheckChessAccountRequest::class, 'rules'))->toBeTrue()
        ->and(method_exists(ConnectChessAccountController::class, 'check'))->toBeTrue()
        ->and(method_exists(ConnectChessAccountController::class, 'store'))->toBeTrue();
});

test('fetchChessComGamesAndActivity fetches chesscom archives and aggregates daily heatmap activity', function (): void {
    $today = CarbonImmutable::today();
    $yesterday = $today->subDay();
    $oldGameDate = $today->subYears(2);

    $currentMonthUrl = 'https://api.chess.com/pub/player/hikaru/games/'.$today->format('Y/m');
    $oldMonthUrl = 'https://api.chess.com/pub/player/hikaru/games/'.$oldGameDate->format('Y/m');

    Http::fake([
        'https://api.chess.com/pub/player/hikaru/games/archives' => Http::response([
            'archives' => [$oldMonthUrl, $currentMonthUrl],
        ], 200),
        $oldMonthUrl => Http::response([
            'games' => [
                [
                    'uuid' => 'old-game-uuid',
                    'url' => 'https://www.chess.com/game/live/old',
                    'time_class' => 'blitz',
                    'time_control' => '180',
                    'rated' => true,
                    'end_time' => $oldGameDate->setTime(10, 0)->timestamp,
                    'white' => ['username' => 'Hikaru', 'rating' => 3200, 'result' => 'win'],
                    'black' => ['username' => 'Opponent0', 'rating' => 2900, 'result' => 'resigned'],
                ],
            ],
        ], 200),
        $currentMonthUrl => Http::response([
            'games' => [
                [
                    'uuid' => 'game-1-uuid',
                    'url' => 'https://www.chess.com/game/live/1',
                    'time_class' => 'rapid',
                    'time_control' => '600',
                    'rated' => true,
                    'end_time' => $yesterday->setTime(14, 0)->timestamp,
                    'white' => ['username' => 'Hikaru', 'rating' => 2810, 'result' => 'win'],
                    'black' => ['username' => 'Opponent1', 'rating' => 2700, 'result' => 'checkmated'],
                ],
                [
                    'uuid' => 'game-2-uuid',
                    'url' => 'https://www.chess.com/game/live/2',
                    'time_class' => 'blitz',
                    'time_control' => '180',
                    'rated' => true,
                    'end_time' => $today->setTime(9, 0)->timestamp,
                    'white' => ['username' => 'Opponent2', 'rating' => 3100, 'result' => 'agreed'],
                    'black' => ['username' => 'Hikaru', 'rating' => 3280, 'result' => 'agreed'],
                ],
                [
                    'uuid' => 'game-3-uuid',
                    'url' => 'https://www.chess.com/game/daily/3',
                    'time_class' => 'daily',
                    'time_control' => '1/86400',
                    'rated' => true,
                    'end_time' => $today->setTime(12, 0)->timestamp,
                    'white' => ['username' => 'Hikaru', 'rating' => 2450, 'result' => 'resigned'],
                    'black' => ['username' => 'Opponent3', 'rating' => 2460, 'result' => 'win'],
                ],
            ],
        ], 200),
    ]);

    $result = app(ChessPlatformService::class)->fetchChessComGamesAndActivity('Hikaru', 5);

    $modesFound = collect($result['recent_games'])->pluck('time_class')->all();

    expect($result['recent_games'])->toHaveCount(4)
        ->and($modesFound)->toContain('rapid', 'blitz', 'classic')
        ->and($result['daily_activities'])->toHaveCount(2);

    $yesterdayStats = $result['daily_activities'][$yesterday->toDateString()] ?? null;
    expect($yesterdayStats)->toBe([
        'games_count' => 1,
        'wins_count' => 1,
        'draws_count' => 0,
        'losses_count' => 0,
        'rapid_count' => 1,
        'blitz_count' => 0,
        'bullet_count' => 0,
        'classic_count' => 0,
        'rapid_rating' => 2810,
        'blitz_rating' => null,
        'bullet_rating' => null,
        'classic_rating' => null,
    ]);

    $todayStats = $result['daily_activities'][$today->toDateString()] ?? null;
    expect($todayStats)->toBe([
        'games_count' => 2,
        'wins_count' => 0,
        'draws_count' => 1,
        'losses_count' => 1,
        'rapid_count' => 0,
        'blitz_count' => 1,
        'bullet_count' => 0,
        'classic_count' => 1,
        'rapid_rating' => null,
        'blitz_rating' => 3280,
        'bullet_rating' => null,
        'classic_rating' => 2450,
    ]);
});

test('chesscom requests include User-Agent header with authenticated user email', function (): void {
    $user = new User(['name' => 'pridmizu', 'email' => 'pridmizu17@gmail.com']);
    $user->id = 1;

    Http::fake([
        'https://api.chess.com/pub/player/hikaru' => Http::response([
            'username' => 'Hikaru',
            'name' => 'Hikaru Nakamura',
            'avatar' => 'https://example.com/hikaru.jpg',
        ], 200),
    ]);

    $this->actingAs($user);

    $response = $this->postJson(route('onboarding.connect.check'), [
        'platform' => 'chesscom',
        'username' => 'Hikaru',
    ]);

    $response->assertOk();

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://api.chess.com/pub/player/hikaru'
            && $request->hasHeader('User-Agent', 'chess-project-personal/1.0 (pridmizu17@gmail.com)');
    });
});

test('fetchChessComGamesAndActivity loads all games across all archives when limit is null', function (): void {
    $today = CarbonImmutable::today();
    $twoYearsAgo = $today->subYears(2);

    $oldArchiveUrl = 'https://api.chess.com/pub/player/hikaru/games/'.$twoYearsAgo->format('Y/m');
    $currentArchiveUrl = 'https://api.chess.com/pub/player/hikaru/games/'.$today->format('Y/m');

    $makeGame = fn (string $uuid, int $ts): array => [
        'uuid' => $uuid,
        'url' => 'https://www.chess.com/game/live/'.$uuid,
        'time_class' => 'blitz',
        'time_control' => '180',
        'rated' => true,
        'end_time' => $ts,
        'white' => ['username' => 'Hikaru', 'rating' => 3280, 'result' => 'win'],
        'black' => ['username' => 'Opponent', 'rating' => 3100, 'result' => 'resigned'],
    ];

    Http::fake([
        'https://api.chess.com/pub/player/hikaru/games/archives' => Http::response([
            'archives' => [$oldArchiveUrl, $currentArchiveUrl],
        ], 200),
        $oldArchiveUrl => Http::response([
            'games' => [
                $makeGame('old-1', $twoYearsAgo->setTime(10, 0)->timestamp),
                $makeGame('old-2', $twoYearsAgo->setTime(11, 0)->timestamp),
                $makeGame('old-3', $twoYearsAgo->setTime(12, 0)->timestamp),
            ],
        ], 200),
        $currentArchiveUrl => Http::response([
            'games' => [
                $makeGame('new-1', $today->setTime(9, 0)->timestamp),
                $makeGame('new-2', $today->setTime(10, 0)->timestamp),
                $makeGame('new-3', $today->setTime(11, 0)->timestamp),
                $makeGame('new-4', $today->setTime(12, 0)->timestamp),
            ],
        ], 200),
    ]);

    $result = app(ChessPlatformService::class)->fetchChessComGamesAndActivity('Hikaru', null);

    expect($result['recent_games'])->toHaveCount(7)
        ->and($result['daily_activities'])->toHaveCount(1);
});
