<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

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
