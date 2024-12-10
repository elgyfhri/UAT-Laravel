<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UatPage;
use App\Models\UatSection;
use App\Models\UatSubSection;
use Carbon\Carbon;
use App\Models\Uat;  // If Uat is a model


class UATController extends Controller
{
    // Mendapatkan semua halaman UAT
    public function index()
    {
        $uats = Uat::with(['perusahaan', 'client'])->get();
        return response()->json($uats);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Debugging log
            \Log::info('Data yang diterima:', $request->all());

            // Validasi input
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'revision_number' => 'nullable|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'user_info' => 'required|array',
                'user_info.username' => 'required|string|max:255',
                'user_info.password' => 'required|string|max:255',
                'client_id' => 'nullable|exists:clients,id', // Ubah required menjadi nullable
                'perusahaan_id' => 'nullable|exists:perusahaans,id', // Ubah required menjadi nullable
                'config_document' => 'nullable|string|max:255',
                'progress_percentage' => 'required|integer|min:0|max:100',
                'pages' => 'array',
                'pages.*.pages' => 'nullable|string|max:255',
                'pages.*.sections' => 'array',
                'pages.*.sections.*.section_on_pages' => 'required|string|max:255',
                'pages.*.sections.*.sub_sections' => 'array',
                'pages.*.sections.*.sub_sections.*.sub_section' => 'required|string|max:255',
            ]);

            // Simpan data UAT Project dengan tambahan user_info, client_id, perusahaan_id, dan doc_name
            $uat = new Uat();
            $uat->project_name = $validated['project_name'];

            // Gabungkan username dan password menjadi satu JSON value
            $userInfo = [
                'username' => $validated['user_info']['username'],
                'password' => $validated['user_info']['password']
            ];
            $uat->user_info = json_encode($userInfo);  // Konversi menjadi JSON string

            // Set client_id, perusahaan_id, dan doc_name
            $uat->client_id = $validated['client_id'] ?? null; // Mengatur menjadi null jika tidak ada
            $uat->perusahaan_id = $validated['perusahaan_id'] ?? null; // Mengatur menjadi null jika tidak ada
            $uat->config_document = $validated['config_document'];
            $uat->revision_number = $validated['revision_number'];
            $uat->start_date = $validated['start_date'];
            $uat->end_date = $validated['end_date'];
            $uat->progress_percentage = $validated['progress_percentage'];

            $uat->save();

            // Simpan setiap Page
            foreach ($request->input('pages') as $pageData) {
                $uatPage = new UatPage();
                $uatPage->uat_id = $uat->id;
                $uatPage->pages = $pageData['pages'];
                $uatPage->url = $pageData['url'] ?? null;  // tambahkan URL jika ada
                $uatPage->cms_admin_panel = $pageData['cms_admin_panel'] ?? null;
                $uatPage->test_result = $pageData['test_result'] ?? null;
                $uatPage->note = $pageData['note'] ?? null;
                $uatPage->save();

                foreach ($pageData['sections'] as $sectionData) {
                    $uatSection = new UatSection();
                    $uatSection->uat_id = $uat->id;
                    $uatSection->page_id = $uatPage->id;
                    $uatSection->section_on_pages = $sectionData['section_on_pages'];
                    $uatSection->url = $sectionData['url'] ?? null;
                    $uatSection->cms_admin_panel = $sectionData['cms_admin_panel'] ?? null;
                    $uatSection->test_result = $sectionData['test_result'] ?? null;
                    $uatSection->result = $sectionData['result'] ?? null;
                    $uatSection->note = $sectionData['note'] ?? null;
                    $uatSection->save();

                    // Simpan setiap Sub Section dalam Section
                    foreach ($sectionData['sub_sections'] as $subSectionData) {
                        $uatSubSection = new UatSubSection();
                        $uatSubSection->uat_id = $uat->id;
                        $uatSubSection->page_id = $uatPage->id;
                        $uatSubSection->section_id = $uatSection->id;
                        $uatSubSection->sub_section = $subSectionData['sub_section'];
                        $uatSubSection->url = $subSectionData['url'] ?? null;
                        $uatSubSection->cms_admin_panel = $subSectionData['cms_admin_panel'] ?? null;
                        $uatSubSection->test_result = $subSectionData['test_result'] ?? null;
                        $uatSubSection->note = $subSectionData['note'] ?? null;
                        $uatSubSection->save();
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data UAT berhasil disimpan'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // Tambahkan log untuk error
            \Log::error('Kesalahan saat menyimpan data:', ['exception' => $e]);

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data', 'details' => $e->getMessage()], 500);
        }
    }


    // Memperbarui halaman UAT
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Log data yang diterima untuk debugging
            \Log::info('Data yang diterima untuk update:', $request->all());

            // Validasi input
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'revision_number' => 'nullable|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'user_info' => 'required|array',
                'user_info.username' => 'required|string|max:255',
                'user_info.password' => 'required|string|max:255',
                'client_id' => 'nullable|exists:clients,id',
                'perusahaan_id' => 'nullable|exists:perusahaans,id',
                'config_document' => 'nullable|string|max:255',
                'progress_percentage' => 'required|integer|min:0|max:100',
                'pages' => 'array',
                'pages.*.pages' => 'nullable|string|max:255',
                'pages.*.url' => 'nullable|string|max:255',
                'pages.*.cms_admin_panel' => 'nullable|string|max:255',
                'pages.*.test_result' => 'nullable|string|max:1',
                'pages.*.note' => 'nullable|string|max:255',
                'pages.*.sections' => 'array',
                'pages.*.sections.*.section_on_pages' => 'required|string|max:255',
                'pages.*.sections.*.url' => 'nullable|string|max:255',
                'pages.*.sections.*.cms_admin_panel' => 'nullable|string|max:255',
                'pages.*.sections.*.test_result' => 'nullable|string|max:1',
                'pages.*.sections.*.result' => 'nullable|string|max:255',
                'pages.*.sections.*.note' => 'nullable|string|max:255',
                'pages.*.sections.*.note_image' => 'nullable|string', // Diubah jadi string base64
                'pages.*.sections.*.sub_sections' => 'array',
                'pages.*.sections.*.sub_sections.*.sub_section' => 'required|string|max:255',
                'pages.*.sections.*.sub_sections.*.url' => 'nullable|string|max:255',
                'pages.*.sections.*.sub_sections.*.cms_admin_panel' => 'nullable|string|max:255',
                'pages.*.sections.*.sub_sections.*.test_result' => 'nullable|string|max:1',
                'pages.*.sections.*.sub_sections.*.note' => 'nullable|string|max:255',
            ]);

            // Temukan data UAT berdasarkan ID
            $uat = Uat::findOrFail($id);

            // Update field UAT
            $uat->project_name = $validated['project_name'];
            $uat->client_id = $validated['client_id'];
            $uat->perusahaan_id = $validated['perusahaan_id'];
            $uat->config_document = $validated['config_document'];
            $uat->revision_number = $validated['revision_number'];
            $uat->start_date = $validated['start_date'];
            $uat->end_date = $validated['end_date'];
            $uat->progress_percentage = $validated['progress_percentage'];

            // Update user_info
            $uat->user_info = json_encode([
                'username' => $validated['user_info']['username'],
                'password' => $validated['user_info']['password']
            ]);

            $uat->save();

            // Hapus halaman terkait sebelum memperbarui data
            UatPage::where('uat_id', $uat->id)->delete();

            // Simpan setiap Page yang diperbarui
            foreach ($validated['pages'] as $pageData) {
                $uatPage = new UatPage();
                $uatPage->uat_id = $uat->id;
                $uatPage->pages = $pageData['pages'];
                $uatPage->url = $pageData['url'] ?? null;
                $uatPage->cms_admin_panel = $pageData['cms_admin_panel'] ?? null;
                $uatPage->test_result = $pageData['test_result'] ?? null;
                $uatPage->note = $pageData['note'] ?? null;
                $uatPage->save();

                // Simpan setiap Section dalam Page
                foreach ($pageData['sections'] as $sectionData) {
                    $uatSection = new UatSection();
                    $uatSection->uat_id = $uat->id;
                    $uatSection->page_id = $uatPage->id;
                    $uatSection->section_on_pages = $sectionData['section_on_pages'];
                    $uatSection->url = $sectionData['url'] ?? null;
                    $uatSection->cms_admin_panel = $sectionData['cms_admin_panel'] ?? null;
                    $uatSection->test_result = $sectionData['test_result'] ?? null;
                    $uatSection->note = $sectionData['note'] ?? null;
                    $uatSection->result = $sectionData['result'] ?? null;

                    // Periksa apakah note_image adalah file yang valid
                    if (isset($sectionData['note_image']) && strpos($sectionData['note_image'], 'data:image') === 0) {
                        // Simpan gambar baru
                        $imageData = $sectionData['note_image'];
                        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData); // Hapus prefix Base64
                        $imageData = base64_decode($imageData); // Decode Base64

                        // Tentukan nama file
                        $imageName = uniqid() . '.jpg'; // Anda bisa menyesuaikan ekstensi berdasarkan format gambar
                        $path = public_path('images/notes/' . $imageName);

                        // Periksa apakah folder bisa ditulis
                        if (is_writable(dirname($path))) {
                            file_put_contents($path, $imageData);  // Simpan file
                        } else {
                            \Log::error("Folder tidak dapat ditulis: " . dirname($path));
                        }

                        // Simpan path gambar ke database
                        $uatSection->note_image = 'images/notes/' . $imageName;
                    } else {
                        // Jika tidak ada gambar baru, pertahankan gambar lama
                        $uatSection->note_image = $sectionData['note_image'] ?? $uatSection->note_image;
                    }



                    $uatSection->save();

                    // Simpan setiap Sub Section dalam Section
                    foreach ($sectionData['sub_sections'] as $subSectionData) {
                        $uatSubSection = new UatSubSection();
                        $uatSubSection->uat_id = $uat->id;
                        $uatSubSection->section_id = $uatSection->id;
                        $uatSubSection->sub_section = $subSectionData['sub_section'];
                        $uatSubSection->url = $subSectionData['url'] ?? null;
                        $uatSubSection->cms_admin_panel = $subSectionData['cms_admin_panel'] ?? null;
                        $uatSubSection->test_result = $subSectionData['test_result'] ?? null;
                        $uatSubSection->note = $subSectionData['note'] ?? null;
                        $uatSubSection->result = $subSectionData['result'] ?? null;
                        $uatSubSection->save();
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Data UAT berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Kesalahan saat memperbarui data:', ['exception' => $e]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat memperbarui data',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        // Ambil data UAT beserta relasi
        $uat = Uat::with('pages.sections.subSections')->findOrFail($id);

        foreach ($uat->pages as $page) {
            foreach ($page->sections as $section) {
                if ($section->note_image) {
                    // Periksa apakah path sudah berupa URL lengkap
                    if (filter_var($section->note_image, FILTER_VALIDATE_URL)) {
                        $section->note_image_url = $section->note_image; // Sudah URL
                    } else {
                        $section->note_image_url = url($section->note_image); // Tambahkan base URL
                    }

                    // Konversi ke Base64 jika file ada
                    $imagePath = public_path(str_replace(url('/'), '', $section->note_image_url));
                    if (file_exists($imagePath)) {
                        $imageData = file_get_contents($imagePath);
                        $section->note_image_base64 = 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
                    } else {
                        $section->note_image_base64 = null; // Jika file tidak ditemukan
                    }
                } else {
                    // Jika tidak ada gambar
                    $section->note_image_url = null;
                    $section->note_image_base64 = null;
                }
            }
        }

        // Jika ditemukan, kembalikan data
        return response()->json($uat);
    }

    public function destroy($id)
    {
        // Temukan UAT dengan ID yang diberikan
        $uat = Uat::findOrFail($id);

        // Hapus UAT, dan secara otomatis pages, sections, sub-sections juga akan terhapus
        $uat->delete();

        // Kembalikan respon JSON dengan status 204 (No Content)
        return response()->json(null, 204);
    }

    public function getUATData()
    {
        // Ambil semua data UAT
        $uats = UAT::all();

        // Inisialisasi array untuk menampung jumlah UAT per bulan
        $uatPerBulan = [];

        // Loop melalui semua UAT dan hitung jumlahnya per bulan
        foreach ($uats as $uat) {
            // Mengambil bulan dari created_at
            $month = Carbon::parse($uat->created_at)->format('F');

            // Jika bulan belum ada dalam array, inisialisasi dengan 0
            if (!isset($uatPerBulan[$month])) {
                $uatPerBulan[$month] = 0;
            }

            // Tambahkan jumlah UAT di bulan tersebut
            $uatPerBulan[$month]++;
        }

        // Kembalikan data dalam format JSON
        return response()->json([
            'data' => $uats,
            'total' => $uats->count(),
            'uat_per_bulan' => $uatPerBulan,
        ]);
    }
}
