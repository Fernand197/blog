<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\PostCollection;
use App\Http\Resources\TagCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(new UserCollection(User::all()));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->only('username', 'email', 'password'), [
            'username' => 'unique:users|string|required|max:255',
            'email'    => 'email|unique:users|required|max:255',
            'password' => 'required|max:8|password:155|confirmed',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            response()->json([
                'errors' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        return response()->json(new UserResource($user), Response::HTTP_CREATED);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  User  $user
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user), Response::HTTP_OK);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  User     $user
     *
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $user->update([
            $request->only('username', 'email'),
        ]);
        $user->save();
        return response()->json(new UserResource($user), Response::HTTP_OK);
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(User $user): JsonResponse
    {
        $user->deleteOrFail();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * listPosts
     *
     * @param  mixed  $user
     *
     * @return JsonResponse
     */
    public function listPosts(User $user): JsonResponse
    {
        return response()->json(new PostCollection($user->posts()->where('published', true)->get()), Response::HTTP_OK);
    }
    
    public function listComments(User $user): JsonResponse
    {
        return response()->json(new CommentCollection($user->comments), Response::HTTP_OK);
    }
    
    public function follow_tag(Tag $tag): JsonResponse
    {
        auth()->user()->followings_tags()->toggle([$tag->id]);
        return view();
        return response()->json(['message' => "Successfully follow tag $tag->title"]);
    }
    
    public function unfollow_tag(Tag $tag): JsonResponse
    {
        auth()->user()->followings_tags()->detach([$tag->id]);
        return response()->json(['message' => "Successfully unfollow tag $tag->title"]);
    }
    
    
    public function follow(User $user): JsonResponse
    {
        auth()->user()->followings()->toggle([$user->id]);
        return response()->json(['message' => "Successfully follow user $user->username"]);
    }
    
    public function unfollow(User $user): JsonResponse
    {
        auth()->user()->followings()->detach([$user->id]);
        return response()->json(['message' => "Successfully unfollow user $user->username"]);
    }
    
    public function listFollowers(User $user): JsonResponse
    {
        $followers = $user->followers;
        $followings = $user->followings;
        $followings_tags = $user->followings_tags;
        return response()->json([
            'followers'   => new UserCollection($followers),
            'followings'  => new UserCollection($followings),
            'follow_tags' => new TagCollection($followings_tags),
        ]);
    }
    
    
}
