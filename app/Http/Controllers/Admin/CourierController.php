<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $couriers = User::where('role', 'courier')->latest()->paginate(10);
        return view('admin.couriers.index', compact('couriers'));
    }

    public function create()
    {
        return view('admin.couriers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('couriers', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'courier',
        ]);

        return redirect()->route('admin.couriers.index')->with('success', 'Kurir berhasil ditambahkan.');
    }

    public function edit(User $courier)
    {
        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, User $courier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $courier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($courier->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($courier->photo);
            }
            $data['photo'] = $request->file('photo')->store('couriers', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $courier->update($data);

        return redirect()->route('admin.couriers.index')->with('success', 'Data kurir berhasil diperbarui.');
    }

    public function destroy(User $courier)
    {
        // Nullify courier_id in orders before testing if they can be deleted
        $courier->delete();
        return redirect()->route('admin.couriers.index')->with('success', 'Kurir berhasil dihapus.');
    }
}
