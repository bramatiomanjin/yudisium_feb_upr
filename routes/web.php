<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;

// Halaman utama untuk menampilkan form yudisium
Route::get('/', [PengajuanController::class, 'create'])->name('pengajuan.create');

// Jalur untuk memproses data form yang disubmit (POST)
Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');

// Halaman sukses setelah berhasil submit
Route::get('/pengajuan/berhasil', [PengajuanController::class, 'success'])->name('pengajuan.success');

use App\Http\Controllers\TrackingController;

// Jalur pencarian status oleh mahasiswa
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [TrackingController::class, 'search'])->name('tracking.search');
Route::get('/detail_tracking', function() { 
        return view('mahasiswa.detail_tracking'); 
    });

// Jalur untuk menampilkan form revisi dan memproses revisi
Route::get('/revisi/{kode_pengajuan}', [TrackingController::class, 'revisiPage'])->name('tracking.revisi');
Route::post('/revisi/{kode_pengajuan}', [TrackingController::class, 'prosesRevisi'])->name('tracking.proses');

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Jalur Login
Route::get('/admin/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/admin/login', [AuthController::class, 'loginProses']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin/register', function() { return view('admin.register'); });
Route::post('/admin/register', [AuthController::class, 'registerProses']);
Route::get('/admin/register', function() { return view('admin.register'); });

// Jalur Dashboard (Dilindungi middleware agar hanya bisa dibuka kalau sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Halaman daftar pengajuan terpisah
    Route::get('/admin/pengajuan', [AdminController::class, 'listPengajuan'])->name('admin.pengajuan');
    
    // API: Data pengajuan dalam format JSON untuk dashboard
    Route::get('/submissions', [AdminController::class, 'submissions']);
    
    // JS Frontend JSON APIs
    Route::get('/submissions/{id}', [\App\Http\Controllers\ApiDataController::class, 'getSubmission']);
    Route::get('/submissions/{id}/documents', [\App\Http\Controllers\ApiDataController::class, 'getDocuments']);
    Route::get('/submissions/{id}/verification', [\App\Http\Controllers\ApiDataController::class, 'getVerificationResult']);
    Route::post('/submissions/{id}/verify', [\App\Http\Controllers\ApiDataController::class, 'verifySubmission']);
    Route::get('/history', [\App\Http\Controllers\ApiDataController::class, 'getHistory']);

    // Route untuk melihat detail pemeriksaan mahasiswa
    Route::get('/admin/pengajuan/{id}', [AdminController::class, 'show']);
    
    // Route POST untuk menyimpan hasil verifikasi (Tambahkan baris ini)
    Route::post('/admin/pengajuan/{id}/verifikasi', [AdminController::class, 'verifikasi']);
    
    // Route untuk membuka file PDF yang ada di folder private
    Route::get('/admin/file/{id_dokumen}', [AdminController::class, 'viewFile']);

    // Rute tambahan untuk halaman Admin
    Route::get('/admin/verifikasi', function() { return view('admin.verifikasi'); });
    Route::get('/admin/review-revisi', function() { return view('admin.review_revisi'); });
    Route::get('/admin/proses-sk', function() { return view('admin.proses_sk'); });

    // Manajemen Admin (Hanya untuk Super Admin)
    Route::get('/superadmin/kelola-admin', [\App\Http\Controllers\SuperAdminController::class, 'index']);
    Route::get('/superadmin/log-aktivitas', [\App\Http\Controllers\SuperAdminController::class, 'logAktivitas']);
    Route::post('/superadmin/tambah-admin', [\App\Http\Controllers\SuperAdminController::class, 'store']);
    Route::post('/superadmin/hapus-admin/{id}', [\App\Http\Controllers\SuperAdminController::class, 'destroy']);
    Route::post('/superadmin/approve-admin/{id}', [\App\Http\Controllers\SuperAdminController::class, 'approve']);
    Route::post('/superadmin/reject-admin/{id}', [\App\Http\Controllers\SuperAdminController::class, 'reject']);
    Route::post('/superadmin/activate-admin/{id}', [\App\Http\Controllers\SuperAdminController::class, 'activate']);
});