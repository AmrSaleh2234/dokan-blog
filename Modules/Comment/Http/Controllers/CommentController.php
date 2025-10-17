<?php

declare(strict_types=1);

namespace Modules\Comment\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Comment\Http\Requests\CommentDeleteRequest;
use Modules\Comment\Http\Requests\CommentStoreRequest;
use Modules\Comment\Http\Requests\CommentUpdateRequest;
use Modules\Comment\Services\CommentService;
use Modules\Comment\Transformers\CommentResource;
use Modules\Post\Services\PostService;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CommentService $commentService,
        private readonly PostService $postService
    ) {
    }

    public function store(CommentStoreRequest $request, int $postId): JsonResponse
    {
        $post = $this->postService->findPost($postId);
        
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $data = $request->validated();
        $data['post_id'] = $postId;
        $data['user_id'] = $request->user()->id;
        
        $comment = $this->commentService->createComment($data);
        
        return response()->json([
            'data' => new CommentResource($comment->load('user')),
        ], 201);
    }

    public function update(CommentUpdateRequest $request, int $id): JsonResponse
    {
        $comment = $this->commentService->findComment($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $this->authorize('update', $comment);
        
        $updated = $this->commentService->updateComment($comment, $request->validated());
        
        return response()->json([
            'data' => new CommentResource($updated->load('user')),
        ]);
    }

    public function destroy(CommentDeleteRequest $request, int $id): JsonResponse
    {
        $comment = $this->commentService->findComment($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        $this->authorize('delete', $comment);
        
        $this->commentService->deleteComment($comment);
        
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function trashed(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $comments = $this->commentService->getTrashedComments($userId);
        
        return response()->json([
            'data' => CommentResource::collection($comments),
        ]);
    }

    public function restore(Request $request, int $id): JsonResponse
    {
        $comment = $this->commentService->findTrashedComment($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comment not found in trash'], 404);
        }

        $this->authorize('restore', $comment);
        
        $this->commentService->restoreComment($comment);
        
        return response()->json(['message' => 'Comment restored successfully'], 200);
    }
}
