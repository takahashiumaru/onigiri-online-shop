<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return static::paginatedResponse(Product::paginate(10));
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }
}
