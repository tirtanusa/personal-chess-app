<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'game_id',
    'ply',
    'move_san',
    'move_uci',
    'eval_cp',
    'mate_in',
    'cp_loss',
    'classification',
    'phase',
])]
class GameMove extends Model
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
            'ply' => 'integer',
            'eval_cp' => 'decimal:2',
            'mate_in' => 'integer',
            'cp_loss' => 'decimal:2',
            'classification' => 'integer',
        ];
    }

    /**
     * Game that this move belongs to.
     *
     * @return BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
