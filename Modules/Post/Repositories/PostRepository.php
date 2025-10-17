<?php

declare(strict_types=1);

namespace Modules\Post\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Post\Entities\Post;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private readonly Post $model
    ) {
    }

    public function all(): Collection
    {
        return $this->model->with(['user', 'category'])
            ->withCount('comments')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Post
    {
        return $this->model->find($id);
    }

    public function findWithRelations(int $id): ?Post
    {
        return $this->model->with(['user', 'category', 'comments.user'])
            ->withCount('comments')
            ->find($id);
    }

    public function create(array $data): Post
    {
        return $this->model->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post->fresh(['user', 'category']);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function getPostsByCategoryIds(array $categoryIds, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->whereIn('category_id', $categoryIds)
            ->with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->paginate($perPage);
    }

    public function getTrashed(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->onlyTrashed()
            ->where('user_id', $userId)
            ->with(['user', 'category'])
            ->withCount('comments')
            ->latest('deleted_at')
            ->paginate($perPage);
    }

    public function findTrashed(int $id): ?Post
    {
        return $this->model->onlyTrashed()->find($id);
    }

    public function restore(Post $post): bool
    {
        return $post->restore();
    }
}
