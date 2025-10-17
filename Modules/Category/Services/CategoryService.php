<?php

declare(strict_types=1);

namespace Modules\Category\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Entities\Category;
use Modules\Category\Repositories\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $repository
    ) {
    }

    public function getAllCategories(): Collection
    {
        return $this->repository->all();
    }

    public function getCategoryTree(): Collection
    {
        return $this->repository->getRootCategories();
    }

    public function findCategory(int $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function createCategory(array $data): Category
    {
        return $this->repository->create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        return $this->repository->update($category, $data);
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->repository->delete($category);
    }

    public function getCategoryWithDescendants(int $id): ?Category
    {
        return $this->repository->getCategoryWithDescendants($id);
    }
}
