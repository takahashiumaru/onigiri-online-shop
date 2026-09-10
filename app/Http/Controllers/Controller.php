<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public static function getVersion(): string
    {
        return json_decode(file_get_contents(base_path('composer.json')), true)['version'] ?? '1.0.0';
    }

    protected static function errorResponse(string $message, int $status = 400)
    {
        return response()->json(['error' => $message], $status);
    }

    protected static function paginatedResponse(\Illuminate\Pagination\LengthAwarePaginator $paginated, array $extra = []): \Illuminate\Http\JsonResponse
    {
        $response = [
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'pageSize' => $paginated->perPage(),
            'totalPages' => $paginated->lastPage(),
        ];

        return response()->json(array_merge($response, $extra));
    }

    /**
     * Generate standardized report response.
     */
    protected static function reportResponse(\Illuminate\Pagination\LengthAwarePaginator $paginated, \Carbon\Carbon $from, \Carbon\Carbon $to): \Illuminate\Http\JsonResponse
    {
        return self::paginatedResponse($paginated, [
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
            'summary' => [
                'totalRevenue' => (int) $paginated->getCollection()->sum('total'),
                'orderCount' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Get aggregated rating stats for a product.
     */
    protected static function getProductRatingStats(int $productId): array
    {
        $stats = \App\Models\OrderItem::where('product_id', $productId)
            ->whereNotNull('rating')
            ->whereHas('order', fn ($q) => $q->where('status', 'delivered'))
            ->selectRaw('AVG(rating) as avg_rating, COUNT(id) as rating_count')
            ->first();

        return [
            'avg' => $stats && $stats->avg_rating ? (float) round($stats->avg_rating, 1) : null,
            'count' => $stats ? (int) $stats->rating_count : 0,
        ];
    }

    /**
     * Handle and standardize API exceptions.
     */
    protected static function handleApiError(\Throwable $e, string $defaultMessage = 'Terjadi kesalahan internal.')
    {
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return self::errorResponse('Resource tidak ditemukan.', 404);
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'error' => 'Validasi gagal.',
                'messages' => $e->errors(),
            ], 422);
        }

        \Illuminate\Support\Facades\Log::error($e->getMessage(), [
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString(),
        ]);

        return self::errorResponse($defaultMessage, 500);
    }
}
