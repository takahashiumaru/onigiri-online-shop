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
- `GET /api/health` - System health check (DB connectivity with latency, memory, storage status, version).
- `GET /api/routes` - List all registered API routes (debug tool).
- `GET /api/products` - List products.
  - Query params: `search` (string), `category` (string), `is_available` (boolean), `perPage` (1-100, default 10), `include=ratings`
- `GET /api/products/categories` - List product categories.
- `GET /api/products/{id}` - Single product detail.
  - Query params: `include=ratings`
- `GET /api/reports/daily` - Daily sales report.
  - Query params: `date` (YYYY-MM-DD, default today), `perPage` (1-100, default 15)
  - Returns `summary` with `totalRevenue` and `orderCount`, along with paginated items (`totalPages`, `total`, `page`, `pageSize`).
- `GET /api/reports/monthly` - Monthly sales report.
  - Query params: `month` (1-12), `year` (YYYY), `perPage` (1-100, default 15)
  - Returns `summary` with `totalRevenue` and `orderCount`, along with paginated items (`totalPages`, `total`, `page`, `pageSize`).
- `GET /api/orders` - List paginated user/admin orders (Sanctum-protected).
  - Query params: `status` (string), `payment_status` (string), `perPage` (1-100, default 10)
- `GET /api/orders/{id}` - Order detail (Sanctum-protected).
- `GET /api/user` - Get current authenticated user (Sanctum-protected).
- `POST /api/user/password` - Update password (Sanctum-protected).
  - Body: `{ current_password, new_password, new_password_confirmation }`
