<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::all());
    }

    public function list()
    {
        return response()->json(Client::select('id', 'name')->get());
    }
    
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|string', // Validasi logo sebagai string base64
        ]);

        try {
            // Buat instance client baru
            $client = new Client();
            $client->name = $request->input('name');
            $client->short_name = $request->input('short_name');
            $client->address = $request->input('address');

            // Proses upload logo jika ada
            if ($request->input('logo')) {
                $base64Image = $request->input('logo');
                $imageData = explode(',', $base64Image);
                $imageExtension = 'png'; // Default extension, adjust as needed
                $imageContent = base64_decode($imageData[1]);
                $logoName = time() . '.' . $imageExtension;
                Storage::disk('public')->put('images/' . $logoName, $imageContent);
                $client->logo = 'images/' . $logoName;
            }

            // Simpan ke database
            $client->save();
            
            Log::info('Client created successfully:', ['client' => $client]);

            return response()->json(['message' => 'Client created successfully'], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create client:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create client.'], 500);
        }
    }

    public function show($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|string',
        ]);

        try {
            // Cari client berdasarkan ID
            $client = Client::findOrFail($id);

            // Update data client
            $client->name = $request->input('name');
            $client->short_name = $request->input('short_name');
            $client->address = $request->input('address');

            // Proses update logo jika ada
            if ($request->input('logo')) {
                // Hapus logo lama jika ada
                if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                    Storage::disk('public')->delete($client->logo);
                }
                
                $base64Image = $request->input('logo');
                $imageData = explode(',', $base64Image);
                $imageExtension = 'png'; // Default extension, adjust as needed
                $imageContent = base64_decode($imageData[1]);
                $logoName = time() . '.' . $imageExtension;
                Storage::disk('public')->put('images/' . $logoName, $imageContent);
                $client->logo = 'images/' . $logoName;
            }

            // Simpan perubahan ke database
            $client->save();
            
            Log::info('Client updated successfully:', ['client' => $client]);

            return response()->json(['message' => 'Client updated successfully']);

        } catch (\Exception $e) {
            Log::error('Failed to update client:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update client.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Cari client berdasarkan ID
            $client = Client::findOrFail($id);

            // Hapus logo terkait jika ada
            if ($client->logo && Storage::disk('public')->exists($client->logo)) {
                Storage::disk('public')->delete($client->logo);
            }

            // Hapus client dari database
            $client->delete();
            
            Log::info('Client deleted successfully:', ['client' => $client]);

            return response()->json(['message' => 'Client deleted successfully.']);

        } catch (\Exception $e) {
            Log::error('Failed to delete client:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete client.'], 500);
        }
    }
    public function cekUAT($id)
{
    $client = Client::find($id);
    $terkaitUAT = $client && $client->uat()->exists();

    return response()->json(['terkaitUAT' => $terkaitUAT]);
}

}
