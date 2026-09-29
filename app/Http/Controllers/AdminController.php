<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
    $disk = Storage::disk('local');

    if (!$disk->exists($dokumen->file_path)) {
        Log::warning('Dokumen pengajuan tidak ditemukan di storage.', [
            'dokumen_id' => $dokumen->id,
            'file_path' => $dokumen->file_path,
        ]);

        abort(404, 'File tidak ditemukan.');
    }

    $path = $disk->path($dokumen->file_path);

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

}
