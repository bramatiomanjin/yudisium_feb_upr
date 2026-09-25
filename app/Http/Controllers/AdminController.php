<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanYudisium;
use App\Models\PengajuanDokumen;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AdminController extends Controller
{
    // 1. Menampilkan Dashboard
    public function index()
    {
        return view('admin.dashboard');
    }

    // 1c. Menampilkan Halaman Daftar Pengajuan (terpisah dari dashboard)
    public function listPengajuan()
    {
        return view('admin.pengajuan');
    }

    // 1b. API: Mengembalikan data pengajuan dalam format JSON untuk dashboard
    public function submissions()
    {
        $pengajuan = PengajuanYudisium::with('mahasiswa')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $pengajuan->map(function ($item) {
            return [
                'id' => $item->id,
                'nim' => $item->nim,
                'name' => $item->mahasiswa->nama_lengkap ?? '',
                'nama' => $item->mahasiswa->nama_lengkap ?? '',
                'department' => $item->mahasiswa->jurusan ?? '',
                'jurusan' => $item->mahasiswa->jurusan ?? '',
                'email' => $item->mahasiswa->email ?? '',
                'status' => strtolower($item->status),
                'kode_pengajuan' => $item->kode_pengajuan ?? '',
                'created_at' => $item->created_at?->toISOString(),
                'updated_at' => $item->updated_at?->toISOString(),
            ];
        });

        return response()->json($data);
    }

    // 2. Menampilkan Halaman Detail Pemeriksaan
    public function show(string $id)
    {
        return view('admin.detail_pengajuan');
    }

    // 3. Fungsi untuk membuka file privat (diperbaiki menggunakan Storage Laravel)
    public function viewFile(string $id_dokumen)
{
    $dokumen = PengajuanDokumen::findOrFail($id_dokumen);

    if (!Storage::exists($dokumen->file_path)) {
        abort(
            404,
            'File tidak ditemukan di server. Path DB: ' .
            $dokumen->file_path
        );
    }

    $path = Storage::path($dokumen->file_path);

    $filename = $dokumen->nama_file_asli
        ?: basename($path);

    $extension = strtolower(
        pathinfo($filename, PATHINFO_EXTENSION)
    );

    $mimeType = match ($extension) {
        'pdf' => 'application/pdf',
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        default => $dokumen->mime_type
            ?: 'application/octet-stream',
    };

    $response = response()->file(
        $path,
        [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
        ]
    );

    $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_INLINE,
        $filename
    );

    return $response;
}

    // 4. Memproses dan Menyimpan Hasil Verifikasi Admin
    public function verifikasi(Request $request, string $id)
    {
        $pengajuan = PengajuanYudisium::findOrFail($id);
        $adaRevisi = false;

        // 1. Simpan Status Validasi Field Teks
        if ($request->has('validasi')) {
            foreach ($request->validasi as $validasi_id => $data) {
                // Update ke database
                \App\Models\ValidasiField::where('id', $validasi_id)->update([
                    'status_validasi' => $data['status'],
                    'feedback' => $data['feedback'],
                    'checked_by' => Auth::id(),
                    'checked_at' => now(),
                ]);
                
                if ($data['status'] == 'REVISI') {
                    $adaRevisi = true;
                }
            }
        }

        // 2. Simpan Status Validasi Dokumen PDF
        if ($request->has('dokumen')) {
            foreach ($request->dokumen as $dokumen_id => $data) {
                // Update ke database
                \App\Models\PengajuanDokumen::where('id', $dokumen_id)->update([
                    'status_validasi' => $data['status'],
                    'feedback' => $data['feedback'],
                    'checked_by' => Auth::id(),
                    'checked_at' => now(),
                ]);
                
                if ($data['status'] == 'REVISI') {
                    $adaRevisi = true;
                }
            }
        }

        // 3. Tentukan Status Akhir Pengajuan
        // Kita cek apakah masih ada data yang belum diperiksa (PENDING)
        $masihPendingField = \App\Models\ValidasiField::where('pengajuan_id', $id)->where('status_validasi', 'PENDING')->exists();
        $masihPendingDokumen = \App\Models\PengajuanDokumen::where('pengajuan_id', $id)->where('status_validasi', 'PENDING')->exists();

        $oldStatus = $pengajuan->status;
        if ($adaRevisi) {
            $newStatus = 'PERLU_REVISI';
        } elseif (!$masihPendingField && !$masihPendingDokumen) {
            $newStatus = 'TERVERIFIKASI';
            $pengajuan->verified_at = now();
        } else {
            $newStatus = 'MENUNGGU_VERIFIKASI';
        }

        $pengajuan->status = $newStatus;
        if ($pengajuan->isDirty('status')) {
            $pengajuan->save();
            \App\Models\RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => $newStatus,
                'catatan' => 'Verifikasi dokumen dan field (Klasik)',
                'changed_by' => Auth::id()
            ]);
        } else {
            $pengajuan->save();
        }

        // Redirect kembali ke halaman detail
        return redirect('/admin/pengajuan/'.$id);
    }
}