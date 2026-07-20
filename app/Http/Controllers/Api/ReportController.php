<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get paginated orders based on a date range.
     */
    private function getPaginatedOrders(Carbon $from, Carbon $to, int $perPage = 15)
    {
        return Order::whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->with('user:id,name,email')
            ->select('id', 'user_id', 'total', 'payment_status', 'created_at')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get daily report.
     */
    public function daily(Request $request)
    {
        $request->validate(['date' => 'nullable|date']);
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $orders = $this->getPaginatedOrders($date->copy()->startOfDay(), $date->copy()->endOfDay());

        return response()->json($orders);
    }

    /**
     * Get monthly report.
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020',
        ]);
        
        $date = Carbon::createFromDate(
            $request->year ?? date('Y'),
            $request->month ?? date('m'),
            1
        );

        $orders = $this->getPaginatedOrders($date->copy()->startOfMonth(), $date->copy()->endOfMonth());

        return response()->json($orders);
    }
}
