@php
    $navAvatar = $layout['avatarUrl'] ?? null;
    $navName = $layout['name'] ?? 'Player';
    $navHandle = $layout['handle'] ?? '@player';
    $navRating = $layout['rating'] ?? '—';
@endphp

<x-layouts.app
    title="Player Profile - Gambit Arena"
    active-nav="profile"
    :avatar-url="$navAvatar"
    :profile-name="$navName"
    :profile-handle="$navHandle"
    :profile-rating="$navRating"
>
    <div class="w-full flex flex-col gap-5">
        @if (session('profile_notice'))
            <div class="rounded-xl border border-secondary/35 bg-secondary/10 px-3.5 py-2.5 text-xs font-body text-secondary-200 flex items-center gap-2">
                <x-lucide-info class="w-4 h-4 text-secondary shrink-0" />
                <span>{{ session('profile_notice') }}</span>
            </div>
        @endif

        <!-- Player Profile Summary, Performance Bar & Ratings -->
        <x-profile.summary-card :summary="$summary ?? null" />

        <!-- Last 7 Days Performance Chart -->
        <x-profile.weekly-performance :weekly="$weeklyPerformance ?? null" />

        <!-- Favorite Openings (White / Black Interactive Switch) -->
        <x-profile.favorite-openings :openings="$favoriteOpenings ?? null" />

        <!-- Match History & Interactive Result/Format Filters -->
        <x-profile.match-history :history="$matchHistory ?? null" />

        <!-- Export PGN & Full Archive Footer -->
        <x-profile.export-actions :has-player="($player ?? null) !== null" />
    </div>
</x-layouts.app>
