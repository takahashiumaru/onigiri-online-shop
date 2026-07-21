<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/version', function () {
    return response()->json([
        'version' => Controller::getVersion()
    ]);
});

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status' => 'ok',
        'database' => $dbStatus,
        'version' => Controller::getVersion(),
        'timestamp' => now()->toIso8601String(),
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
    ]);
});
