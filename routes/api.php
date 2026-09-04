<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Available endpoints:
| - GET  /api/health: API status, DB connectivity, storage, and app version.
| - GET  /api/routes: List all registered API routes (debug only).
| - GET  /api/products: Paginated list of products (?search=, ?category=, ?perPage=, ?include=ratings).
| - GET  /api/products/{id}: Detailed information for a single product (?include=ratings).
| - GET  /api/reports/daily: Paginated daily sales report (?date=, ?perPage=).
| - GET  /api/reports/monthly: Paginated monthly sales report (?month=, ?year=, ?perPage=).
| - GET  /api/user: Current authenticated user (Sanctum-protected).
| - POST /api/user/password: Update user password (Sanctum-protected).
|
*/

Route::get('/health', [HealthController::class, 'index']);
Route::get('/routes', [HealthController::class, 'routes']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/reports/daily', [ReportController::class, 'daily']);
Route::get('/reports/monthly', [ReportController::class, 'monthly']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/password', [PasswordController::class, 'update']);
});
