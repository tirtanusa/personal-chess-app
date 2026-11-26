<?php

declare(strict_types=1);

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\CheckChessAccountRequest;
use App\Http\Requests\Onboarding\ConnectChessAccountRequest;
use App\Services\ChessPlatformService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ConnectChessAccountController extends Controller
{
    public function __construct(
        private readonly ChessPlatformService $chessPlatformService,
    ) {}

    /**
     * Display the chess account onboarding view.
     */
    public function create(): View
    {
        return view('onboarding.connect-account');
    }

    /**
     * Check whether the given chess username exists on Chess.com or Lichess.
     */
    public function check(CheckChessAccountRequest $request): JsonResponse
    {
        $platform = $request->string('platform')->toString();
        $username = $request->string('username')->toString();
        $platformLabel = $platform === 'chesscom' ? 'Chess.com' : 'Lichess';

        try {
            $profile = $this->chessPlatformService->verifyPlayer(
                platform: $platform,
                username: $username,
                contactEmail: $request->user()?->email,
            );
        } catch (RuntimeException) {
            return response()->json([
                'found' => false,
                'message' => "Unable to reach {$platformLabel} API right now. Please try again.",
            ], 503);
        }

        if ($profile === null) {
            return response()->json([
                'found' => false,
                'message' => "Username \"{$username}\" was not found on {$platformLabel}.",
            ], 404);
        }

        return response()->json([
            'found' => true,
            'message' => "Verified on {$platformLabel}.",
            'player' => $profile,
        ]);
    }

    /**
     * Connect the verified chess account to the authenticated user.
     */
    public function store(ConnectChessAccountRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $this->chessPlatformService->connectPlayerToUser(
            user: $user,
            platform: $request->string('platform')->toString(),
            username: $request->string('username')->toString(),
        );

        return redirect()->route('home');
    }
}
