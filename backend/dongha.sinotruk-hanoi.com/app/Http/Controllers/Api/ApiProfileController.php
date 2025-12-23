<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiProfileController extends Controller
{
    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        // For now, get user by username from request header or session
        $username = $request->header('X-Admin-Username') ?? $request->input('username');

        if (!$username) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('name', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'username' => $user->name,
            'full_name' => $user->full_name,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'admin' => $user->admin,
        ]);
    }

    /**
     * Update current user profile
     */
    public function update(Request $request)
    {
        $username = $request->header('X-Admin-Username') ?? $request->input('username');

        if (!$username) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('name', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update allowed fields
        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }
        if ($request->has('avatar')) {
            $user->avatar = $request->avatar;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'username' => $user->name,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ]
        ]);
    }
}
