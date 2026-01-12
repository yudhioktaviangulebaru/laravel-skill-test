<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
        return 'post.create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $post = Post::create($validated);

        $post->load('author');

        if (! $post) {
            throw new BadRequestException('Failed to create post');
        }

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $post)
    {
        $post = Post::active()->findOrFail($post);
        $post->load('author');

        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update-post', $post);

        return 'post.edit';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update-post', $post);
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
    public function destroy(string $id)
    {
        //
    }
}
