<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Services\ProfileDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileDashboardService $profileDashboardService,
    ) {}

    /**
     * Display the player's Profile page.
     */
    public function show(Request $request): View
    {
        $options = array_filter([
            'per_page' => $request->query('per_page'),
            'page' => $request->query('page'),
            'result' => $request->query('result'),
            'format' => $request->query('format'),
        ], fn (mixed $value): bool => $value !== null && $value !== '');

        $data = $options === []
            ? $this->profileDashboardService->getProfileData($request->user())
            : $this->profileDashboardService->getProfileData($request->user(), $options);

        return view('profile', $data);
    }

    /**
     * Download the connected player's stored match history as a PGN archive.
     */
    public function exportPgn(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $player = $user?->players()->latest('user_player.created_at')->first();

        if (! $player instanceof Player) {
            return redirect()->route('onboarding.connect')
                ->with('sync_error', 'Connect a Chess.com or Lichess account first.');
        }

        $pgns = Game::query()
            ->where(function ($query) use ($player): void {
                $query->where('white_player_id', $player->id)
                    ->orWhere('black_player_id', $player->id);
            })
            ->whereNotNull('pgn')
            ->where('pgn', '!=', '')
            ->orderByDesc('end_time')
            ->pluck('pgn')
            ->map(fn (mixed $pgn): string => trim((string) $pgn))
            ->filter(fn (string $pgn): bool => $pgn !== '')
            ->values();

        if ($pgns->isEmpty()) {
            return redirect()->route('profile')
                ->with('profile_notice', 'No stored PGNs available to export yet.');
        }

        $filename = preg_replace('/[^a-z0-9_-]/i', '_', $player->chesscom_username).'-games.pgn';
        $content = $pgns->implode("\n\n")."\n";

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/x-chess-pgn; charset=utf-8',
        ]);
    }
}
