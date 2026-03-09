<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 5 produk terbaru dari database (gambar dari storage jika ada)
        $products = Product::latest()->take(5)->get();

        return view('customer.home', compact('products'));
    }

    public function products(Request $request)
    {
        $query = Product::where('is_available', true);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(12);

        $categories = Product::select('category')->distinct()->pluck('category');

        return view('customer.products', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $related = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->take(4)
            ->get();

        return view('customer.product-detail', compact('product', 'related'));
    }
}
