<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Handle Midtrans server-to-server notification (simplified).
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        // coba dapatkan order lewat order_id atau order_number
        $orderId = $payload['order_id'] ?? $payload['order_number'] ?? null;
        if (! $orderId) {
            return response('ok', 200);
        }

        // Sesuaikan cara pencarian dengan implementasi Anda (order_number atau id)
        $order = Order::where('order_number', $orderId)->first() ?? Order::find($orderId);
        if (! $order) {
            return response('ok', 200);
        }

        $txStatus = strtolower($payload['transaction_status'] ?? $payload['status'] ?? '');

        // Map beberapa status ke payment_status / order status yang sesuai
        if (in_array($txStatus, ['capture', 'settlement', 'success'])) {
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                // ubah status pesanan setelah bayar, mis. ke processing
                $order->status = 'processing';
                $order->save();
            }
        } elseif (in_array($txStatus, ['deny', 'cancel', 'expire', 'expired'])) {
            // Jika sebelumnya belum dibatalkan, kembalikan stok
            if ($order->status !== 'cancelled') {
                $order->restoreStock();
            }

            $order->payment_status = ($txStatus === 'expire' || $txStatus === 'expired') ? 'expired' : 'failed';
            $order->status = 'cancelled';
            $order->save();
        } elseif ($txStatus === 'pending') {
            $order->payment_status = 'pending';
            $order->save();
        }

        return response('ok', 200);
    }

    /**
     * Confirm payment from client-side after snap.onSuccess (immediate UX).
     */
    public function confirm(Request $request)
    {
        // Terima baik order_id (integer) maupun order_number (string)
        $orderId = $request->input('order_id');
        $orderNumber = $request->input('order_number');

        Log::info('Payment confirm called', ['order_id' => $orderId, 'order_number' => $orderNumber, 'user_id' => $request->user()->id ?? null]);

        $order = null;
        try {
            $order = Order::where('id', $orderId)->orWhere('order_number', $orderNumber)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $_e) {
            Log::warning('Payment confirm: order not found', ['order_id' => $orderId, 'order_number' => $orderNumber]);
            return self::errorResponse('Order tidak ditemukan.', 404);
        }

        // Jika route dilindungi auth, user akan tersedia; lakukan verifikasi kepemilikan bila ada
        if ($request->user() && $order->user_id !== $request->user()->id) {
            Log::warning('Payment confirm: unauthorized user', ['order_id' => $order->id, 'user_id' => $request->user()->id]);

            return self::errorResponse('Akses ditolak.', 403);
        }

        // Hanya update jika belum paid
        if ($order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
            $order->status = 'processing';
            $order->save();

            Log::info('Payment confirm: order updated to paid', ['order_id' => $order->id]);
        } else {
            Log::info('Payment confirm: order already paid', ['order_id' => $order->id]);
        }

        return response()->json(['ok' => true]);
    }
}
