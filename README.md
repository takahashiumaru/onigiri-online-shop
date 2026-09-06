# Onigiri Online Shop

A point-of-sale and e-commerce system for managing orders and products.

## Features
- Product catalog management
- Order processing and reporting (daily/monthly sales)
- User authentication via Sanctum
- API-first architecture with standardized responses

## Tech Stack
- Framework: Laravel 12.x
- Frontend: Vite + TailwindCSS
- Database: SQLite

## Setup
1. Clone the repository.
2. Run `composer install`.
3. Run `npm install && npm run build`.
4. Run `php artisan migrate --seed`.
5. Run `php artisan serve`.

## API Documentation
- `GET /api/health` - System health check.
- `GET /api/products` - List products.
- `GET /api/reports/daily` - Daily sales report.
- `GET /api/reports/monthly` - Monthly sales report.
