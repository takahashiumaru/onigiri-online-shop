<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $orders = Order::whereDate('created_at', $date)
            ->where('payment_status', 'paid')
            ->with('user', 'items.product')
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'total_items' => $orders->sum(fn($o) => $o->items->sum('quantity')),
        ];

        return view('admin.reports.daily', compact('orders', 'stats', 'date'));
    }

    public function monthly(Request $request)
    {
        $month = (int) ($request->month ?: date('m'));
        $year = (int) ($request->year ?: date('Y'));
        
        $orders = Order::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_status', 'paid')
            ->with('user', 'items.product')
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'total_items' => $orders->sum(fn($o) => $o->items->sum('quantity')),
        ];

        return view('admin.reports.monthly', compact('orders', 'stats', 'month', 'year'));
    }
}
