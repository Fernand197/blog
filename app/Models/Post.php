<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HasRelationships\morphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id', 'image', 'slug', 'published', 'published_at', 'body'];
    // protected $with = ['user', 'comments', 'tags', 'categories'];

    /**
     * Get all of the comments for the Post
     *
     * @return morphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * The categories that belong to the Post
     *
     * @return BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * The tags that belong to the Post
     *
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the user that owns the Post
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
