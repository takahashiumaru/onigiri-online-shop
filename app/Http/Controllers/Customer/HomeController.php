<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::where('is_available', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $categories = Product::where('is_available', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('customer.home', compact('featured', 'categories'));
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
