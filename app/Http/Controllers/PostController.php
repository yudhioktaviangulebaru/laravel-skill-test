<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * @var LengthAwarePaginator<Post>
         */
        $list = Post::with('author')->latest()
            ->active()
            ->paginate(20);

        return new PostCollection($list);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'posts.create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $post = Post::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        $post->load('author');

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if (! $post->isPublished() && auth()->id() !== $post->user_id) {
            abort(404);
        }

        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {

        return 'posts.edit';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $validated = $request->validated();

        $post->update($validated);
        $post->load('author');

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($post),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }
}
