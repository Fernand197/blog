<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function listComments(Post $post)
    {
        return response()->json(new CommentCollection($post->comments));
    }

    public function postComment(Request $request, Post $post)
    {
        $comment = $post->comments()->create($request->only([
            'body'
        ]));
        return response()->json(new CommentResource($comment), Response::HTTP_CREATED);
    }

    public function updateComment(Request $request, Post $post, Comment $comment)
    {
        $post->comments()->where('id', $comment->id)->first()->update($request->only([
            'body'
        ]));
        $comment = $post->comments()->where('id', $comment->id)->first();
        return response()->json(new CommentResource($comment), Response::HTTP_OK);
    }

    public function deleteComment(Request $request, Post $post, Comment $comment)
    {
        $post->comments()->where('id', $comment->id)->first()->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
