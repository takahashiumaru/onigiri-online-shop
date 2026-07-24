<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/version', function () {
    return response()->json([
        'version' => Controller::getVersion(),
    ]);
});

Route::get('/health', function () {
    $dbStatus = 'disconnected';
    $dbLatency = null;

    try {
        $startTime = microtime(true);
        DB::connection()->getPdo();
        DB::select('SELECT 1');
        $dbLatency = round((microtime(true) - $startTime) * 1000, 2);
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status' => 'ok',
        'database' => [
            'status' => $dbStatus,
            'latency_ms' => $dbLatency,
        ],
        'version' => Controller::getVersion(),
        'timestamp' => now()->toIso8601String(),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
    ]);
});
