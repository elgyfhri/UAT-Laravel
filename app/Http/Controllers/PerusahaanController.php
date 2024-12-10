<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PerusahaanController extends Controller
{
    public function index()
    {
        return response()->json(Perusahaan::all());
    }

    public function list()
    {
        return response()->json(Perusahaan::select('id', 'name')->get());
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi untuk file gambar
            'config_document' => 'nullable|string'
        ]);

        try {
            // Buat instance perusahaan baru
            $perusahaan = new Perusahaan();
            $perusahaan->name = $request->input('name');
            $perusahaan->short_name = $request->input('short_name');
            $perusahaan->website = $request->input('website');
            $perusahaan->address = $request->input('address');
            $perusahaan->config_document = $request->input('config_document');

            // Proses upload logo jika ada
            if ($request->hasFile('logo')) {
                $logoName = time() . '.' . $request->file('logo')->extension();
                $request->file('logo')->storeAs('public/images', $logoName);
                $perusahaan->logo = 'images/' . $logoName;
            }

            // Simpan ke database
            $perusahaan->save();
            
            Log::info('Perusahaan created successfully:', ['perusahaan' => $perusahaan]);

            return response()->json(['message' => 'Perusahaan created successfully'], 201);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan perusahaan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menyimpan perusahaan.'], 500);
        }
    }

    public function show($id)
    {
        $perusahaan = Perusahaan::find($id);

        if (!$perusahaan) {
            return response()->json(['error' => 'Perusahaan tidak ditemukan'], 404);
        }

        return response()->json($perusahaan);
    }

    public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'short_name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'website' => 'nullable|url', // Pastikan validasi website sudah benar
        'logo' => 'nullable|string',
        'config_document' => 'required|string'
    ]);

    try {
        // Cari perusahaan berdasarkan ID
        $perusahaan = Perusahaan::findOrFail($id);

        // Update data perusahaan
        $perusahaan->name = $request->input('name');
        $perusahaan->short_name = $request->input('short_name');
        $perusahaan->address = $request->input('address');
        $perusahaan->website = $request->input('website'); // Pastikan website diupdate
        $perusahaan->config_document = $request->input('config_document');

        // Proses update logo jika ada
        if ($request->input('logo')) {
            $base64Image = $request->input('logo');
            $imageData = explode(',', $base64Image);
            $imageExtension = 'png'; // Default extension, adjust as needed
            $imageContent = base64_decode($imageData[1]);
            $logoName = time() . '.' . $imageExtension;
            Storage::disk('public')->put('images/' . $logoName, $imageContent);
            $perusahaan->logo = 'images/' . $logoName;
        }

        // Simpan perubahan ke database
        $perusahaan->save();
        
        Log::info('Perusahaan updated successfully:', ['perusahaan' => $perusahaan]);

        return response()->json(['message' => 'Perusahaan updated successfully']);

    } catch (\Exception $e) {
        Log::error('Gagal mengupdate perusahaan:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Gagal mengupdate perusahaan.'], 500);
    }
}



    public function destroy($id)
    {
        try {
            // Cari perusahaan berdasarkan ID
            $perusahaan = Perusahaan::findOrFail($id);

            // Hapus logo terkait jika ada
            if ($perusahaan->logo && Storage::disk('public')->exists($perusahaan->logo)) {
                Storage::disk('public')->delete($perusahaan->logo);
            }

            // Hapus perusahaan dari database
            $perusahaan->delete();
            
            Log::info('Perusahaan deleted successfully:', ['perusahaan' => $perusahaan]);

            return response()->json(['message' => 'Perusahaan deleted successfully.']);

        } catch (\Exception $e) {
            Log::error('Gagal menghapus perusahaan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menghapus perusahaan.'], 500);
        }
    }

    // Di PerusahaanController.php
public function getConfigDocument($id)
{
    $perusahaan = Perusahaan::find($id);
    if ($perusahaan) {
        return response()->json(['config_document' => $perusahaan->config_document]);
    }
    return response()->json(['error' => 'Perusahaan tidak ditemukan'], 404);
}

public function cekUAT($id)
{
    $perusahaan = Perusahaan::find($id);
    $terkaitUAT = $perusahaan && $perusahaan->uat()->exists();

    return response()->json(['terkaitUAT' => $terkaitUAT]);
}



}
