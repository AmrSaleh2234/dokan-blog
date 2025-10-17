<?php

declare(strict_types=1);

namespace Modules\Post\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Post\Http\Requests\PostDeleteRequest;
use Modules\Post\Http\Requests\PostStoreRequest;
use Modules\Post\Http\Requests\PostUpdateRequest;
use Modules\Post\Services\PostService;
use Modules\Post\Transformers\PostResource;

class PostController extends Controller
{
    use AuthorizesRequests;

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

        $this->authorize('update', $post);
        
        $updated = $this->postService->updatePost($post, $request->validated());
        
        return response()->json([
            'data' => new PostResource($updated),
        ]);
    }

    public function destroy(PostDeleteRequest $request, int $id): JsonResponse
    {
        $post = $this->postService->findPost($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $this->authorize('delete', $post);
        
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

    public function byCategoryOnly(Request $request, int $categoryId): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $posts = $this->postService->getPostsByCategoryOnly($categoryId, (int) $perPage);
        
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

    public function trashed(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $userId = $request->user()->id;
        $posts = $this->postService->getTrashedPosts($userId, (int) $perPage);
        
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

    public function restore(Request $request, int $id): JsonResponse
    {
        $post = $this->postService->findTrashedPost($id);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found in trash'], 404);
        }

        $this->authorize('restore', $post);
        
        $this->postService->restorePost($post);
        
        return response()->json(['message' => 'Post restored successfully'], 200);
    }
}
