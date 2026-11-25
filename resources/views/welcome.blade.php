<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-border">
            <div>
                <h1 class="text-3xl font-headline font-bold text-white tracking-tight flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary/10 text-primary text-xl">♟</span>
                    Personal Chess App
                </h1>
                <p class="text-neutral-400 font-body text-sm mt-1">Design System &amp; Theme Showcase with Blade Lucide Icons</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="badge-label">
                    <x-lucide-pencil class="w-3.5 h-3.5 text-neutral-950" />
                    Theme Active
                </span>
            </div>
        </div>

        <!-- Main Grid Layout mimicking Guideline -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: Palettes (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <!-- Primary Card -->
                <div class="card-surface space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-headline font-semibold text-white">Primary</span>
                        <span class="font-label text-xs text-neutral-400">#10B981</span>
                    </div>
                    <div class="h-10 rounded-xl overflow-hidden flex shadow-inner">
                        <div class="flex-1 bg-primary-950"></div>
                        <div class="flex-1 bg-primary-800"></div>
                        <div class="flex-1 bg-primary-600"></div>
                        <div class="flex-1 bg-primary-500"></div>
                        <div class="flex-1 bg-primary-400"></div>
                        <div class="flex-1 bg-primary-200"></div>
                        <div class="flex-1 bg-primary-50"></div>
                    </div>
                </div>

                <!-- Secondary Card -->
                <div class="card-surface space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-headline font-semibold text-white">Secondary</span>
                        <span class="font-label text-xs text-neutral-400">#F59E0B</span>
                    </div>
                    <div class="h-10 rounded-xl overflow-hidden flex shadow-inner">
                        <div class="flex-1 bg-secondary-950"></div>
                        <div class="flex-1 bg-secondary-800"></div>
                        <div class="flex-1 bg-secondary-600"></div>
                        <div class="flex-1 bg-secondary-500"></div>
                        <div class="flex-1 bg-secondary-400"></div>
                        <div class="flex-1 bg-secondary-200"></div>
                        <div class="flex-1 bg-secondary-50"></div>
                    </div>
                </div>

                <!-- Tertiary Card -->
                <div class="card-surface space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-headline font-semibold text-white">Tertiary</span>
                        <span class="font-label text-xs text-neutral-400">#6366F1</span>
                    </div>
                    <div class="h-10 rounded-xl overflow-hidden flex shadow-inner">
                        <div class="flex-1 bg-tertiary-950"></div>
                        <div class="flex-1 bg-tertiary-800"></div>
                        <div class="flex-1 bg-tertiary-600"></div>
                        <div class="flex-1 bg-tertiary-500"></div>
                        <div class="flex-1 bg-tertiary-400"></div>
                        <div class="flex-1 bg-tertiary-200"></div>
                        <div class="flex-1 bg-tertiary-50"></div>
                    </div>
                </div>

                <!-- Neutral Card -->
                <div class="card-surface space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-headline font-semibold text-white">Neutral</span>
                        <span class="font-label text-xs text-neutral-400">#0F1117</span>
                    </div>
                    <div class="h-10 rounded-xl overflow-hidden flex shadow-inner">
                        <div class="flex-1 bg-neutral-950"></div>
                        <div class="flex-1 bg-neutral-900"></div>
                        <div class="flex-1 bg-neutral-700"></div>
                        <div class="flex-1 bg-neutral-500"></div>
                        <div class="flex-1 bg-neutral-300"></div>
                        <div class="flex-1 bg-neutral-100"></div>
                        <div class="flex-1 bg-neutral-50"></div>
                    </div>
                </div>
            </div>

            <!-- Middle Column: Typography (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <!-- Headline Card -->
                <div class="card-surface flex flex-col justify-between h-[132px]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-neutral-400 font-medium">Headline</span>
                        <span class="font-label text-xs text-neutral-400">Space Grotesk</span>
                    </div>
                    <div class="font-headline text-5xl font-bold text-white tracking-tight">
                        Aa
                    </div>
                </div>

                <!-- Body Card -->
                <div class="card-surface flex flex-col justify-between h-[132px]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-neutral-400 font-medium">Body</span>
                        <span class="font-label text-xs text-neutral-400">Plus Jakarta Sans</span>
                    </div>
                    <div class="font-body text-5xl font-medium text-neutral-200">
                        Aa
                    </div>
                </div>

                <!-- Label Card -->
                <div class="card-surface flex flex-col justify-between h-[132px]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-neutral-400 font-medium">Label</span>
                        <span class="font-label text-xs text-neutral-400">JetBrains Mono</span>
                    </div>
                    <div class="font-label text-5xl font-normal text-neutral-200">
                        Aa
                    </div>
                </div>
            </div>

            <!-- Right Column: UI Components & Actions (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <!-- Buttons Card -->
                <div class="card-surface space-y-4">
                    <div class="text-xs text-neutral-400 font-medium">Buttons</div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <button class="btn btn-primary">Primary</button>
                        <button class="btn btn-secondary">Secondary</button>
                        <button class="btn btn-inverted">Inverted</button>
                        <button class="btn btn-outlined">Outlined</button>
                    </div>
                </div>

                <!-- Search Input & Progress Bars -->
                <div class="card-surface space-y-4">
                    <div class="relative">
                        <x-lucide-search class="w-4 h-4 text-neutral-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input type="text" class="input-search" placeholder="Search...">
                    </div>

                    <!-- Progress / Level Bars -->
                    <div class="space-y-2 pt-1">
                        <div class="w-full bg-neutral-850 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-primary h-full w-3/4 rounded-full"></div>
                        </div>
                        <div class="w-full bg-neutral-850 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-secondary h-full w-1/2 rounded-full"></div>
                        </div>
                        <div class="w-full bg-neutral-850 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-tertiary h-full w-2/5 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Circles & Navigation Pill (from Guideline) -->
                <div class="card-surface space-y-4">
                    <div class="flex items-center justify-between">
                        <!-- Square tertiary button with pencil -->
                        <div class="w-9 h-9 rounded-lg bg-tertiary/20 text-tertiary flex items-center justify-center">
                            <x-lucide-pencil class="w-4 h-4" />
                        </div>

                        <!-- Badge label -->
                        <span class="badge-label">
                            <x-lucide-pencil class="w-3.5 h-3.5 text-neutral-950" />
                            Label
                        </span>

                        <!-- Action icon circles matching guideline -->
                        <div class="flex items-center gap-1.5">
                            <button class="action-circle bg-primary text-neutral-950" title="Magic / Sparkles">
                                <x-lucide-wand-sparkles class="w-4 h-4" />
                            </button>
                            <button class="action-circle bg-secondary text-neutral-950" title="Shapes">
                                <x-lucide-shapes class="w-4 h-4" />
                            </button>
                            <button class="action-circle bg-tertiary text-white" title="Tag">
                                <x-lucide-tag class="w-4 h-4" />
                            </button>
                            <button class="action-circle bg-danger-400 text-neutral-950" title="Delete">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Floating Navigation Pill (from top-right of guideline) -->
                    <div class="bg-neutral-850/80 border border-neutral-700/50 rounded-2xl p-2 flex items-center justify-around">
                        <button class="w-8 h-8 rounded-lg bg-primary text-neutral-950 flex items-center justify-center">
                            <x-lucide-house class="w-4 h-4" />
                        </button>
                        <button class="text-neutral-400 hover:text-white transition-colors">
                            <x-lucide-search class="w-4 h-4" />
                        </button>
                        <button class="text-neutral-400 hover:text-white transition-colors">
                            <x-lucide-user class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
