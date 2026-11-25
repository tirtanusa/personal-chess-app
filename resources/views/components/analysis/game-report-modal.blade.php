<div
    id="game-analysis-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="analysis-modal-title"
    class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4 bg-black/75 backdrop-blur-sm opacity-0 transition-opacity duration-200"
>
    <!-- Modal Panel (Sticky Header + Scrollable Body + Sticky Footer) -->
    <div
        data-modal-panel
        class="w-full max-w-[430px] max-h-[calc(100dvh-1.5rem)] sm:max-h-[90dvh] bg-surface border border-border-subtle rounded-3xl shadow-2xl flex flex-col overflow-hidden scale-95 transition-all duration-200"
    >
        <!-- Sticky Modal Header -->
        <div class="shrink-0 px-4 pt-3 pb-3 border-b border-border-subtle/70 flex flex-col gap-2.5">
            <!-- Drag Handle Indicator -->
            <div class="w-10 h-1 rounded-full bg-border-strong mx-auto"></div>

            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-9 h-9 rounded-xl bg-primary/15 border border-primary/25 text-primary flex items-center justify-center shrink-0">
                        <x-lucide-chart-no-axes-combined class="w-4 h-4" />
                    </span>

                    <div class="flex flex-col gap-0.5 min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 id="analysis-modal-title" class="font-headline text-sm sm:text-base font-bold text-white truncate">
                                Game Analysis Report
                            </h2>
                            <span class="px-1.5 py-0.5 rounded bg-surface-elevated border border-border font-label text-[9px] font-semibold text-neutral-300 shrink-0">
                                SF 16.1
                            </span>
                        </div>
                        <p class="font-body text-[11px] text-neutral-400 truncate">
                            Match vs <strong class="text-neutral-200 font-semibold" data-modal-opponent>Viktor_K (1890)</strong>
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    data-modal-close
                    class="w-8 h-8 rounded-full bg-canvas/80 border border-border-subtle hover:border-border text-neutral-400 hover:text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                    aria-label="Close modal"
                >
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Scrollable Body -->
        <div class="flex-1 overflow-y-auto no-scrollbar px-4 py-3 flex flex-col gap-3">
            <!-- Accuracy Comparison Summary -->
            <div class="bg-canvas/75 border border-border-subtle rounded-2xl p-2.5 grid grid-cols-[1fr_auto_1fr] items-center gap-2">
                <!-- You -->
                <div class="bg-surface/60 border border-border-subtle rounded-xl py-2 px-2.5 flex flex-col items-center gap-0.5 text-center">
                    <span class="inline-flex items-center gap-1.5 font-label text-[9px] font-semibold tracking-wider uppercase text-neutral-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        <span>You</span>
                    </span>
                    <span class="font-headline text-xl font-bold text-primary leading-tight" data-modal-you-acc>91.4%</span>
                    <span class="font-body text-[10px] text-neutral-400">Move Accuracy</span>
                </div>

                <!-- Result & Total Moves -->
                <div class="flex flex-col items-center justify-center gap-1 px-1">
                    <span
                        data-modal-result-badge
                        class="px-2.5 py-0.5 rounded-full bg-primary/15 border border-primary/30 font-label text-[10px] font-bold tracking-wider uppercase text-primary"
                    >
                        WIN
                    </span>
                    <span class="font-label text-[10px] text-neutral-400" data-modal-moves>38 Mvs</span>
                </div>

                <!-- Opponent -->
                <div class="bg-surface/60 border border-border-subtle rounded-xl py-2 px-2.5 flex flex-col items-center gap-0.5 text-center">
                    <span class="inline-flex items-center gap-1.5 font-label text-[9px] font-semibold tracking-wider uppercase text-neutral-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-600 border border-neutral-400"></span>
                        <span>Opponent</span>
                    </span>
                    <span class="font-headline text-xl font-bold text-white leading-tight" data-modal-opp-acc>82.6%</span>
                    <span class="font-body text-[10px] text-neutral-400">Move Accuracy</span>
                </div>
            </div>

            <!-- Move Classification Breakdown -->
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <x-lucide-pie-chart class="w-3.5 h-3.5 text-primary" />
                        <span class="font-label text-[10px] font-semibold tracking-wider uppercase text-neutral-300">
                            Game Move Classification
                        </span>
                    </div>
                    <span class="font-body text-[10px] text-neutral-400">You vs Opponent</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <x-analysis.move-classification-row
                        key="best"
                        title="Best"
                        badge="Top Pick"
                        description="Engine's top choice"
                        icon="check"
                        icon-box-class="bg-primary/15 text-primary border-primary/25"
                        badge-class="bg-primary/15 text-primary"
                        you-pill-class="bg-primary/20 text-primary"
                        :default-you="18"
                        :default-opp="13"
                    />

                    <x-analysis.move-classification-row
                        key="excellent"
                        title="Excellent"
                        badge="Great"
                        description="Nearly equal to best move"
                        icon="thumbs-up"
                        icon-box-class="bg-lime-500/15 text-lime-400 border-lime-500/25"
                        badge-class="bg-lime-500/15 text-lime-400"
                        you-pill-class="bg-lime-500/20 text-lime-400"
                        :default-you="12"
                        :default-opp="10"
                    />

                    <x-analysis.move-classification-row
                        key="good"
                        title="Good"
                        badge="Solid"
                        description="Maintains a solid position"
                        icon="check"
                        icon-box-class="bg-surface-elevated text-neutral-300 border-border"
                        badge-class="bg-surface-elevated text-neutral-300"
                        you-pill-class="bg-tertiary/20 text-tertiary-200"
                        :default-you="7"
                        :default-opp="9"
                    />

                    <x-analysis.move-classification-row
                        key="inaccuracy"
                        title="Inaccuracy"
                        badge="Inaccurate"
                        description="Slightly reduces advantage"
                        symbol="?!"
                        icon-box-class="bg-secondary/15 text-secondary border-secondary/25"
                        badge-class="bg-secondary/15 text-secondary"
                        you-pill-class="bg-secondary/20 text-secondary"
                        :default-you="3"
                        :default-opp="5"
                    />

                    <x-analysis.move-classification-row
                        key="mistake"
                        title="Mistake"
                        badge="Error"
                        description="Loses initiative or advantage"
                        symbol="?"
                        icon-box-class="bg-orange-500/15 text-orange-400 border-orange-500/25"
                        badge-class="bg-orange-500/15 text-orange-400"
                        you-pill-class="bg-orange-500/20 text-orange-400"
                        :default-you="1"
                        :default-opp="3"
                    />

                    <x-analysis.move-classification-row
                        key="blunder"
                        title="Blunder"
                        badge="Fatal"
                        description="Critical error changing the outcome"
                        symbol="??"
                        icon-box-class="bg-danger/15 text-danger-400 border-danger/25"
                        badge-class="bg-danger/15 text-danger-400"
                        you-pill-class="bg-primary/20 text-primary"
                        :default-you="0"
                        :default-opp="2"
                    />
                </div>
            </div>

            <!-- Key Insight Box -->
            <div class="bg-canvas/75 border border-border-subtle rounded-xl p-3 flex items-start gap-2.5">
                <x-lucide-lightbulb class="w-4 h-4 text-secondary shrink-0 mt-0.5" />
                <p class="font-body text-[11px] leading-relaxed text-neutral-300" data-modal-insight>
                    <strong class="text-primary font-semibold">Key Insight:</strong>
                    <span data-modal-insight-body>You made zero blunders across all 38 moves. The decisive advantage occurred on move 28 when your opponent played a </span>
                    <span class="text-danger-400 font-semibold" data-modal-insight-highlight>Blunder (28... Bxe5??).</span>
                </p>
            </div>
        </div>

        <!-- Sticky Footer Action Buttons -->
        <div class="shrink-0 px-4 pt-2.5 pb-3.5 border-t border-border-subtle/70 bg-surface grid grid-cols-5 gap-2.5">
            <button
                type="button"
                class="col-span-2 py-2.5 px-3 rounded-xl bg-surface-elevated hover:bg-neutral-800 border border-border text-neutral-200 font-headline font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
            >
                <x-lucide-share class="w-3.5 h-3.5 shrink-0" />
                <span>Share PGN</span>
            </button>

            <button
                type="button"
                class="col-span-3 py-2.5 px-4 rounded-xl bg-primary-200 hover:bg-primary-100 active:scale-[0.99] text-neutral-950 font-headline font-bold text-xs shadow-[0_0_20px_-4px_rgba(76,213,157,0.45)] flex items-center justify-center gap-1.5 transition-all cursor-pointer"
            >
                <x-lucide-circle-play class="w-4 h-4 shrink-0" />
                <span>Review Move by Move</span>
            </button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('game-analysis-modal');
                if (!modal) return;

                const panel = modal.querySelector('[data-modal-panel]');
                const closeButtons = modal.querySelectorAll('[data-modal-close]');
                const opponentEl = modal.querySelector('[data-modal-opponent]');
                const youAccEl = modal.querySelector('[data-modal-you-acc]');
                const oppAccEl = modal.querySelector('[data-modal-opp-acc]');
                const resultBadgeEl = modal.querySelector('[data-modal-result-badge]');
                const movesEl = modal.querySelector('[data-modal-moves]');
                const insightBodyEl = modal.querySelector('[data-modal-insight-body]');
                const insightHighlightEl = modal.querySelector('[data-modal-insight-highlight]');

                const badgeVariants = {
                    win: 'px-2.5 py-0.5 rounded-full bg-primary/15 border border-primary/30 font-label text-[10px] font-bold tracking-wider uppercase text-primary',
                    draw: 'px-2.5 py-0.5 rounded-full bg-secondary/15 border border-secondary/30 font-label text-[10px] font-bold tracking-wider uppercase text-secondary',
                    loss: 'px-2.5 py-0.5 rounded-full bg-danger/15 border border-danger/30 font-label text-[10px] font-bold tracking-wider uppercase text-danger-400',
                };

                const openModal = (data) => {
                    if (data) {
                        opponentEl.textContent = data.opponent || '';
                        youAccEl.textContent = data.youAccuracy || '0%';
                        oppAccEl.textContent = data.oppAccuracy || '0%';
                        movesEl.textContent = data.moves || '';
                        resultBadgeEl.textContent = data.resultLabel || 'WIN';
                        resultBadgeEl.className = badgeVariants[data.resultVariant] || badgeVariants.win;

                        if (data.stats) {
                            Object.entries(data.stats).forEach(([key, pair]) => {
                                const youNode = modal.querySelector(`[data-stat-you="${key}"]`);
                                const oppNode = modal.querySelector(`[data-stat-opp="${key}"]`);
                                if (youNode) youNode.textContent = pair[0];
                                if (oppNode) oppNode.textContent = pair[1];
                            });
                        }

                        if (insightBodyEl) insightBodyEl.textContent = data.insightBody || '';
                        if (insightHighlightEl) insightHighlightEl.textContent = data.insightHighlight || '';
                    }

                    const scrollArea = panel.querySelector('.overflow-y-auto');
                    if (scrollArea) scrollArea.scrollTop = 0;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');

                    requestAnimationFrame(() => {
                        modal.classList.remove('opacity-0');
                        panel.classList.remove('scale-95');
                    });
                };

                const closeModal = () => {
                    modal.classList.add('opacity-0');
                    panel.classList.add('scale-95');
                    document.body.classList.remove('overflow-hidden');

                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 200);
                };

                closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });

                // Delegated trigger for any clickable game history card
                document.addEventListener('click', (e) => {
                    const trigger = e.target.closest('[data-analysis-payload]');
                    if (!trigger) return;

                    try {
                        const payload = JSON.parse(trigger.getAttribute('data-analysis-payload'));
                        openModal(payload);
                    } catch (err) {
                        openModal(null);
                    }
                });
            });
        </script>
    @endpush
@endonce
