<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\ChessPlatformService;
use App\Services\HomeDashboardService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PDOException;
use RuntimeException;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeDashboardService $homeDashboardService,
        private readonly ChessPlatformService $chessPlatformService,
    ) {}

    /**
     * Display the player's Home dashboard.
     */
    public function index(Request $request): View
    {
        return view('home', $this->homeDashboardService->getDashboardData($request->user()));
    }

    /**
     * Force-refresh the connected chess player's ratings, recent games, and heatmap data.
     */
    public function refresh(Request $request): RedirectResponse
    {
        $user = $request->user();
        $player = $user?->players()->latest('user_player.created_at')->first();

        if (! $player instanceof Player) {
            return redirect()->route('onboarding.connect')
                ->with('sync_error', 'Connect a Chess.com or Lichess account first.');
        }

        try {
            $refreshed = $this->chessPlatformService->refreshPlayer($player);
        } catch (QueryException|PDOException $exception) {
            report($exception);

            $message = 'Failed to save synced chess data to database. Please ensure "php artisan migrate" has been run and try again.';

            return redirect()->route('home')->with('sync_error', $message);
        } catch (ValidationException|RuntimeException $exception) {
            report($exception);

            $message = $exception instanceof ValidationException
                ? ($exception->validator->errors()->first() ?: 'Unable to refresh player data right now.')
                : 'Unable to reach the chess platform API right now. Please try again.';

            return redirect()->route('home')->with('sync_error', $message);
        }

        $platformLabel = $refreshed->platform === 'lichess' ? 'Lichess' : 'Chess.com';

        return redirect()->route('home')
            ->with('status', "Data synced from {$platformLabel} for @{$refreshed->chesscom_username}.");
    }
}
