<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/version', function () {
    $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
    return response()->json([
        'version' => $composerJson['version'] ?? 'unknown'
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
        'app_env' => config('app.env'),
        'app_debug' => config('app.debug'),
    ]);
});
