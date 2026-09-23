<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Get paginated list of couriers.
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) ($request->query('perPage') ?? 10);
            $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 10;

            $couriers = User::where('role', 'courier')
                ->select('id', 'name', 'email', 'phone', 'address', 'photo', 'created_at')
                ->latest()
                ->paginate($perPage);

            return static::paginatedResponse($couriers);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat daftar kurir.');
        }
    }

    /**
     * Get single courier details.
     */
    public function show($id)
    {
        try {
            $courier = User::where('role', 'courier')
                ->select('id', 'name', 'email', 'phone', 'address', 'photo', 'created_at')
                ->findOrFail($id);

            return response()->json($courier);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return static::errorResponse('Kurir tidak ditemukan.', 404);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat detail kurir.');
        }
    }
}
