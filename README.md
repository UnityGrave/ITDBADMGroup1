<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Konibui E-commerce Platform

A modern e-commerce platform built with Laravel 12 and Docker, featuring a comprehensive buyback system for product resale.

## 🚀 Getting Started

This project uses Docker Compose to orchestrate a complete development environment with Laravel, MySQL, Nginx, and phpMyAdmin.

### Prerequisites

- Docker Desktop installed and running
- Git for version control
- At least 4GB of available RAM

### Quick Setup

Follow these simple steps to get your development environment running:

#### 1. Clone the Repository
```bash
git clone <repository-url>
cd ITDBADMGroup1
```

#### 2. Create Environment Configuration
```bash
cp .env.example .env
```

#### 3. Build and Start the Environment
```bash
docker-compose up -d --build
```

#### 4. Install Laravel Dependencies
```bash
docker-compose exec app composer install
```

#### 5. Generate Application Key
```bash
docker-compose exec app php artisan key:generate
```

#### 6. Run Database Migrations
```bash
docker-compose exec app php artisan migrate
```

#### 7. Cache Configuration (Optional but Recommended)
```bash
docker-compose exec app php artisan config:cache
```

### 🌐 Access Points

Once setup is complete, you can access:

- **Main Application**: http://localhost:8080
- **Database Administration**: http://localhost:8081
  - Username: `konibui_user`
  - Password: `konibui_password`
  - Server: `db`

### 🐳 Docker Services

The environment consists of four interconnected services:

| Service | Container Name | Purpose | Port |
|---------|---------------|---------|------|
| **app** | konibui_app | Laravel 12 + PHP 8.2-FPM | Internal: 9000 |
| **webserver** | konibui_webserver | Nginx reverse proxy | 8080 → 80 |
| **db** | konibui_db | MySQL 8.0 database | Internal: 3306 |
| **db-admin** | konibui_phpmyadmin | phpMyAdmin interface | 8081 → 80 |

### 📁 Project Structure

```
ITDBADMGroup1/
├── app/                    # Laravel application logic
├── bootstrap/              # Laravel bootstrap files
├── config/                 # Laravel configuration files
├── database/               # Migrations, factories, seeders
├── docker/                 # Docker configuration files
│   └── nginx/
│       └── nginx.conf      # Custom Nginx configuration
├── public/                 # Web server document root
├── resources/              # Views, assets, lang files
├── routes/                 # Application routes
├── storage/                # Application storage
├── vendor/                 # Composer dependencies
├── docker-compose.yml      # Docker services definition
├── Dockerfile             # Custom PHP-FPM image
└── .env.example           # Environment variables template
```

### 🛠️ Common Development Commands

#### Managing the Environment
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild containers
docker-compose up -d --build

# View service logs
docker-compose logs [service_name]
```

#### Laravel Artisan Commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Create migration
docker-compose exec app php artisan make:migration create_table_name

# Seed database
docker-compose exec app php artisan db:seed

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

#### Composer Commands
```bash
# Install dependencies
docker-compose exec app composer install

# Add new package
docker-compose exec app composer require package/name

# Update dependencies
docker-compose exec app composer update
```

### 🔧 Configuration

#### Database Settings
The application is configured to use MySQL with the following default credentials:
- **Host**: `db` (Docker service name)
- **Database**: `konibui`
- **Username**: `konibui_user`
- **Password**: `konibui_password`
- **Root Password**: `root_password`

#### Environment Variables
Key environment variables in `.env`:
```env
APP_NAME=Konibui
APP_URL=http://localhost:8080
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=konibui
DB_USERNAME=konibui_user
DB_PASSWORD=konibui_password
```

### 🚨 Troubleshooting

#### Port Conflicts
If ports 8080 or 8081 are already in use:
```yaml
# In docker-compose.yml, change:
ports:
  - "8090:80"  # For webserver
  - "8082:80"  # For db-admin
```

#### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www:www /var/www/html/storage
docker-compose exec app chown -R www:www /var/www/html/bootstrap/cache
```

#### Database Connection Issues
1. Ensure database container is healthy:
   ```bash
   docker-compose ps
   ```
2. Check database logs:
   ```bash
   docker-compose logs db
   ```

#### Container Issues
```bash
# Remove all containers and start fresh
docker-compose down --remove-orphans
docker system prune -f
docker-compose up -d --build
```

### 📊 Verification Steps

After setup, verify everything is working:

1. ✅ **All containers running**: `docker-compose ps`
2. ✅ **Laravel accessible**: Visit http://localhost:8080
3. ✅ **Database accessible**: Visit http://localhost:8081
4. ✅ **Database connection**: Check Laravel logs for any database errors

### 🔄 Daily Development Workflow

1. **Start**: `docker-compose up -d`
2. **Code**: Edit files directly (live-synced via volumes)
3. **Database**: Use phpMyAdmin or run migrations as needed
4. **Stop**: `docker-compose down` (when done for the day)

### 🛡️ Security Notes

- Default passwords are for **development only**
- Change all credentials before deploying to production
- The database port is not exposed externally for security
- All services communicate via Docker internal network

### 🎯 Next Steps

1. Implement user authentication
2. Set up e-commerce product catalog
3. Build the buyback system
4. Configure payment gateways
5. Add comprehensive testing

---

## Technical Architecture

This project implements a containerized Laravel application with:
- **PHP 8.2** with FPM for optimal performance
- **MySQL 8.0** for reliable data storage
- **Nginx** as reverse proxy and static file server
- **Docker networking** for service communication
- **Volume persistence** for database data
- **Health checks** for reliable startup sequencing

For detailed technical documentation, see `DOCKER_SETUP.md`.
