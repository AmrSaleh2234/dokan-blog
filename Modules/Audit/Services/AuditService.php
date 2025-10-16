<?php

declare(strict_types=1);

namespace Modules\Audit\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Audit\Repositories\AuditRepository;
use OwenIt\Auditing\Models\Audit;

class AuditService
{
    public function __construct(
        private readonly AuditRepository $auditRepository
    ) {
    }

    public function getAllAudits(int $perPage = 15): LengthAwarePaginator
    {
        return $this->auditRepository->paginate($perPage);
    }

    public function findAudit(int $id): ?Audit
    {
        return $this->auditRepository->find($id);
    }

    public function getAuditsByModel(string $model, int $perPage = 15): LengthAwarePaginator
    {
        return $this->auditRepository->getByModel($model, $perPage);
    }

    public function getAuditsByModelAndId(string $model, int $modelId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->auditRepository->getByModelAndId($model, $modelId, $perPage);
    }

    public function getAuditsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->auditRepository->getByUser($userId, $perPage);
    }
}
