# Docker Setup

## Prerequisites

- Docker Desktop ([Download](https://www.docker.com/products/docker-desktop))
- 4GB RAM minimum

## Quick Start

```bash
# 1. Clone repository
git clone <repository-url>
cd dokan-blog

# 2. Navigate to devops directory
cd devops

# 3. Start all containers
docker-compose up -d

# 4. Install PHP dependencies
docker-compose run --rm composer install

# 5. Setup environment
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# 6. Run migrations and seeders
docker-compose exec app php artisan migrate --seed
```

Access at http://localhost:9000/api

## Services

| Service | Port | Purpose |
|---------|------|---------|
| app | - | PHP 8.3 FPM |
| nginx | 9000 | Web server |
| postgres | 5433 | PostgreSQL 16 (laravel/secret) |
| redis | 6379 | Cache |
| mailhog | 8025 | Email testing |
| pgadmin | 8082 | Database UI (admin@admin.com/secret) |
| composer | - | Dependency manager |

## Environment

```env
APP_NAME="Dokan Blog"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:9000

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=redis
```

## Common Commands

```bash
# Start all containers
docker-compose up -d

# Start and view logs
docker-compose up

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down

# Stop and remove everything (including volumes)
docker-compose down -v

# Restart specific service
docker-compose restart app

# View running containers
docker-compose ps

# View logs (all services)
docker-compose logs -f

# View logs (specific service)
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f postgres
```

```bash
# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:list

# Run composer
docker-compose run --rm composer install
docker-compose run --rm composer update
docker-compose run --rm composer require package/name

# Access container shell
docker-compose exec app bash
docker-compose exec app sh

# Run tests
docker-compose exec app php artisan test
docker-compose exec app php artisan test --filter CategoryTest

# View database
docker-compose exec postgres psql -U laravel -d laravel
```

```bash
# Check container health
docker-compose ps

# View resource usage
docker stats

# Inspect container
docker inspect dokan_blog_app

# View container logs (last 100 lines)
docker-compose logs --tail=100 app

# Follow logs in real-time
docker-compose logs -f app

# Execute command in container
docker-compose exec app ls -la storage/
```

## Database Management

### pgAdmin

1. Open http://localhost:8082
2. Login with `admin@admin.com` / `secret`
3. Right-click "Servers" → "Register" → "Server"
4. **General Tab**: Name = "Dokan Blog"
5. **Connection Tab**:
   - Host: `postgres`
   - Port: `5432`
   - Database: `laravel`
   - Username: `laravel`
   - Password: `secret`

### PostgreSQL CLI

```bash
# Connect to database
docker-compose exec postgres psql -U laravel -d laravel

# Common SQL commands
\dt              # List tables
\d posts         # Describe posts table
SELECT * FROM users LIMIT 10;
\q               # Quit
```

### Backup & Restore

```bash
# Backup database
docker-compose exec postgres pg_dump -U laravel laravel > backup.sql

# Restore database
docker-compose exec -T postgres psql -U laravel laravel < backup.sql
```

## Email Testing

Open http://localhost:8025 to view all captured emails.

## Troubleshooting

**Port in use:**
```bash
netstat -ano | findstr :9000
taskkill /PID <process_id> /F
```

**Container won't start:**
```bash
docker-compose logs app
docker-compose down && docker-compose up -d --build
```

**Database connection:**
```bash
docker-compose ps postgres
docker-compose logs postgres
docker-compose restart postgres
```

**Permissions:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

**Clear cache:**
```bash
docker-compose exec app php artisan optimize:clear
docker-compose restart
```

## Using MySQL

Edit `docker-compose.yml` to add MySQL service and update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
```

Then restart:
```bash
docker-compose down -v
docker-compose up -d --build
```

## Production

Create `docker-compose.prod.yml` with production settings:

```bash
docker-compose -f docker-compose.prod.yml up -d --build
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
```
