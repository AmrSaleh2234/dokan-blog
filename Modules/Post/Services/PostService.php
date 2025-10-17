<?php

declare(strict_types=1);

namespace Modules\Post\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Services\CategoryService;
use Modules\Post\Entities\Post;
use Modules\Post\Repositories\PostRepositoryInterface;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $repository,
        private readonly CategoryService $categoryService
    ) {
    }

    public function getAllPosts(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findPost(int $id): ?Post
    {
        return $this->repository->findWithRelations($id);
    }

    public function createPost(array $data): Post
    {
        return $this->repository->create($data);
    }

    public function updatePost(Post $post, array $data): Post
    {
        return $this->repository->update($post, $data);
    }

    public function deletePost(Post $post): bool
    {
        return $this->repository->delete($post);
    }

    public function getPostsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        $category = $this->categoryService->findCategory($categoryId);
        
        if (!$category) {
            return $this->repository->getPostsByCategoryIds([], $perPage);
        }

        return $category->postsWithDescendants()
            ->with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->paginate($perPage);
    }

    public function getPostsByCategoryOnly(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        $category = $this->categoryService->findCategory($categoryId);
        
        if (!$category) {
            return $this->repository->getPostsByCategoryIds([], $perPage);
        }

        return $category->posts()
            ->with(['user', 'category'])
            ->withCount('comments')
            ->latest()
            ->paginate($perPage);
    }

    public function getTrashedPosts(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getTrashed($userId, $perPage);
    }

    public function findTrashedPost(int $id): ?Post
    {
        return $this->repository->findTrashed($id);
    }

    public function restorePost(Post $post): bool
    {
        return $this->repository->restore($post);
    }
}
