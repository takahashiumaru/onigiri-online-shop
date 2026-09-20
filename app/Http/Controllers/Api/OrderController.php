<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get list of orders for authenticated user or all orders (if admin).
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = (int) ($request->query('perPage') ?? 10);
            $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 10;

            $query = Order::query()->with(['items.product', 'user:id,name,email']);

            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->query('status'));
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->query('payment_status'));
            }

            $orders = $query->latest()->paginate($perPage);

            return static::paginatedResponse($orders);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat daftar pesanan.');
        }
    }

    /**
     * Get single order detail.
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $query = Order::with(['items.product', 'courier:id,name,phone', 'user:id,name,email']);

            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }

            $order = $query->findOrFail($id);

            return static::successResponse($order);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return static::errorResponse('Pesanan tidak ditemukan.', 404);
        } catch (\Throwable $e) {
            return static::handleApiError($e, 'Gagal memuat detail pesanan.');
        }
    }
}
