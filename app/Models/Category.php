<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['body'];
    //protected $with = ['post'];

    /**
     * The posts that belong to the Category
     *
     * @return BelongsToMany
     */
    public function posts()
    {
        dd();
        return $this->belongsToMany(Post::class);
    }
}
