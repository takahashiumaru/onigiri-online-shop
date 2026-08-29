<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->query('perPage') ?? 10);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 10;

        $query = Product::query();

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->query('search') . '%');
        }

        if ($request->query('include') === 'ratings') {
            $query->withRatings();
        }

        $products = $query->paginate($perPage);

        return static::paginatedResponse($products);
    }

    public function show(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            if ($request->query('include') === 'ratings') {
                $stats = self::getProductRatingStats($product->id);
                $product->rating_avg = $stats['avg'];
                $product->rating_count = $stats['count'];
            }

            return response()->json($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return self::errorResponse('Produk tidak ditemukan.', 404);
        }
    }
}
