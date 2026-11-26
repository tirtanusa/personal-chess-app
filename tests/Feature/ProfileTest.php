<?php

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerDailyActivity;
use App\Models\User;
use App\Services\ProfileDashboardService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

uses(TestCase::class);

test('profile page renders clean fallback without hardcoded dummy player data', function (): void {
    $response = $this->get(route('profile'));

    $response->assertOk()
        ->assertDontSee('Alexey V.')
        ->assertDontSee('1,482')
        ->assertSee('Connect Chess.com / Lichess Account');
});

test('profile service computes summary weekly openings and match history for connected player', function (): void {
    $today = CarbonImmutable::today();
    $dayAgo = $today->subDay();
    $threeDaysAgo = $today->subDays(3);

    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'tirtanusa',
        'display_name' => 'Tirta Nusa',
        'avatar_url' => 'https://example.com/tirta.jpg',
        'country_code' => 'ID',
        'rapid_rating' => 1149,
        'blitz_rating' => 915,
        'bullet_rating' => 880,
        'classic_rating' => null,
    ]);
    $player->id = 'player-uuid-1';

    $user = new User(['name' => 'tirtanusa', 'email' => 'pridmizu17@gmail.com']);
    $user->id = 1;

    $activities = collect([
        $threeDaysAgo->toDateString() => new PlayerDailyActivity([
            'activity_date' => $threeDaysAgo->toDateString(),
            'games_count' => 3,
            'wins_count' => 2,
            'draws_count' => 0,
            'losses_count' => 1,
            'blitz_count' => 3,
            'blitz_rating' => 880,
        ]),
        $dayAgo->toDateString() => new PlayerDailyActivity([
            'activity_date' => $dayAgo->toDateString(),
            'games_count' => 2,
            'wins_count' => 1,
            'draws_count' => 1,
            'losses_count' => 0,
            'blitz_count' => 2,
            'blitz_rating' => 900,
        ]),
        $today->toDateString() => new PlayerDailyActivity([
            'activity_date' => $today->toDateString(),
            'games_count' => 2,
            'wins_count' => 2,
            'draws_count' => 0,
            'losses_count' => 0,
            'blitz_count' => 2,
            'blitz_rating' => 915,
            'rapid_rating' => 1165,
        ]),
    ]);

    $games = collect([
        new Game([
            'url' => 'https://www.chess.com/game/live/101',
            'time_class' => 'blitz',
            'time_control' => '300',
            'end_time' => $today->setTime(12, 0)->timestamp,
            'white_player_id' => $player->id,
            'white_player_username' => 'tirtanusa',
            'black_player_username' => 'yayukz',
            'white_rating' => 915,
            'black_rating' => 890,
            'white_result' => 'win',
            'black_result' => 'resigned',
            'eco_code' => 'B12',
            'opening_name' => 'Caro Kann Defense',
            'pgn' => "[Date \"2026.11.26\"]\n1. e4 c6 2. d4 d5 28. Qh7# 1-0",
        ]),
        new Game([
            'url' => 'https://www.chess.com/game/live/102',
            'time_class' => 'rapid',
            'time_control' => '600',
            'end_time' => $dayAgo->setTime(15, 0)->timestamp,
            'black_player_id' => $player->id,
            'white_player_username' => 'IsmaMugas',
            'black_player_username' => 'tirtanusa',
            'white_rating' => 1140,
            'black_rating' => 1149,
            'white_result' => 'win',
            'black_result' => 'checkmated',
            'eco_code' => 'A07',
            'opening_name' => 'Kings Indian Attack',
            'pgn' => "[Date \"2026.11.25\"]\n1. Nf3 d5 2. g3 Nf6 3. Bg2 e6 35. Rb8# 1-0",
        ]),
    ]);

    $service = new ProfileDashboardService;
    $summary = $service->buildSummary($player, $user, $activities, $games, $today);
    $weekly = $service->buildWeeklyPerformance($player, $activities, $games, $today);
    $openings = $service->buildFavoriteOpenings($games, $player);
    $history = $service->buildMatchHistory($games, $player, $activities);

    expect($summary['streak'])->toBe(2)
        ->and($summary['totalGames'])->toBe('7')
        ->and($summary['wins'])->toBe(5)
        ->and($summary['draws'])->toBe(1)
        ->and($summary['losses'])->toBe(1)
        ->and($summary['ratings'][0]['peak'])->toBe('Peak 1165')
        ->and($weekly['netLabel'])->toBe('+35 ELO Net (Blitz)')
        ->and($openings['white'][0]['name'])->toBe('Caro Kann Defense')
        ->and($openings['white'][0]['moves'])->toBe('1. e4 c6 2. d4 d5')
        ->and($openings['white'][0]['accuracy'])->toBe('Engine Pending')
        ->and($openings['black'][0]['name'])->toBe('Kings Indian Attack')
        ->and($history['matches'])->toHaveCount(2)
        ->and($history['matches'][0]['meta'])->toContain('28 moves');

    $mock = Mockery::mock(ProfileDashboardService::class);
    $mock->shouldReceive('getProfileData')
        ->once()
        ->with($user)
        ->andReturn([
            'player' => $player,
            'layout' => [
                'avatarUrl' => 'https://example.com/tirta.jpg',
                'name' => 'Tirta Nusa',
                'handle' => '@tirtanusa',
                'rating' => '915',
            ],
            'summary' => $summary,
            'weeklyPerformance' => $weekly,
            'favoriteOpenings' => $openings,
            'matchHistory' => $history,
        ]);

    $this->app->instance(ProfileDashboardService::class, $mock);
    $this->actingAs($user);

    $response = $this->get(route('profile'));

    $response->assertOk()
        ->assertSee('Tirta Nusa')
        ->assertSee('2 Days Streak')
        ->assertSee('+35 ELO Net (Blitz)')
        ->assertSee('Caro Kann Defense')
        ->assertSee('Kings Indian Attack')
        ->assertSee('yayukz')
        ->assertSee('IsmaMugas')
        ->assertSee('Engine Pending')
        ->assertSee('Download PGN / Export Match History')
        ->assertDontSee('BRILLIANT');
});

test('profile export pgn route requires authentication', function (): void {
    $response = $this->get(route('profile.export-pgn'));

    $response->assertRedirect(route('login'));
});

test('profile match history supports 5, 10, and 100 games per page with filters', function (): void {
    $today = CarbonImmutable::today();
    $player = new Player([
        'platform' => 'chesscom',
        'chesscom_username' => 'tirtanusa',
    ]);
    $player->id = 'player-uuid-1';

    $games = collect(range(1, 12))->map(fn (int $i): Game => new Game([
        'url' => "https://www.chess.com/game/live/{$i}",
        'time_class' => $i <= 8 ? 'blitz' : 'rapid',
        'time_control' => '300',
        'end_time' => $today->subHours($i)->timestamp,
        'white_player_id' => $player->id,
        'white_player_username' => 'tirtanusa',
        'black_player_username' => "Opponent{$i}",
        'white_rating' => 1000,
        'black_rating' => 990,
        'white_result' => $i % 2 === 0 ? 'win' : 'resigned',
        'black_result' => $i % 2 === 0 ? 'resigned' : 'win',
        'opening_name' => 'Italian Game',
        'pgn' => '1. e4 e5 2. Nf3 Nc6 1-0',
    ]));

    $service = new ProfileDashboardService;

    $page1Per5 = $service->buildMatchHistory($games, $player, collect(), 5, 1);
    expect($page1Per5['matches'])->toHaveCount(5)
        ->and($page1Per5['pagination']['perPage'])->toBe(5)
        ->and($page1Per5['pagination']['perPageOptions'])->toBe([5, 10, 100])
        ->and($page1Per5['pagination']['totalPages'])->toBe(3)
        ->and($page1Per5['pagination']['from'])->toBe(1)
        ->and($page1Per5['pagination']['to'])->toBe(5);

    $page2Per5 = $service->buildMatchHistory($games, $player, collect(), 5, 2);
    expect($page2Per5['matches'])->toHaveCount(5)
        ->and($page2Per5['matches'][0]['opponent'])->toBe('Opponent6')
        ->and($page2Per5['pagination']['from'])->toBe(6)
        ->and($page2Per5['pagination']['to'])->toBe(10);

    $per10 = $service->buildMatchHistory($games, $player, collect(), 10, 1);
    expect($per10['matches'])->toHaveCount(10)
        ->and($per10['pagination']['totalPages'])->toBe(2);

    $per100 = $service->buildMatchHistory($games, $player, collect(), 100, 1);
    expect($per100['matches'])->toHaveCount(12)
        ->and($per100['pagination']['totalPages'])->toBe(1);

    $filtered = $service->buildMatchHistory($games, $player, collect(), 5, 1, 'win', 'blitz');
    expect($filtered['matches'])->toHaveCount(4)
        ->and($filtered['pagination']['totalItems'])->toBe(4);

    $user = new User(['name' => 'tirtanusa', 'email' => 'pridmizu17@gmail.com']);
    $user->id = 1;

    $mock = Mockery::mock(ProfileDashboardService::class);
    $mock->shouldReceive('getProfileData')
        ->once()
        ->with($user, [
            'per_page' => '5',
            'page' => '2',
            'result' => 'all',
            'format' => 'blitz',
        ])
        ->andReturn([
            'player' => $player,
            'layout' => [
                'avatarUrl' => null,
                'name' => 'tirtanusa',
                'handle' => '@tirtanusa',
                'rating' => '1000',
            ],
            'summary' => $service->buildSummary($player, $user, collect(), $games, $today),
            'weeklyPerformance' => $service->buildWeeklyPerformance($player, collect(), $games, $today),
            'favoriteOpenings' => $service->buildFavoriteOpenings($games, $player),
            'matchHistory' => $page2Per5,
        ]);

    $this->app->instance(ProfileDashboardService::class, $mock);
    $this->actingAs($user);

    $response = $this->get(route('profile', [
        'per_page' => 5,
        'page' => 2,
        'result' => 'all',
        'format' => 'blitz',
    ]));

    $response->assertOk()
        ->assertSee('data-per-page-option="5"', false)
        ->assertSee('data-per-page-option="10"', false)
        ->assertSee('data-per-page-option="100"', false)
        ->assertSee('6–10')
        ->assertSee('2 / 3');
});
