# Konibui Docker Environment Setup

This document provides instructions for setting up the Konibui e-commerce platform using Docker Compose.

## Prerequisites

- Docker Desktop installed and running
- Docker Compose v3.8 or higher
- Git (for cloning the repository)

## Quick Start

1. **Clone the repository** (if not already done):
   ```bash
   git clone <repository-url>
   cd ITDBADMGroup1
   ```

2. **Create environment file**:
   ```bash
   cp .env.example .env
   ```

3. **Build and start the environment**:
   ```bash
   docker-compose up -d --build
   ```

4. **Install Laravel dependencies** (when Laravel project is added):
   ```bash
   docker-compose exec app composer install
   ```

5. **Generate application key** (when Laravel project is added):
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run database migrations** (when Laravel project is added):
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

## Services

The Docker environment consists of four services:

### 1. App (PHP-FPM)
- **Container**: `konibui_app`
- **Purpose**: Runs the Laravel application
- **PHP Version**: 8.2
- **Extensions**: PDO MySQL, GD, ZIP, BCMath, etc.

### 2. Database (MySQL)
- **Container**: `konibui_db`
- **Purpose**: MySQL 8.0 database server
- **Port**: 3306 (exposed to host)
- **Default Database**: `konibui`
- **Default User**: `konibui_user`

### 3. Web Server (Nginx)
- **Container**: `konibui_webserver`
- **Purpose**: HTTP server and reverse proxy
- **Port**: 8080 (host) → 80 (container)
- **URL**: http://localhost:8080

### 4. Database Admin (phpMyAdmin)
- **Container**: `konibui_phpmyadmin`
- **Purpose**: Database administration interface
- **Port**: 8081 (host) → 80 (container)
- **URL**: http://localhost:8081

## Network

All services are connected via a custom bridge network called `konibui_network`. Services can communicate with each other using their service names as hostnames:

- `app` - PHP-FPM application
- `db` - MySQL database
- `webserver` - Nginx server
- `db-admin` - phpMyAdmin

## Volumes

- **konibui_db_data**: Persistent MySQL data storage
- **Project files**: Mounted to `/var/www/html` in the app container
- **Storage**: Laravel storage directory with proper permissions
- **Bootstrap cache**: Laravel bootstrap cache directory

## Environment Variables

Key environment variables (defined in `.env`):

```env
DB_HOST=db
DB_PORT=3306
DB_DATABASE=konibui
DB_USERNAME=konibui_user
DB_PASSWORD=konibui_password
DB_ROOT_PASSWORD=root_password
```

## Common Commands

### Start the environment:
```bash
docker-compose up -d
```

### Stop the environment:
```bash
docker-compose down
```

### View logs:
```bash
docker-compose logs -f [service_name]
```

### Execute commands in the app container:
```bash
docker-compose exec app php artisan [command]
docker-compose exec app composer [command]
```

### Access MySQL directly:
```bash
docker-compose exec db mysql -u root -p
```

### Rebuild containers:
```bash
docker-compose up -d --build
```

## Troubleshooting

### Permission Issues
If you encounter permission issues with storage or cache directories:
```bash
docker-compose exec app chown -R www:www /var/www/html/storage
docker-compose exec app chown -R www:www /var/www/html/bootstrap/cache
```

### Database Connection Issues
1. Ensure the database container is healthy:
   ```bash
   docker-compose ps
   ```
2. Check database logs:
   ```bash
   docker-compose logs db
   ```

### Port Conflicts
If ports 8080 or 8081 are already in use, modify the port mappings in `docker-compose.yml`:
```yaml
ports:
  - "8090:80"  # Change 8080 to 8090
```

## Development Workflow

1. **Daily startup**: `docker-compose up -d`
2. **Code changes**: Edit files directly (auto-synced via volumes)
3. **Database changes**: Run migrations via `docker-compose exec app php artisan migrate`
4. **View application**: http://localhost:8080
5. **Database admin**: http://localhost:8081
6. **Daily shutdown**: `docker-compose down`

## Security Notes

- Default passwords are for development only
- Change all default credentials for production use
- The database port (3306) is exposed for development convenience
- Remove port exposure for production deployment

## Acceptance Criteria Verification

✅ **docker-compose.yml file present** in project root  
✅ **Four services defined**: app, db, webserver, db-admin  
✅ **Custom bridge network** (`konibui_network`) defined and attached to all services  
✅ **Service communication** via service names as hostnames  
✅ **Port mappings**: webserver (8080:80), db-admin (8081:80)  
✅ **Latest stable images**: Nginx:alpine, MySQL:8.0, phpMyAdmin:latest  
✅ **No port conflicts** between webserver and db-admin services  

## Testing the Setup

After running `docker-compose up -d --build`, verify:

1. **All containers running**: `docker-compose ps`
2. **Web access**: Visit http://localhost:8080
3. **Database admin**: Visit http://localhost:8081
4. **Service communication**: Check database connection status on main page
5. **Logs**: `docker-compose logs` should show no critical errors
