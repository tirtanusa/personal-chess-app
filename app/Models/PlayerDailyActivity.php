<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'player_id',
    'activity_date',
    'games_count',
    'wins_count',
    'draws_count',
    'losses_count',
    'rapid_count',
    'blitz_count',
    'bullet_count',
    'classic_count',
    'rapid_rating',
    'blitz_rating',
    'bullet_rating',
    'classic_rating',
])]
class PlayerDailyActivity extends Model
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
            'activity_date' => 'date',
            'games_count' => 'integer',
            'wins_count' => 'integer',
            'draws_count' => 'integer',
            'losses_count' => 'integer',
            'rapid_count' => 'integer',
            'blitz_count' => 'integer',
            'bullet_count' => 'integer',
            'classic_count' => 'integer',
            'rapid_rating' => 'integer',
            'blitz_rating' => 'integer',
            'bullet_rating' => 'integer',
            'classic_rating' => 'integer',
        ];
    }

    /**
     * The player this daily activity summary belongs to.
     *
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
