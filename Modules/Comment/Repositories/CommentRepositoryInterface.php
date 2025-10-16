<?php

declare(strict_types=1);

namespace Modules\Comment\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Comment\Entities\Comment;

interface CommentRepositoryInterface
{
    public function create(array $data): Comment;
    
    public function find(int $id): ?Comment;
    
    public function update(Comment $comment, array $data): Comment;
    
    public function delete(Comment $comment): bool;
    
    public function getByPostId(int $postId): Collection;
}
