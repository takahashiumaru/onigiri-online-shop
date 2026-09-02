# Onigiri Online Shop

A Laravel-based online shop for onigiri, featuring product management, cart,
checkout, courier dashboard, and admin reports.

## Features
- **Customer**: Browse products, manage cart, checkout, pay via Midtrans, rate
  delivered orders.
- **Admin**: Manage products, couriers, orders, and view daily/monthly reports.
- **Courier**: View ready-for-delivery orders, update delivery status.
- **Notifications**: In-app notifications for orders/payments/ratings.
- **Observability**: JSON API with health, route listing, and report endpoints.

## Tech Stack
- **Framework**: Laravel 12.x
- **Language**: PHP 8.3
- **Database**: SQLite (default; MySQL/Postgres supported via `.env`)
- **Frontend**: Vite + Tailwind CSS
- **Payment**: Midtrans Snap

## Setup
1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Copy environment template and generate app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Run database migrations:
   ```bash
   php artisan migrate --force
   ```
4. (Optional) Seed sample data:
   ```bash
   php artisan db:seed
   ```
   **Default Login Accounts:**
   - **Admin**: `admin@onigiri.com` / `password`
   - **Customer**: `customer@onigiri.com` / `password`
   - **Courier**: `kurir@onigiri.com` / `password`

5. Install Node dependencies and build frontend assets:
   ```bash
   npm install
   npm run build
   ```

## Local Development
- Run the dev server (HTTP + Vite hot reload):
  ```bash
  php artisan serve
  npm run dev
  ```
- Visit `http://127.0.0.1:8000`.

## API Endpoints

| Method | Endpoint                  | Description                                                      |
|--------|---------------------------|------------------------------------------------------------------|
| GET    | `/api/health`             | Health check (DB connectivity + latency + version + OS/memory + storage).  |
| GET    | `/api/routes`             | List all registered API routes (debug only).                     |
| GET    | `/api/products`           | Paginated list of products (optional `?search=`, `?category=`, `?perPage=10`, `?include=ratings`). |
| GET    | `/api/products/{id}`      | Detailed information for a single product (optional `?include=ratings`). |
| GET    | `/api/reports/daily`      | Paginated daily sales report (optional `?date=YYYY-MM-DD`, `?perPage=15`). |
| GET    | `/api/reports/monthly`    | Paginated monthly sales report (optional `?month=1&year=2025`, `?perPage=15`). |
| GET    | `/api/user`               | Current authenticated user (Sanctum-protected).                  |
| POST   | `/api/user/password`      | Change user password (Sanctum-protected).                        |

### Health Check Response
```json
{
  "status": "ok",
  "uptime": 0,
  "version": "1.0.0",
  "timestamp": "2025-01-01T00:00:00+00:00",
  "system": { "os": "Linux", "php_version": "8.3.0", "memory_usage": "32 MB" },
  "database": { "status": "connected", "latency_ms": 1.23 },
  "storage": { "status": "writable" },
  "environment": "production"
}
```
Returns `200 OK` when healthy, `503 Service Unavailable` when the database is
disconnected.

## Tests
```bash
php artisan test
```