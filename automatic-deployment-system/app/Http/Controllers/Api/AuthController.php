<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! Auth::attempt($request->only('nim', 'password'))) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('nim', $request->nim)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            "data" => [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'logout success'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'string|required',
            'new_password' => 'string|required|min:6',
            'confirm_password' => 'string|required|same:new_password',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'confirm_password.same' => 'Konfirmasi password harus sama dengan password.',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Password lama tidak sesuai.'
            ], 401);
        }

        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Password baru tidak boleh sama dengan password lama.'
            ], 403);
        }

        try {
            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Terjadi kesalahan saat mengubah password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
