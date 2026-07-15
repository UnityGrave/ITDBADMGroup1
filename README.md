# Konibui — Trading Card Game E-Commerce Platform

A full-featured e-commerce platform for **trading card games (TCG)**, built with **Laravel 12** and **Livewire**. Konibui pairs a real storefront — catalog, cart, checkout, multi-currency pricing, and full-text search — with **defense-in-depth database security** (application RBAC mirrored by MySQL roles, plus triggers and stored procedures).

## Overview

Konibui models a real TCG marketplace: products are cards belonging to **sets** and **rarities**, tracked through **inventory**, sold via a **cart → checkout → order** flow, and priced across multiple **currencies**. It was built as a database-administration project, so the schema goes well beyond CRUD — it enforces security and business rules at the database layer itself.

## Features

- **TCG catalog** — products organised by card, set, rarity, and category
- **Inventory management** — stock tracked per product
- **Cart & checkout** — add to cart, checkout, and order-success flow
- **Order management** — orders, order items, and status updates
- **Multi-currency** — per-currency pricing with product price overrides and a user's preferred currency (via `brick/money`)
- **Full-text search** — powered by Laravel Scout + the TNTSearch driver
- **Authentication** — Laravel Breeze
- **Admin area** — product and user management, order status control
- **Defense-in-depth security:**
  - Application **role-based access control** (Admin / Employee / Customer)
  - **MySQL database roles** mirroring the app roles, so restrictions hold even if the app layer is bypassed
  - **Triggers** and **stored procedures** for data integrity and business logic

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | **Laravel 12** (PHP 8.2) |
| Interactivity | **Livewire 3** + Volt |
| Auth | Laravel Breeze |
| Search | Laravel Scout + TeamTNT TNTSearch |
| Money | brick/money (multi-currency) |
| Database | **MySQL 8** (roles, triggers, stored procedures) |
| Infra | Docker Compose — app (PHP-FPM), Nginx, MySQL, phpMyAdmin |
| Tooling | Vite, Pint, Pest/PHPUnit |

## Data Model (key entities)

`Card` · `Set` · `Rarity` · `Category` · `Product` · `Inventory` · `CartItem` · `Order` · `OrderItem` · `Currency` · `ProductPriceOverride` · `Role` · `User`

## Getting Started (Docker)

### Prerequisites
- Docker Desktop
- Composer, Node.js + npm (for local asset builds)

### Setup
```bash
git clone https://github.com/UnityGrave/ITDBADMGroup1.git
cd ITDBADMGroup1

cp .env.example .env               # then set APP_NAME=Konibui and DB_* values

docker-compose up -d --build       # app + MySQL + Nginx + phpMyAdmin
docker-compose exec app composer install
npm install && npm run dev

docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```

### Access
- App: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

The database roles, triggers, and stored procedures are created automatically by the migrations. See the repo's `DOCKER_SETUP.md` for details.

## Project Structure
```
app/
  Models/            # Card, Set, Rarity, Product, Inventory, Order, Currency, Role, User…
  Http/Controllers/  # Product, ProductDetail, Order, Admin, Auth, DefenseInDepthTest
database/migrations/ # TCG core, products/inventory, cart, orders, multi-currency,
                     #   MySQL roles, triggers, stored procedures
routes/web.php       # products, cart, checkout, orders, admin/user management
resources/           # Livewire/Blade views, assets
```

## Developed At

Database-administration (ITDBADM) course project at **De La Salle University**.

## License

MIT — see [`LICENSE`](LICENSE). 
