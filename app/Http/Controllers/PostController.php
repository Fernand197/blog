<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagCollection;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(new PostCollection(Post::all()), Response::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  Post  $post
     *
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json(new PostResource($post));
    }


    /**
     * Store the newly post created in the storage
     *
     * @param  Request  $request
     * @param  User     $user
     *
     * @return JsonResponse
     */
    public function postUsers(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->only(['title', 'description', 'content']), [
            'title'   => 'required|string',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $request->request->add(['slug' => Str::slug($request->title)]);
        $post = $user->posts()->create($request->all());
        $categories_id = [];
        $tags_id = [];

        if ($request->input('tags')) {
            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $tag = Str::slug($tag);
                $tag = Tag::firstOrCreate(['slug' => $tag]);
                $tags_id[] = $tag->id;
            }
            $post->tags()->syncWithoutDetaching($tags_id);
        }
        if ($request->input('categories')) {
            $categories = explode(',', $request->input('categories'));
            foreach ($categories as $category) {
                $category = Str::slug($category);
                $category = Category::updateOrCreate(['slug' => $category]);
                $categories_id[] = $category->id;
            }
            $post->categories()->syncWithoutDetaching($categories_id);
        }

        return response()->json(new PostResource($post), Response::HTTP_CREATED);
    }

    /**
     * Update the post resource in the storage
     *
     * @param  Request  $request
     * @param  User     $user
     * @param  Post     $post
     *
     * @return JsonResponse
     */
    public function updatePost(Request $request, User $user, Post $post): JsonResponse
    {
        $validator = Validator::make($request->only(['title', 'description', 'content']), [
            'title'   => 'required|string',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $request->request->add(['slug' => Str::slug($request->title)]);

        $user->posts()
            ->where('id', $post->id)
            ->first()
            ->update($request->only(['title', 'description', 'content', 'slug']));

        $categories_id = [];
        $tags_id = [];
        if ($request->input('tags')) {
            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $tag = Str::slug($tag);
                $tag = Tag::updateOrCreate(['body' => $tag]);
                $tags_id[] = $tag->id;
            }
            $post->tags()->sync($tags_id);
        }
        if ($request->input('categories')) {
            $categories = explode(',', $request->input('categories'));
            foreach ($categories as $category) {
                $category = Str::slug($category);
                $category = Category::updateOrCreate(['body' => $category]);
                $categories_id[] = $category->id;
            }
            $post->categories()->sync($categories_id);
        }

        return response()->json(new PostResource($post), Response::HTTP_OK);
    }

    /**
     * Remove the post resource from storage
     *
     * @param  User  $user
     * @param  Post  $post
     *
     * @return JsonResponse
     */
    public function deletePost(User $user, Post $post): JsonResponse
    {
        $post = $user->posts()->where('id', $post->id)->first();
        $post->user()->dissociate();
        $post->tags()->detach();
        $post->categories()->detach();
        $post->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Post  $post
     *
     * @return JsonResponse
     */
    public function tagsPost(Post $post): JsonResponse
    {
        $tags = $post->tags;
        return response()->json(new TagCollection($tags), Response::HTTP_OK);
    }
}
