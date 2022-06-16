<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Get all the comments of related post model
     *
     * @param  Post  $post
     *
     * @return JsonResponse
     */
    public function listComments(Post $post): JsonResponse
    {
        return response()->json(new CommentCollection($post->comments));
    }
    
    /**
     * comment a post
     *
     * @param  Request  $request
     * @param  Post     $post
     *
     * @return JsonResponse
     */
    public function postComment(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors;
            return response()->json(['errors' => $errors],);
        }
        $comment = $post->comments()->create($request->only([
            'content',
        ]));
        $comment->user()->associate(auth()->user());
        $comment->save();
        return response()->json(new CommentResource($comment), Response::HTTP_CREATED);
    }
    
    /**
     * Update an existing comment on a post
     *
     * @param  Request  $request
     * @param  Post     $post
     * @param  Comment  $comment
     *
     * @return JsonResponse
     */
    public function updateComment(Request $request, Post $post, Comment $comment): JsonResponse
    {
        $validator = Validator::make($request->only('content'), [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors;
            return response()->json(['errors' => $errors],);
        }
        $post->comments()->where('id', $comment->id)->first()->update($request->only([
            'content',
        ]));
        $comment->save();
        return response()->json(new CommentResource($comment), Response::HTTP_OK);
    }
    
    /**
     * Delete an existing comment on a post
     *
     * @param  Post     $post
     * @param  Comment  $comment
     *
     * @return JsonResponse
     */
    public function deleteComment(Post $post, Comment $comment): JsonResponse
    {
        $comment->user()->dissociate();
        $comment->save();
        $post->comments()->where('id', $comment->id)->first()->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
