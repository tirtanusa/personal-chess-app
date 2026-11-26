<x-layouts.app
    title="Home - Gambit Arena"
    active-nav="home"
    :avatar-url="$profile['avatarUrl']"
    :profile-name="$profile['name']"
    :profile-handle="'@' . $profile['username']"
    :profile-rating="$profile['ratings'][0]['value']"
>
    <div class="w-full flex flex-col gap-5">
        @if (session('status'))
            <div class="rounded-xl border border-primary/30 bg-primary/10 px-3.5 py-2.5 text-xs font-body text-primary-100 flex items-center gap-2">
                <x-lucide-check-circle-2 class="w-4 h-4 text-primary shrink-0" />
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('sync_error'))
            <div class="rounded-xl border border-danger/35 bg-danger/10 px-3.5 py-2.5 text-xs font-body text-danger-200 flex items-center gap-2">
                <x-lucide-alert-circle class="w-4 h-4 text-danger-400 shrink-0" />
                <span>{{ session('sync_error') }}</span>
            </div>
        @endif

        <!-- Player Profile & Ratings -->
        <x-home.profile-card
            :name="$profile['name']"
            :avatar-url="$profile['avatarUrl']"
            :flag="$profile['flag']"
            :streak="$profile['streak']"
            :form="$profile['form']"
            :ratings="$profile['ratings']"
            :last-synced="$profile['lastSynced'] ?? null"
            :can-refresh="$player !== null"
        />

        <!-- Visual Separator between Account Summary & Mode-Filtered Analytics -->
        <div class="w-full border-t border-dashed border-border" role="separator" aria-hidden="true"></div>

        <!-- MODE Selector (Blitz, Rapid, Bullet, Classical) -->
        <x-home.fast-pair-carousel :active-mode="$activeMode ?? 'blitz'" />

        <!-- Game Insights (Engine metrics pending, Recent Results per mode) -->
        <x-home.game-insights
            :pending="$insightsPending"
            :active-mode="$activeMode ?? 'blitz'"
            :recent-matches="$recentMatches"
            :recent-matches-by-mode="$recentMatchesByMode ?? null"
        />

        <!-- Rating Progression Chart (follows selected MODE) -->
        <x-home.rating-progression
            :active-mode="$activeMode ?? 'blitz'"
            :by-mode="$ratingProgressionByMode ?? null"
        />

        <!-- Activity Streak & Heatmap (follows selected MODE) -->
        <x-home.activity-heatmap
            :active-mode="$activeMode ?? 'blitz'"
            :heatmap="$activityHeatmap ?? null"
            :heatmaps-by-mode="$activityHeatmapsByMode ?? null"
        />
    </div>
</x-layouts.app>
