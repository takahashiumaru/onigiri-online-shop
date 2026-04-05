<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'pending_payment' => Order::where('payment_status', 'pending')->count(),
            'need_courier' => Order::where('status', 'processing')->whereNull('courier_id')->count(),
            'low_stock' => Product::where('stock', '<=', 20)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
        ];

        $recent_orders = Order::with('user', 'items')
            ->latest()
            ->take(10)
            ->get();

        $top_products = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_products'));
    }
}
