<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static PostFactory factory()
 * @method static PostFactory|null newFactory()
 */
class Post extends Model
{
    /** @phpstan-ignore-next-line  */
    use HasFactory;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->attributes['published_at'] = now();

            return $post;
        });
    }

    protected $casts = [
        'published_at' => 'immutable_datetime',
        'last_replied_at' => 'immutable_datetime',
        'file_size' => 'int',
    ];

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

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeWhereIsThread(Builder $query): Builder
    {
        return $this->where('post_id', null);
    }

    public function isThread(): bool
    {
        return $this->attributes['post_id'] === null;
    }

    public function bumpLastRepliedAt(): void
    {
        $this->attributes['last_replied_at'] = now();
    }
}
