<?php

declare(strict_types=1);

namespace Modules\Post\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Post\Entities\Post;

interface PostRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?Post;
    
    public function findWithRelations(int $id): ?Post;
    
    public function create(array $data): Post;
    
    public function update(Post $post, array $data): Post;
    
    public function delete(Post $post): bool;
    
    public function getPostsByCategoryIds(array $categoryIds, int $perPage = 15): LengthAwarePaginator;
}
