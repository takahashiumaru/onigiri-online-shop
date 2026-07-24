<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // queries for counts (unfiltered by tab)
        $countQuery = Order::where('user_id', $user->id);

        // date range filter should apply to counts too if provided
        if ($request->filled('from')) {
            $countQuery->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $countQuery->whereDate('created_at', '<=', $request->input('to'));
        }

        $counts = [
            'all' => (clone $countQuery)->count(),
            'waiting' => (clone $countQuery)->whereIn('status', ['pending', 'processing'])->count(),
            'shipping' => (clone $countQuery)->where('status', 'shipped')->count(),
            'done' => (clone $countQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $countQuery)->where('status', 'cancelled')->count(),
        ];

        $query = Order::where('user_id', $user->id)->orderBy('created_at', 'desc');

        // date range filter (main query)
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

        // eager-load product on items
        $orders = $query->with('items.product')->paginate(10)->appends($request->except('page'));

        return view('customer.orders', compact('orders', 'counts'));
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

        \DB::transaction(function () use ($order) {
            // Kembalikan stok untuk setiap item di pesanan ini
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Ubah status pesanan menjadi cancelled
            $order->status = 'cancelled';

            // Jika belum bayar, tandai payment_status sebagai failed
            if ($order->payment_status === 'pending') {
                $order->payment_status = 'failed';
            }

            $order->save();
        });

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan dan stok telah dikembalikan.');
    }
}
