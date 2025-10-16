<?php

declare(strict_types=1);

namespace Modules\Audit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Audit\Services\AuditService;
use Modules\Audit\Transformers\AuditResource;

class AuditController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $audits = $this->auditService->getAllAudits((int) $perPage);
        
        return response()->json([
            'data' => AuditResource::collection($audits),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $audit = $this->auditService->findAudit($id);
        
        if (!$audit) {
            return response()->json(['message' => 'Audit not found'], 404);
        }
        
        return response()->json([
            'data' => new AuditResource($audit),
        ]);
    }

    public function byModel(Request $request, string $model): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $audits = $this->auditService->getAuditsByModel($model, (int) $perPage);
        
        return response()->json([
            'data' => AuditResource::collection($audits),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }

    public function byModelAndId(Request $request, string $model, int $modelId): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $audits = $this->auditService->getAuditsByModelAndId($model, $modelId, (int) $perPage);
        
        return response()->json([
            'data' => AuditResource::collection($audits),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }

    public function byUser(Request $request, int $userId): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $audits = $this->auditService->getAuditsByUser($userId, (int) $perPage);
        
        return response()->json([
            'data' => AuditResource::collection($audits),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }
}
