<?php

declare(strict_types=1);

namespace Modules\Post\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Post\Http\Requests\PostStoreRequest;
use Modules\Post\Http\Requests\PostUpdateRequest;
use Modules\Post\Services\PostService;
use Modules\Post\Transformers\PostResource;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $posts = $this->postService->getPaginatedPosts((int) $perPage);
        
        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function store(PostStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        
        $post = $this->postService->createPost($data);
        
        return response()->json([
            'data' => new PostResource($post->load(['user', 'category'])),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postService->findPost($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        
        return response()->json([
            'data' => new PostResource($post),
        ]);
    }

    public function update(PostUpdateRequest $request, int $id): JsonResponse
    {
        $post = $this->postService->findPost($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if (!$request->user()->can('update', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $updated = $this->postService->updatePost($post, $request->validated());
        
        return response()->json([
            'data' => new PostResource($updated),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = $this->postService->findPost($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if (!$request->user()->can('delete', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $this->postService->deletePost($post);
        
        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function byCategory(Request $request, int $categoryId): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $posts = $this->postService->getPostsByCategory($categoryId, (int) $perPage);
        
        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}
