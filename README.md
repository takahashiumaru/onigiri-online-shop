# Onigiri Online Shop

An online shop application built with Laravel 12, Vite, and Tailwind CSS.

## Stack
- PHP 8.3
- Laravel 12
- SQLite (for local development)
- Vite (Asset bundling)
- Tailwind CSS (Styling)

## Setup
1. Clone the repository.
2. Run `composer install`.
3. Run `npm install`.
4. Create a `.env` file from `.env.example`.
5. Run `php artisan key:generate`.
6. Run `php artisan migrate`.
7. Run `npm run build`.
8. Run `php artisan serve` to start the development server.

## API Endpoints
### Public
- `GET /api/version`: Returns the application version (dynamically read from `composer.json`).
- `GET /api/health`: Returns API status, database connection status, and app version.
- `GET /api/reports/daily`: Get daily paginated paid orders report (optional parameter `date`).
- `GET /api/reports/monthly`: Get monthly paginated paid orders report (optional parameters `month` and `year`).

### Admin Routes (`/admin/*`) - Requires `auth:admin`
- `GET /admin/dashboard`: Admin dashboard with stats (revenue, orders, customers, products).
- `Resource /admin/products`: Full CRUD for products.
- `PATCH /admin/products/{product}/update-stock`: Quick stock update (supports `set`, `add`, `subtract`).
- `Resource /admin/couriers`: Manage couriers.
- `GET /admin/orders`: List all orders with filters (status, payment, search).
- `GET /admin/orders/ready`: Orders ready for courier assignment.
- `GET /admin/orders/{order}`: Order detail with courier assignment option.
- `PATCH /admin/orders/{order}/status`: Update order status (pending, processing, shipped, delivered, cancelled).
- `GET /admin/reports/daily`: Daily sales report.
- `GET /admin/reports/monthly`: Monthly sales report.

### Customer Routes - Requires `auth:customer`
- `GET /cart`: View cart.
- `POST /cart/add/{product}`: Add to cart.
- `PATCH /cart/{cartItem}`: Update cart item.
- `DELETE /cart/{cartItem}`: Remove item from cart.
- `DELETE /cart`: Clear cart.
- `GET /checkout`: View checkout.
- `POST /checkout`: Process payment.
- `GET /orders`: View order history.
- `GET /orders/{order}`: Order detail.
- `PATCH /orders/{order}/cancel`: Cancel order (customer-facing).
- `POST /payment/confirm`: Confirm payment status from client.

### Courier Routes (`/courier/*`) - Requires `auth:courier`
- `GET /courier/dashboard`: Courier dashboard listing assigned orders.
- `GET /courier/orders/{order}`: View assigned order details.
- `POST /courier/orders/{order}/delivery`: Update delivery proof/status.

## Versioning
Version is managed in `composer.json`. Use `Controller::getVersion()` to retrieve it.
