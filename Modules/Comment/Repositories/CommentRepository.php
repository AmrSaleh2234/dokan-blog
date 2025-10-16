<?php

declare(strict_types=1);

namespace Modules\Comment\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Comment\Entities\Comment;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private readonly Comment $model
    ) {
    }

    public function create(array $data): Comment
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?Comment
    {
        return $this->model->find($id);
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment->fresh();
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function getByPostId(int $postId): Collection
    {
        return $this->model->where('post_id', $postId)
            ->with('user')
            ->latest()
            ->get();
    }
}
