<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->attributes['published_at'] = now();

            return $post;
        });
    }

    /**
     * @return BelongsTo<Board, Post>
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * @return BelongsTo<Post, Post>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * @return HasMany<Post>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Post::class, 'post_id');
    }

    public function isThread(): bool
    {
        return $this->attributes['post_id'] === null;
    }
}
