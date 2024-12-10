<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_approved' => false, // Set status persetujuan menjadi false saat pendaftaran
        ]);

        // Kembalikan respons dengan status dan user ID
        return response()->json([
            'status' => true,
            'message' => "Registration successful. Please wait for admin approval.",
            'userId' => $user->id // Kembalikan ID pengguna
        ]);
    }

    // Metode untuk memeriksa persetujuan
    public function checkApproval($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json(['approved' => $user->is_approved]);
    }

    
    // Metode untuk menyetujui pengguna
    public function approveUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->is_approved = true;
        $user->approved_at = now(); // Set waktu persetujuan
        $user->save();

        return response()->json(['message' => 'User approved successfully.']);
    }
}
