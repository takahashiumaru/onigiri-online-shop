<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::where('user_id', $user->id)->orderBy('created_at', 'desc');

        // date range filter (server-side)
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        // tab filter (waiting / shipping / done / cancelled)
        $tab = $request->input('tab', 'all');
        if ($tab !== 'all') {
            if ($tab === 'waiting') {
                $query->whereIn('status', ['pending', 'processing']);
            } elseif ($tab === 'shipping') {
                $query->where('status', 'shipped');
            } elseif ($tab === 'done') {
                $query->where('status', 'delivered');
            } elseif ($tab === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }

        // eager-load product on items so views can access product images without extra queries
        $orders = $query->with('items.product')->paginate(10)->appends($request->except('page'));

        return view('customer.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('customer.order-detail', compact('order'));
    }

    /**
     * Batalkan pesanan oleh customer pemiliknya.
     */
    public function cancel(Request $request, Order $order)
    {
        // Pastikan pemilik pesanan adalah user yang sedang login
        if ($order->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Anda tidak berhak membatalkan pesanan ini.');
        }

        // Daftar status yang boleh dibatalkan
        $cancelableStatuses = ['pending', 'processing'];

        if (! in_array($order->status, $cancelableStatuses) || $order->status === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan pada status saat ini.');
        }

        // Ubah status pesanan menjadi cancelled
        $order->status = 'cancelled';

        // Jika perlu, Anda bisa sesuaikan payment_status juga; contoh:
        if ($order->payment_status !== 'paid') {
            $order->payment_status = 'failed';
        }

        $order->save();

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
