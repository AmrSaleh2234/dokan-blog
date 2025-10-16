# Setup Guide

## Installation

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database

**MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dokan_blog
DB_USERNAME=root
DB_PASSWORD=your_password
```

**For PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dokan_blog
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**SQLite:**
```env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

### 4. Create Database

**MySQL:**
```bash
mysql -u root -p -e "CREATE DATABASE dokan_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**For PostgreSQL:**
```bash
createdb dokan_blog
```

**SQLite:**
```bash
touch database/database.sqlite
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Database

```bash
php artisan db:seed
```

### 7. Install Frontend Dependencies

```bash
npm install
```

### 8. Build Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Start Development Server

```bash
php artisan serve
```

API at `http://localhost:8000/api`

## Run Tests

```bash
php artisan test
```

## Docker Setup

See [DOCKER.md](./DOCKER.md) for complete Docker setup guide.

## Troubleshooting

**Class not found:**
```bash
composer dump-autoload
```

**Migration fails:**
```bash
php artisan config:clear
php artisan migrate:fresh
```

**Routes not found:**
```bash
php artisan route:clear
```

## Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

Set permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
