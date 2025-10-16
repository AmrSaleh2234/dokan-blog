# Dokan Blog - Laravel 12 Modular Architecture

Production-ready Laravel 12 blog with modular architecture, hierarchical categories, and Sanctum authentication.

## Documentation

- [QUICK_START.md](./QUICK_START.md) - Get started in 5 minutes
- [DOCKER.md](./DOCKER.md) - Docker setup
- [SETUP.md](./SETUP.md) - Detailed installation
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Architecture details
- [Postman Collection](./postman/DokanBlog.postman_collection.json) - Complete API collection with examples

## Architecture

This project follows a **clean layered architecture** with dependency injection:

```
Controller → Service → Repository → Model
```

- **Controllers**: Handle HTTP requests, delegate to services (no business logic)
- **Services**: Contain business logic, orchestrate repositories
- **Repositories**: Data access layer, interact with models
- **Models**: Eloquent ORM entities
- **Requests**: Form validation (authorization + rules)
- **Resources**: API response transformation
- **Policies**: Authorization logic
- **Tests**: Feature tests for all endpoints

## Modules

- **Auth** - Sanctum authentication
- **Category** - Hierarchical categories with `laravel-tree`
- **Post** - Blog posts with soft deletes
- **Comment** - Post comments
- **Audit** - Laravel Auditing integration for tracking all model changes

## Installation

### Docker (Recommended)

Fastest setup with all dependencies included.

### Prerequisites

- PHP 8.2+
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & npm (for frontend assets)

### Local Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd dokan-blog
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database** (edit `.env`)
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=blog
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed database**
   ```bash
   php artisan db:seed
   ```

7. **Install Sanctum**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000/api`

## Postman Collection

A comprehensive Postman collection is available in `postman/DokanBlog.postman_collection.json` with:

- ✅ **All API endpoints** documented with examples
- ✅ **Sample requests and responses** for every endpoint
- ✅ **Automatic token handling** - Login/Register automatically saves auth token
- ✅ **Environment variables** - Import `postman/DokanBlog.local.postman_environment.json`
- ✅ **Nested comments** examples showing hierarchical structure
- ✅ **Pagination examples** for list endpoints

### Import to Postman

1. Open Postman
2. Click **Import** button
3. Import `postman/DokanBlog.postman_collection.json`
4. Import `postman/DokanBlog.local.postman_environment.json`
5. Select the "DokanBlog.local" environment
6. Start with **Auth → Register** or **Auth → Login**

The collection includes examples for:
- User registration and authentication
- Hierarchical category trees
- Post creation with relationships
- Nested comment threads
- Pagination and filtering

---

```bash
cd devops
docker-compose up -d
docker-compose run --rm composer install
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
```

Access at http://localhost:9000/api

See [DOCKER.md](./DOCKER.md) for details.

### Local Installation

See [SETUP.md](./SETUP.md) for instructions.

## Testing the API

### Using Postman (Recommended)

Import the complete Postman collection from `postman/DokanBlog.postman_collection.json` which includes:

- All endpoints with example requests/responses
- Automatic authentication token management
- Sample data for testing
- Environment configuration

See the [Postman Collection](#postman-collection) section below for import instructions.

### Using cURL

You can also test endpoints using cURL. See examples below.

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/register` | Register new user | No |
| POST | `/api/auth/login` | Login user | No |
| POST | `/api/auth/logout` | Logout user | Yes |
| GET | `/api/auth/me` | Get authenticated user | Yes |

### Categories

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/categories` | List all categories | No |
| GET | `/api/categories/tree` | Get hierarchical category tree | No |
| POST | `/api/categories` | Create category | No |
| GET | `/api/categories/{id}` | Show single category | No |
| PUT | `/api/categories/{id}` | Update category | No |
| DELETE | `/api/categories/{id}` | Delete category | No |
| GET | `/api/categories/{id}/posts` | Get posts by category (includes descendants) | No |

### Posts

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/posts` | List all posts (paginated) | No |
| POST | `/api/posts` | Create post | Yes |
| GET | `/api/posts/{id}` | Show single post with comments | No |
| PUT | `/api/posts/{id}` | Update post (owner only) | Yes |
| DELETE | `/api/posts/{id}` | Soft delete post (owner only) | Yes |

### Comments

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/posts/{postId}/comments` | Add comment to post | Yes |
| PUT | `/api/comments/{id}` | Update comment (owner only) | Yes |
| DELETE | `/api/comments/{id}` | Delete comment (owner only) | Yes |

### Audits

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/audits` | List all audit logs (paginated) | Yes |
| GET | `/api/audits/{id}` | Show single audit log | Yes |
| GET | `/api/audits/model/{model}` | Get audits by model type | Yes |
| GET | `/api/audits/model/{model}/{id}` | Get audits for specific model instance | Yes |
| GET | `/api/audits/user/{userId}` | Get audits by user | Yes |

**Note:** The Audit module automatically tracks all create, update, and delete operations on Post, Comment, and Category models. Each audit log includes the user who made the change, what changed (old/new values), timestamp, IP address, and user agent.

## API Examples

### Register User

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

Response:
```json
{
  "token": "1|abc123..."
}
```

### Get Hierarchical Categories

```bash
curl -X GET http://localhost:8000/api/categories/tree
```

Response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "parent_id": null,
      "children": [
        {
          "id": 2,
          "name": "Mobile",
          "parent_id": 1,
          "children": [
            {
              "id": 3,
              "name": "Android",
              "parent_id": 2,
              "children": []
            }
          ]
        }
      ]
    }
  ]
}
```

### Create Post (Authenticated)

```bash
curl -X POST http://localhost:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "This is the post content...",
    "category_id": 1
  }'
```

### Get Posts by Category (including descendants)

```bash
curl -X GET http://localhost:8000/api/categories/1/posts
```

### Add Comment to Post (Authenticated)

```bash
curl -X POST http://localhost:8000/api/posts/1/comments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "Great post!"
  }'
```

## Running Tests

Run all feature tests:

```bash
php artisan test
```

Run tests for specific module:

```bash
php artisan test --filter CategoryTest
php artisan test --filter PostTest
php artisan test --filter CommentTest
php artisan test --filter AuthTest
```

## Project Structure

```
dokan-blog/
├── Modules/
│   ├── Auth/
│   │   ├── Entities/User.php
│   │   ├── Http/
│   │   │   ├── Controllers/AuthController.php
│   │   │   └── Requests/
│   │   ├── Services/AuthService.php
│   │   ├── Repositories/
│   │   ├── Routes/api.php
│   │   ├── Database/
│   │   ├── Tests/Feature/
│   │   └── Providers/
│   ├── Category/
│   ├── Post/
│   └── Comment/
├── app/
├── config/
├── database/
├── routes/
└── tests/
```

## Authorization

- **Posts**: Only the post owner can update or delete their posts
- **Comments**: Only the comment owner can update or delete their comments
- Policies are registered in each module's `ServiceProvider`

## Features

- **Complete Postman collection** with request/response examples
- **Audit logging** - Track all model changes with Laravel Auditing
- Modular architecture with `nwidart/laravel-modules`
- Hierarchical categories with `xalaida/laravel-tree`
- Sanctum authentication
- Controller → Service → Repository → Model pattern
- Dependency injection
- Form Request validation
- API Resources
- Authorization policies
- Soft deletes
- Feature tests
- PSR-12, strict typing

## Technologies

- Laravel 12
- Laravel Sanctum
- Laravel Auditing (owen-it/laravel-auditing v14)
- nwidart/laravel-modules v11
- xalaida/laravel-tree v2
- Pest PHP

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
