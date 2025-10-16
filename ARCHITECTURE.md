# Architecture Documentation

## Overview

Dokan Blog follows a **modular monolith** architecture pattern, where each feature domain is encapsulated in a self-contained module with clear boundaries and dependencies.

## Layered Architecture

Each module follows a strict layered architecture with dependency injection:

```
┌─────────────────────────────────────────────┐
│           HTTP Layer                         │
│  - Controllers (handle requests)             │
│  - Requests (validation)                     │
│  - Resources (response transformation)       │
└──────────────┬──────────────────────────────┘
               │ depends on
┌──────────────▼──────────────────────────────┐
│           Service Layer                      │
│  - Business logic                            │
│  - Orchestration                             │
│  - Use cases                                 │
└──────────────┬──────────────────────────────┘
               │ depends on
┌──────────────▼──────────────────────────────┐
│         Repository Layer                     │
│  - Data access                               │
│  - Query logic                               │
│  - Interface contracts                       │
└──────────────┬──────────────────────────────┘
               │ depends on
┌──────────────▼──────────────────────────────┐
│           Model Layer                        │
│  - Eloquent models                           │
│  - Relationships                             │
│  - Accessors/Mutators                        │
└─────────────────────────────────────────────┘
```

## Module Structure

Each module has a consistent structure:

```
Modules/
└── ModuleName/
    ├── module.json                 # Module configuration
    ├── Entities/                   # Models
    │   └── Entity.php
    ├── Repositories/               # Data access layer
    │   ├── EntityRepositoryInterface.php
    │   └── EntityRepository.php
    ├── Services/                   # Business logic
    │   └── EntityService.php
    ├── Http/
    │   ├── Controllers/           # Request handlers
    │   │   └── EntityController.php
    │   └── Requests/              # Validation
    │       ├── EntityStoreRequest.php
    │       └── EntityUpdateRequest.php
    ├── Transformers/              # API Resources
    │   └── EntityResource.php
    ├── Policies/                  # Authorization
    │   └── EntityPolicy.php
    ├── Routes/                    # Route definitions
    │   └── api.php
    ├── Database/
    │   ├── Migrations/           # Database schema
    │   ├── Factories/            # Test factories
    │   └── Seeders/              # Data seeders
    ├── Tests/
    │   └── Feature/              # Feature tests
    │       └── EntityTest.php
    └── Providers/                # Service provider
        └── EntityServiceProvider.php
```

## Dependency Rules

### 1. Controller Layer

**Responsibilities:**
- Receive HTTP requests
- Validate input (via Form Requests)
- Delegate to Services
- Return formatted responses (via Resources)

**Rules:**
- ❌ MUST NOT contain business logic
- ❌ MUST NOT directly access repositories
- ❌ MUST NOT directly access models
- ✅ MUST inject services via constructor
- ✅ MUST use Form Requests for validation
- ✅ MUST use Resources for responses

**Example:**
```php
public function __construct(
    private readonly PostService $postService
) {}

public function store(PostStoreRequest $request): JsonResponse
{
    $post = $this->postService->createPost($request->validated());
    return response()->json(['data' => new PostResource($post)], 201);
}
```

### 2. Service Layer

**Responsibilities:**
- Implement business logic
- Orchestrate multiple repositories
- Handle complex operations
- Enforce business rules

**Rules:**
- ❌ MUST NOT handle HTTP concerns
- ❌ MUST NOT directly access models
- ✅ MUST inject repositories via constructor
- ✅ MUST implement all business logic
- ✅ CAN call multiple repositories
- ✅ CAN interact with other services

**Example:**
```php
public function __construct(
    private readonly PostRepositoryInterface $repository,
    private readonly CategoryService $categoryService
) {}

public function getPostsByCategory(int $categoryId): Collection
{
    $category = $this->categoryService->findCategory($categoryId);
    $categoryIds = $this->categoryService->getDescendantIds($category);
    return $this->repository->getPostsByCategoryIds($categoryIds);
}
```

### 3. Repository Layer

**Responsibilities:**
- Abstract data access
- Execute database queries
- Return models or collections

**Rules:**
- ❌ MUST NOT contain business logic
- ❌ MUST NOT handle HTTP concerns
- ✅ MUST implement an interface
- ✅ MUST inject models via constructor
- ✅ ONLY repositories can call model methods

**Example:**
```php
public function __construct(
    private readonly Post $model
) {}

public function findWithRelations(int $id): ?Post
{
    return $this->model->with(['user', 'category', 'comments'])
        ->withCount('comments')
        ->find($id);
}
```

### 4. Model Layer

**Responsibilities:**
- Define database structure
- Define relationships
- Provide accessors/mutators

**Rules:**
- ❌ MUST NOT contain business logic
- ✅ MUST define fillable/guarded
- ✅ MUST define relationships
- ✅ MUST define casts

## Dependency Injection

All dependencies are resolved through Laravel's service container.

### Binding Repositories

In each module's `ServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(
        PostRepositoryInterface::class,
        PostRepository::class
    );
}
```

### Constructor Injection

All classes use constructor injection:

```php
// ✅ Correct
public function __construct(
    private readonly PostService $postService
) {}

// ❌ Incorrect - using new keyword
public function __construct()
{
    $this->postService = new PostService();
}
```

## Validation Strategy

Validation is handled in **Form Request** classes, not in controllers.

### Request Classes

```php
class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null; // Check authentication
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}
```

## Authorization Strategy

Authorization is handled in **Policy** classes.

### Policy Example

```php
class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
```

### Using Policies in Controllers

```php
if (!$request->user()->can('update', $post)) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

## Response Strategy

All API responses use **Resource** classes for consistent formatting.

### Resource Example

```php
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_name' => $this->user?->name,
            'category_name' => $this->category?->name,
            'comments_count' => $this->comments_count ?? 0,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
```

## Testing Strategy

### Feature Tests

Each module has feature tests covering:
- CRUD operations
- Authentication requirements
- Authorization rules
- Validation rules
- Edge cases

### Test Example

```php
public function test_post_owner_can_update_post(): void
{
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->putJson("/api/posts/{$post->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Updated Title']);
}
```

## Coding Standards

### PSR-12 Compliance

All code follows PSR-12 standards:
- 4 spaces for indentation
- No trailing whitespace
- Single blank line at end of file
- Proper spacing around operators

### Strict Typing

All files use strict typing:

```php
<?php

declare(strict_types=1);

namespace Modules\Post\Services;
```

### Type Hints

All parameters and return types are explicitly typed:

```php
public function createPost(array $data): Post
{
    return $this->repository->create($data);
}
```

## Module Independence

Modules should be as independent as possible:

### ✅ Allowed Dependencies

- Module can depend on shared contracts/interfaces
- Module can depend on base Laravel classes
- Module can call other module services

### ❌ Avoid Dependencies

- Direct model access across modules
- Tight coupling between modules
- Circular dependencies

## Database Conventions

### Migration Naming

```
YYYY_MM_DD_HHMMSS_create_table_name_table.php
```

### Table Naming

- Plural snake_case: `posts`, `categories`, `user_profiles`
- Foreign keys: `{table_singular}_id` (e.g., `user_id`, `category_id`)
- Pivot tables: alphabetical order (e.g., `post_tag`)

### Indexes

- Add indexes on foreign keys
- Add indexes on frequently queried columns
- Add composite indexes for multi-column queries

## API Conventions

### Endpoints

- Use plural nouns: `/api/posts`, `/api/categories`
- Use RESTful verbs: GET, POST, PUT, DELETE
- Use nested routes for relationships: `/api/posts/{id}/comments`

### HTTP Status Codes

- `200 OK` - Successful GET, PUT, DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Validation error
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Not authorized
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server error

### Response Format

```json
{
  "data": { ... },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

## Performance Considerations

### Eager Loading

Always eager load relationships to avoid N+1 queries:

```php
Post::with(['user', 'category'])->get();
```

### Pagination

Use pagination for large datasets:

```php
Post::paginate(15);
```

### Caching

Cache frequently accessed data:

```php
Cache::remember('categories.tree', 3600, function () {
    return Category::whereNull('parent_id')->with('children')->get();
});
```

## Security Best Practices

1. **Always validate input** using Form Requests
2. **Use policies** for authorization
3. **Hash passwords** using `bcrypt` or `Hash::make()`
4. **Sanitize output** in API Resources
5. **Use CSRF protection** for web routes
6. **Rate limit API endpoints**
7. **Never expose sensitive data** in responses

## Conclusion

This architecture ensures:
- ✅ Separation of concerns
- ✅ Testability
- ✅ Maintainability
- ✅ Scalability
- ✅ Code reusability
- ✅ Clear dependencies

By following these patterns and conventions, the codebase remains clean, organized, and easy to extend.
