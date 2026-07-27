<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Available endpoints:
| - GET /api/health: API status, DB connectivity, and app version.
| - GET /api/routes: List all registered API routes (debug only).
| - GET /api/reports/daily: Paginated daily sales report.
| - GET /api/reports/monthly: Paginated monthly sales report.
|
*/

Route::get('/health', [HealthController::class, 'index']);
Route::get('/routes', [HealthController::class, 'routes']);

Route::get('/reports/daily', [ReportController::class, 'daily']);
Route::get('/reports/monthly', [ReportController::class, 'monthly']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
