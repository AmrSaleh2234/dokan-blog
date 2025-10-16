<?php

declare(strict_types=1);

namespace Modules\Auth\Repositories;

use Modules\Auth\Entities\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function find(int $id): ?User
    {
        return $this->model->find($id);
    }
}
