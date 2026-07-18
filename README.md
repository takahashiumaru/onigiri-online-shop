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
7. Run `npm run dev` to start the development server.

## API Endpoints
- `GET /api/version`: Returns the application version.
- `GET /api/health`: Returns API and database connection status.
