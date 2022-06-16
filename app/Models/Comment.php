<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\hasRelations\morphMany;
use Illuminate\Database\Eloquent\HasRelationships\morphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['body'];
    protected $with = ['user', 'comments', 'post'];

    /**
     * Get the post that owns the Comment
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that owns the Comment
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent commentable model (Post or Comment)
     *
     * @return morphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Get all of the replies for the Comment
     *
     * @return morphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
