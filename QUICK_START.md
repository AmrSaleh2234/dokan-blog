# Quick Start

## Local Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Configure your database in .env (MySQL example)
# DB_CONNECTION=mysql
# DB_DATABASE=dokan_blog
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Create database
mysql -u root -p -e "CREATE DATABASE dokan_blog"

# 6. Run migrations
php artisan migrate

# 7. Seed database with sample data
php artisan db:seed

# 8. Start development server
php artisan serve
```

## Docker Installation (Recommended)

```bash
# 1. Navigate to devops directory
cd devops

# 2. Start all containers (PHP, Nginx, PostgreSQL, Redis, MailHog)
docker-compose up -d

# 3. Install dependencies
docker-compose run --rm composer install

# 4. Copy environment file
docker-compose exec app cp .env.example .env

# 5. Generate application key
docker-compose exec app php artisan key:generate

# 6. Run migrations
docker-compose exec app php artisan migrate

# 7. Seed database with sample data
docker-compose exec app php artisan db:seed
```

Access at:
- API: http://localhost:9000/api
- MailHog: http://localhost:8025  
- pgAdmin: http://localhost:8082

## Test the API

### 1. Get Categories Tree
```bash
# Local installation
curl http://localhost:8000/api/categories/tree

# Docker installation
curl http://localhost:9000/api/categories/tree
```

### 2. Login
```bash
# Local installation
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'

# Docker installation
curl -X POST http://localhost:9000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'
```

**Copy the token from the response!**

### 3. Create a Post (replace YOUR_TOKEN)
```bash
# Local installation
curl -X POST http://localhost:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "This is my first post content!",
    "category_id": 1
  }'

# Docker installation
curl -X POST http://localhost:9000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "This is my first post content!",
    "category_id": 1
  }'
```

### 4. Get All Posts
```bash
# Local installation
curl http://localhost:8000/api/posts

# Docker installation
curl http://localhost:9000/api/posts
```

## Run Tests

```bash
# Local installation
php artisan test

# Docker installation
docker-compose exec app php artisan test
```


## Troubleshooting

### Local Installation

**Issue: Migrations fail**
```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

**Issue: Tests fail**
```bash
composer dump-autoload
php artisan test
```

**Issue: Routes not found**
```bash
php artisan route:clear
php artisan optimize:clear
```

### Docker Installation

**Issue: Containers won't start**
```bash
# Check what's using the ports
netstat -ano | findstr :9000

# View container logs
docker-compose logs

# Rebuild containers
docker-compose down
docker-compose up -d --build
```

**Issue: Database connection refused**
```bash
# Check if containers are running
docker-compose ps

# Check database logs
docker-compose logs postgres

# Restart containers
docker-compose restart
```

**Issue: Permission errors**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

**Issue: Clear cache in Docker**
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
```

## More Information

- [README.md](./README.md) - Full API documentation
- [DOCKER.md](./DOCKER.md) - Docker guide
- [ARCHITECTURE.md](./ARCHITECTURE.md) - Architecture details
- [SETUP.md](./SETUP.md) - Detailed setup
