<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerDailyActivity;
use App\Models\User;
use App\Services\Concerns\FormatsPlayerStats;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class HomeDashboardService
{
    use FormatsPlayerStats;
    use FormatsPlayerStats;

    /**
     * Supported time-control modes on the Home dashboard.
     *
     * @var array<int, string>
     */
    public const MODES = ['blitz', 'rapid', 'bullet', 'classic'];

    /**
     * Display labels for each mode key.
     *
     * @var array<string, string>
     */
    public const MODE_LABELS = [
        'blitz' => 'Blitz',
        'rapid' => 'Rapid',
        'bullet' => 'Bullet',
        'classic' => 'Classical',
    ];

    /**
     * Build all view data needed for the Home dashboard.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(?User $user): array
    {
        $player = $user?->players()->latest('user_player.created_at')->first();

        if (! $player instanceof Player) {
            return $this->defaultDashboardData($user);
        }

        $overallRecentGames = Game::query()
            ->where(function ($query) use ($player): void {
                $query->where('white_player_id', $player->id)
                    ->orWhere('black_player_id', $player->id);
            })
            ->orderByDesc('end_time')
            ->limit(5)
            ->get()
            ->sortByDesc(fn (Game $game): int => $game->end_time?->timestamp ?? 0)
            ->values();

        $startDate = CarbonImmutable::today()
            ->startOfWeek(CarbonInterface::SUNDAY)
            ->subWeeks(52);

        $activities = PlayerDailyActivity::query()
            ->where('player_id', $player->id)
            ->whereDate('activity_date', '>=', $startDate->toDateString())
            ->get()
            ->keyBy(fn (PlayerDailyActivity $row): string => $row->activity_date->toDateString());

        $recentGamesByMode = [];
        $recentMatchesByMode = [];
        $ratingProgressionByMode = [];
        $activityHeatmapsByMode = [];

        foreach (self::MODES as $mode) {
            $modeGames = Game::query()
                ->where('time_class', $mode)
                ->where(function ($query) use ($player): void {
                    $query->where('white_player_id', $player->id)
                        ->orWhere('black_player_id', $player->id);
                })
                ->orderByDesc('end_time')
                ->limit(5)
                ->get()
                ->sortByDesc(fn (Game $game): int => $game->end_time?->timestamp ?? 0)
                ->values();

            $recentGamesByMode[$mode] = $modeGames;
            $recentMatchesByMode[$mode] = $this->buildRecentMatches($modeGames, $player);
            $ratingProgressionByMode[$mode] = $this->buildRatingProgression($player, $mode, $modeGames, $activities);
            $activityHeatmapsByMode[$mode] = $this->buildActivityHeatmap($activities, $mode);
        }

        $form = $this->buildForm($overallRecentGames, $player);
        $streak = $this->calculateUnbeatenStreak($overallRecentGames, $player);
        $activeMode = 'blitz';

        return [
            'player' => $player,
            'activeMode' => $activeMode,
            'profile' => [
                'name' => $player->display_name ?: $player->chesscom_username,
                'username' => $player->chesscom_username,
                'avatarUrl' => $player->avatar_url !== '' ? $player->avatar_url : null,
                'flag' => $this->countryCodeToFlag($player->country_code),
                'streak' => $streak,
                'form' => $form,
                'ratings' => $this->buildRatings($player),
                'lastSynced' => $player->last_synched_at?->format('M j, Y'),
            ],
            'insightsPending' => true,
            'recentMatches' => $recentMatchesByMode[$activeMode],
            'recentMatchesByMode' => $recentMatchesByMode,
            'ratingProgressionByMode' => $ratingProgressionByMode,
            'activityHeatmap' => $activityHeatmapsByMode[$activeMode],
            'activityHeatmapsByMode' => $activityHeatmapsByMode,
        ];
    }

    /**
     * Build the 4 rating tiles (Rapid, Blitz, Bullet, Classic).
     *
     * @return array<int, array<string, string>>
     */
    private function buildRatings(Player $player): array
    {
        return [
            [
                'label' => 'RAPID',
                'value' => $player->rapid_rating !== null ? (string) $player->rapid_rating : '—',
                'sub' => $player->rapid_rating !== null ? 'Current' : 'Unrated',
                'subClass' => $player->rapid_rating !== null ? 'text-primary font-semibold' : 'text-neutral-500',
                'icon' => 'clock',
                'iconClass' => 'text-primary',
            ],
            [
                'label' => 'BLITZ',
                'value' => $player->blitz_rating !== null ? (string) $player->blitz_rating : '—',
                'sub' => $player->blitz_rating !== null ? 'Current' : 'Unrated',
                'subClass' => $player->blitz_rating !== null ? 'text-neutral-300' : 'text-neutral-500',
                'icon' => 'zap',
                'iconClass' => 'text-secondary',
            ],
            [
                'label' => 'BULLET',
                'value' => $player->bullet_rating !== null ? (string) $player->bullet_rating : '—',
                'sub' => $player->bullet_rating !== null ? 'Current' : 'Unrated',
                'subClass' => $player->bullet_rating !== null ? 'text-neutral-300' : 'text-neutral-500',
                'icon' => 'rocket',
                'iconClass' => 'text-tertiary-300',
            ],
            [
                'label' => 'CLASSIC',
                'value' => $player->classic_rating !== null ? (string) $player->classic_rating : '—',
                'sub' => $player->classic_rating !== null ? 'Current' : 'Unrated',
                'subClass' => $player->classic_rating !== null ? 'text-secondary font-medium' : 'text-neutral-500',
                'icon' => 'crown',
                'iconClass' => 'text-secondary',
            ],
        ];
    }

    /**
     * Build chronological W/D/L badges for up to 5 previous games.
     *
     * @param  Collection<int, Game>  $games
     * @return array<int, string>
     */
    private function buildForm(Collection $games, Player $player): array
    {
        return $games
            ->reverse()
            ->map(fn (Game $game): string => $this->resolveResultCode($game, $player))
            ->values()
            ->all();
    }

    /**
     * Count consecutive unbeaten games starting from the most recent game.
     *
     * @param  Collection<int, Game>  $games
     */
    private function calculateUnbeatenStreak(Collection $games, Player $player): int
    {
        $streak = 0;

        foreach ($games as $game) {
            if ($this->resolveResultCode($game, $player) === 'L') {
                break;
            }

            $streak++;
        }

        return $streak;
    }

    /**
     * Format recent games for the Recent Results list.
     *
     * @param  Collection<int, Game>  $games
     * @return array<int, array<string, mixed>>
     */
    private function buildRecentMatches(Collection $games, Player $player): array
    {
        return $games->map(function (Game $game) use ($player): array {
            $isWhite = $this->isPlayerWhite($game, $player);
            $resultCode = $this->resolveResultCode($game, $player);

            $opponent = $isWhite ? $game->black_player_username : $game->white_player_username;
            $opponentRating = $isWhite ? $game->black_rating : $game->white_rating;
            $movesCount = $this->estimateMovesCount((string) $game->pgn);

            [$resultText, $accentClass, $badgeClass] = match ($resultCode) {
                'W' => ['WIN', 'bg-primary', 'text-primary'],
                'D' => ['DRAW', 'bg-secondary', 'text-secondary'],
                default => ['LOSS', 'bg-danger-400', 'text-danger-400'],
            };

            $modeLabel = ucfirst($game->time_class ?: 'rapid');
            if ($game->time_control !== '') {
                $modeLabel .= ' '.$this->formatTimeControl($game->time_control);
            }

            return [
                'opponent' => 'vs '.$opponent,
                'rating' => $opponentRating > 0 ? (string) $opponentRating : '—',
                'opening' => $game->opening_name ?: 'Standard Chess',
                'moves' => $movesCount > 0 ? "{$movesCount} moves" : 'Completed',
                'resultText' => $resultText,
                'mode' => $modeLabel,
                'accentClass' => $accentClass,
                'resultClass' => $badgeClass,
            ];
        })->all();
    }

    /**
     * Fallback dashboard payload when no chess account is connected yet.
     *
     * @return array{
     *     player: null,
     *     profile: array<string, mixed>,
     *     insightsPending: bool,
     *     recentMatches: array<int, array<string, mixed>>
     * }
     */
    private function defaultDashboardData(?User $user): array
    {
        return [
            'player' => null,
            'profile' => [
                'name' => $user?->name ?? 'Alexey V.',
                'username' => $user?->name ?? 'alexey_v',
                'avatarUrl' => null,
                'flag' => '',
                'streak' => 0,
                'form' => [],
                'ratings' => [
                    [
                        'label' => 'RAPID',
                        'value' => '—',
                        'sub' => 'Connect account',
                        'subClass' => 'text-neutral-500',
                        'icon' => 'clock',
                        'iconClass' => 'text-primary',
                    ],
                    [
                        'label' => 'BLITZ',
                        'value' => '—',
                        'sub' => 'Connect account',
                        'subClass' => 'text-neutral-500',
                        'icon' => 'zap',
                        'iconClass' => 'text-secondary',
                    ],
                    [
                        'label' => 'BULLET',
                        'value' => '—',
                        'sub' => 'Connect account',
                        'subClass' => 'text-neutral-500',
                        'icon' => 'rocket',
                        'iconClass' => 'text-tertiary-300',
                    ],
                    [
                        'label' => 'CLASSIC',
                        'value' => '—',
                        'sub' => 'Connect account',
                        'subClass' => 'text-neutral-500',
                        'icon' => 'crown',
                        'iconClass' => 'text-secondary',
                    ],
                ],
                'lastSynced' => null,
            ],
            'insightsPending' => true,
            'recentMatches' => [],
            'activeMode' => 'blitz',
            'recentMatchesByMode' => array_fill_keys(self::MODES, []),
            'ratingProgressionByMode' => $this->defaultRatingProgressionByMode(),
            'activityHeatmap' => $this->buildActivityHeatmap(collect()),
            'activityHeatmapsByMode' => array_fill_keys(self::MODES, $this->buildActivityHeatmap(collect())),
        ];
    }

    /**
     * Build rating progression chart series (7D, 30D, 90D) for a specific mode.
     *
     * @param  Collection<int, Game>  $modeGames
     * @param  Collection<string, PlayerDailyActivity>  $activitiesByDate
     * @return array<string, mixed>
     */
    public function buildRatingProgression(
        Player $player,
        string $mode,
        Collection $modeGames,
        Collection $activitiesByDate,
    ): array {
        $today = CarbonImmutable::today();
        $ratingColumn = "{$mode}_rating";
        $currentRating = $player->{$ratingColumn} !== null ? (int) $player->{$ratingColumn} : null;

        $dailyPoints = $this->extractDailyRatingSeries($activitiesByDate, $ratingColumn, $modeGames, $player);

        if ($currentRating === null && $dailyPoints !== []) {
            $currentRating = (int) end($dailyPoints)['rating'];
        }

        $periods = [];
        foreach (['7D' => 7, '30D' => 30, '90D' => 90] as $periodKey => $days) {
            $windowStart = $today->subDays($days - 1);
            $windowStartDate = $windowStart->toDateString();

            $windowPoints = array_values(array_filter(
                $dailyPoints,
                fn (array $point): bool => $point['date'] >= $windowStartDate
            ));

            // If fewer than 2 active days exist in this window, use intra-day per-game ratings when available.
            if (count($windowPoints) < 2) {
                $gameLevelPoints = $this->extractGameLevelRatingSeries($modeGames, $player, $windowStart, $today);
                if (count($gameLevelPoints) >= 2) {
                    $windowPoints = $gameLevelPoints;
                } elseif (count($windowPoints) === 1) {
                    $priorPoint = $this->findLatestPointBefore($dailyPoints, $modeGames, $player, $windowStartDate);
                    if ($priorPoint !== null) {
                        array_unshift($windowPoints, [
                            'date' => $windowStartDate,
                            'label' => $windowStart->format('M j'),
                            'rating' => $priorPoint['rating'],
                        ]);
                    }
                }
            }

            $periods[$periodKey] = $this->buildRatingPeriodPayload(
                $periodKey,
                $windowStart,
                $today,
                $windowPoints,
                $currentRating,
            );
        }

        $defaultPeriod = $periods['30D'];

        return [
            'mode' => $mode,
            'label' => self::MODE_LABELS[$mode],
            'rating' => $defaultPeriod['rating'],
            'peak' => $defaultPeriod['peak'],
            'deltaText' => $defaultPeriod['deltaText'],
            'deltaClass' => $defaultPeriod['deltaClass'],
            'periods' => $periods,
        ];
    }

    /**
     * Extract chronological daily rating snapshots from daily activities or fallback game records.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activitiesByDate
     * @param  Collection<int, Game>  $modeGames
     * @return array<int, array{date: string, label: string, rating: int}>
     */
    private function extractDailyRatingSeries(
        Collection $activitiesByDate,
        string $ratingColumn,
        Collection $modeGames,
        Player $player,
    ): array {
        $points = $activitiesByDate
            ->filter(fn (PlayerDailyActivity $row): bool => $row->{$ratingColumn} !== null && (int) $row->{$ratingColumn} > 0)
            ->sortBy(fn (PlayerDailyActivity $row): string => $row->activity_date->toDateString())
            ->map(fn (PlayerDailyActivity $row): array => [
                'date' => $row->activity_date->toDateString(),
                'label' => $row->activity_date->format('M j'),
                'rating' => (int) $row->{$ratingColumn},
            ])
            ->values()
            ->all();

        if ($points !== []) {
            return $points;
        }

        $byDate = [];
        foreach ($modeGames->reverse() as $game) {
            if (! $game->end_time) {
                continue;
            }

            $isWhite = $this->isPlayerWhite($game, $player);
            $rating = (int) ($isWhite ? $game->white_rating : $game->black_rating);
            if ($rating <= 0) {
                continue;
            }

            $dateStr = $game->end_time->toDateString();
            $byDate[$dateStr] = [
                'date' => $dateStr,
                'label' => $game->end_time->format('M j'),
                'rating' => $rating,
            ];
        }

        ksort($byDate);

        return array_values($byDate);
    }

    /**
     * Extract individual game-level chronological points within a date window without collapsing same-day games.
     *
     * @param  Collection<int, Game>  $modeGames
     * @return array<int, array{date: string, label: string, rating: int}>
     */
    private function extractGameLevelRatingSeries(
        Collection $modeGames,
        Player $player,
        CarbonImmutable $windowStart,
        CarbonImmutable $today,
    ): array {
        $startTs = $windowStart->startOfDay()->timestamp;
        $endTs = $today->endOfDay()->timestamp;
        $points = [];

        foreach ($modeGames->reverse() as $game) {
            if (! $game->end_time) {
                continue;
            }

            $ts = $game->end_time->timestamp;
            if ($ts < $startTs || $ts > $endTs) {
                continue;
            }

            $isWhite = $this->isPlayerWhite($game, $player);
            $rating = (int) ($isWhite ? $game->white_rating : $game->black_rating);
            if ($rating <= 0) {
                continue;
            }

            $points[] = [
                'date' => $game->end_time->toDateString(),
                'label' => $game->end_time->format('M j H:i'),
                'rating' => $rating,
            ];
        }

        return $points;
    }

    /**
     * Find the most recent rating point strictly before the given window start date.
     *
     * @param  array<int, array{date: string, label: string, rating: int}>  $dailyPoints
     * @param  Collection<int, Game>  $modeGames
     * @return ?array{date: string, label: string, rating: int}
     */
    private function findLatestPointBefore(
        array $dailyPoints,
        Collection $modeGames,
        Player $player,
        string $beforeDate,
    ): ?array {
        $prior = null;

        foreach ($dailyPoints as $point) {
            if ($point['date'] < $beforeDate) {
                $prior = $point;
            }
        }

        if ($prior !== null) {
            return $prior;
        }

        foreach ($modeGames->reverse() as $game) {
            if (! $game->end_time || $game->end_time->toDateString() >= $beforeDate) {
                continue;
            }

            $isWhite = $this->isPlayerWhite($game, $player);
            $rating = (int) ($isWhite ? $game->white_rating : $game->black_rating);
            if ($rating > 0) {
                $prior = [
                    'date' => $game->end_time->toDateString(),
                    'label' => $game->end_time->format('M j'),
                    'rating' => $rating,
                ];
            }
        }

        return $prior;
    }

    /**
     * Format stats and SVG curve geometry for a single rating progression period.
     *
     * @param  array<int, array{date: string, label: string, rating: int}>  $windowPoints
     * @return array<string, mixed>
     */
    private function buildRatingPeriodPayload(
        string $periodKey,
        CarbonImmutable $windowStart,
        CarbonImmutable $today,
        array $windowPoints,
        ?int $currentRating,
    ): array {
        $ratings = array_column($windowPoints, 'rating');
        $peak = $ratings !== []
            ? max((int) ($currentRating ?? 0), max($ratings))
            : $currentRating;

        if (count($windowPoints) >= 2) {
            $delta = end($windowPoints)['rating'] - $windowPoints[0]['rating'];
            [$deltaText, $deltaClass] = match (true) {
                $delta > 0 => ["↑ +{$delta} ELO ({$periodKey})", 'text-primary'],
                $delta < 0 => ['↓ -'.abs($delta)." ELO ({$periodKey})", 'text-danger-400'],
                default => ["0 ELO ({$periodKey})", 'text-neutral-300'],
            };
        } elseif ($currentRating === null) {
            $deltaText = 'Unrated';
            $deltaClass = 'text-neutral-500';
        } elseif (count($windowPoints) === 1) {
            $deltaText = "1 active day ({$periodKey})";
            $deltaClass = 'text-neutral-300';
        } else {
            $deltaText = "No games in {$periodKey}";
            $deltaClass = 'text-neutral-500';
        }

        $geometry = $this->buildRatingChartGeometry($windowPoints, $currentRating, $windowStart, $today);

        return [
            'rating' => $currentRating !== null ? (string) $currentRating : '—',
            'peak' => $peak !== null ? (string) $peak : '—',
            'deltaText' => $deltaText,
            'deltaClass' => $deltaClass,
            'linePath' => $geometry['linePath'],
            'areaPath' => $geometry['areaPath'],
            'yLabels' => $geometry['yLabels'],
            'xLabels' => $geometry['xLabels'],
            'dots' => $geometry['dots'],
            'hasCurve' => $geometry['hasCurve'],
        ];
    }

    /**
     * Convert rating points into SVG path coordinates and axis labels for viewBox 0 0 360 120.
     *
     * @param  array<int, array{date: string, label: string, rating: int}>  $points
     * @return array<string, mixed>
     */
    private function buildRatingChartGeometry(
        array $points,
        ?int $currentRating,
        CarbonImmutable $windowStart,
        CarbonImmutable $today,
    ): array {
        if ($points === [] && $currentRating === null) {
            return [
                'linePath' => 'M 40 56 L 352 56',
                'areaPath' => 'M 40 56 L 352 56 L 352 104 L 40 104 Z',
                'yLabels' => ['high' => '—', 'mid' => '—', 'low' => '—'],
                'xLabels' => ['start' => $windowStart->format('M j'), 'mid' => 'No data', 'end' => 'Today'],
                'dots' => [],
                'hasCurve' => false,
            ];
        }

        if (count($points) <= 1) {
            $elo = $points !== [] ? $points[0]['rating'] : (int) $currentRating;
            $label = $points !== [] ? $points[0]['label'] : 'Today';

            return [
                'linePath' => 'M 40 56 L 352 56',
                'areaPath' => 'M 40 56 L 352 56 L 352 104 L 40 104 Z',
                'yLabels' => [
                    'high' => (string) ($elo + 10),
                    'mid' => (string) $elo,
                    'low' => (string) max(0, $elo - 10),
                ],
                'xLabels' => [
                    'start' => $windowStart->format('M j'),
                    'mid' => $points !== [] ? $points[0]['label'] : $windowStart->addDays((int) round($windowStart->diffInDays($today) / 2))->format('M j'),
                    'end' => 'Today',
                ],
                'dots' => [
                    ['x' => 352.0, 'y' => 56.0, 'label' => $label, 'rating' => $elo],
                ],
                'hasCurve' => true,
            ];
        }

        $ratings = array_column($points, 'rating');
        $rawMin = min($ratings);
        $rawMax = max($ratings);
        $span = max(20, $rawMax - $rawMin);
        $pad = (int) ceil($span * 0.15);
        $lowBound = max(0, $rawMin - $pad);
        $highBound = $rawMax + $pad;
        $range = max(1, $highBound - $lowBound);

        $leftX = 40.0;
        $rightX = 352.0;
        $topY = 20.0;
        $bottomY = 90.0;

        $sampled = $this->downsampleChartPoints($points, 16);
        $count = count($sampled);
        $coords = [];

        foreach ($sampled as $index => $point) {
            $x = $count > 1
                ? round($leftX + (($rightX - $leftX) * ($index / ($count - 1))), 1)
                : $rightX;
            $ratio = ($point['rating'] - $lowBound) / $range;
            $y = round($bottomY - ($ratio * ($bottomY - $topY)), 1);

            $coords[] = [
                'x' => $x,
                'y' => $y,
                'label' => $point['label'],
                'rating' => $point['rating'],
            ];
        }

        $linePath = $this->buildSmoothSvgPath($coords);
        $areaPath = "{$linePath} L {$rightX} 104 L {$leftX} 104 Z";
        $midIndex = intdiv($count - 1, 2);

        return [
            'linePath' => $linePath,
            'areaPath' => $areaPath,
            'yLabels' => [
                'high' => (string) $highBound,
                'mid' => (string) ((int) round(($highBound + $lowBound) / 2)),
                'low' => (string) $lowBound,
            ],
            'xLabels' => [
                'start' => $sampled[0]['label'],
                'mid' => $sampled[$midIndex]['label'],
                'end' => $sampled[$count - 1]['date'] === $today->toDateString() ? 'Today' : $sampled[$count - 1]['label'],
            ],
            'dots' => $coords,
            'hasCurve' => true,
        ];
    }

    /**
     * Downsample dense daily points while always preserving first and last points.
     *
     * @param  array<int, array{date: string, label: string, rating: int}>  $points
     * @return array<int, array{date: string, label: string, rating: int}>
     */
    private function downsampleChartPoints(array $points, int $maxPoints): array
    {
        $total = count($points);
        if ($total <= $maxPoints) {
            return $points;
        }

        $sampled = [];
        for ($i = 0; $i < $maxPoints; $i++) {
            $idx = (int) round(($i * ($total - 1)) / ($maxPoints - 1));
            $sampled[] = $points[$idx];
        }

        return $sampled;
    }

    /**
     * Default rating progression cards when no player account is connected.
     *
     * @return array<string, array<string, mixed>>
     */
    private function defaultRatingProgressionByMode(): array
    {
        $today = CarbonImmutable::today();
        $result = [];

        foreach (self::MODES as $mode) {
            $periods = [];
            foreach (['7D' => 7, '30D' => 30, '90D' => 90] as $periodKey => $days) {
                $periods[$periodKey] = $this->buildRatingPeriodPayload(
                    $periodKey,
                    $today->subDays($days - 1),
                    $today,
                    [],
                    null,
                );
                $periods[$periodKey]['deltaText'] = 'Connect account';
            }

            $result[$mode] = [
                'mode' => $mode,
                'label' => self::MODE_LABELS[$mode],
                'rating' => '—',
                'peak' => '—',
                'deltaText' => 'Connect account',
                'deltaClass' => 'text-neutral-500',
                'periods' => $periods,
            ];
        }

        return $result;
    }

    /**
     * Build the 53-week GitHub-style heatmap grid and summary KPIs from daily activity rows.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activitiesByDate
     * @return array{
     *     currentStreak: int,
     *     totalGamesThisYear: int,
     *     winrate: string,
     *     mostActiveLabel: string,
     *     weeks: array<int, array<int, array<string, mixed>>>,
     *     monthLabels: array<int, string>
     * }
     */
    public function buildActivityHeatmap(Collection $activitiesByDate, ?string $mode = null): array
    {
        $today = CarbonImmutable::today();
        $startDate = $today->startOfWeek(CarbonInterface::SUNDAY)->subWeeks(52);
        $countField = match ($mode) {
            'rapid' => 'rapid_count',
            'blitz' => 'blitz_count',
            'bullet' => 'bullet_count',
            'classic' => 'classic_count',
            default => 'games_count',
        };

        $weeks = [];
        $monthLabels = [];
        $lastMonthIndex = -5;
        $totalGamesThisYear = 0;
        $totalGamesInWindow = 0;
        $totalAllGamesInWindow = 0;
        $totalWinsInWindow = 0;
        $weekdayTotals = [];

        for ($w = 0; $w < 53; $w++) {
            $weekStart = $startDate->addWeeks($w);
            $weekDays = [];
            $monthLabelForWeek = '';

            for ($d = 0; $d < 7; $d++) {
                $cellDate = $weekStart->addDays($d);
                $isFuture = $cellDate->isAfter($today);

                if (! $isFuture && $cellDate->day === 1 && ($w - $lastMonthIndex) >= 3) {
                    $monthLabelForWeek = $cellDate->format('M');
                    $lastMonthIndex = $w;
                } elseif ($w === 0 && $d === 0) {
                    $monthLabelForWeek = $cellDate->format('M');
                    $lastMonthIndex = 0;
                }

                if ($isFuture) {
                    $weekDays[] = ['future' => true];

                    continue;
                }

                /** @var ?PlayerDailyActivity $activity */
                $activity = $activitiesByDate->get($cellDate->toDateString());
                $games = (int) ($activity?->{$countField} ?? 0);

                if ($cellDate->year === $today->year) {
                    $totalGamesThisYear += $games;
                }

                $totalGamesInWindow += $games;
                $totalAllGamesInWindow += (int) ($activity?->games_count ?? 0);
                $totalWinsInWindow += (int) ($activity?->wins_count ?? 0);

                $weekday = $cellDate->format('l');
                $weekdayTotals[$weekday] = ($weekdayTotals[$weekday] ?? 0) + $games;

                $level = match (true) {
                    $games === 0 => 0,
                    $games <= 2 => 1,
                    $games <= 5 => 2,
                    $games <= 9 => 3,
                    default => 4,
                };

                $weekDays[] = [
                    'future' => false,
                    'date' => $cellDate->format('M j, Y'),
                    'games' => $games,
                    'level' => $level,
                ];
            }

            $weeks[] = $weekDays;
            $monthLabels[] = $monthLabelForWeek;
        }

        $currentStreak = $this->calculateDailyStreak($activitiesByDate, $today, $startDate, $countField);

        $winrate = ($totalGamesInWindow > 0 && $totalAllGamesInWindow > 0)
            ? number_format(($totalWinsInWindow / $totalAllGamesInWindow) * 100, 1).'%'
            : '—';

        $mostActiveLabel = 'No activity yet';
        if ($weekdayTotals !== []) {
            arsort($weekdayTotals);
            $topDay = (string) array_key_first($weekdayTotals);
            $topGames = $weekdayTotals[$topDay] ?? 0;
            if ($topGames > 0) {
                $unit = $topGames === 1 ? 'game' : 'games';
                $mostActiveLabel = "{$topDay} ({$topGames} {$unit})";
            }
        }

        return [
            'currentStreak' => $currentStreak,
            'totalGamesThisYear' => $totalGamesThisYear,
            'winrate' => $winrate,
            'mostActiveLabel' => $mostActiveLabel,
            'weeks' => $weeks,
            'monthLabels' => $monthLabels,
        ];
    }
}
