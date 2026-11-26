<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'chesscom_uuid',
    'url',
    'time_class',
    'time_control',
    'rated',
    'end_time',
    'white_player_username',
    'black_player_username',
    'white_rating',
    'black_rating',
    'white_player_id',
    'black_player_id',
    'white_result',
    'black_result',
    'eco_code',
    'opening_name',
    'opening_url',
    'pgn',
    'analysis_status',
    'analysis_error',
])]
class Game extends Model
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
            'rated' => 'boolean',
            'end_time' => 'datetime',
            'white_rating' => 'integer',
            'black_rating' => 'integer',
        ];
    }

    /**
     * Player playing the white pieces.
     *
     * @return BelongsTo<Player, $this>
     */
    public function whitePlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'white_player_id');
    }

    /**
     * Player playing the black pieces.
     *
     * @return BelongsTo<Player, $this>
     */
    public function blackPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'black_player_id');
    }

    /**
     * Ordered move-by-move evaluations for this game.
     *
     * @return HasMany<GameMove, $this>
     */
    public function moves(): HasMany
    {
        return $this->hasMany(GameMove::class)->orderBy('ply');
    }

    /**
     * Side-level analysis summary stats (white and black).
     *
     * @return HasMany<GameSideStat, $this>
     */
    public function sideStats(): HasMany
    {
        return $this->hasMany(GameSideStat::class);
    }
}
