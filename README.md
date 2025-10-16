# Dokan Blog - Laravel 12 Modular Architecture

Production-ready Laravel 12 blog with modular architecture, hierarchical categories, and Sanctum authentication.

## Documentation

- [QUICK_START.md](./QUICK_START.md) - Get started in 5 minutes
- [DOCKER.md](./DOCKER.md) - Docker setup
- [SETUP.md](./SETUP.md) - Detailed installation
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Architecture details

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
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dokan_blog
   DB_USERNAME=root
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
- nwidart/laravel-modules v11
- xalaida/laravel-tree v2
- Pest PHP

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
