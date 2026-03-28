<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ModeratorController extends Controller
{
    /**
     * Display all admins.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return response()->json([
            'admins' => $admins
        ]);
    }

    /**
     * Toggle admin active status.
     */
    public function toggleActive(Request $request, $id)
    {
        $admin = User::find($id);

        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Admin not found.'], 404);
        }

        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $admin->is_active = $request->is_active;
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin status updated successfully.',
            'admin' => $admin
        ]);
    }

    /**
     * Add a new admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'phone_number' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);

        $admin = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin added successfully.',
            'admin' => $admin
        ], 201);
    }

    /**
     * Show specific admin.
     */
    public function show($id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'admin' => $admin
        ]);
    }

    /**
     * Update admin details.
     */
    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found.'], 404);
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
        ]);

        $admin->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully.',
            'admin' => $admin
        ]);
    }

    /**
     * Delete an admin.
     */
    public function destroy($id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found.'], 404);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully.'
        ]);
    }
}