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

        return response()->json([
            'status' => 'ok',
            'app_name' => config('app.name'),
            'database' => [
                'status' => $db['status'],
                'latency_ms' => $db['latency'],
            ],
            'version' => static::getVersion(),
            'timestamp' => now()->toIso8601String(),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ]);
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
