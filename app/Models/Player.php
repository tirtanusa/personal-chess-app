<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'platform',
    'chesscom_username',
    'display_name',
    'avatar_url',
    'country_code',
    'rapid_rating',
    'blitz_rating',
    'bullet_rating',
    'classic_rating',
    'last_archived_year',
    'last_archived_month',
    'last_synched_at',
])]
class Player extends Model
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
            'rapid_rating' => 'integer',
            'blitz_rating' => 'integer',
            'bullet_rating' => 'integer',
            'classic_rating' => 'integer',
            'last_archived_year' => 'integer',
            'last_archived_month' => 'integer',
            'last_synched_at' => 'date',
        ];
    }

    /**
     * Users who track this player.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_player')->withTimestamps();
    }

    /**
     * Games played as white.
     *
     * @return HasMany<Game, $this>
     */
    public function whiteGames(): HasMany
    {
        return $this->hasMany(Game::class, 'white_player_id');
    }

    /**
     * Games played as black.
     *
     * @return HasMany<Game, $this>
     */
    public function blackGames(): HasMany
    {
        return $this->hasMany(Game::class, 'black_player_id');
    }

    /**
     * Engine analysis statistics per side for this player.
     *
     * @return HasMany<GameSideStat, $this>
     */
    public function sideStats(): HasMany
    {
        return $this->hasMany(GameSideStat::class);
    }

    /**
     * Sync history logs for this player.
     *
     * @return HasMany<SyncLog, $this>
     */
    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class);
    }

    /**
     * Daily aggregated activity entries for the 1-year heatmap.
     *
     * @return HasMany<PlayerDailyActivity, $this>
     */
    public function dailyActivities(): HasMany
    {
        return $this->hasMany(PlayerDailyActivity::class);
    }
}
