<x-layouts.app
    title="Player Profile - Gambit Arena"
    active-nav="profile"
>
    <div class="w-full flex flex-col gap-5">
        <!-- Player Profile Summary, Performance Bar & Ratings -->
        <x-profile.summary-card />

        <!-- Last 7 Days Performance Chart -->
        <x-profile.weekly-performance />

        <!-- Favorite Openings (White / Black Interactive Switch) -->
        <x-profile.favorite-openings />

        <!-- Match History & Interactive Result/Format Filters -->
        <x-profile.match-history />

        <!-- Export PGN & Full Archive Footer -->
        <x-profile.export-actions />
    </div>
</x-layouts.app>
