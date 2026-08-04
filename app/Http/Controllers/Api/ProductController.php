<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::paginate(10);

        if ($request->query('include') === 'ratings') {
            $products->getCollection()->transform(function ($product) {
                $stats = self::getProductRatingStats($product->id);
                $product->rating_avg = $stats['avg'];
                $product->rating_count = $stats['count'];

                return $product;
            });
        }

        return static::paginatedResponse($products);
    }

    public function show($id)
    {
        try {
            return response()->json(Product::findOrFail($id));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return self::errorResponse('Produk tidak ditemukan.', 404);
        }
    }
}
