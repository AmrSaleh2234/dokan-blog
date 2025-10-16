<?php

declare(strict_types=1);

namespace Modules\Auth\Repositories;

use Modules\Auth\Entities\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    
    public function findByEmail(string $email): ?User;
    
    public function find(int $id): ?User;
}
