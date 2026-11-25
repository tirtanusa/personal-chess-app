<x-layouts.app
    title="Player Analysis - Gambit Arena"
    active-nav="analysis"
>
    <div class="w-full flex flex-col gap-6">
        <!-- Player Username Search & Server Filter -->
        <x-analysis.player-search />

        <!-- Saved Players Horizontal Carousel -->
        <x-analysis.saved-players />

        <!-- Alternative Analysis Options -->
        <x-analysis.alternative-options />

        <!-- Popular Players & Grandmasters -->
        <x-analysis.popular-grandmasters />
    </div>
</x-layouts.app>
