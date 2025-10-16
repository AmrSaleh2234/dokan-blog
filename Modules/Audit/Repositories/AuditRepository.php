<?php

declare(strict_types=1);

namespace Modules\Audit\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use OwenIt\Auditing\Models\Audit;

class AuditRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Audit::with(['user'])
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function find(int $id): ?Audit
    {
        return Audit::with(['user'])->find($id);
    }

    public function getByModel(string $model, int $perPage = 15): LengthAwarePaginator
    {
        return Audit::with(['user'])
            ->where('auditable_type', $model)
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getByModelAndId(string $model, int $modelId, int $perPage = 15): LengthAwarePaginator
    {
        return Audit::with(['user'])
            ->where('auditable_type', $model)
            ->where('auditable_id', $modelId)
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Audit::with(['user'])
            ->where('user_id', $userId)
            ->latest('created_at')
            ->paginate($perPage);
    }
}
