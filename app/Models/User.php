<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * @var mixed
     */


    /**
     * Get all of the comments for the User
     *
     * @return HasManyThrough
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Post::class, 'user_id', 'commentable_id');
    }

    /**
     * Get all of the posts for the User
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }


    public function followers(): MorphToMany
    {
        return $this->morphToMany(__CLASS__, 'followable');
    }

    public function followings(): MorphToMany
    {
        return $this->morphedByMany(__CLASS__, 'followable');
    }

    public function followings_tags(): MorphToMany
    {
        return $this->morphedByMany(Tag::class, 'followable');
    }
}
