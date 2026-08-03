<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class HealthController extends Controller
{
    public function index()
    {
        $db = $this->checkDatabase();

        $dbStatus = $db['status'];
        $httpStatus = $dbStatus === 'connected' ? 200 : 503;

        return response()->json([
            'status' => $dbStatus === 'connected' ? 'ok' : 'degraded',
            'version' => static::getVersion(),
            'timestamp' => now()->toIso8601String(),
            'system' => [
                'os' => PHP_OS,
                'php_version' => PHP_VERSION,
                'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            ],
            'database' => [
                'status' => $dbStatus,
                'latency_ms' => $db['latency'],
            ],
        ], $httpStatus);
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    private function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            return ['status' => 'connected', 'latency' => $latency];
        } catch (\Exception $e) {
            return ['status' => 'disconnected', 'latency' => null];
        }
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
