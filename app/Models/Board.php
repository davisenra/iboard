<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    public $timestamps = false;

    /**
     * @return HasMany<Post>
     */
    public function posts(): HasMany
    {
        return $this->HasMany(Post::class);
    }
}
