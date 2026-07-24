<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        // Remove cart items whose product was deleted
        $cartItems = $cartItems->filter(fn ($item) => $item->product !== null);

        $total = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('customer.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if (! $product->isInStock()) {
            return back()->with('error', 'Produk tidak tersedia atau stok habis.');
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $quantity = (int) $request->quantity;

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', "Stok tidak mencukupi. Tersisa: {$product->stock}, sudah di keranjang: {$cartItem->quantity}.");
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            // Default to 20 if quantity is 1 (standard "Buy" click) and it's a new item in cart
            if ($quantity === 1 && $product->stock >= 20) {
                $quantity = 20;
            }

            if ($quantity > $product->stock) {
                return back()->with('error', "Stok tidak mencukupi. Tersisa: {$product->stock}.");
            }

            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return back()->with('success', $product->name.' ditambahkan ke keranjang!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Manual ownership check instead of Gate/Policy
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $quantity = (int) $request->quantity;

        if ($quantity > $cartItem->product->stock) {
            return back()->with('error', "Stok tidak mencukupi. Tersisa: {$cartItem->product->stock}.");
        }

        $cartItem->update(['quantity' => $quantity]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
