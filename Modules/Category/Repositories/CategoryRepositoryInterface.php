<?php

declare(strict_types=1);

namespace Modules\Category\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Category\Entities\Category;

interface CategoryRepositoryInterface
{
    public function all(): Collection;
    
    public function find(int $id): ?Category;
    
    public function create(array $data): Category;
    
    public function update(Category $category, array $data): Category;
    
    public function delete(Category $category): bool;
    
    public function getRootCategories(): Collection;
    
    public function getCategoryWithDescendants(int $id): ?Category;
}
