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
}
