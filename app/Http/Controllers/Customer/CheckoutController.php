<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // <-- added

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
        $cartItems = $cartItems->filter(fn($item) => $item->product !== null);

        if ($cartItems->isEmpty()) {
            CartItem::where('user_id', Auth::id())->delete();
            return redirect()->route('cart.index')->with('error', 'Produk tidak tersedia. Keranjang telah dikosongkan.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);
        $shippingCost = 10000;
        $total = $subtotal + $shippingCost;

        return view('customer.checkout', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'notes'            => 'nullable|string|max:500',
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get()
            ->filter(fn($item) => $item->product !== null);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('error', "Stok {$item->product->name} tidak mencukupi. Tersisa: {$item->product->stock}");
            }
            if (!$item->product->is_available) {
                return back()->with('error', "Produk {$item->product->name} tidak tersedia.");
            }
        }

        DB::beginTransaction();
        try {
            $subtotal     = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);
            $shippingCost = 10000;
            $total        = $subtotal + $shippingCost;

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => 'ONI-' . strtoupper(Str::random(8)),
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total'            => $total,
                'shipping_name'    => $request->shipping_name,
                'shipping_phone'   => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes'            => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product->id,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->image,
                    'price'         => $item->product->price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $item->quantity * $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            // Build params for Midtrans Snap (unchanged structure)
            $user = Auth::user();

            $itemDetails = $cartItems->values()->map(fn($item) => [
                'id'       => (string) $item->product->id,
                'price'    => (int) $item->product->price,
                'quantity' => (int) $item->quantity,
                'name'     => mb_substr($item->product->name, 0, 50),
            ])->toArray();

            $itemDetails[] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $shippingCost,
                'quantity' => 1,
                'name'     => 'Biaya Pengiriman',
            ];

            $params = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? $request->shipping_phone,
                ],
                'item_details' => $itemDetails,
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
                // Inform user that token was not available (friendly message)
                $redirect = $redirect->with('warning', 'Terjadi masalah koneksi ke Midtrans. Pesanan berhasil dibuat, silakan coba Generate Token pembayaran pada halaman berikut atau hubungi admin.');
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items');
        $snapToken = $order->midtrans_snap_token;

        return view('customer.checkout-success', compact('order', 'snapToken'));
    }

    /**
     * Attempt to get Midtrans Snap token using HTTP client with limited retries and timeout.
     * Returns token string on success or null on failure.
     */
    private function attemptGetSnapToken(array $params, int $maxAttempts = 2, int $timeoutSeconds = 6): ?string
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
                    if (!empty($data['token'])) {
                        return $data['token'];
                    }
                    Log::warning("Midtrans response but no token (attempt {$attempt}): " . json_encode($data));
                } else {
                    Log::warning("Midtrans HTTP error (attempt {$attempt}): HTTP {$response->status()} - " . $response->body());
                }
            } catch (\Exception $ex) {
                Log::warning("Midtrans connection attempt {$attempt} failed: " . $ex->getMessage());
            }

            // short backoff before retrying
            if ($attempt < $maxAttempts) {
                sleep(1);
            }
        }

        return null;
    }
}
