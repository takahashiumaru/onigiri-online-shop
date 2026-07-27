<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class HealthController extends Controller
{
    public function index()
    {
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
            'version' => $this->getVersion(),
            'timestamp' => now()->toIso8601String(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ]);
    }

    public function routes()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        });

        return response()->json($routes);
    }
}
