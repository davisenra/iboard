<?php

namespace App\Models;

use Database\Factories\BoardFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static BoardFactory factory()
 * @method static BoardFactory|null newFactory()
 */
class Board extends Model
{
    /** @phpstan-ignore-next-line  */
    use HasFactory;

    public $timestamps = false;

    /**
     * @return HasMany<Post>
     */
    public function threads(): HasMany
    {
        return $this
            ->hasMany(Post::class, 'board_id', 'id')
            ->whereNull('post_id');
    }

    /**
     * @return Collection<int, Board>
     */
    public static function getBoardsRoutesList(): Collection
    {
        return Board::all('route');
    }
}
