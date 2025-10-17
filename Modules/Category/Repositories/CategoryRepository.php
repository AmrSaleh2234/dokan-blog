<?php

declare(strict_types=1);

namespace Modules\Category\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Entities\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    use SoftDeletes;
    public function __construct(
        private readonly Category $model
    ) {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    public function getRootCategories(): Collection
    {
        return $this->model->query()->orderBy('name')->get()->tree();
    }

    public function getCategoryWithDescendants(int $id): ?Category
    {
        return $this->model->with('descendants')->find($id);
    }
}
