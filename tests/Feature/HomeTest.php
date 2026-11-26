<?php

use App\Http\Controllers\HomeController;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerDailyActivity;
use App\Models\User;
use App\Services\ChessPlatformService;
use App\Services\HomeDashboardService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class);

test('home page displays rapid blitz bullet and classic ratings without tactics', function (): void {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('RAPID')
        ->assertSee('BLITZ')
        ->assertSee('BULLET')
        ->assertSee('CLASSIC')
        ->assertSee('border-dashed', false)
        ->assertSee('MODE')
        ->assertDontSee('Fast Pair & Arena', false)
        ->assertDontSee('TACTICS')
        ->assertSee('Engine Pending');
});

test('home page displays connected player data and recent results from service', function (): void {
    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $service = Mockery::mock(HomeDashboardService::class);
    $service->shouldReceive('getDashboardData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => null,
            'profile' => [
                'name' => 'Hikaru Nakamura',
                'username' => 'hikaru',
                'avatarUrl' => 'https://example.com/hikaru.jpg',
                'flag' => '🇺🇸',
                'streak' => 4,
                'form' => ['W', 'D', 'W', 'W', 'W'],
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '2810', 'sub' => 'Current', 'subClass' => 'text-primary font-semibold', 'icon' => 'clock', 'iconClass' => 'text-primary'],
                    ['label' => 'BLITZ', 'value' => '3280', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '3315', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '2450', 'sub' => 'Current', 'subClass' => 'text-secondary font-medium', 'icon' => 'crown', 'iconClass' => 'text-secondary'],
                ],
            ],
            'insightsPending' => true,
            'recentMatches' => [
                [
                    'opponent' => 'vs MagnusCarlsen',
                    'rating' => '3290',
                    'opening' => 'Ruy Lopez',
                    'moves' => '42 moves',
                    'resultText' => 'WIN',
                    'mode' => 'Blitz 3+0',
                    'accentClass' => 'bg-primary',
                    'resultClass' => 'text-primary',
                ],
            ],
        ]);

    $this->app->instance(HomeDashboardService::class, $service);

    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Hikaru Nakamura')
        ->assertSee('https://example.com/hikaru.jpg', false)
        ->assertSee('2810')
        ->assertSee('3280')
        ->assertSee('3315')
        ->assertSee('2450')
        ->assertSee('4 Unbeaten')
        ->assertSee('Engine Pending')
        ->assertSee('vs MagnusCarlsen')
        ->assertSee('Ruy Lopez');
});

test('home page renders real activity heatmap streak winrate and yearly games from service', function (): void {
    $today = CarbonImmutable::today();
    $yesterday = $today->subDay();

    $activities = collect([
        $yesterday->toDateString() => new PlayerDailyActivity([
            'activity_date' => $yesterday->toDateString(),
            'games_count' => 4,
            'wins_count' => 3,
            'draws_count' => 0,
            'losses_count' => 1,
        ]),
        $today->toDateString() => new PlayerDailyActivity([
            'activity_date' => $today->toDateString(),
            'games_count' => 6,
            'wins_count' => 4,
            'draws_count' => 1,
            'losses_count' => 1,
        ]),
    ]);

    $heatmap = (new HomeDashboardService)->buildActivityHeatmap($activities);

    expect($heatmap['currentStreak'])->toBe(2)
        ->and($heatmap['totalGamesThisYear'])->toBe(10)
        ->and($heatmap['winrate'])->toBe('70.0%')
        ->and($heatmap['mostActiveLabel'])->toBe($today->format('l').' (6 games)');

    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $service = Mockery::mock(HomeDashboardService::class);
    $service->shouldReceive('getDashboardData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => null,
            'profile' => [
                'name' => 'Hikaru Nakamura',
                'username' => 'hikaru',
                'avatarUrl' => null,
                'flag' => '🇺🇸',
                'streak' => 2,
                'form' => ['W', 'W'],
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '2810', 'sub' => 'Current', 'subClass' => 'text-primary font-semibold', 'icon' => 'clock', 'iconClass' => 'text-primary'],
                    ['label' => 'BLITZ', 'value' => '3280', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '3315', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '2450', 'sub' => 'Current', 'subClass' => 'text-secondary font-medium', 'icon' => 'crown', 'iconClass' => 'text-secondary'],
                ],
            ],
            'insightsPending' => true,
            'recentMatches' => [],
            'activityHeatmap' => $heatmap,
        ]);

    $this->app->instance(HomeDashboardService::class, $service);

    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('2 Days')
        ->assertSee('70.0%')
        ->assertSee($today->format('l').' (6 games)')
        ->assertSee('data-games="6"', false)
        ->assertSee('data-games="4"', false);
});

test('home page renders refresh data button when player is connected', function (): void {
    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
        'display_name' => 'Hikaru Nakamura',
    ]);

    $service = Mockery::mock(HomeDashboardService::class);
    $service->shouldReceive('getDashboardData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => $player,
            'profile' => [
                'name' => 'Hikaru Nakamura',
                'username' => 'hikaru',
                'avatarUrl' => null,
                'flag' => '🇺🇸',
                'streak' => 0,
                'form' => [],
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '2810', 'sub' => 'Current', 'subClass' => 'text-primary font-semibold', 'icon' => 'clock', 'iconClass' => 'text-primary'],
                    ['label' => 'BLITZ', 'value' => '3280', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '3315', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '2450', 'sub' => 'Current', 'subClass' => 'text-secondary font-medium', 'icon' => 'crown', 'iconClass' => 'text-secondary'],
                ],
                'lastSynced' => 'Nov 26, 2026',
            ],
            'insightsPending' => true,
            'recentMatches' => [],
        ]);

    $this->app->instance(HomeDashboardService::class, $service);

    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('data-refresh-form', false)
        ->assertSee('Refresh Data')
        ->assertSee('Last synced:')
        ->assertSee('Nov 26, 2026');
});

test('home refresh endpoint requires authentication', function (): void {
    $response = $this->post(route('home.refresh'));

    $response->assertRedirect(route('login'));
});

test('home controller refresh forces player re-sync and returns status banner', function (): void {
    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
    ]);

    $relation = Mockery::mock(BelongsToMany::class);
    $relation->shouldReceive('latest')->once()->with('user_player.created_at')->andReturnSelf();
    $relation->shouldReceive('first')->once()->andReturn($player);

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('players')->once()->andReturn($relation);

    $chessService = Mockery::mock(ChessPlatformService::class);
    $chessService->shouldReceive('refreshPlayer')->once()->with($player)->andReturn($player);

    $controller = new HomeController(
        app(HomeDashboardService::class),
        $chessService,
    );

    $request = Request::create('/home/refresh', 'POST');
    $request->setUserResolver(fn (): User => $user);

    $response = $controller->refresh($request);

    expect($response->getTargetUrl())->toBe(route('home'))
        ->and($response->getSession()->get('status'))->toBe('Data synced from Chess.com for @hikaru.');
});

test('home controller refresh returns sync_error when platform sync fails', function (): void {
    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
    ]);

    $relation = Mockery::mock(BelongsToMany::class);
    $relation->shouldReceive('latest')->once()->with('user_player.created_at')->andReturnSelf();
    $relation->shouldReceive('first')->once()->andReturn($player);

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('players')->once()->andReturn($relation);

    $chessService = Mockery::mock(ChessPlatformService::class);
    $chessService->shouldReceive('refreshPlayer')
        ->once()
        ->with($player)
        ->andThrow(ValidationException::withMessages([
            'username' => 'Unable to verify account right now. Please try again.',
        ]));

    $controller = new HomeController(
        app(HomeDashboardService::class),
        $chessService,
    );

    $request = Request::create('/home/refresh', 'POST');
    $request->setUserResolver(fn (): User => $user);

    $response = $controller->refresh($request);

    expect($response->getTargetUrl())->toBe(route('home'))
        ->and($response->getSession()->get('sync_error'))->toBe('Unable to verify account right now. Please try again.');
});

test('home page renders separate mode sections for blitz rapid bullet and classical', function (): void {
    $today = CarbonImmutable::today();
    $activities = collect([
        $today->toDateString() => new PlayerDailyActivity([
            'activity_date' => $today->toDateString(),
            'games_count' => 5,
            'wins_count' => 4,
            'draws_count' => 0,
            'losses_count' => 1,
            'rapid_count' => 2,
            'blitz_count' => 3,
            'bullet_count' => 0,
            'classic_count' => 0,
        ]),
    ]);

    $dashboardService = new HomeDashboardService;
    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $service = Mockery::mock(HomeDashboardService::class);
    $service->shouldReceive('getDashboardData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => null,
            'activeMode' => 'blitz',
            'profile' => [
                'name' => 'Hikaru Nakamura',
                'username' => 'hikaru',
                'avatarUrl' => null,
                'flag' => '🇺🇸',
                'streak' => 3,
                'form' => ['W', 'W'],
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '2810', 'sub' => 'Current', 'subClass' => 'text-primary font-semibold', 'icon' => 'clock', 'iconClass' => 'text-primary'],
                    ['label' => 'BLITZ', 'value' => '3280', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '3315', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '2450', 'sub' => 'Current', 'subClass' => 'text-secondary font-medium', 'icon' => 'crown', 'iconClass' => 'text-secondary'],
                ],
                'lastSynced' => null,
            ],
            'insightsPending' => true,
            'recentMatches' => [],
            'recentMatchesByMode' => [
                'blitz' => [[
                    'opponent' => 'vs BlitzRival',
                    'rating' => '3200',
                    'opening' => 'Sicilian Defense',
                    'moves' => '30 moves',
                    'resultText' => 'WIN',
                    'mode' => 'Blitz 3m',
                    'accentClass' => 'bg-primary',
                    'resultClass' => 'text-primary',
                ]],
                'rapid' => [[
                    'opponent' => 'vs RapidRival',
                    'rating' => '2790',
                    'opening' => 'Queen Gambit',
                    'moves' => '45 moves',
                    'resultText' => 'DRAW',
                    'mode' => 'Rapid 10m',
                    'accentClass' => 'bg-secondary',
                    'resultClass' => 'text-secondary',
                ]],
                'bullet' => [],
                'classic' => [],
            ],
            'ratingProgressionByMode' => [
                'blitz' => ['mode' => 'blitz', 'label' => 'Blitz', 'rating' => '3280', 'peak' => '3280', 'deltaText' => '↑ +12 ELO (Recent)', 'deltaClass' => 'text-primary'],
                'rapid' => ['mode' => 'rapid', 'label' => 'Rapid', 'rating' => '2810', 'peak' => '2810', 'deltaText' => 'Current Rapid Rating', 'deltaClass' => 'text-primary'],
                'bullet' => ['mode' => 'bullet', 'label' => 'Bullet', 'rating' => '3315', 'peak' => '3315', 'deltaText' => 'Current Bullet Rating', 'deltaClass' => 'text-primary'],
                'classic' => ['mode' => 'classic', 'label' => 'Classical', 'rating' => '2450', 'peak' => '2450', 'deltaText' => 'Current Classical Rating', 'deltaClass' => 'text-primary'],
            ],
            'activityHeatmap' => $dashboardService->buildActivityHeatmap($activities, 'blitz'),
            'activityHeatmapsByMode' => [
                'blitz' => $dashboardService->buildActivityHeatmap($activities, 'blitz'),
                'rapid' => $dashboardService->buildActivityHeatmap($activities, 'rapid'),
                'bullet' => $dashboardService->buildActivityHeatmap($activities, 'bullet'),
                'classic' => $dashboardService->buildActivityHeatmap($activities, 'classic'),
            ],
        ]);

    $this->app->instance(HomeDashboardService::class, $service);
    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('data-mode-btn="blitz"', false)
        ->assertSee('data-mode-btn="rapid"', false)
        ->assertSee('data-mode-btn="bullet"', false)
        ->assertSee('data-mode-btn="classic"', false)
        ->assertSee('Classical')
        ->assertSee('vs BlitzRival')
        ->assertSee('vs RapidRival')
        ->assertSee('No recent Bullet games found yet.')
        ->assertSee('No recent Classical games found yet.');
});

test('home rating progression builds real 7D 30D 90D series and renders chart', function (): void {
    $today = CarbonImmutable::today();
    $day15 = $today->subDays(15);
    $day45 = $today->subDays(45);

    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
        'blitz_rating' => 3280,
        'rapid_rating' => 2810,
        'bullet_rating' => 3315,
        'classic_rating' => 2450,
    ]);

    $activities = collect([
        $day45->toDateString() => new PlayerDailyActivity([
            'activity_date' => $day45->toDateString(),
            'games_count' => 3,
            'blitz_count' => 3,
            'blitz_rating' => 3200,
        ]),
        $day15->toDateString() => new PlayerDailyActivity([
            'activity_date' => $day15->toDateString(),
            'games_count' => 4,
            'blitz_count' => 4,
            'blitz_rating' => 3250,
        ]),
        $today->toDateString() => new PlayerDailyActivity([
            'activity_date' => $today->toDateString(),
            'games_count' => 5,
            'blitz_count' => 5,
            'blitz_rating' => 3280,
        ]),
    ]);

    $dashboardService = new HomeDashboardService;
    $blitzProgression = $dashboardService->buildRatingProgression($player, 'blitz', collect(), $activities);

    expect($blitzProgression['periods']['7D']['deltaText'])->toBe('↑ +30 ELO (7D)')
        ->and($blitzProgression['periods']['30D']['deltaText'])->toBe('↑ +30 ELO (30D)')
        ->and($blitzProgression['periods']['90D']['deltaText'])->toBe('↑ +80 ELO (90D)')
        ->and($blitzProgression['periods']['90D']['peak'])->toBe('3280')
        ->and($blitzProgression['periods']['30D']['dots'])->toHaveCount(2);

    $user = new User(['name' => 'tirtanusa', 'email' => 'tirtanusa@gambit.io']);
    $user->id = 1;

    $service = Mockery::mock(HomeDashboardService::class);
    $service->shouldReceive('getDashboardData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => $player,
            'activeMode' => 'blitz',
            'profile' => [
                'name' => 'Hikaru Nakamura',
                'username' => 'hikaru',
                'avatarUrl' => null,
                'flag' => '🇺🇸',
                'streak' => 2,
                'form' => ['W', 'W'],
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '2810', 'sub' => 'Current', 'subClass' => 'text-primary font-semibold', 'icon' => 'clock', 'iconClass' => 'text-primary'],
                    ['label' => 'BLITZ', 'value' => '3280', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '3315', 'sub' => 'Current', 'subClass' => 'text-neutral-300', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '2450', 'sub' => 'Current', 'subClass' => 'text-secondary font-medium', 'icon' => 'crown', 'iconClass' => 'text-secondary'],
                ],
                'lastSynced' => $today->format('M j, Y'),
            ],
            'insightsPending' => true,
            'recentMatches' => [],
            'recentMatchesByMode' => ['blitz' => [], 'rapid' => [], 'bullet' => [], 'classic' => []],
            'ratingProgressionByMode' => [
                'blitz' => $blitzProgression,
                'rapid' => $dashboardService->buildRatingProgression($player, 'rapid', collect(), $activities),
                'bullet' => $dashboardService->buildRatingProgression($player, 'bullet', collect(), $activities),
                'classic' => $dashboardService->buildRatingProgression($player, 'classic', collect(), $activities),
            ],
            'activityHeatmap' => $dashboardService->buildActivityHeatmap($activities, 'blitz'),
            'activityHeatmapsByMode' => [
                'blitz' => $dashboardService->buildActivityHeatmap($activities, 'blitz'),
                'rapid' => $dashboardService->buildActivityHeatmap($activities, 'rapid'),
                'bullet' => $dashboardService->buildActivityHeatmap($activities, 'bullet'),
                'classic' => $dashboardService->buildActivityHeatmap($activities, 'classic'),
            ],
        ]);

    $this->app->instance(HomeDashboardService::class, $service);
    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('↑ +30 ELO (30D)')
        ->assertSee('+80 ELO (90D)')
        ->assertSee('data-rating-series', false)
        ->assertSee('data-rating-dot', false);
});

test('home controller refresh reports database migration error separately from API error', function (): void {
    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
    ]);

    $relation = Mockery::mock(BelongsToMany::class);
    $relation->shouldReceive('latest')->once()->with('user_player.created_at')->andReturnSelf();
    $relation->shouldReceive('first')->once()->andReturn($player);

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('players')->once()->andReturn($relation);

    $chessService = Mockery::mock(ChessPlatformService::class);
    $chessService->shouldReceive('refreshPlayer')
        ->once()
        ->with($player)
        ->andThrow(new PDOException("SQLSTATE[42S22]: Column not found: 1054 Unknown column 'rapid_rating'"));

    $controller = new HomeController(
        app(HomeDashboardService::class),
        $chessService,
    );

    $request = Request::create('/home/refresh', 'POST');
    $request->setUserResolver(fn (): User => $user);

    $response = $controller->refresh($request);

    expect($response->getTargetUrl())->toBe(route('home'))
        ->and($response->getSession()->get('sync_error'))->toContain('php artisan migrate');
});

test('home rating progression plots per-game points when multiple games occur on a single active day', function (): void {
    $today = CarbonImmutable::today();
    $singleDay = $today->subDays(10);

    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'hikaru',
        'rapid_rating' => 1149,
    ]);
    $player->id = 'player-uuid-1';

    $activities = collect([
        $singleDay->toDateString() => new PlayerDailyActivity([
            'activity_date' => $singleDay->toDateString(),
            'games_count' => 3,
            'rapid_count' => 3,
            'rapid_rating' => 1149,
        ]),
    ]);

    // Stored newest-first matching orderByDesc('end_time')
    $modeGames = collect([
        new Game([
            'time_class' => 'rapid',
            'end_time' => $singleDay->setTime(16, 0)->timestamp,
            'white_player_id' => $player->id,
            'white_rating' => 1149,
            'black_rating' => 1140,
        ]),
        new Game([
            'time_class' => 'rapid',
            'end_time' => $singleDay->setTime(15, 0)->timestamp,
            'white_player_id' => $player->id,
            'white_rating' => 1134,
            'black_rating' => 1130,
        ]),
        new Game([
            'time_class' => 'rapid',
            'end_time' => $singleDay->setTime(14, 0)->timestamp,
            'white_player_id' => $player->id,
            'white_rating' => 1120,
            'black_rating' => 1125,
        ]),
    ]);

    $progression = (new HomeDashboardService)->buildRatingProgression($player, 'rapid', $modeGames, $activities);

    expect($progression['periods']['90D']['deltaText'])->toBe('↑ +29 ELO (90D)')
        ->and($progression['periods']['90D']['dots'])->toHaveCount(3)
        ->and($progression['periods']['90D']['yLabels']['low'])->not->toBe($progression['periods']['90D']['yLabels']['high']);
});

test('estimateMovesCount ignores PGN date headers and clock decimals', function (): void {
    $pgn = <<<'PGN'
[Event "Live Chess"]
[Site "Chess.com"]
[Date "2026.11.25"]
[UTCDate "2026.11.25"]
[White "IsmaMugas"]
[Black "tirtanusa"]
[Result "1-0"]

1. e4 {[%clk 0:04:59.9]} 1... c6 {[%clk 0:04:58.2]} 2. d4 {[%clk 0:04:57.1]} 2... d5 34. Qh7# {[%clk 0:01:12.4]} 1-0
PGN;

    $service = new HomeDashboardService;

    expect($service->estimateMovesCount($pgn))->toBe(34)
        ->and($service->estimateMovesCount('1.e4 c5 2.Nf3 25.Rd1#'))->toBe(25)
        ->and($service->estimateMovesCount('e4 c5 Nf3 d6 d4 cxd4'))->toBe(3);
});
