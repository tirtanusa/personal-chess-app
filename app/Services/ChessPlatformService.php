<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerDailyActivity;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ChessPlatformService
{
    /**
     * Verify whether a chess username exists on the given platform.
     *
     * @return array{
     *     platform: string,
     *     username: string,
     *     display_name: string,
     *     avatar_url: string,
     *     country_code: ?string,
     *     title: ?string,
     *     profile_url: ?string,
     *     ratings: array{rapid: ?int, blitz: ?int, bullet: ?int, classic: ?int}
     * }|null
     *
     * @throws RuntimeException When the external platform API cannot be reached or fails.
     */
    public function verifyPlayer(string $platform, string $username, ?string $contactEmail = null): ?array
    {
        $normalized = strtolower(trim($username));

        $response = $this->requestPlatform($platform, match ($platform) {
            'chesscom' => 'https://api.chess.com/pub/player/'.rawurlencode($normalized),
            'lichess' => 'https://lichess.org/api/user/'.rawurlencode($normalized),
            default => throw new RuntimeException("Unsupported chess platform [{$platform}]."),
        }, $contactEmail);

        if ($response->status() === 404) {
            return null;
        }

        if (! $response->successful()) {
            throw new RuntimeException("Platform API returned unexpected status [{$response->status()}].");
        }

        return $this->normalizeProfile($platform, $response, $normalized);
    }

    /**
     * Verify, fetch ratings + last 5 games, and attach the player to the user.
     *
     * @throws ValidationException
     */
    public function connectPlayerToUser(User $user, string $platform, string $username): Player
    {
        return $this->syncPlayerAccount($user, $platform, $username, contactEmail: $user->email);
    }

    /**
     * Force-refresh an existing connected player's profile, ratings, recent games, and heatmap activity.
     *
     * @throws ValidationException
     */
    public function refreshPlayer(Player $player, ?string $contactEmail = null): Player
    {
        $email = $this->resolveContactEmail($contactEmail, $player);

        return $this->syncPlayerAccount(null, $player->platform, $player->chesscom_username, $player, $email);
    }

    /**
     * Fetch fresh account data from the platform API and persist player, games, and heatmap rows.
     *
     * @throws ValidationException
     */
    private function syncPlayerAccount(
        ?User $user,
        string $platform,
        string $username,
        ?Player $existingPlayer = null,
        ?string $contactEmail = null,
    ): Player {
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $email = $contactEmail ?? $user?->email ?? Auth::user()?->email;

        try {
            $profile = $this->verifyPlayer($platform, $username, $email);
        } catch (RuntimeException) {
            throw ValidationException::withMessages([
                'username' => 'Unable to verify account right now. Please try again.',
            ]);
        }

        if ($profile === null) {
            throw ValidationException::withMessages([
                'username' => 'Chess account not found on the selected platform.',
            ]);
        }

        $ratings = $platform === 'chesscom'
            ? $this->fetchChessComRatings($profile['username'], $email)
            : $profile['ratings'];

        if ($platform === 'chesscom') {
            [
                'recent_games' => $recentGames,
                'daily_activities' => $dailyActivities,
            ] = $this->fetchChessComGamesAndActivity($profile['username'], null, $email);
        } else {
            $recentGames = $this->fetchRecentGames($platform, $profile['username'], null);
            $dailyActivities = [];
        }

        return DB::transaction(function () use ($user, $platform, $profile, $ratings, $recentGames, $dailyActivities, $existingPlayer): Player {
            $now = now();
            $normalizedUsername = strtolower($profile['username']);

            $latestEndTs = collect($recentGames)->max('end_time');
            $latestArchiveDate = is_numeric($latestEndTs) && (int) $latestEndTs > 0
                ? CarbonImmutable::createFromTimestamp((int) $latestEndTs, config('app.timezone'))
                : $now;

            $attributes = [
                'platform' => $platform,
                'display_name' => Str::limit($profile['display_name'], 255, ''),
                'avatar_url' => Str::limit($profile['avatar_url'], 255, ''),
                'country_code' => $profile['country_code'],
                'rapid_rating' => $ratings['rapid'],
                'blitz_rating' => $ratings['blitz'],
                'bullet_rating' => $ratings['bullet'],
                'classic_rating' => $ratings['classic'],
                'last_archived_year' => (int) $latestArchiveDate->format('Y'),
                'last_archived_month' => (int) $latestArchiveDate->format('n'),
                'last_synched_at' => $now->toDateString(),
            ];

            if ($existingPlayer !== null) {
                $existingPlayer->update($attributes);
                $player = $existingPlayer->fresh() ?? $existingPlayer;
            } else {
                $player = Player::query()->updateOrCreate(
                    ['chesscom_username' => $normalizedUsername],
                    $attributes,
                );
            }

            if ($user !== null) {
                $user->players()->syncWithoutDetaching([$player->id]);
            }

            $this->storeRecentGames($player, $recentGames);
            $this->storeDailyActivities($player, $dailyActivities);

            return $player;
        });
    }

    /**
     * Refresh 1-year daily Chess.com activity counts for an existing player.
     */
    public function syncChessComActivity(Player $player, ?string $contactEmail = null): void
    {
        if ($player->platform !== 'chesscom') {
            return;
        }

        $email = $this->resolveContactEmail($contactEmail, $player);

        ['daily_activities' => $dailyActivities] = $this->fetchChessComGamesAndActivity(
            $player->chesscom_username,
            0,
            $email,
        );

        DB::transaction(function () use ($player, $dailyActivities): void {
            $this->storeDailyActivities($player, $dailyActivities);
        });
    }

    /**
     * Fetch ratings for a Chess.com player from the stats endpoint.
     *
     * @return array{rapid: ?int, blitz: ?int, bullet: ?int, classic: ?int}
     */
    private function fetchChessComRatings(string $username, ?string $contactEmail = null): array
    {
        $normalized = strtolower(trim($username));

        try {
            $response = $this->requestPlatform(
                'chesscom',
                'https://api.chess.com/pub/player/'.rawurlencode($normalized).'/stats',
                $contactEmail,
            );
        } catch (RuntimeException) {
            return ['rapid' => null, 'blitz' => null, 'bullet' => null, 'classic' => null];
        }

        if (! $response->successful()) {
            return ['rapid' => null, 'blitz' => null, 'bullet' => null, 'classic' => null];
        }

        /** @var array<string, mixed> $stats */
        $stats = $response->json() ?? [];

        return [
            'rapid' => $this->extractNestedRating($stats, 'chess_rapid'),
            'blitz' => $this->extractNestedRating($stats, 'chess_blitz'),
            'bullet' => $this->extractNestedRating($stats, 'chess_bullet'),
            'classic' => $this->extractNestedRating($stats, 'chess_daily'),
        ];
    }

    /**
     * Fetch games for a player from Chess.com or Lichess (all games when $limit is null).
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentGames(string $platform, string $username, ?int $limit = 5): array
    {
        return match ($platform) {
            'chesscom' => $this->fetchRecentChessComGames($username, $limit),
            'lichess' => $this->fetchRecentLichessGames($username, $limit),
            default => [],
        };
    }

    /**
     * Fetch games (all archives when $recentLimit is null) and 1-year daily activity from Chess.com.
     *
     * @return array{
     *     recent_games: array<int, array<string, mixed>>,
     *     daily_activities: array<string, array<string, int|null>>
     * }
     */
    public function fetchChessComGamesAndActivity(
        string $username,
        ?int $recentLimit = null,
        ?string $contactEmail = null,
    ): array {
        $normalized = strtolower(trim($username));

        try {
            $archivesRes = $this->requestPlatform(
                'chesscom',
                'https://api.chess.com/pub/player/'.rawurlencode($normalized).'/games/archives',
                $contactEmail,
            );
        } catch (RuntimeException) {
            return ['recent_games' => [], 'daily_activities' => []];
        }

        if (! $archivesRes->successful()) {
            return ['recent_games' => [], 'daily_activities' => []];
        }

        /** @var array<int, string> $archives */
        $archives = $archivesRes->json('archives') ?? [];

        if ($archives === []) {
            return ['recent_games' => [], 'daily_activities' => []];
        }

        $today = CarbonImmutable::today(config('app.timezone'));
        $heatmapStart = $today->startOfWeek(CarbonInterface::SUNDAY)->subWeeks(52);
        $targetArchives = $recentLimit === null
            ? array_values($archives)
            : $this->selectChessComArchives($archives, $heatmapStart);

        $allGames = [];
        $recentByMode = [
            'rapid' => [],
            'blitz' => [],
            'bullet' => [],
            'classic' => [],
        ];
        $dailyActivities = [];
        $latestTsByDateMode = [];
        $windowStartTs = $heatmapStart->startOfDay()->timestamp;
        $windowEndTs = max($today->endOfDay()->timestamp, time() + 86400);

        // Iterate sequentially newest-to-oldest to avoid Chess.com parallel request 429 rate-limiting.
        for ($i = count($targetArchives) - 1; $i >= 0; $i--) {
            $archiveUrl = (string) $targetArchives[$i];

            try {
                $response = $this->requestPlatform('chesscom', $archiveUrl, $contactEmail);
                if ($response->status() === 429) {
                    usleep(300000);
                    $response = $this->requestPlatform('chesscom', $archiveUrl, $contactEmail);
                }
            } catch (RuntimeException) {
                continue;
            }

            if (! $response->successful()) {
                continue;
            }

            /** @var array<int, array<string, mixed>> $monthGames */
            $monthGames = $response->json('games') ?? [];

            foreach (array_reverse($monthGames) as $rawGame) {
                if (! is_array($rawGame)) {
                    continue;
                }

                if ($recentLimit === null) {
                    $allGames[] = $this->normalizeChessComGame($rawGame);
                } elseif ($recentLimit > 0) {
                    $normalizedGame = $this->normalizeChessComGame($rawGame);
                    $this->collectRecentGameByMode($recentByMode, $normalizedGame, $recentLimit);
                }

                $this->accumulateChessComDailyActivity(
                    $dailyActivities,
                    $latestTsByDateMode,
                    $rawGame,
                    $normalized,
                    $windowStartTs,
                    $windowEndTs,
                );
            }
        }

        ksort($dailyActivities);

        $recentGames = $recentLimit === null
            ? $allGames
            : array_merge(...array_values($recentByMode));

        return [
            'recent_games' => $recentGames,
            'daily_activities' => $dailyActivities,
        ];
    }

    /**
     * Select Chess.com archive URLs covering the 1-year heatmap window plus at least the latest 2 months.
     *
     * @param  array<int, string>  $archives
     * @return array<int, string>
     */
    private function selectChessComArchives(array $archives, CarbonImmutable $heatmapStart): array
    {
        $startMonth = $heatmapStart->startOfMonth();
        $latestTwo = array_slice($archives, -2);
        $selected = [];

        foreach ($archives as $url) {
            if (in_array($url, $latestTwo, true)) {
                $selected[] = $url;

                continue;
            }

            if (preg_match('#/(\d{4})/(\d{2})/?$#', (string) $url, $matches)) {
                $archiveMonth = CarbonImmutable::createFromDate((int) $matches[1], (int) $matches[2], 1)->startOfMonth();
                if ($archiveMonth->greaterThanOrEqualTo($startMonth)) {
                    $selected[] = $url;
                }
            }
        }

        return array_values(array_unique($selected));
    }

    /**
     * Add a single Chess.com game to the daily activity counts and closing rating bucket.
     *
     * @param  array<string, array<string, int|null>>  $buckets
     * @param  array<string, array<string, int>>  $latestTsByDateMode
     * @param  array<string, mixed>  $rawGame
     */
    private function accumulateChessComDailyActivity(
        array &$buckets,
        array &$latestTsByDateMode,
        array $rawGame,
        string $normalizedUsername,
        int $windowStartTs,
        int $windowEndTs,
    ): void {
        $endTime = (int) ($rawGame['end_time'] ?? 0);
        if ($endTime < $windowStartTs || $endTime > $windowEndTs) {
            return;
        }

        $white = is_array($rawGame['white'] ?? null) ? $rawGame['white'] : [];
        $black = is_array($rawGame['black'] ?? null) ? $rawGame['black'] : [];

        $isWhite = strtolower((string) ($white['username'] ?? '')) === $normalizedUsername;
        $isBlack = strtolower((string) ($black['username'] ?? '')) === $normalizedUsername;

        if (! $isWhite && ! $isBlack) {
            return;
        }

        $dateKey = CarbonImmutable::createFromTimestamp($endTime, config('app.timezone'))->toDateString();

        if (! isset($buckets[$dateKey])) {
            $buckets[$dateKey] = [
                'games_count' => 0,
                'wins_count' => 0,
                'draws_count' => 0,
                'losses_count' => 0,
                'rapid_count' => 0,
                'blitz_count' => 0,
                'bullet_count' => 0,
                'classic_count' => 0,
                'rapid_rating' => null,
                'blitz_rating' => null,
                'bullet_rating' => null,
                'classic_rating' => null,
            ];
        }

        $buckets[$dateKey]['games_count']++;

        $rawResult = (string) ($isWhite ? ($white['result'] ?? '') : ($black['result'] ?? ''));
        $outcome = $this->normalizeChessComResult($rawResult);
        match ($outcome) {
            'win' => $buckets[$dateKey]['wins_count']++,
            'draw' => $buckets[$dateKey]['draws_count']++,
            default => $buckets[$dateKey]['losses_count']++,
        };

        $timeClass = (string) ($rawGame['time_class'] ?? '');
        if ($timeClass === 'daily') {
            $timeClass = 'classic';
        }

        $column = "{$timeClass}_count";
        if (isset($buckets[$dateKey][$column])) {
            $buckets[$dateKey][$column]++;
        }

        $ratingColumn = "{$timeClass}_rating";
        $playerRating = (int) ($isWhite ? ($white['rating'] ?? 0) : ($black['rating'] ?? 0));
        if (
            array_key_exists($ratingColumn, $buckets[$dateKey])
            && $playerRating > 0
            && $endTime >= ($latestTsByDateMode[$dateKey][$timeClass] ?? -1)
        ) {
            $latestTsByDateMode[$dateKey][$timeClass] = $endTime;
            $buckets[$dateKey][$ratingColumn] = $playerRating;
        }
    }

    /**
     * Fetch games from Chess.com monthly archives.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentChessComGames(string $username, ?int $limit): array
    {
        return $this->fetchChessComGamesAndActivity($username, $limit)['recent_games'];
    }

    /**
     * Fetch games from Lichess NDJSON endpoint (all games when $limit is null).
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentLichessGames(string $username, ?int $limit): array
    {
        $normalized = strtolower(trim($username));
        $query = [
            'pgnInJson' => 'true',
            'opening' => 'true',
        ];
        if ($limit !== null) {
            $query['max'] = max(40, $limit * 8);
        }

        $url = 'https://lichess.org/api/games/user/'.rawurlencode($normalized).'?'.http_build_query($query);

        try {
            $response = Http::withHeaders(['Accept' => 'application/x-ndjson'])
                ->timeout($limit === null ? 60 : 10)
                ->get($url);
        } catch (\Throwable) {
            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($response->body())) ?: [];
        $allGames = [];
        $recentByMode = [
            'rapid' => [],
            'blitz' => [],
            'bullet' => [],
            'classic' => [],
        ];

        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            $decoded = json_decode($line, true);
            if (! is_array($decoded)) {
                continue;
            }

            $normalizedGame = $this->normalizeLichessGame($decoded);

            if ($limit === null) {
                $allGames[] = $normalizedGame;
            } else {
                $this->collectRecentGameByMode($recentByMode, $normalizedGame, $limit);
            }
        }

        return $limit === null
            ? $allGames
            : array_merge(...array_values($recentByMode));
    }

    /**
     * Collect up to $limitPerMode recent games grouped by mode.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $buckets
     * @param  array<string, mixed>  $normalizedGame
     */
    private function collectRecentGameByMode(array &$buckets, array $normalizedGame, int $limitPerMode): void
    {
        $mode = (string) ($normalizedGame['time_class'] ?? '');
        if (! isset($buckets[$mode])) {
            return;
        }

        if (count($buckets[$mode]) < $limitPerMode) {
            $buckets[$mode][] = $normalizedGame;
        }
    }

    /**
     * Persist normalized games in bulk chunks and link matching white/black player IDs.
     *
     * @param  array<int, array<string, mixed>>  $games
     */
    private function storeRecentGames(Player $player, array $games): void
    {
        if ($games === []) {
            return;
        }

        $playerUsername = strtolower($player->chesscom_username);
        $now = now()->toDateTimeString();
        $rowsByUuid = [];

        foreach ($games as $game) {
            $uuid = Str::limit((string) $game['external_id'], 255, '');
            if ($uuid === '') {
                continue;
            }

            $isWhite = strtolower((string) $game['white_player_username']) === $playerUsername;
            $isBlack = strtolower((string) $game['black_player_username']) === $playerUsername;

            $rowsByUuid[$uuid] = [
                'id' => Str::uuid()->toString(),
                'chesscom_uuid' => $uuid,
                'url' => Str::limit((string) $game['url'], 255, ''),
                'time_class' => Str::limit((string) $game['time_class'], 255, ''),
                'time_control' => Str::limit((string) $game['time_control'], 255, ''),
                'rated' => $game['rated'] ? '1' : '0',
                'end_time' => (string) $game['end_time'],
                'white_player_username' => Str::limit((string) $game['white_player_username'], 255, ''),
                'black_player_username' => Str::limit((string) $game['black_player_username'], 255, ''),
                'white_rating' => (string) $game['white_rating'],
                'black_rating' => (string) $game['black_rating'],
                'white_player_id' => $isWhite ? $player->id : null,
                'black_player_id' => $isBlack ? $player->id : null,
                'white_result' => (string) $game['white_result'],
                'black_result' => (string) $game['black_result'],
                'eco_code' => Str::limit((string) $game['eco_code'], 255, ''),
                'opening_name' => Str::limit((string) $game['opening_name'], 255, ''),
                'opening_url' => $game['opening_url'] !== null ? Str::limit((string) $game['opening_url'], 255, '') : null,
                'pgn' => $game['pgn'],
                'analysis_status' => 'pending',
                'analysis_error' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $updateColumns = [
            'url',
            'time_class',
            'time_control',
            'rated',
            'end_time',
            'white_player_username',
            'black_player_username',
            'white_rating',
            'black_rating',
            'white_player_id',
            'black_player_id',
            'white_result',
            'black_result',
            'eco_code',
            'opening_name',
            'opening_url',
            'pgn',
            'updated_at',
        ];

        foreach (array_chunk(array_values($rowsByUuid), 250) as $chunk) {
            Game::query()->upsert($chunk, ['chesscom_uuid'], $updateColumns);
        }
    }

    /**
     * Persist daily activity heatmap rows for the player in bulk chunks.
     *
     * @param  array<string, array<string, int|null>>  $dailyActivities
     */
    private function storeDailyActivities(Player $player, array $dailyActivities): void
    {
        if ($dailyActivities === []) {
            return;
        }

        $now = now()->toDateTimeString();
        $rows = [];

        foreach ($dailyActivities as $date => $counts) {
            $rows[] = array_merge([
                'id' => Str::uuid()->toString(),
                'player_id' => $player->id,
                'activity_date' => $date,
                'created_at' => $now,
                'updated_at' => $now,
            ], $counts);
        }

        $updateColumns = [
            'games_count',
            'wins_count',
            'draws_count',
            'losses_count',
            'rapid_count',
            'blitz_count',
            'bullet_count',
            'classic_count',
            'rapid_rating',
            'blitz_rating',
            'bullet_rating',
            'classic_rating',
            'updated_at',
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            PlayerDailyActivity::query()->upsert($chunk, ['player_id', 'activity_date'], $updateColumns);
        }
    }

    /**
     * Normalize a Chess.com game payload into database-ready fields.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeChessComGame(array $raw): array
    {
        $white = is_array($raw['white'] ?? null) ? $raw['white'] : [];
        $black = is_array($raw['black'] ?? null) ? $raw['black'] : [];
        $pgn = (string) ($raw['pgn'] ?? '');
        $openingUrl = isset($raw['eco']) ? (string) $raw['eco'] : null;

        $ecoCode = '';
        if ($pgn !== '' && preg_match('/\[ECO\s+"([^"]+)"\]/', $pgn, $matches)) {
            $ecoCode = $matches[1];
        }

        $openingName = $this->extractOpeningNameFromUrl($openingUrl)
            ?: ($ecoCode !== '' ? "Opening {$ecoCode}" : 'Standard Chess');

        $timeClass = (string) ($raw['time_class'] ?? 'rapid');
        if ($timeClass === 'daily') {
            $timeClass = 'classic';
        }

        $url = (string) ($raw['url'] ?? '');
        $externalId = (string) ($raw['uuid'] ?? ($url !== '' ? basename($url) : uniqid('chesscom_', true)));

        return [
            'external_id' => $externalId,
            'url' => $url,
            'time_class' => $timeClass,
            'time_control' => (string) ($raw['time_control'] ?? ''),
            'rated' => (bool) ($raw['rated'] ?? true),
            'end_time' => (int) ($raw['end_time'] ?? time()),
            'white_player_username' => (string) ($white['username'] ?? 'Unknown'),
            'black_player_username' => (string) ($black['username'] ?? 'Unknown'),
            'white_rating' => (int) ($white['rating'] ?? 0),
            'black_rating' => (int) ($black['rating'] ?? 0),
            'white_result' => $this->normalizeChessComResult((string) ($white['result'] ?? '')),
            'black_result' => $this->normalizeChessComResult((string) ($black['result'] ?? '')),
            'eco_code' => $ecoCode,
            'opening_name' => $openingName,
            'opening_url' => $openingUrl,
            'pgn' => $pgn,
        ];
    }

    /**
     * Normalize a Lichess game payload into database-ready fields.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeLichessGame(array $raw): array
    {
        $id = (string) ($raw['id'] ?? uniqid('lichess_', true));
        $players = is_array($raw['players'] ?? null) ? $raw['players'] : [];
        $white = is_array($players['white'] ?? null) ? $players['white'] : [];
        $black = is_array($players['black'] ?? null) ? $players['black'] : [];
        $whiteUser = is_array($white['user'] ?? null) ? $white['user'] : [];
        $blackUser = is_array($black['user'] ?? null) ? $black['user'] : [];

        $winner = isset($raw['winner']) ? (string) $raw['winner'] : null;
        $whiteResult = $winner === 'white' ? 'win' : ($winner === 'black' ? 'loss' : 'draw');
        $blackResult = $winner === 'black' ? 'win' : ($winner === 'white' ? 'loss' : 'draw');

        $clock = is_array($raw['clock'] ?? null) ? $raw['clock'] : [];
        $timeControl = isset($clock['initial'])
            ? ((int) $clock['initial']).'+'.((int) ($clock['increment'] ?? 0))
            : (string) ($raw['speed'] ?? '');

        $speed = (string) ($raw['speed'] ?? 'rapid');
        if (in_array($speed, ['classical', 'correspondence'], true)) {
            $speed = 'classic';
        }

        $opening = is_array($raw['opening'] ?? null) ? $raw['opening'] : [];
        $endTimeMs = (int) ($raw['lastMoveAt'] ?? $raw['createdAt'] ?? (time() * 1000));

        return [
            'external_id' => 'lichess_'.$id,
            'url' => 'https://lichess.org/'.$id,
            'time_class' => $speed,
            'time_control' => $timeControl,
            'rated' => (bool) ($raw['rated'] ?? true),
            'end_time' => intdiv($endTimeMs, 1000),
            'white_player_username' => (string) ($whiteUser['name'] ?? $whiteUser['id'] ?? 'Anonymous'),
            'black_player_username' => (string) ($blackUser['name'] ?? $blackUser['id'] ?? 'Anonymous'),
            'white_rating' => (int) ($white['rating'] ?? 0),
            'black_rating' => (int) ($black['rating'] ?? 0),
            'white_result' => $whiteResult,
            'black_result' => $blackResult,
            'eco_code' => (string) ($opening['eco'] ?? ''),
            'opening_name' => (string) ($opening['name'] ?? 'Standard Chess'),
            'opening_url' => null,
            'pgn' => (string) ($raw['pgn'] ?? $raw['moves'] ?? ''),
        ];
    }

    /**
     * Send an HTTP GET request to a chess platform endpoint.
     *
     * @throws RuntimeException
     */
    private function requestPlatform(string $platform, string $url, ?string $contactEmail = null): Response
    {
        $headers = match ($platform) {
            'chesscom' => [
                'Accept' => 'application/json',
                'User-Agent' => $this->buildChessComUserAgent($contactEmail),
            ],
            'lichess' => [
                'Accept' => 'application/json',
            ],
            default => throw new RuntimeException("Unsupported chess platform [{$platform}]."),
        };

        try {
            return Http::withHeaders($headers)->timeout(10)->get($url);
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to reach the chess platform API.', previous: $e);
        }
    }

    /**
     * Build the Chess.com PubAPI User-Agent header using the active user's email address.
     */
    public function buildChessComUserAgent(?string $contactEmail = null): string
    {
        $email = trim((string) ($contactEmail ?? Auth::user()?->email ?? ''));

        if ($email === '') {
            $email = trim((string) config('mail.from.address', 'noreply@chess-project-personal.local'));
        }

        $sanitizedEmail = str_replace(["\r", "\n"], '', $email);

        return "chess-project-personal/1.0 ({$sanitizedEmail})";
    }

    /**
     * Resolve the contact email from an explicit value, the authenticated user, or linked player owner.
     */
    private function resolveContactEmail(?string $contactEmail, Player $player): ?string
    {
        if ($contactEmail !== null && trim($contactEmail) !== '') {
            return $contactEmail;
        }

        $authEmail = Auth::user()?->email;
        if (is_string($authEmail) && trim($authEmail) !== '') {
            return $authEmail;
        }

        if (! $player->exists) {
            return null;
        }

        $ownerEmail = $player->users()->value('email');

        return is_string($ownerEmail) && trim($ownerEmail) !== '' ? $ownerEmail : null;
    }

    /**
     * Normalize platform-specific JSON responses into a uniform player array.
     *
     * @return array{
     *     platform: string,
     *     username: string,
     *     display_name: string,
     *     avatar_url: string,
     *     country_code: ?string,
     *     title: ?string,
     *     profile_url: ?string,
     *     ratings: array{rapid: ?int, blitz: ?int, bullet: ?int, classic: ?int}
     * }|null
     */
    private function normalizeProfile(string $platform, Response $response, string $fallbackUsername): ?array
    {
        /** @var array<string, mixed> $payload */
        $payload = $response->json() ?? [];

        if ($platform === 'lichess') {
            if (! empty($payload['disabled'])) {
                return null;
            }

            $username = (string) ($payload['username'] ?? $payload['id'] ?? $fallbackUsername);
            $profile = is_array($payload['profile'] ?? null) ? $payload['profile'] : [];
            $fullName = trim((string) (($profile['firstName'] ?? '').' '.($profile['lastName'] ?? '')));
            $perfs = is_array($payload['perfs'] ?? null) ? $payload['perfs'] : [];

            return [
                'platform' => 'lichess',
                'username' => $username,
                'display_name' => $fullName !== '' ? $fullName : $username,
                'avatar_url' => '',
                'country_code' => isset($profile['flag']) ? strtoupper((string) $profile['flag']) : null,
                'title' => isset($payload['title']) ? (string) $payload['title'] : null,
                'profile_url' => isset($payload['url']) ? (string) $payload['url'] : 'https://lichess.org/@/'.$username,
                'ratings' => [
                    'rapid' => $this->extractLichessPerfRating($perfs, 'rapid'),
                    'blitz' => $this->extractLichessPerfRating($perfs, 'blitz'),
                    'bullet' => $this->extractLichessPerfRating($perfs, 'bullet'),
                    'classic' => $this->extractLichessPerfRating($perfs, 'classical'),
                ],
            ];
        }

        $username = (string) ($payload['username'] ?? $fallbackUsername);
        $countryUrl = isset($payload['country']) ? (string) $payload['country'] : '';
        $countryCode = $countryUrl !== '' ? strtoupper(basename($countryUrl)) : null;

        return [
            'platform' => 'chesscom',
            'username' => $username,
            'display_name' => (string) ($payload['name'] ?? $username),
            'avatar_url' => (string) ($payload['avatar'] ?? ''),
            'country_code' => $countryCode && strlen($countryCode) === 2 ? $countryCode : null,
            'title' => isset($payload['title']) ? (string) $payload['title'] : null,
            'profile_url' => isset($payload['url']) ? (string) $payload['url'] : 'https://www.chess.com/member/'.$username,
            'ratings' => ['rapid' => null, 'blitz' => null, 'bullet' => null, 'classic' => null],
        ];
    }

    /**
     * Extract Chess.com rating from nested stats structure.
     *
     * @param  array<string, mixed>  $stats
     */
    private function extractNestedRating(array $stats, string $key): ?int
    {
        $bucket = $stats[$key] ?? null;
        if (! is_array($bucket)) {
            return null;
        }

        $last = $bucket['last'] ?? null;
        if (! is_array($last) || ! isset($last['rating'])) {
            return null;
        }

        return (int) $last['rating'];
    }

    /**
     * Extract Lichess rating from perfs object.
     *
     * @param  array<string, mixed>  $perfs
     */
    private function extractLichessPerfRating(array $perfs, string $key): ?int
    {
        $bucket = $perfs[$key] ?? null;
        if (! is_array($bucket) || ! isset($bucket['rating'])) {
            return null;
        }

        return (int) $bucket['rating'];
    }

    /**
     * Map Chess.com game result string to win/draw/loss.
     */
    private function normalizeChessComResult(string $result): string
    {
        return match ($result) {
            'win' => 'win',
            'agreed', 'repetition', 'stalemate', 'insufficient', '50move', 'timevsinsufficient' => 'draw',
            default => 'loss',
        };
    }

    /**
     * Convert a Chess.com ECO URL slug into a readable opening name.
     */
    private function extractOpeningNameFromUrl(?string $url): string
    {
        if ($url === null || $url === '') {
            return '';
        }

        $slug = basename(parse_url($url, PHP_URL_PATH) ?: '');
        if ($slug === '') {
            return '';
        }

        $cleaned = preg_replace('/-\d+\..*$/', '', $slug) ?? $slug;

        return str_replace('-', ' ', $cleaned);
    }
}
