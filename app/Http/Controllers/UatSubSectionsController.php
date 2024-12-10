<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UatSubSection;
use App\Models\UatSection;

class UatSubSectionController extends Controller
{
    // Mendapatkan semua sub_sections untuk section tertentu
    public function index($sectionId)
    {
        $section = UatSection::with('subSections')->findOrFail($sectionId);
        return response()->json($section->subSections);
    }

    // Menyimpan sub_section baru untuk section tertentu
    public function store(Request $request, $sectionId)
    {
        $validated = $request->validate([
            'sub_section' => 'required|string',
            'url' => 'nullable|url',
            'cms_admin_panel' => 'nullable|string',
            'test_result' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $section = UatSection::findOrFail($sectionId);
        $subSection = $section->subSections()->create($validated);

        return response()->json($subSection, 201);
    }

    // Mengupdate sub_section tertentu
    public function update(Request $request, $subSectionId)
    {
        $validated = $request->validate([
            'sub_section' => 'required|string',
            'url' => 'nullable|url',
            'cms_admin_panel' => 'nullable|string',
            'test_result' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $subSection = UatSubSection::findOrFail($subSectionId);
        $subSection->update($validated);

        return response()->json($subSection);
    }

    // Menghapus sub_section tertentu
    public function destroy($subSectionId)
    {
        $subSection = UatSubSection::findOrFail($subSectionId);
        $subSection->delete();

        return response()->json(null, 204);
    }
}
