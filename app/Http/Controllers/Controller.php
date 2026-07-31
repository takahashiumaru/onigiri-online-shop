<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public static function getVersion(): string
    {
        return json_decode(file_get_contents(base_path('composer.json')), true)['version'] ?? '1.0.0';
    }

    protected static function paginatedResponse($paginated)
    {
        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'pageSize' => $paginated->perPage(),
            'totalPages' => $paginated->lastPage(),
        ]);
    }

    protected static function reportResponse($paginated, $from, $to)
    {
        return response()->json([
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'pageSize' => $paginated->perPage(),
            'totalPages' => $paginated->lastPage(),
        ]);
    }

    protected static function getProductRatingStats($productId)
    {
        $q = \App\Models\OrderItem::where('product_id', $productId)
            ->whereNotNull('rating')
            ->whereHas('order', function ($q2) {
                $q2->where('status', 'delivered');
            });

        $avg = $q->avg('rating');
        $count = $q->count();

        return [
            'avg' => $avg ? round($avg, 1) : null,
            'count' => $count,
        ];
    }
}
