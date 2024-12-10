<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UatPage;
use App\Models\UatSection;
use App\Models\UatSubSection;
use App\Http\Resources\UatPagesResource;                            
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;



class UatPagesController extends Controller
{
    // Mendapatkan semua halaman UAT dengan sections dan sub_sections
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $uatPages = UatPage::with('sections.subSections')->latest()->paginate(5);

        return new UatPagesResource(true, 'List Data UAT', $uatPages);
    }
    
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */

    // Store UAT Pages
    public function store(Request $request)
{
    // Menyimpan halaman UAT
    $pageData = $request->pages[0]; // Ambil halaman pertama
    $uatPage = UatPage::create([
        'pages' => $pageData['pages'],
        'url' => $pageData['url'],
        'cms_admin_panel' => $pageData['cms_admin_panel'],
        'test_result' => $pageData['test_result'] ?? null,
        'note' => $pageData['note'] ?? null,
    ]);

    // Menyimpan sections terkait halaman
    foreach ($pageData['sections'] as $sectionData) {
        $uatSection = UatSection::create([
            'page_id' => $uatPage->id,
            'section_on_pages' => $sectionData['section_on_pages'],
            'url' => $sectionData['url'],
            'cms_admin_panel' => $sectionData['cms_admin_panel'],
            'test_result' => $sectionData['test_result'] ?? null,
            'note' => $sectionData['note'] ?? null,
        ]);

        // Menyimpan sub-sections terkait section
        foreach ($sectionData['sub_sections'] as $subSectionData) {
            UatSubSection::create([
                'section_id' => $uatSection->id, // Pastikan nama kolom sesuai
                'sub_section' => $subSectionData['sub_section'],
                'url' => $subSectionData['url'],
                'cms_admin_panel' => $subSectionData['cms_admin_panel'],
                'test_result' => $subSectionData['test_result'] ?? null,
                'note' => $subSectionData['note'] ?? null,
            ]);
        }
    }

    return response()->json(['message' => 'Data berhasil disimpan']);
}

                      



    // Update UAT Page
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pages' => 'required|string',
            'url' => 'nullable|url',
            'cms_admin_panel' => 'nullable|string',
            'test_result' => 'nullable|string',
            'note' => 'nullable|string',
            'sections' => 'nullable|array',
            'sections.*.section_on_pages' => 'nullable|string',
            'sections.*.url' => 'nullable|url',
            'sections.*.cms_admin_panel' => 'nullable|string',
            'sections.*.test_result' => 'nullable|string',
            'sections.*.note' => 'nullable|string',
            'sections.*.sub_sections' => 'nullable|array',
            'sections.*.sub_sections.*.sub_section' => 'nullable|string',
            'sections.*.sub_sections.*.url' => 'nullable|url',
            'sections.*.sub_sections.*.cms_admin_panel' => 'nullable|string',
            'sections.*.sub_sections.*.test_result' => 'nullable|string',
            'sections.*.sub_sections.*.note' => 'nullable|string',
        ]);

        // Temukan halaman UAT
        $uatPage = UatPage::findOrFail($id);
        $uatPage->update([
            'pages' => $validated['pages'],
            'url' => $validated['url'],
            'cms_admin_panel' => $validated['cms_admin_panel'],
            'test_result' => $validated['test_result'],
            'note' => $validated['note'],
        ]);

        // Hapus dan update sections dan sub_sections
        $uatPage->sections()->delete();
        if (isset($validated['sections'])) {
            foreach ($validated['sections'] as $sectionData) {
                $section = $uatPage->sections()->create([
                    'section_on_pages' => $sectionData['section_on_pages'],
                    'url' => $sectionData['url'],
                    'cms_admin_panel' => $sectionData['cms_admin_panel'],
                    'test_result' => $sectionData['test_result'],
                    'note' => $sectionData['note'],
                ]);

                // Simpan SubSections
                if (isset($sectionData['sub_sections'])) {
                    foreach ($sectionData['sub_sections'] as $subSectionData) {
                        $section->subSections()->create($subSectionData);
                    }
                }
            }
        }

        // Mengembalikan data yang diperbarui sebagai resource
        return new UatPagesResource(true, 'Data UAT Berhasil Diperbarui!', $uatPage);
    }

    // Hapus UAT Page
    public function destroy($id)
    {
        $uatPage = UatPage::findOrFail($id);
        $uatPage->delete();

        // Mengembalikan response yang menunjukkan penghapusan berhasil
        return response()->json(['message' => 'Data UAT Berhasil Dihapus!'], 200);
    }
}
