<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(new TagCollection(Tag::all()), Response::HTTP_OK);
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
        $validator = Validator::make($request->only('title', 'description'), [
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $request->request->add(['slug' => Str::slug($request->title)]);
        $tag = Tag::create($request->only('title', 'description', 'slug'));
        return response()->json(new TagResource($tag), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Tag  $tag
     *
     * @return JsonResponse
     */
    public function show(Tag $tag): JsonResponse
    {
        return response()->json(new PostCollection($tag->posts), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Tag      $tag
     *
     * @return JsonResponse
     */
    public function update(Request $request, Tag $tag): JsonResponse
    {
        $validator = Validator::make($request->only('title', 'description'), [
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $request->request->add(['slug' => Str::slug($request->title)]);
        $tag->update($request->only('title', 'description', 'slug'));
        return response()->json(new TagResource($tag), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tag  $tag
     *
     * @return JsonResponse
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
