<x-layouts.app
    title="{{ $player['name'] }} Analysis - Gambit Arena"
    header-variant="page"
    header-title="Player Analysis"
    :back-url="route('analysis')"
    :show-bottom-nav="false"
>
    <div class="w-full flex flex-col gap-4">
        <!-- Player Identity, Overall Stats & Mode Ratings -->
        <x-player-analysis.profile-overview :player="$player" />

        <!-- 7-Day ELO Rating Trend -->
        <x-player-analysis.elo-trend
            :net="$player['eloNet']"
            :matches="$player['eloMatches']"
        />

        <!-- Opening Repertoire (White / Black Switch) -->
        <x-player-analysis.opening-repertoire />

        <!-- Player Game History (Clickable -> Opens Game Analysis Modal) -->
        <x-player-analysis.game-history :first-name="$player['firstName']" />
    </div>
</x-layouts.app>
