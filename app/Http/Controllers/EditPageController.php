<?php

namespace App\Http\Controllers;

use App\Models\EditPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EditPageController extends Controller
{
    // Mengambil data edit page pertama
    public function index()
    {
        $editPage = EditPage::first();
        return response()->json($editPage);
    }

    // Memperbarui data navbar, card hero, dan hero title pada edit_page
    public function update(Request $request)
{
    // Validasi input untuk semua field yang diperlukan, termasuk card_hero_image sebagai nullable
    $request->validate([
        'navbar_title' => 'required|string|max:255',
        'navbar_menu1' => 'required|string|max:255',
        'navbar_menu2' => 'required|string|max:255',
        'navbar_menu3' => 'required|string|max:255',
        'card_hero_title' => 'required|string|max:255',
        'card_hero_text' => 'required|string|max:255',
        'hero_title' => 'required|string|max:255',
        'hero_text' => 'required|string|max:255',
        'card_hero_image' => 'nullable|string' // Pastikan card_hero_image bersifat nullable
    ]);

    try {
        // Mengambil halaman pertama yang ada
        $page = EditPage::first();

        if (!$page) {
            return response()->json(['error' => 'Page not found.'], 404);
        }

        // Mengambil data gambar dari input
        $imageData = $request->input('card_hero_image');

        // Jika gambar dihapus, pastikan menghapus gambar lama
        if ($imageData === null && $page->card_hero_image) {
            $oldImagePath = public_path($page->card_hero_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Hapus gambar lama
            }
            $page->card_hero_image = null; // Set kolom card_hero_image ke null
        }

        // Jika ada gambar baru yang diunggah, proses gambar tersebut
        if ($imageData && strpos($imageData, 'data:image/') === 0) {
            // Menangani data gambar base64
            if (strpos($imageData, 'data:image/jpeg;base64,') === 0) {
                $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
                $extension = 'jpg';
            } elseif (strpos($imageData, 'data:image/png;base64,') === 0) {
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $extension = 'png';
            } else {
                return response()->json(['message' => 'Unsupported image format'], 400);
            }

            // Decode data base64 dan simpan file
            $imageData = str_replace(' ', '+', $imageData);
            $imagePath = 'uploads/' . time() . '.' . $extension;
            $imageFullPath = public_path($imagePath);

            if (!file_put_contents($imageFullPath, base64_decode($imageData))) {
                return response()->json(['message' => 'Failed to save image'], 500);
            }

            // Update path gambar card_hero_image di database
            $page->card_hero_image = $imagePath;
        }

        // Memperbarui data lainnya
        $page->navbar_title = $request->input('navbar_title');
        $page->navbar_menu1 = $request->input('navbar_menu1');
        $page->navbar_menu2 = $request->input('navbar_menu2');
        $page->navbar_menu3 = $request->input('navbar_menu3');
        $page->card_hero_title = $request->input('card_hero_title');
        $page->card_hero_text = $request->input('card_hero_text');
        $page->hero_title = $request->input('hero_title');
        $page->hero_text = $request->input('hero_text');

        // Simpan perubahan
        $page->save();

        Log::info('Page updated successfully:', ['page' => $page]);

        return response()->json(['message' => 'Page updated successfully']);
    } catch (\Exception $e) {
        Log::error('Failed to update page:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to update page.'], 500);
    }
}

}
