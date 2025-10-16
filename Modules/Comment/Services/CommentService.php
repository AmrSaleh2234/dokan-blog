<?php

declare(strict_types=1);

namespace Modules\Comment\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Repositories\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        private readonly CommentRepositoryInterface $repository
    ) {
    }

    public function createComment(array $data): Comment
    {
        return $this->repository->create($data);
    }

    public function findComment(int $id): ?Comment
    {
        return $this->repository->find($id);
    }

    public function updateComment(Comment $comment, array $data): Comment
    {
        return $this->repository->update($comment, $data);
    }

    public function deleteComment(Comment $comment): bool
    {
        return $this->repository->delete($comment);
    }

    public function getCommentsByPost(int $postId): Collection
    {
        return $this->repository->getByPostId($postId);
    }
}
