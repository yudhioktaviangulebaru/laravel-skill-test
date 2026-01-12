<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $user_id
 */
class Post extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];

    protected function casts()
    {
        return [
            'is_draft' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Author of the post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (Post $post) {
            $post->setAttribute('user_id', Auth::id());
        });
    }

    /**
     * Scope to show post that is not draft and scheduled
     *
     * @param  mixed  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_draft', false)
            ->where('published_at', '<=', now());
    }
}
