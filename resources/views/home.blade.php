<x-layouts.app title="Home - Gambit Arena" active-nav="home">
    <div class="w-full flex flex-col gap-5">
        <!-- Player Profile & Ratings -->
        <x-home.profile-card />

        <!-- Fast Pair & Arena (Horizontally Scrollable within matching margins) -->
        <x-home.fast-pair-carousel />

        <!-- Game Insights & Recent Results -->
        <x-home.game-insights />

        <!-- Rating Progression Chart -->
        <x-home.rating-progression />

        <!-- Activity Streak & Heatmap -->
        <x-home.activity-heatmap />
    </div>
</x-layouts.app>
