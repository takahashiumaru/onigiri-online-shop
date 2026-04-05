<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'shipped');
        
        $orders = Order::with('user', 'items.product')
            ->where('courier_id', auth()->id())
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('courier.dashboard', compact('orders', 'status'));
    }

    public function show(Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }
        $order->load(['items.product', 'user']);
        return view('courier.order-detail', compact('order'));
    }

    public function updateDelivery(Request $request, Order $order)
    {
        if ($order->courier_id !== auth()->id()) {
            abort(403);
        }

        // Only allow delivery update for orders currently in shipping mode
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Status pesanan tidak sesuai untuk diperbarui.');
        }

        $path = null;

        // Priority 1: Base64 from Camera API
        if ($request->filled('proof_of_delivery_base64')) {
            $imageData = $request->input('proof_of_delivery_base64');
            if (str_contains($imageData, 'data:image')) {
                $imageArray = explode(',', $imageData);
                $decodedImage = base64_decode($imageArray[1]);
                $fileName = 'proofs/' . uniqid() . '.jpg';
                
                Storage::disk('public')->put($fileName, $decodedImage);
                $path = $fileName;
            }
        } 
        
        // Priority 2: Standard file upload fallback
        if (!$path && $request->hasFile('proof_of_delivery')) {
            $request->validate([
                'proof_of_delivery' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);
            $path = $request->file('proof_of_delivery')->store('proofs', 'public');
        }

        if ($path) {
            $order->update([
                'status' => 'delivered',
                'proof_of_delivery' => $path,
            ]);

            return redirect()->route('courier.dashboard')->with('success', 'Pesanan berhasil diselesaikan dengan bukti pengiriman.');
        }

        return back()->with('error', 'Silakan ambil foto bukti pengiriman terlebih dahulu.');
    }
}
