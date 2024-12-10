<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserController extends Controller
{
    public function store(Request $request)
{
    // Validate incoming request
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:8',
        'role' => 'required|string|in:user,admin,superadmin',
        'profile_image' => 'nullable|string', // Ensure this is optional
    ]);

    $user = new User;
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->password = bcrypt($validatedData['password']);
    $user->role = $validatedData['role'];

    // Set default values for is_active and is_approved
    $user->is_active = '1'; // Set is_active to '1'
    $user->is_approved = '1'; // Set is_approved to '1'

    // Handle profile image upload
    if (!empty($validatedData['profile_image'])) {
        $image = $validatedData['profile_image'];
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '.jpg';
        $path = public_path('uploads/' . $imageName);
        file_put_contents($path, base64_decode($image));
        $user->profile_image = 'uploads/' . $imageName;
    }

    $user->save();

    return response()->json(['message' => 'User added successfully']);
}

    public function Profile()
    {
        // $user = Auth::user();
        // return response()->json([
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'role' => $user->role, // Make sure the role is defined in your users table
        //     'photoUrl' => $user->photo_url // Assuming you have a photo_url field
        // ]);
        return response()->json($request->user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout successful']);
    }

    public function index()
{
    try {
        $users = User::all();
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan saat mengambil data pengguna.'], 500);
    }
}

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        \Log::info('Update request data:', $request->all()); // Log request data
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|string|in:user,admin,superadmin',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string'
        ]);
    
        $user = auth()->user();
    
        // Check if a new profile image is provided
        $photoData = $request->input('profile_image');
        if ($photoData && strpos($photoData, 'data:image/') === 0) {
            // Determine the image type (JPEG, PNG, etc.)
            if (strpos($photoData, 'data:image/jpeg;base64,') === 0) {
                $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
                $extension = 'jpg';
            } elseif (strpos($photoData, 'data:image/png;base64,') === 0) {
                $photoData = str_replace('data:image/png;base64,', '', $photoData);
                $extension = 'png';
            } else {
                return response()->json(['message' => 'Unsupported image format'], 400);
            }
    
            $photoData = str_replace(' ', '+', $photoData);
            $photoPath = 'uploads/' . time() . '.' . $extension;
            $fullPath = public_path($photoPath);
    
            // Ensure the directory exists
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
    
            // Save the new profile image
            file_put_contents($fullPath, base64_decode($photoData));
            $user->profile_image = $photoPath; // Update the profile image path
        }
    
        // Update user profile details
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->bio = $request->input('bio');
        // Save the updated user data
        $user->save();
    
        \Log::info('User profile updated:', $user->toArray()); // Log updated user data
    
        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function removeProfileImage()
    {
        $user = auth()->user();
        if ($user->profile_image) {
            // Hapus file dari server
            $imagePath = public_path($user->profile_image);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Hapus file
            }
            $user->profile_image = null; // Set image path ke null
            $user->save(); // Simpan perubahan
    
            return response()->json(['message' => 'Profile photo removed successfully']);
        }
    
        return response()->json(['message' => 'No profile photo to remove'], 404);
    }    


    public function updateUser(Request $request, $id)
    {
        \Log::info('Update request data:', $request->all()); // Log request data
    
        // Validasi input menggunakan 'sometimes' untuk tidak memerlukan semua field
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'role' => 'sometimes|required|string|in:user,admin',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|string'
        ]);
        // Cari pengguna berdasarkan ID
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Cek dan simpan gambar profil jika ada
        if ($request->has('profile_image')) {
            $photoData = $request->input('profile_image');
            if (strpos($photoData, 'data:image/') === 0) {
                if (strpos($photoData, 'data:image/jpeg;base64,') === 0) {
                    $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
                    $extension = 'jpg';
                } elseif (strpos($photoData, 'data:image/png;base64,') === 0) {
                    $photoData = str_replace('data:image/png;base64,', '', $photoData);
                    $extension = 'png';
                } else {
                    return response()->json(['message' => 'Unsupported image format'], 400);
                }
    
                $photoData = str_replace(' ', '+', $photoData);
                $photoPath = 'uploads/' . time() . '.' . $extension;
                $fullPath = public_path($photoPath);
    
                // Pastikan direktori ada
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
    
                // Simpan gambar baru
                file_put_contents($fullPath, base64_decode($photoData));
                $user->profile_image = $photoPath; // Update path gambar profil
            }
        }
    
        // Memperbarui hanya field yang ada dalam request
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        if ($request->has('role')) {
            $user->role = $request->input('role');
        }
        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }
    
        // Simpan data pengguna yang diperbarui
        $user->save();
    
        \Log::info('User profile updated:', $user->toArray()); // Log updated user data
    
        return response()->json(['message' => 'Profile updated successfully']);
    }
    

public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete user'], 500);
    }
}

// UserController.php
public function suspend($id)
{
    $user = User::findOrFail($id);
    $user->is_active = false; // Set is_active ke false untuk menangguhkan akun
    $user->save();

    return response()->json([
        'status' => true,
        'message' => "User account suspended successfully."
    ]);
}

public function activate($id)
{
    $user = User::findOrFail($id);
    
    // Set is_active dan is_approved ke true
    $user->is_active = true; 
    $user->is_approved = true; // Mengatur is_approved ke true
    $user->save();

    return response()->json([
        'status' => true,
        'message' => "User account activated successfully."
    ]);
}



// Dalam UserController.php
public function checkApproval($id)
{
    $user = User::find($id); // Mencari pengguna berdasarkan ID

    if ($user) {
        return response()->json(['approved' => $user->is_approved]); // Mengembalikan status persetujuan
    } else {
        return response()->json(['error' => 'User not found'], 404);
    }
}


// Dalam metode yang digunakan untuk menyetujui pengguna
public function approveUser($userId)
{
    $user = User::find($userId);
    if ($user) {
        $user->is_approved = true; // Set status persetujuan
        $user->approved_at = now(); // Set waktu persetujuan
        $user->save();
    }

    return response()->json(['message' => 'User approved successfully.']);
}


}
