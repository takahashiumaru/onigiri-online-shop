<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) ($request->query('perPage') ?? 10);
            $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 10;

            $query = Product::query();

            if ($request->filled('category')) {
                $category = (string) $request->query('category');
                $query->where('category', $category);
            }

            if ($request->filled('search')) {
                $search = (string) $request->query('search');
                $query->where('name', 'like', '%'.$search.'%');
            }

            if ($request->has('is_available')) {
                $isAvailable = filter_var($request->query('is_available'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($isAvailable !== null) {
                    $query->where('is_available', $isAvailable);
                }
            }

            if ((string) $request->query('include') === 'ratings') {
                $query->withRatings();
            }

            $products = $query->paginate($perPage);

            return static::paginatedResponse($products);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat daftar produk.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            if ((string) $request->query('include') === 'ratings') {
                $stats = static::getProductRatingStats($product->id);
                $product->rating_avg = $stats['avg'];
                $product->rating_count = $stats['count'];
            }

            return response()->json($product);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat detail produk.');
        }
    }

    public function categories()
    {
        try {
            $categories = Product::whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            return response()->json([
                'data' => $categories,
                'total' => count($categories),
            ]);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat daftar kategori.');
        }
    }
}
