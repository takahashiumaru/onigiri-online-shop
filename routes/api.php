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
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::get('/health', [HealthController::class, 'index']);
Route::get('/routes', [HealthController::class, 'routes']);

Route::get('/reports/daily', [ReportController::class, 'daily']);
Route::get('/reports/monthly', [ReportController::class, 'monthly']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
