<?php

declare(strict_types=1);

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Category\Http\Requests\CategoryStoreRequest;
use Modules\Category\Http\Requests\CategoryUpdateRequest;
use Modules\Category\Services\CategoryService;
use Modules\Category\Transformers\CategoryResource;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        
        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function tree(): JsonResponse
    {
        $tree = $this->categoryService->getCategoryTree();
        
        return response()->json([
            'data' => CategoryResource::collection($tree),
        ]);
    }

    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        
        return response()->json([
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->findCategory($id);
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        
        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    public function update(CategoryUpdateRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->findCategory($id);
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        
        $updated = $this->categoryService->updateCategory($category, $request->validated());
        
        return response()->json([
            'data' => new CategoryResource($updated),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->findCategory($id);
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        
        $this->categoryService->deleteCategory($category);
        
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
