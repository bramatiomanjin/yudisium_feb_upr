<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiDataController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupDokumenController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SkController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// =========================================================
// MAHASISWA
// =========================================================

// Halaman utama form pengajuan
Route::get(
    '/',
    [PengajuanController::class, 'create']
)->name('pengajuan.create');

// Submit pengajuan
Route::post(
    '/pengajuan',
    [PengajuanController::class, 'store']
)->middleware('throttle:submission')->name('pengajuan.store');

// Halaman sukses
Route::get(
    '/pengajuan/berhasil',
    [PengajuanController::class, 'success']
)->name('pengajuan.success');

// Tracking mahasiswa
Route::get(
    '/tracking',
    [TrackingController::class, 'index']
)->name('tracking.index');

Route::post(
    '/tracking',
    [TrackingController::class, 'search']
)->middleware('throttle:tracking')->name('tracking.search');

Route::post(
    '/tracking/revision-access',
    [TrackingController::class, 'revisionAccess']
)->middleware('throttle:revision-access')->name('tracking.revision-access');

// Detail tracking
Route::get(
    '/detail_tracking',
    function () {

        return view(
            'mahasiswa.detail_tracking'
        );
    }
);

// Halaman revisi
Route::get(
    '/revisi/{kode_pengajuan}',
    [TrackingController::class, 'revisiPage']
)->name('tracking.revisi');

// Submit revisi
Route::post(
    '/revisi/{kode_pengajuan}',
    [TrackingController::class, 'prosesRevisi']
)->middleware('throttle:revision-submit')->name('tracking.proses');

// =========================================================
// AUTH ADMIN
// =========================================================

Route::get(
    '/admin/login',
    [AuthController::class, 'loginPage']
)->name('login');

Route::post(
    '/admin/login',
    [AuthController::class, 'loginProses']
)->middleware('throttle:login');

Route::post(
    '/admin/logout',
    [AuthController::class, 'logout']
)->name('logout');

// Register Admin
Route::get(
    '/admin/register',
    function () {

        return view(
            'admin.register'
        );
    }
);

Route::post(
    '/admin/register',
    [AuthController::class, 'registerProses']
)->middleware('throttle:admin-register');

// =========================================================
// API DATA PENGAJUAN (PUBLIC WITH KODE)
// =========================================================

Route::get(
    '/submissions/{id}',
    [ApiDataController::class, 'getSubmission']
);

Route::get(
    '/submissions/{id}/documents',
    [ApiDataController::class, 'getDocuments']
);

Route::get(
    '/submissions/{id}/verification',
    [ApiDataController::class, 'getVerificationResult']
);

// =========================================================
// AREA ADMIN
// =========================================================

Route::middleware('auth')->group(
    function () {

        // =================================================
        // DASHBOARD
        // =================================================

        Route::get(
            '/admin/dashboard',
            [AdminController::class, 'index']
        )->name('admin.dashboard');

        // =================================================
        // PENGATURAN DOKUMEN
        // =================================================

        Route::get(
            '/admin/pengaturan-dokumen',
            [SettingController::class, 'pengaturanDokumen']
        )->middleware('super.admin')->name('admin.pengaturan-dokumen');

        Route::post(
            '/admin/pengaturan-dokumen',
            [SettingController::class, 'updatePengaturanDokumen']
        )->middleware('super.admin')->name('admin.pengaturan-dokumen.update');

        Route::post('/admin/toggle-pengajuan-status', [SettingController::class, 'togglePengajuanStatus'])
            ->name('admin.toggle-pengajuan');

        Route::get('/admin/sys-migrate', function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                return "Database berhasil diupdate! Silakan kembali ke halaman dashboard.";
            } catch (\Exception $e) {
                return "Error: " . $e->getMessage();
            }
        })->name('admin.sys-migrate');

        // =================================================
        // DAFTAR PENGAJUAN
        // =================================================

        Route::get(
            '/admin/pengajuan',
            [AdminController::class, 'listPengajuan']
        )->name('admin.pengajuan');

        // =================================================
        // API DATA PENGAJUAN
        // =================================================

        Route::get(
            '/submissions',
            [AdminController::class, 'submissions']
        );

        Route::post(
            '/submissions/{id}/verify',
            [ApiDataController::class, 'verifySubmission']
        );

        // =================================================
        // REVISI
        // =================================================

        Route::get(
            '/submissions/{id}/revision',
            [ApiDataController::class, 'getRevisionSubmission']
        );

        Route::post(
            '/submissions/{id}/revision/review',
            [ApiDataController::class, 'reviewRevision']
        );

        // =================================================
        // PROSES SK
        // =================================================

        Route::patch(
            '/submissions/{id}/sk-status',
            [SkController::class, 'updateStatus']
        );

        // =================================================
        // HISTORY
        // =================================================

        Route::get(
            '/history',
            [ApiDataController::class, 'getHistory']
        );

        // =================================================
        // EXPORT EXCEL
        // =================================================

        Route::get(
            '/admin/export-yudisium',
            [ExcelExportController::class, 'export']
        )->name('admin.export-yudisium');

        // =================================================
        // BACKUP DOKUMEN MAHASISWA
        // =================================================

        Route::get(
            '/admin/backup-dokumen',
            [BackupDokumenController::class, 'download']
        )->middleware('super.admin')->name('admin.backup-dokumen');

        Route::get(
            '/admin/history',
            function () {
                return view(
                    'admin.history',
                    [
                        'logs' => collect(),
                    ]
                );
            }
        )->name('admin.history');

        // =================================================
        // DETAIL PENGAJUAN
        // =================================================

        Route::get(
            '/admin/pengajuan/{id}',
            [AdminController::class, 'show']
        );

        // =================================================
        // FILE PRIVATE
        // =================================================

        Route::get(
            '/admin/file/{id_dokumen}',
            [AdminController::class, 'viewFile']
        );

        // =================================================
        // HALAMAN ADMIN
        // =================================================

        Route::get(
            '/admin/verifikasi',
            function () {

                return view(
                    'admin.verifikasi'
                );
            }
        );

        Route::get(
            '/admin/review-revisi',
            function () {

                return view(
                    'admin.review_revisi'
                );
            }
        );

        Route::get(
            '/admin/proses-sk',
            function () {

                return view(
                    'admin.proses_sk'
                );
            }
        );

        // =================================================
        // SUPER ADMIN
        // =================================================

        Route::middleware('super.admin')->group(function () {
            Route::get(
                '/superadmin/kelola-admin',
                [SuperAdminController::class, 'index']
            )->name('superadmin.kelola-admin');

            Route::get(
                '/superadmin/log-aktivitas',
                [SuperAdminController::class, 'logAktivitas']
            )->name('superadmin.log-aktivitas');

            Route::post(
                '/superadmin/tambah-admin',
                [SuperAdminController::class, 'store']
            )->name('superadmin.tambah-admin');

            Route::post(
                '/superadmin/hapus-admin/{id}',
                [SuperAdminController::class, 'destroy']
            )->name('superadmin.nonaktifkan-admin');

            Route::post(
                '/superadmin/approve-admin/{id}',
                [SuperAdminController::class, 'approve']
            )->name('superadmin.approve-admin');

            Route::post(
                '/superadmin/reject-admin/{id}',
                [SuperAdminController::class, 'reject']
            )->name('superadmin.reject-admin');

            Route::post(
                '/superadmin/activate-admin/{id}',
                [SuperAdminController::class, 'activate']
            )->name('superadmin.activate-admin');
        });
    }
);
