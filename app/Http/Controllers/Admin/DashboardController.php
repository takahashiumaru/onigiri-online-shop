<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->getDashboardStats();

        $recent_orders = Order::with('user', 'items')
            ->latest()
            ->take(10)
            ->get();

        $top_products = Product::query()
            ->withCount('orderItems as sales_count')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_products'));
    }

    private function getDashboardStats(): array
    {
        return [
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'pending_payment' => Order::where('payment_status', 'pending')->count(),
            'needs_processing' => Order::where('status', 'processing')->whereNull('courier_id')->count(),
            'low_stock' => Product::where('stock', '<=', 20)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
        ];
    }
}
