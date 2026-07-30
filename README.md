# Onigiri Online Shop

A Laravel-based online shop for onigiri, featuring product management, cart, checkout, and courier dashboard.

## Tech Stack
- Framework: Laravel 12.x
- Language: PHP 8.3
- Database: SQLite (default)
- Frontend: Vite + Tailwind CSS

## Setup
1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

## API Endpoints
- `GET /api/health`: System health status (database connectivity + latency + version + OS/memory).
- `GET /api/routes`: List of all registered API routes (debug).
- `GET /api/products`: Paginated list of products.
- `GET /api/products/{id}`: Detailed information of a product.
- `GET /api/reports/daily`: Paginated daily sales report.
- `GET /api/reports/monthly`: Paginated monthly sales report.
