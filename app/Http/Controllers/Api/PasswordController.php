<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Update the authenticated user's password.
     */
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            $user = $request->user();

            if (! $user || ! Hash::check($data['current_password'], $user->password)) {
                return self::errorResponse('Password saat ini tidak sesuai.', 422);
            }

            $user->password = Hash::make($data['new_password']);
            $user->save();

            return response()->json(['message' => 'Password berhasil diperbarui.']);
        } catch (\Throwable $e) {
            return self::handleApiError($e, 'Gagal memperbarui password.');
        }
    }
}
