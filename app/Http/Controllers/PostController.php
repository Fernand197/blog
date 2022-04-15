<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagCollection;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(new PostCollection(Post::all()), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = Post::create($request->only([
            'title', 'image', 'slug', 'body'
        ]));

        return response()->json(new PostResource($post), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $post->update($request->only([
            'title', 'category', 'body'
        ]));
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }



    public function postUsers(Request $request, User $user)
    {
        $post = $user->posts()->create($request->only(['title', 'image', 'body', 'slug']));
        if ($request->input('tags')) {
            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $tag = Str::slug($tag);
                $tag = Tag::firstOrCreate(['body' => $tag]);
                $tags_id[] = $tag->id;
            }
            $post->tags()->syncWithoutDetaching($tags_id);
        }
        return response()->json(new PostResource($post), Response::HTTP_CREATED);
    }

    public function updatePost(Request $request, User $user, Post $post)
    {
        $user->posts()
            ->where('id', $post->id)
            ->first()
            ->update($request->only(['title', 'image', 'body', 'slug']));
        if ($request->input('tags')) {
            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $tag = Str::slug($tag);
                $tag = Tag::createOrUpdate(['body' => $tag]);
                $tags_id[] = $tag->id;
            }
            $post->tags()->sync($tags_id);
        }
        $post = $user->posts()->where('id', $post->id)->first();
        return response()->json(new PostResource($post), Response::HTTP_OK);
    }

    public function deletePost(User $user, Post $post)
    {
        $post = $user->posts()->where('id', $post->id)->first();
        $post->tags()->detach();
        $post->categories()->detach();
        $post->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function tagsPost(Post $post)
    {
        $tags = Post::find($post->id)->tags;
        return response()->json(new TagCollection($tags), Response::HTTP_OK);
    }
}
