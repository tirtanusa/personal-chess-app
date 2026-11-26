<?php

declare(strict_types=1);

namespace App\Services\Concerns;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerDailyActivity;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

trait FormatsPlayerStats
{
    /**
     * Determine W, D, or L for the given player in a game.
     */
    private function resolveResultCode(Game $game, Player $player): string
    {
        $isWhite = $this->isPlayerWhite($game, $player);
        $raw = strtolower($isWhite ? $game->white_result : $game->black_result);

        return match ($raw) {
            'win' => 'W',
            'draw', 'agreed', 'repetition', 'stalemate', 'insufficient', '50move', 'timevsinsufficient' => 'D',
            default => 'L',
        };
    }

    /**
     * Determine whether the player played the white pieces.
     */
    private function isPlayerWhite(Game $game, Player $player): bool
    {
        if ($game->white_player_id !== null) {
            return $game->white_player_id === $player->id;
        }

        return strtolower($game->white_player_username) === strtolower($player->chesscom_username);
    }

    /**
     * Estimate full move count from a PGN or move string.
     */
    public function estimateMovesCount(string $pgn): int
    {
        if ($pgn === '') {
            return 0;
        }

        $body = $this->stripPgnMetadata($pgn);

        if (preg_match_all('/(?:^|\s)(\d+)\.(?:\.\.)?(?=\s|$|[a-zA-Z])/', $body, $matches) && ! empty($matches[1])) {
            return (int) max(array_map('intval', $matches[1]));
        }

        $tokens = preg_split('/\s+/', trim($body)) ?: [];
        $plies = count(array_filter($tokens, fn (string $t): bool => $t !== '' && ! in_array($t, ['1-0', '0-1', '1/2-1/2', '*'], true)));

        return (int) ceil($plies / 2);
    }

    /**
     * Extract up to $fullMoves opening moves preview from a PGN string.
     */
    private function extractOpeningMovesPreview(string $pgn, int $fullMoves = 3): string
    {
        if ($pgn === '') {
            return '';
        }

        $body = $this->stripPgnMetadata($pgn);
        $tokens = preg_split('/\s+/', trim($body)) ?: [];
        $collected = [];
        $moveNum = 0;

        foreach ($tokens as $token) {
            if ($token === '' || in_array($token, ['1-0', '0-1', '1/2-1/2', '*'], true)) {
                continue;
            }

            // Skip Black continuation marker like "1..." when building a compact opening preview.
            if (preg_match('/^\d+\.\.\.$/', $token)) {
                continue;
            }

            if (preg_match('/^(\d+)\.$/', $token, $m)) {
                $moveNum = (int) $m[1];
                if ($moveNum > $fullMoves) {
                    break;
                }
                $collected[] = $token;

                continue;
            }

            if (preg_match('/^(\d+)\.([a-zA-Z].*)$/', $token, $m)) {
                $moveNum = (int) $m[1];
                if ($moveNum > $fullMoves) {
                    break;
                }
                $collected[] = $m[1].'. '.$m[2];

                continue;
            }

            if ($moveNum > $fullMoves) {
                break;
            }

            $collected[] = $token;
        }

        return implode(' ', $collected);
    }

    /**
     * Remove PGN header tags and brace annotations from move text.
     */
    private function stripPgnMetadata(string $pgn): string
    {
        $body = preg_replace('/^\s*\[[^\r\n]*\]\s*$/m', '', $pgn) ?? $pgn;
        $body = preg_replace('/\[[^\]]*\]/', '', $body) ?? $body;

        return preg_replace('/\{[^}]*\}/', ' ', $body) ?? $body;
    }

    /**
     * Format raw seconds time control (e.g. "600" -> "10m", "180+2" -> "3+2").
     */
    private function formatTimeControl(string $timeControl): string
    {
        if (str_contains($timeControl, '+')) {
            [$base, $inc] = explode('+', $timeControl, 2);
            if (is_numeric($base)) {
                $mins = (int) round(((int) $base) / 60);

                return ($mins > 0 ? $mins : $base).'+'.$inc;
            }
        }

        if (is_numeric($timeControl)) {
            $seconds = (int) $timeControl;
            if ($seconds >= 60) {
                return ((int) round($seconds / 60)).'m';
            }

            return $seconds.'s';
        }

        return $timeControl;
    }

    /**
     * Convert a 2-letter ISO country code into an emoji flag.
     */
    private function countryCodeToFlag(?string $countryCode): string
    {
        if ($countryCode === null || strlen($countryCode) !== 2) {
            return '';
        }

        $upper = strtoupper($countryCode);
        $first = mb_chr(127397 + ord($upper[0]));
        $second = mb_chr(127397 + ord($upper[1]));

        return ($first && $second) ? $first.$second : '';
    }

    /**
     * Build a smooth SVG cubic Bezier path through ordered chart coordinates.
     *
     * @param  array<int, array{x: float, y: float}>  $coords
     */
    private function buildSmoothSvgPath(array $coords, string $fallback = 'M 40 56 L 352 56'): string
    {
        $total = count($coords);
        if ($total === 0) {
            return $fallback;
        }

        $path = "M {$coords[0]['x']} {$coords[0]['y']}";
        for ($i = 0; $i < $total - 1; $i++) {
            $curr = $coords[$i];
            $next = $coords[$i + 1];
            $midX = round(($curr['x'] + $next['x']) / 2, 1);
            $path .= " C {$midX} {$curr['y']}, {$midX} {$next['y']}, {$next['x']} {$next['y']}";
        }

        return $path;
    }

    /**
     * Count consecutive days with at least one played game ending today or yesterday.
     *
     * @param  Collection<string, PlayerDailyActivity>  $activitiesByDate
     */
    private function calculateDailyStreak(
        Collection $activitiesByDate,
        CarbonImmutable $today,
        CarbonImmutable $startDate,
        string $countField = 'games_count',
    ): int {
        $todayCount = (int) ($activitiesByDate->get($today->toDateString())?->{$countField} ?? 0);
        $cursor = $todayCount > 0 ? $today : $today->subDay();
        $streak = 0;

        while (! $cursor->isBefore($startDate)) {
            $count = (int) ($activitiesByDate->get($cursor->toDateString())?->{$countField} ?? 0);
            if ($count <= 0) {
                break;
            }

            $streak++;
            $cursor = $cursor->subDay();
        }

        return $streak;
    }
}
