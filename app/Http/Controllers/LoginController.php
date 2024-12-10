<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function check(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) 
        {
            $user = Auth::user();

            // Tambahkan pengecekan is_active
            if (!$user->is_active) {
                Auth::logout(); // Logout otomatis jika akun belum diizinkan
                return response()->json([
                    'status' => false,
                    'message' => "Akun Anda belum diizinkan oleh admin."
                ], 403); // Kode status 403 Forbidden
            }

            // Buat atau ambil token API
            $token = $user->api_token ?: Str::random(60);
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => "Success",
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => "Invalid credentials"
        ], 401); // Status 401 Unauthorized jika kredensial salah
    }
}

