<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $from = Carbon::parse($request->date ?? 'today')->startOfDay();
        $to = $from->copy()->endOfDay();
        $orders = $this->getPaginatedOrders($from, $to);

        return $this->reportResponse($orders, $from, $to);
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

        $from = $date->copy()->startOfMonth();
        $to = $date->copy()->endOfMonth();
        $orders = $this->getPaginatedOrders($from, $to);

        return $this->reportResponse($orders, $from, $to);
    }
}
