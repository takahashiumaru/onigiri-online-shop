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
            $perPage = $request->query('perPage');
            $validatedPerPage = (is_numeric($perPage) && $perPage > 0 && $perPage <= 100) ? (int) $perPage : 10;
            
            $query = Order::query()->with(['items.product', 'user:id,name,email']);

            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }

            $status = $request->query('status');
            if (is_string($status)) {
                $query->where('status', $status);
            }

            $paymentStatus = $request->query('payment_status');
            if (is_string($paymentStatus)) {
                $query->where('payment_status', $paymentStatus);
            }

            $orders = $query->latest()->paginate($validatedPerPage);

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
