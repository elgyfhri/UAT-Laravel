<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\UatController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UatPagesController;
// use App\Http\Controllers\UatPagesController;
use App\Http\Controllers\UatSectionsController;
use App\Http\Controllers\UatSubSectionsController;
use App\Http\Controllers\EditPageController;
use App\Http\Controllers\ImageController;


Route::delete('/delete-image', [UatController::class, 'deleteImage']);
Route::get('images/notes/{filename}', function ($filename) {
    $path = storage_path('app/images/notes/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    return abort(404);
});

Route::get('/uat/{id}/pdf', [UatController::class, 'showForPDF']);

// post
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);
Route::apiResource('/uat_pages', App\Http\Controllers\Api\UatPagesController::class);
Route::post('/uat_sections', [UatSectionsController::class, 'store']);
Route::post('/uat_sub_sections', [UatSubSectionsController::class, 'store']);

// Rute Client
Route::get('/clients', [ClientController::class, 'index']);
Route::post('/clients', [ClientController::class, 'store']);
Route::post('/upload-logo', [ClientController::class, 'uploadLogo']);
Route::get('/clients/list', [ClientController::class, 'list']);
Route::get('/clients/{id}/cek-uat', [ClientController::class, 'cekUAT']);


// Rute Perusahaan
Route::get('/perusahaan', [PerusahaanController::class, 'index']);
Route::post('/perusahaan', [PerusahaanController::class, 'store']);
Route::get('/perusahaan/list', [PerusahaanController::class, 'list']);
Route::get('/perusahaan/{id}/cek-uat', [PerusahaanController::class, 'cekUAT']);

// Rute UAT
Route::get('/uats', [UatController::class, 'index']);
Route::post('/uats', [UatController::class, 'store']);
Route::get('/uats/{id}', [UatController::class, 'show']);
Route::get('/uat-data', [UATController::class, 'getUATData']);
Route::get('/uats/trashed', [UatController::class, 'trashed']);
Route::resource('uats', UatController::class);

// Rute Otentikasi dan User
Route::post('/login', [LoginController::class, 'check']);
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/check-approval/{userId}', [RegisterController::class, 'checkApproval']);
Route::post('/approve-user/{userId}', [RegisterController::class, 'approveUser']); 
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/users', [UserController::class, 'index']);
Route::patch('/users/{id}/approve', [UserController::class, 'approveUser']);
Route::middleware('auth:api')->get('/users/profile', function (Request $request) {
    return response()->json($request->user());
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/datauser', [UserController::class, 'index'])->name('datauser');
});

// routes/api.php
Route::patch('/users/{id}/suspend', [UserController::class, 'suspend']);
Route::patch('/users/{id}/activate', [UserController::class, 'activate']);
Route::middleware('auth:api')->put('/users/profile', [UserController::class, 'updateProfile']);
Route::post('/add-user', [UserController::class, 'store']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::middleware(['auth:api', 'admin'])->get('/master-data', [MasterDataController::class, 'index']);
Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::delete('/users/profile/photo', [UserController::class, 'removeProfileImage']);


// Rute lain untuk keperluan lain
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::get('/dashboard-data', [HomeController::class, 'getDashboardData']);
Route::post('/uats/{id}', [UatController::class, 'update']);
Route::delete('/uats/{id}', [UatController::class, 'destroy']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/clients/{id}', [ClientController::class, 'show']);
Route::post('/clients/{id}', [ClientController::class, 'update']);
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
Route::get('/perusahaan/{id}', [PerusahaanController::class, 'show']);
Route::put('/perusahaan/{id}', [PerusahaanController::class, 'update']);
Route::delete('/perusahaan/{id}', [PerusahaanController::class, 'destroy']);
// Route::group(['prefix'=>'uat']{
    // Rute untuk UatPages
    // Route::get('/uat_pages', [UatPagesController::class, 'index']);
    // Route::post('/uat_pages', [UatPagesController::class, 'store']);
    // });
    // Route::apiResource('/uat-pages', UATPagesController::class);
    // Route::post('/submit-Uat', [UATController::class, 'store']);


//editpages
Route::get('/edit-page', [EditPageController::class, 'index']);
Route::put('/edit-page', [EditPageController::class, 'update']);