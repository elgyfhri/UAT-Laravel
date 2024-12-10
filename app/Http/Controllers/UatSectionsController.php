<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UatSection;
use App\Models\UatPage;

class UatSectionController extends Controller
{
    // Mendapatkan semua sections untuk halaman UAT tertentu
    public function index($uatPageId)
    {
        $uatPage = UatPage::with('sections')->findOrFail($uatPageId);
        return response()->json($uatPage->sections);
    }

    // Menyimpan section baru untuk UAT page tertentu
    public function store(Request $request, $uatPageId)
    {
        $validated = $request->validate([
            'section_on_pages' => 'required|string',
            'url' => 'nullable|url',
            'cms_admin_panel' => 'nullable|string',
            'test_result' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $uatPage = UatPage::findOrFail($uatPageId);
        $section = $uatPage->sections()->create($validated);

        return response()->json($section, 201);
    }

    // Mengupdate section tertentu
    public function update(Request $request, $sectionId)
    {
        $validated = $request->validate([
            'section_on_pages' => 'required|string',
            'url' => 'nullable|url',
            'cms_admin_panel' => 'nullable|string',
            'test_result' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $section = UatSection::findOrFail($sectionId);
        $section->update($validated);

        return response()->json($section);
    }

    // Menghapus section tertentu
    public function destroy($sectionId)
    {
        $section = UatSection::findOrFail($sectionId);
        $section->delete();

        return response()->json(null, 204);
    }
}
