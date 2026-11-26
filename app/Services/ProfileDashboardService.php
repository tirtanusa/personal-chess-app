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

class ProfileDashboardService
{
    use FormatsPlayerStats;

    /**
     * Build all view data needed for the Player Profile page.
     *
     * @param  array{per_page?: int, page?: int, result?: string, format?: string}  $options
     * @return array<string, mixed>
     */
    public function getProfileData(?User $user, array $options = []): array
    {
        $player = $user?->players()->latest('user_player.created_at')->first();

        $requestedPerPage = (int) ($options['per_page'] ?? 10);
        $perPage = in_array($requestedPerPage, [5, 10, 100], true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($options['page'] ?? 1));
        $resultFilter = in_array($options['result'] ?? 'all', ['all', 'win', 'loss', 'draw'], true)
            ? (string) ($options['result'] ?? 'all')
            : 'all';
        $formatFilter = in_array($options['format'] ?? 'all', ['all', 'blitz', 'rapid', 'bullet', 'classic'], true)
            ? (string) ($options['format'] ?? 'all')
            : 'all';

        if (! $player instanceof Player) {
            return $this->defaultProfileData($user, $perPage, $page, $resultFilter, $formatFilter);
        }

        $today = CarbonImmutable::today();
        $yearStart = $today->startOfWeek(CarbonInterface::SUNDAY)->subWeeks(52);

        $activities = PlayerDailyActivity::query()
            ->where('player_id', $player->id)
            ->whereDate('activity_date', '>=', $yearStart->toDateString())
            ->get()
            ->keyBy(fn (PlayerDailyActivity $row): string => $row->activity_date->toDateString());

        $games = Game::query()
            ->where(function ($query) use ($player): void {
                $query->where('white_player_id', $player->id)
                    ->orWhere('black_player_id', $player->id);
            })
            ->orderByDesc('end_time')
            ->get()
            ->sortByDesc(fn (Game $game): int => $game->end_time?->timestamp ?? 0)
            ->values();

        return [
            'player' => $player,
            'layout' => [
                'avatarUrl' => $player->avatar_url !== '' ? $player->avatar_url : null,
                'name' => $player->display_name ?: $player->chesscom_username,
                'handle' => '@'.$player->chesscom_username,
                'rating' => $this->resolvePrimaryRating($player),
            ],
            'summary' => $this->buildSummary($player, $user, $activities, $games, $today, $yearStart),
            'weeklyPerformance' => $this->buildWeeklyPerformance($player, $activities, $games, $today),
            'favoriteOpenings' => $this->buildFavoriteOpenings($games, $player),
            'matchHistory' => $this->buildMatchHistory(
                $games,
                $player,
                $activities,
                $perPage,
                $page,
                $resultFilter,
                $formatFilter,
            ),
        ];
    }

    /**
     * Build summary identity, W/D/L performance bar, and 4-mode rating cards.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activities
     * @param  Collection<int, Game>  $games
     * @return array<string, mixed>
     */
    public function buildSummary(
        Player $player,
        ?User $user,
        Collection $activities,
        Collection $games,
        ?CarbonImmutable $today = null,
        ?CarbonImmutable $yearStart = null,
    ): array {
        $today ??= CarbonImmutable::today();
        $yearStart ??= $today->startOfWeek(CarbonInterface::SUNDAY)->subWeeks(52);

        $activityTotal = (int) $activities->sum('games_count');
        if ($activityTotal > 0) {
            $wins = (int) $activities->sum('wins_count');
            $draws = (int) $activities->sum('draws_count');
            $losses = (int) $activities->sum('losses_count');
            $totalGames = $activityTotal;
        } else {
            $wins = 0;
            $draws = 0;
            $losses = 0;
            foreach ($games as $game) {
                match ($this->resolveResultCode($game, $player)) {
                    'W' => $wins++,
                    'D' => $draws++,
                    default => $losses++,
                };
            }
            $totalGames = $wins + $draws + $losses;
        }

        $winPct = $totalGames > 0 ? round(($wins / $totalGames) * 100, 1) : 0.0;
        $drawPct = $totalGames > 0 ? round(($draws / $totalGames) * 100, 1) : 0.0;
        $lossPct = $totalGames > 0 ? max(0.0, round(100 - $winPct - $drawPct, 1)) : 0.0;

        $streak = $this->calculateDailyStreak($activities, $today, $yearStart);
        $memberDate = $player->created_at ?? $user?->created_at;

        return [
            'name' => $player->display_name ?: $player->chesscom_username,
            'username' => $player->chesscom_username,
            'platform' => $player->platform === 'lichess' ? 'Lichess' : 'Chess.com',
            'avatarUrl' => $player->avatar_url !== '' ? $player->avatar_url : null,
            'flag' => $this->countryCodeToFlag($player->country_code),
            'streak' => $streak,
            'memberSince' => $memberDate?->format('M Y') ?? 'Active',
            'totalGames' => number_format($totalGames),
            'winRate' => $totalGames > 0 ? number_format($winPct, 1).'%' : '—',
            'wins' => $wins,
            'draws' => $draws,
            'losses' => $losses,
            'winPct' => $winPct,
            'drawPct' => $drawPct,
            'lossPct' => $lossPct,
            'ratings' => $this->buildModeRatingsWithPeaks($player, $activities),
        ];
    }

    /**
     * Build the 4 rating summary cards (Rapid, Blitz, Bullet, Classical) with yearly peak ratings.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activities
     * @return array<int, array<string, string>>
     */
    private function buildModeRatingsWithPeaks(Player $player, Collection $activities): array
    {
        $defs = [
            ['key' => 'rapid', 'label' => 'RAPID', 'icon' => 'clock', 'iconClass' => 'text-neutral-400', 'peakClass' => 'text-primary'],
            ['key' => 'blitz', 'label' => 'BLITZ', 'icon' => 'zap', 'iconClass' => 'text-secondary', 'peakClass' => 'text-secondary'],
            ['key' => 'bullet', 'label' => 'BULLET', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300', 'peakClass' => 'text-neutral-300'],
            ['key' => 'classic', 'label' => 'CLASSIC', 'icon' => 'crown', 'iconClass' => 'text-primary', 'peakClass' => 'text-neutral-300'],
        ];

        $cards = [];
        foreach ($defs as $def) {
            $column = $def['key'].'_rating';
            $current = $player->{$column};
            $maxFromDaily = $activities
                ->pluck($column)
                ->filter(fn (mixed $val): bool => $val !== null && (int) $val > 0)
                ->max();

            $peak = null;
            if ($current !== null || $maxFromDaily !== null) {
                $peak = max((int) ($current ?? 0), (int) ($maxFromDaily ?? 0));
            }

            $cards[] = [
                'label' => $def['label'],
                'value' => $current !== null ? (string) $current : '—',
                'peak' => $peak !== null ? "Peak {$peak}" : 'Unrated',
                'peakClass' => $current !== null ? $def['peakClass'] : 'text-neutral-500',
                'icon' => $def['icon'],
                'iconClass' => $def['iconClass'],
            ];
        }

        return $cards;
    }

    /**
     * Build 7-day rating & activity performance curve across Monday..Sunday or last 7 days.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activities
     * @param  Collection<int, Game>  $games
     * @return array<string, mixed>
     */
    public function buildWeeklyPerformance(
        Player $player,
        Collection $activities,
        Collection $games,
        ?CarbonImmutable $today = null,
    ): array {
        $today ??= CarbonImmutable::today();
        $windowStart = $today->subDays(6);

        $preferredMode = $this->resolveWeeklyRatingMode($player, $activities, $windowStart);
        $ratingColumn = $preferredMode.'_rating';
        $modeLabel = match ($preferredMode) {
            'rapid' => 'Rapid',
            'bullet' => 'Bullet',
            'classic' => 'Classical',
            default => 'Blitz',
        };

        $days = [];
        $pointValues = [];
        $totalGames7d = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = $windowStart->addDays($i);
            $dateStr = $date->toDateString();
            /** @var ?PlayerDailyActivity $row */
            $row = $activities->get($dateStr);

            $gamesCount = (int) ($row?->games_count ?? 0);
            $totalGames7d += $gamesCount;
            $rating = $row?->{$ratingColumn};
            $ratingInt = $rating !== null && (int) $rating > 0 ? (int) $rating : null;

            $wins = (int) ($row?->wins_count ?? 0);
            $losses = (int) ($row?->losses_count ?? 0);
            $strokeColor = match (true) {
                $gamesCount === 0 => '#64748B',
                $wins > $losses => '#4CD59D',
                $losses > $wins => '#F43F5E',
                default => '#F59E0B',
            };

            if ($ratingInt !== null) {
                $pointValues[] = [
                    'index' => $i,
                    'rating' => $ratingInt,
                    'color' => $strokeColor,
                ];
            }

            $days[] = [
                'label' => strtoupper($date->format('D')),
                'dateLabel' => $date->format('M j'),
                'games' => $gamesCount,
                'rating' => $ratingInt,
                'active' => $gamesCount > 0,
                'isToday' => $i === 6,
            ];
        }

        // Compute net change across available ELO points in the 7 days (or fallback to recent games).
        $netDelta = null;
        if (count($pointValues) >= 2) {
            $netDelta = end($pointValues)['rating'] - $pointValues[0]['rating'];
        }

        if ($netDelta !== null) {
            $prefix = $netDelta > 0 ? '+' : ($netDelta < 0 ? '-' : '');
            $netLabel = $netDelta === 0
                ? "0 ELO Net ({$modeLabel})"
                : $prefix.abs($netDelta)." ELO Net ({$modeLabel})";
            $netClass = $netDelta < 0 ? 'text-danger-400' : ($netDelta > 0 ? 'text-primary' : 'text-neutral-300');
        } elseif ($totalGames7d > 0) {
            $netLabel = "{$totalGames7d} ".($totalGames7d === 1 ? 'Game' : 'Games').' Played';
            $netClass = 'text-primary';
        } else {
            $netLabel = 'No games (7D)';
            $netClass = 'text-neutral-500';
        }

        $geometry = $this->buildWeeklySvgGeometry($days, $pointValues, $totalGames7d);

        return [
            'modeLabel' => $modeLabel,
            'netLabel' => $netLabel,
            'netClass' => $netClass,
            'days' => $days,
            'linePath' => $geometry['linePath'],
            'areaPath' => $geometry['areaPath'],
            'dots' => $geometry['dots'],
            'hasCurve' => $geometry['hasCurve'],
        ];
    }

    /**
     * Build SVG coordinates for the 7-day performance sparkline (viewBox 0 0 360 96).
     *
     * @param  array<int, array<string, mixed>>  $days
     * @param  array<int, array{index: int, rating: int, color: string}>  $pointValues
     * @return array<string, mixed>
     */
    private function buildWeeklySvgGeometry(array $days, array $pointValues, int $totalGames7d): array
    {
        $xs = [20.0, 74.0, 128.0, 182.0, 236.0, 290.0, 342.0];
        $topY = 18.0;
        $bottomY = 72.0;

        if (count($pointValues) >= 2) {
            $ratings = array_column($pointValues, 'rating');
            $min = min($ratings);
            $max = max($ratings);
            $pad = max(8, (int) ceil(($max - $min) * 0.2));
            $low = $min - $pad;
            $high = $max + $pad;
            $range = max(1, $high - $low);

            $coords = [];
            $dots = [];
            foreach ($pointValues as $entry) {
                $idx = $entry['index'];
                $x = $xs[$idx];
                $ratio = ($entry['rating'] - $low) / $range;
                $y = round($bottomY - ($ratio * ($bottomY - $topY)), 1);
                $coords[] = ['x' => $x, 'y' => $y];
                $dots[] = [
                    'cx' => $x,
                    'cy' => $y,
                    'stroke' => $entry['color'],
                    'isHighlight' => $idx === 6 || $entry['rating'] === $max,
                    'title' => $days[$idx]['dateLabel'].": {$entry['rating']} ELO",
                ];
            }

            $linePath = $this->buildSmoothSvgPath($coords, 'M 20 50 L 342 50');
            $firstX = $coords[0]['x'];
            $lastX = end($coords)['x'];
            $areaPath = "{$linePath} L {$lastX} 84 L {$firstX} 84 Z";

            return [
                'linePath' => $linePath,
                'areaPath' => $areaPath,
                'dots' => $dots,
                'hasCurve' => true,
            ];
        }

        if ($totalGames7d > 0) {
            $maxGames = max(1, max(array_column($days, 'games')));
            $coords = [];
            $dots = [];

            foreach ($days as $idx => $day) {
                $x = $xs[$idx];
                $ratio = $day['games'] / $maxGames;
                $y = round($bottomY - ($ratio * ($bottomY - $topY)), 1);
                $coords[] = ['x' => $x, 'y' => $y];
                $dots[] = [
                    'cx' => $x,
                    'cy' => $y,
                    'stroke' => $day['games'] > 0 ? '#4CD59D' : '#475569',
                    'isHighlight' => $day['games'] === $maxGames && $day['games'] > 0,
                    'title' => $day['dateLabel'].": {$day['games']} games",
                ];
            }

            $linePath = $this->buildSmoothSvgPath($coords, 'M 20 72 L 342 72');

            return [
                'linePath' => $linePath,
                'areaPath' => "{$linePath} L 342 84 L 20 84 Z",
                'dots' => $dots,
                'hasCurve' => true,
            ];
        }

        return [
            'linePath' => 'M 20 56 L 342 56',
            'areaPath' => 'M 20 56 L 342 56 L 342 84 L 20 84 Z',
            'dots' => [],
            'hasCurve' => false,
        ];
    }

    /**
     * Pick the mode with active 7-day rating snapshots (or highest-priority rated mode).
     *
     * @param  Collection<string, PlayerDailyActivity>  $activities
     */
    private function resolveWeeklyRatingMode(Player $player, Collection $activities, CarbonImmutable $windowStart): string
    {
        $modes = ['blitz', 'rapid', 'bullet', 'classic'];
        $startStr = $windowStart->toDateString();

        foreach ($modes as $mode) {
            $col = $mode.'_rating';
            $count = $activities
                ->filter(fn (PlayerDailyActivity $row): bool => $row->activity_date->toDateString() >= $startStr && $row->{$col} !== null)
                ->count();
            if ($count >= 2) {
                return $mode;
            }
        }

        foreach ($modes as $mode) {
            if ($player->{$mode.'_rating'} !== null) {
                return $mode;
            }
        }

        return 'blitz';
    }

    /**
     * Group stored games into top 3 White and Black openings.
     *
     * @param  Collection<int, Game>  $games
     * @return array{white: array<int, array<string, mixed>>, black: array<int, array<string, mixed>>}
     */
    public function buildFavoriteOpenings(Collection $games, Player $player): array
    {
        $buckets = ['white' => [], 'black' => []];

        foreach ($games as $game) {
            $side = $this->isPlayerWhite($game, $player) ? 'white' : 'black';
            $name = trim((string) $game->opening_name);
            if ($name === '' || $name === 'Standard Chess') {
                $eco = trim((string) $game->eco_code);
                $name = $eco !== '' ? "Opening {$eco}" : 'Standard Chess';
            }

            if (! isset($buckets[$side][$name])) {
                $buckets[$side][$name] = [
                    'name' => $name,
                    'moves' => $this->extractOpeningMovesPreview((string) $game->pgn, 3),
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'total' => 0,
                ];
            }

            if ($buckets[$side][$name]['moves'] === '' && $game->pgn !== '') {
                $buckets[$side][$name]['moves'] = $this->extractOpeningMovesPreview((string) $game->pgn, 3);
            }

            $buckets[$side][$name]['total']++;
            match ($this->resolveResultCode($game, $player)) {
                'W' => $buckets[$side][$name]['wins']++,
                'D' => $buckets[$side][$name]['draws']++,
                default => $buckets[$side][$name]['losses']++,
            };
        }

        $formatted = ['white' => [], 'black' => []];
        foreach (['white', 'black'] as $side) {
            uasort($buckets[$side], fn (array $a, array $b): int => $b['total'] <=> $a['total']);
            $top = array_slice(array_values($buckets[$side]), 0, 3);

            $formatted[$side] = array_map(function (array $entry) use ($side): array {
                $total = $entry['total'];
                $wPct = $total > 0 ? (int) round(($entry['wins'] / $total) * 100) : 0;
                $dPct = $total > 0 ? (int) round(($entry['draws'] / $total) * 100) : 0;
                $lPct = max(0, 100 - $wPct - $dPct);

                return [
                    'name' => $entry['name'],
                    'moves' => $entry['moves'] !== '' ? $entry['moves'] : 'Opening sequence unavailable',
                    'winRate' => "{$wPct}% Win",
                    'games' => $total.' '.($total === 1 ? 'game' : 'games'),
                    'record' => "{$entry['wins']}W • {$entry['draws']}D • {$entry['losses']}L",
                    'accuracy' => 'Engine Pending',
                    'w' => $wPct,
                    'd' => $dPct,
                    'l' => $lPct,
                    'dotClass' => $side === 'white' ? 'bg-primary' : 'bg-secondary',
                ];
            }, $top);
        }

        return $formatted;
    }

    /**
     * Build paginated interactive match history list and filter metadata.
     *
     * @param  Collection<int, Game>  $games
     * @param  Collection<string, PlayerDailyActivity>  $activities
     * @return array{
     *     totalLabel: string,
     *     matches: array<int, array<string, mixed>>,
     *     filters: array{result: string, format: string},
     *     pagination: array<string, mixed>
     * }
     */
    public function buildMatchHistory(
        Collection $games,
        Player $player,
        Collection $activities,
        int $perPage = 10,
        int $page = 1,
        string $resultFilter = 'all',
        string $formatFilter = 'all',
    ): array {
        $perPage = in_array($perPage, [5, 10, 100], true) ? $perPage : 10;
        $yearlyTotal = (int) $activities->sum('games_count');
        $overallCount = max($yearlyTotal, $games->count());

        $filteredGames = $games->filter(function (Game $game) use ($player, $resultFilter, $formatFilter): bool {
            if ($resultFilter !== 'all') {
                $resultKey = match ($this->resolveResultCode($game, $player)) {
                    'W' => 'win',
                    'D' => 'draw',
                    default => 'loss',
                };
                if ($resultKey !== $resultFilter) {
                    return false;
                }
            }

            if ($formatFilter !== 'all') {
                $format = strtolower((string) ($game->time_class ?: 'rapid'));
                if ($format === 'daily') {
                    $format = 'classic';
                }
                if ($format !== $formatFilter) {
                    return false;
                }
            }

            return true;
        })->values();

        $filteredTotal = $filteredGames->count();
        $totalPages = max(1, (int) ceil($filteredTotal / $perPage));
        $currentPage = min(max(1, $page), $totalPages);
        $offset = ($currentPage - 1) * $perPage;
        $pageGames = $filteredGames->slice($offset, $perPage)->values();

        $matches = $pageGames->map(function (Game $game) use ($player): array {
            $isWhite = $this->isPlayerWhite($game, $player);
            $resultCode = $this->resolveResultCode($game, $player);

            $opponent = $isWhite ? $game->black_player_username : $game->white_player_username;
            $opponentRating = (int) ($isWhite ? $game->black_rating : $game->white_rating);
            $playerRating = (int) ($isWhite ? $game->white_rating : $game->black_rating);
            $movesCount = $this->estimateMovesCount((string) $game->pgn);

            $format = strtolower((string) ($game->time_class ?: 'rapid'));
            if ($format === 'daily') {
                $format = 'classic';
            }

            $formatLabel = $format === 'classic' ? 'Classical' : ucfirst($format);
            $modeLabel = $game->time_control !== ''
                ? "{$formatLabel} ".$this->formatTimeControl($game->time_control)
                : $formatLabel;

            $timeAgo = $game->end_time?->diffForHumans() ?? 'Recently';
            $movesPart = $movesCount > 0 ? "{$movesCount} moves" : 'Completed';

            [$resultKey, $statusLabel, $statusClass, $accentClass] = match ($resultCode) {
                'W' => ['win', 'Win', 'bg-primary/15 text-primary border-primary/30', 'bg-primary'],
                'D' => ['draw', 'Draw', 'bg-secondary/15 text-secondary border-secondary/30', 'bg-secondary'],
                default => ['loss', 'Loss', 'bg-danger/15 text-danger-400 border-danger/30', 'bg-danger-400'],
            };

            return [
                'opponent' => $opponent,
                'rating' => $opponentRating > 0 ? (string) $opponentRating : '—',
                'playerRating' => $playerRating > 0 ? (string) $playerRating : '—',
                'opening' => $game->opening_name ?: 'Standard Chess',
                'meta' => "{$timeAgo} • {$modeLabel} • {$movesPart}",
                'result' => $resultKey,
                'format' => $format,
                'statusLabel' => $statusLabel,
                'statusClass' => $statusClass,
                'accentClass' => $accentClass,
                'url' => $game->url,
            ];
        })->all();

        $from = $filteredTotal > 0 ? $offset + 1 : 0;
        $to = $filteredTotal > 0 ? min($offset + count($matches), $filteredTotal) : 0;

        return [
            'totalLabel' => number_format($overallCount).' '.($overallCount === 1 ? 'Match' : 'Matches'),
            'matches' => $matches,
            'filters' => [
                'result' => $resultFilter,
                'format' => $formatFilter,
            ],
            'pagination' => [
                'perPage' => $perPage,
                'perPageOptions' => [5, 10, 100],
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'totalItems' => $filteredTotal,
                'from' => $from,
                'to' => $to,
                'hasPrev' => $currentPage > 1,
                'hasNext' => $currentPage < $totalPages,
            ],
        ];
    }

    /**
     * Resolve highest-priority available rating for the navbar header.
     */
    private function resolvePrimaryRating(Player $player): string
    {
        foreach (['blitz_rating', 'rapid_rating', 'bullet_rating', 'classic_rating'] as $column) {
            if ($player->{$column} !== null) {
                return (string) $player->{$column};
            }
        }

        return '—';
    }

    /**
     * Default profile view payload when no chess account is connected yet.
     *
     * @return array<string, mixed>
     */
    private function defaultProfileData(
        ?User $user,
        int $perPage = 10,
        int $page = 1,
        string $resultFilter = 'all',
        string $formatFilter = 'all',
    ): array {
        $name = $user?->name ?? 'Player';
        $today = CarbonImmutable::today();

        return [
            'player' => null,
            'layout' => [
                'avatarUrl' => null,
                'name' => $name,
                'handle' => '@'.strtolower($name),
                'rating' => '—',
            ],
            'summary' => [
                'name' => $name,
                'username' => strtolower($name),
                'platform' => null,
                'avatarUrl' => null,
                'flag' => '',
                'streak' => 0,
                'memberSince' => $user?->created_at?->format('M Y') ?? 'New',
                'totalGames' => '0',
                'winRate' => '—',
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'winPct' => 0.0,
                'drawPct' => 0.0,
                'lossPct' => 0.0,
                'ratings' => [
                    ['label' => 'RAPID', 'value' => '—', 'peak' => 'Connect account', 'peakClass' => 'text-neutral-500', 'icon' => 'clock', 'iconClass' => 'text-neutral-400'],
                    ['label' => 'BLITZ', 'value' => '—', 'peak' => 'Connect account', 'peakClass' => 'text-neutral-500', 'icon' => 'zap', 'iconClass' => 'text-secondary'],
                    ['label' => 'BULLET', 'value' => '—', 'peak' => 'Connect account', 'peakClass' => 'text-neutral-500', 'icon' => 'rocket', 'iconClass' => 'text-tertiary-300'],
                    ['label' => 'CLASSIC', 'value' => '—', 'peak' => 'Connect account', 'peakClass' => 'text-neutral-500', 'icon' => 'crown', 'iconClass' => 'text-primary'],
                ],
            ],
            'weeklyPerformance' => $this->buildWeeklyPerformance(new Player, collect(), collect(), $today),
            'favoriteOpenings' => ['white' => [], 'black' => []],
            'matchHistory' => [
                'totalLabel' => '0 Matches',
                'matches' => [],
                'filters' => [
                    'result' => $resultFilter,
                    'format' => $formatFilter,
                ],
                'pagination' => [
                    'perPage' => $perPage,
                    'perPageOptions' => [5, 10, 100],
                    'currentPage' => $page,
                    'totalPages' => 1,
                    'totalItems' => 0,
                    'from' => 0,
                    'to' => 0,
                    'hasPrev' => false,
                    'hasNext' => false,
                ],
            ],
        ];
    }
}
