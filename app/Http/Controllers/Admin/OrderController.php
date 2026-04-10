<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function readyForCourier(Request $request)
    {
        $query = Order::with('user', 'items')->where('status', 'processing');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($sq) => $sq->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $orders = $query->latest()->paginate(20);
        $couriers = \App\Models\User::where('role', 'courier')->get();

        return view('admin.orders.ready', compact('orders', 'couriers'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'courier']);
        $couriers = \App\Models\User::where('role', 'courier')->get();
        return view('admin.orders.show', compact('order', 'couriers'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $rules = [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ];

        if ($request->status === 'shipped') {
            $rules['courier_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $updateData = ['status' => $request->status];

        if ($request->status === 'shipped') {
            $updateData['courier_id'] = $request->courier_id;
        }

        $order->update($updateData);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
