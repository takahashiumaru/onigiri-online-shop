<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/version', function () {
    return response()->json([
        'version' => \App\Http\Controllers\Controller::getVersion()
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
