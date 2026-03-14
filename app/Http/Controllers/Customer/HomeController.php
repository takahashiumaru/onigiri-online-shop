<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\OrderItem;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil 5 produk terbaru dari database (gambar dari storage jika ada)
        $products = Product::latest()->take(5)->get();

        // hitung statistik ulasan untuk produk yang tampil (hanya dari order 'delivered')
        $productStats = [];
        foreach ($products as $p) {
            $q = OrderItem::where('product_id', $p->id)
                ->whereNotNull('rating')
                ->whereHas('order', function ($q2) {
                    $q2->where('status', 'delivered');
                });
            $avg = $q->avg('rating');
            $count = $q->count();
            $productStats[$p->id] = [
                'avg' => $avg ? round($avg, 1) : null,
                'count' => $count,
            ];
        }

        return view('customer.home', compact('products', 'productStats'));
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

        // compute rating stats for products on this page
        $productStats = [];
        $pageCollection = $products instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $products->getCollection()
            : collect($products);
        foreach ($pageCollection as $p) {
            $q = OrderItem::where('product_id', $p->id)
                ->whereNotNull('rating')
                ->whereHas('order', function ($q2) {
                    $q2->where('status', 'delivered');
                });
            $avg = $q->avg('rating');
            $count = $q->count();
            $productStats[$p->id] = [
                'avg' => $avg ? round($avg, 1) : null,
                'count' => $count,
            ];
        }

        return view('customer.products', compact('products', 'categories', 'productStats'));
    }

    public function show(Product $product)
    {
        // ambil review dari order yang sudah 'delivered' dan memiliki rating
        $reviews = OrderItem::with(['order'])
            ->where('product_id', $product->id)
            ->whereNotNull('rating')
            ->whereHas('order', function ($q) {
                $q->where('status', 'delivered');
            })
            ->orderByDesc('updated_at')
            ->get();

        return view('customer.product-detail', compact('product', 'reviews'));
    }
}
