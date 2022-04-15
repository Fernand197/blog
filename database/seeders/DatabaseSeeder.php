<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create();
        $categories = Category::factory()->count(10)->create();
        $tags = Tag::factory()->count(10)->create();
        $posts = Post::factory()->count(50)->create();
        // $comments = Comment::factory()->count(150)->create();
        foreach ($posts as $post) {
            $comment_post = new Comment;
            $comment_post->body = Str::random(50);
            $post->categories()->attach(rand(1, 10));
            $post->tags()->attach(rand(1, 10));
            $post->comments()->save($comment_post);
        }

        for ($i = 0; $i < 50; $i++) {
            $comment_comment = new Comment;
            $comment_comment->body = Str::random(50);
            Comment::find(rand(1, 50))
                ->comments()
                ->save($comment_comment);
        }
    }
}
