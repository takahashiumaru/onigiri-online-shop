<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // <-- added

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Filter out cart items whose product is deleted/null
        $cartItems = $cartItems->filter(fn ($item) => $item->product !== null);

        if ($cartItems->isEmpty()) {
            CartItem::where('user_id', Auth::id())->delete();

            return redirect()->route('cart.index')->with('error', 'Produk tidak tersedia. Keranjang telah dikosongkan.');
        }

        $totalQty = $cartItems->sum('quantity');
        if ($totalQty < 20) {
            return redirect()->route('cart.index')->with('error', "Minimal pembelian adalah 20 pcs. Kamu baru memesan {$totalQty} pcs.");
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
        $shippingCost = 10000;
        $total = $subtotal + $shippingCost;

        return view('customer.checkout', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
    }

    /**
     * Anticipation: Manually regenerate token if it was missing or failed.
     */
    public function regenerateToken(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return back()->with('info', 'Pesanan ini sudah dibayar.');
        }

        // Check if config exists
        if (empty(config('midtrans.server_key'))) {
            return back()->with('error', 'Konfigurasi pembayaran belum lengkap (Server Key kosong). Silakan hubungi admin.');
        }

        // Rebuild params
        $items = $order->items->map(fn ($item) => [
            'id' => (string) $item->product_id,
            'price' => (int) $item->price,
            'quantity' => (int) $item->quantity,
            'name' => mb_substr($item->product_name, 0, 50),
        ])->toArray();

        $items[] = [
            'id' => 'SHIPPING',
            'price' => (int) $order->shipping_cost,
            'quantity' => 1,
            'name' => 'Biaya Pengiriman',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? $order->shipping_phone,
            ],
            'item_details' => $items,
            'callbacks' => [
                'finish' => route('checkout.success', $order),
            ],
        ];

        $snapToken = $this->attemptGetSnapToken($params);

        if ($snapToken) {
            $order->update(['midtrans_snap_token' => $snapToken]);

            return back()->with('success', 'Token pembayaran berhasil dibuat! Silakan lanjutkan pembayaran.');
        }

        return back()->with('error', 'Gagal membuat token pembayaran. Pastikan koneksi internet stabil atau hubungi admin.');
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get()
            ->filter(fn ($item) => $item->product !== null);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $totalQty = $cartItems->sum('quantity');
        if ($totalQty < 10) {
            return redirect()->route('cart.index')->with('error', "Minimal pembelian adalah 10 pcs. Kamu baru memesan {$totalQty} pcs.");
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('error', "Stok {$item->product->name} tidak mencukupi. Tersisa: {$item->product->stock}");
            }
            if (! $item->product->is_available) {
                return back()->with('error', "Produk {$item->product->name} tidak tersedia.");
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
            $shippingCost = 10000;
            $total = $subtotal + $shippingCost;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ONI-'.strtoupper(Str::random(8)),
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            // Build params for Midtrans Snap (unchanged structure)
            $user = Auth::user();

            $itemDetails = $cartItems->values()->map(fn ($item) => [
                'id' => (string) $item->product->id,
                'price' => (int) $item->product->price,
                'quantity' => (int) $item->quantity,
                'name' => mb_substr($item->product->name, 0, 50),
            ])->toArray();

            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) $shippingCost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? $request->shipping_phone,
                ],
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('checkout.success', $order),
                ],
            ];

            // Try to get Snap token with controlled timeout & retries.
            $snapToken = $this->attemptGetSnapToken($params, $maxAttempts = 2, $timeoutSeconds = 6);

            if ($snapToken) {
                $order->update(['midtrans_snap_token' => $snapToken]);
            } else {
                // Leave token null but keep order so user can retry from success page
                $order->update(['midtrans_snap_token' => null]);
                Log::warning("Midtrans snap token not generated for order {$order->order_number}");
            }

            // Clear cart only after everything succeeded
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            $redirect = redirect()->route('checkout.success', $order);
            if (! $snapToken) {
                $msg = empty(config('midtrans.server_key'))
                    ? 'Konfigurasi pembayaran (Server Key) belum diset di server. Pesanan disimpan, silakan hubungi admin.'
                    : 'Terjadi masalah koneksi ke Midtrans saat membuat token. Pesanan berhasil dibuat, silakan coba "Generate Token" kembali di bawah.';

                $redirect = $redirect->with('warning', $msg);
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: '.$e->getMessage());
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika status lokal masih pending, coba sinkronisasi dengan Midtrans
        if ($order->status === 'pending' || $order->payment_status === 'pending') {
            $this->syncStatusWithMidtrans($order);
        }

        $order->load('items');
        $snapToken = $order->midtrans_snap_token;

        return view('customer.checkout-success', compact('order', 'snapToken'));
    }

    /**
     * Sync order status with Midtrans API for real-time updates on page load.
     */
    private function syncStatusWithMidtrans(Order $order): void
    {
        $serverKey = config('midtrans.server_key');
        if (empty($serverKey)) {
            return;
        }

        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(5)
                ->get($baseUrl.$order->order_number.'/status');

            if ($response->successful()) {
                $data = $response->json();
                $txStatus = strtolower($data['transaction_status'] ?? '');

                if (in_array($txStatus, ['capture', 'settlement', 'success'])) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                } elseif (in_array($txStatus, ['deny', 'cancel', 'expire', 'expired'])) {
                    // Restore stock if not already cancelled
                    if ($order->status !== 'cancelled') {
                        $order->restoreStock();
                    }

                    $order->update([
                        'payment_status' => ($txStatus === 'expire' || $txStatus === 'expired') ? 'expired' : 'failed',
                        'status' => 'cancelled',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to sync Midtrans status for {$order->order_number}: ".$e->getMessage());
        }
    }

    /**
     * Attempt to get Midtrans Snap token using HTTP client with limited retries and timeout.
     * Returns token string on success or null on failure.
     */
    private function attemptGetSnapToken(array $params, int $maxAttempts = 2, int $timeoutSeconds = 30): ?string
    {
        $url = config('midtrans.is_production') ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $serverKey = config('midtrans.server_key');

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->timeout($timeoutSeconds)
                    ->acceptJson()
                    ->post($url, $params);

                if ($response->successful()) {
                    $data = $response->json();
                    if (! empty($data['token'])) {
                        return $data['token'];
                    }
                    Log::warning("Midtrans response but no token (attempt {$attempt}): ".json_encode($data));
                } else {
                    Log::warning("Midtrans HTTP error (attempt {$attempt}): HTTP {$response->status()} - ".$response->body());
                }
            } catch (\Exception $ex) {
                Log::warning("Midtrans connection attempt {$attempt} failed: ".$ex->getMessage());
            }

            // short backoff before retrying
            if ($attempt < $maxAttempts) {
                sleep(1);
            }
        }

        return null;
    }
}
