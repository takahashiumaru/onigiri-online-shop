<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderItemRatingController extends Controller
{
    public function update(Request $request, OrderItem $item)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'rating_review' => 'nullable|string|max:2000',
        ]);

        // ensure current user owns the order that contains this item
        $order = $item->order ?? null;
        $ownerId = $order->user_id ?? $order->customer_id ?? null;

        if (!$order || $ownerId != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->rating = $data['rating'];
        $item->rating_review = $data['rating_review'] ?? null;
        $item->save();

        return response()->json([
            'message' => 'Rating tersimpan',
            'rating' => $item->rating,
            'rating_review' => $item->rating_review,
        ]);
    }
}
