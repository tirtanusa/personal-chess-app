<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'game_id',
    'player_id',
    'color',
    'accuracy',
    'avg_cp_loss',
    'best_moves',
    'mistakes',
    'good_moves',
    'excellent_moves',
    'blunders',
    'inaccuracy',
    'engine_names',
    'engine_version',
    'depth',
])]
class GameSideStat extends Model
{
    use HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accuracy' => 'decimal:2',
            'avg_cp_loss' => 'decimal:2',
            'best_moves' => 'integer',
            'mistakes' => 'integer',
            'good_moves' => 'integer',
            'excellent_moves' => 'integer',
            'blunders' => 'integer',
            'inaccuracy' => 'integer',
        ];
    }

    /**
     * Game associated with these stats.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Player associated with these stats.
     *
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
