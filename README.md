# Onigiri Online Shop

A point-of-sale and e-commerce system for managing orders and products.

## Features
- Product catalog management
- Order processing and reporting (daily/monthly sales)
- User authentication via Sanctum
- API-first architecture with standardized responses and structured error handling

## Tech Stack
- Framework: Laravel 12.x
- Frontend: Vite + TailwindCSS
- Database: SQLite

## Setup
```bash
# Clone the repository
git clone https://github.com/takahashiumaru/onigiri-online-shop.git
cd onigiri-online-shop

# Install dependencies
composer install
npm install

# Build frontend assets
npm run build

# Setup environment & database
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Start development server
php artisan serve
```

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
  - Returns paginated order list with user and items details.
- `GET /api/orders/{id}` - Order detail (Sanctum-protected).
  - Returns full order details with courier, items, and customer info.
- `GET /api/user` - Get current authenticated user (Sanctum-protected).
- `POST /api/user/password` - Update user password (Sanctum-protected).
  - Body: `{ current_password, new_password, new_password_confirmation }`
- `GET /api/couriers` - List paginated couriers.
  - Query params: `perPage` (1-100, default 10)
- `GET /api/couriers/{id}` - Get single courier detail.

## Error Responses
All API endpoints return errors with consistent JSON contracts:
- `400 Bad Request` / `500 Internal Server Error`: `{"error": "<message>"}`
- `404 Not Found`: `{"error": "Resource tidak ditemukan."}`
- `422 Unprocessable Entity`: `{"error": "Validasi gagal.", "messages": { ... }}`
