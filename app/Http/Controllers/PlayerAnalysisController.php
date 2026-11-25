<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class PlayerAnalysisController extends Controller
{
    /**
     * Display the scouting & analysis dossier for a specific player.
     */
    public function show(?string $slug = null): View
    {
        $players = $this->getPlayerProfiles();
        $key = $slug && isset($players[$slug]) ? $slug : 'alireza-firouzja';

        return view('analysis.player', [
            'player' => $players[$key],
        ]);
    }

    /**
     * Return sample scouting dossiers keyed by slug.
     *
     * @return array<string, array<string, mixed>>
     */
    private function getPlayerProfiles(): array
    {
        return [
            'alireza-firouzja' => [
                'slug' => 'alireza-firouzja',
                'firstName' => 'Alireza',
                'name' => 'Alireza Firouzja',
                'initials' => 'AF',
                'flag' => '🇫🇷',
                'title' => 'GM',
                'dossierId' => '#GM-8291',
                'fide' => 'FIDE 2805',
                'lastSeen' => '15m ago',
                'h2hWinrate' => '45.4% Win',
                'h2hRecord' => '(5W · 2D · 4L)',
                'totalWinrate' => '66.8%',
                'totalGames' => '2,770 Games',
                'winsDraws' => '1,840W · 410D',
                'losses' => '520 Losses',
                'avgAccuracy' => '94.2%',
                'accuracyTier' => 'Super-GM Level',
                'rapid' => ['value' => '2750', 'peak' => 'Peak 2765'],
                'blitz' => ['value' => '2805', 'peak' => 'Peak 2820'],
                'bullet' => ['value' => '2890', 'peak' => 'Peak 2905'],
                'eloNet' => '+24 Net',
                'eloMatches' => '18 Matches',
            ],
            'daniel-naroditsky' => [
                'slug' => 'daniel-naroditsky',
                'firstName' => 'Daniel',
                'name' => 'Daniel Naroditsky',
                'initials' => 'DN',
                'flag' => '🇺🇸',
                'title' => 'GM',
                'dossierId' => '#GM-6410',
                'fide' => 'FIDE 2615',
                'lastSeen' => '2h ago',
                'h2hWinrate' => '38.0% Win',
                'h2hRecord' => '(3W · 2D · 5L)',
                'totalWinrate' => '64.5%',
                'totalGames' => '4,120 Games',
                'winsDraws' => '2,657W · 540D',
                'losses' => '923 Losses',
                'avgAccuracy' => '92.8%',
                'accuracyTier' => 'GM Precision',
                'rapid' => ['value' => '2640', 'peak' => 'Peak 2675'],
                'blitz' => ['value' => '2785', 'peak' => 'Peak 2810'],
                'bullet' => ['value' => '2850', 'peak' => 'Peak 2880'],
                'eloNet' => '+19 Net',
                'eloMatches' => '22 Matches',
            ],
            'hikaru-nakamura' => [
                'slug' => 'hikaru-nakamura',
                'firstName' => 'Hikaru',
                'name' => 'Hikaru Nakamura',
                'initials' => 'HN',
                'flag' => '🇺🇸',
                'title' => 'GM',
                'dossierId' => '#GM-0002',
                'fide' => 'FIDE 2875',
                'lastSeen' => 'Active now',
                'h2hWinrate' => '32.0% Win',
                'h2hRecord' => '(2W · 3D · 6L)',
                'totalWinrate' => '71.4%',
                'totalGames' => '6,540 Games',
                'winsDraws' => '4,669W · 890D',
                'losses' => '981 Losses',
                'avgAccuracy' => '95.1%',
                'accuracyTier' => 'Super-GM Level',
                'rapid' => ['value' => '2790', 'peak' => 'Peak 2815'],
                'blitz' => ['value' => '2925', 'peak' => 'Peak 2960'],
                'bullet' => ['value' => '2980', 'peak' => 'Peak 3010'],
                'eloNet' => '+31 Net',
                'eloMatches' => '27 Matches',
            ],
            'magnus-carlsen' => [
                'slug' => 'magnus-carlsen',
                'firstName' => 'Magnus',
                'name' => 'Magnus Carlsen',
                'initials' => 'MC',
                'flag' => '🇳🇴',
                'title' => 'GM',
                'dossierId' => '#GM-0001',
                'fide' => 'FIDE 2882',
                'lastSeen' => '1d ago',
                'h2hWinrate' => '21.0% Win',
                'h2hRecord' => '(1W · 2D · 7L)',
                'totalWinrate' => '74.2%',
                'totalGames' => '5,890 Games',
                'winsDraws' => '4,370W · 910D',
                'losses' => '610 Losses',
                'avgAccuracy' => '96.4%',
                'accuracyTier' => 'World #1 Elite',
                'rapid' => ['value' => '2830', 'peak' => 'Peak 2882'],
                'blitz' => ['value' => '2895', 'peak' => 'Peak 2945'],
                'bullet' => ['value' => '2960', 'peak' => 'Peak 3005'],
                'eloNet' => '+16 Net',
                'eloMatches' => '14 Matches',
            ],
            'dommaraju-gukesh' => [
                'slug' => 'dommaraju-gukesh',
                'firstName' => 'Gukesh',
                'name' => 'Dommaraju Gukesh',
                'initials' => 'DG',
                'flag' => '🇮🇳',
                'title' => 'GM',
                'dossierId' => '#GM-1940',
                'fide' => 'FIDE 2794',
                'lastSeen' => '4h ago',
                'h2hWinrate' => '40.0% Win',
                'h2hRecord' => '(2W · 2D · 4L)',
                'totalWinrate' => '68.1%',
                'totalGames' => '2,180 Games',
                'winsDraws' => '1,484W · 420D',
                'losses' => '276 Losses',
                'avgAccuracy' => '94.6%',
                'accuracyTier' => 'Super-GM Level',
                'rapid' => ['value' => '2740', 'peak' => 'Peak 2760'],
                'blitz' => ['value' => '2765', 'peak' => 'Peak 2790'],
                'bullet' => ['value' => '2810', 'peak' => 'Peak 2835'],
                'eloNet' => '+28 Net',
                'eloMatches' => '16 Matches',
            ],
            'susanto-megaranto' => [
                'slug' => 'susanto-megaranto',
                'firstName' => 'Susanto',
                'name' => 'Susanto Megaranto',
                'initials' => 'SM',
                'flag' => '🇮🇩',
                'title' => 'GM',
                'dossierId' => '#GM-4402',
                'fide' => 'FIDE 2505',
                'lastSeen' => '3h ago',
                'h2hWinrate' => '50.0% Win',
                'h2hRecord' => '(4W · 2D · 4L)',
                'totalWinrate' => '61.5%',
                'totalGames' => '3,120 Games',
                'winsDraws' => '1,918W · 620D',
                'losses' => '582 Losses',
                'avgAccuracy' => '90.4%',
                'accuracyTier' => 'Grandmaster Tier',
                'rapid' => ['value' => '2530', 'peak' => 'Peak 2565'],
                'blitz' => ['value' => '2585', 'peak' => 'Peak 2610'],
                'bullet' => ['value' => '2615', 'peak' => 'Peak 2650'],
                'eloNet' => '+15 Net',
                'eloMatches' => '12 Matches',
            ],
        ];
    }
}
