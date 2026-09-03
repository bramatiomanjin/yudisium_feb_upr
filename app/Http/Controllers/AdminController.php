<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanYudisium;
use App\Models\PengajuanDokumen;

class AdminController extends Controller
{
    // 1. Menampilkan Dashboard
    public function index()
    {
        $pengajuan = PengajuanYudisium::with('mahasiswa')->orderBy('created_at', 'desc')->get();

        $html = '<div style="font-family: sans-serif; padding: 20px;">';
        $html .= '<h2>Dashboard Admin</h2>';
        
        // Menggunakan Facade Auth agar VSCode Intelephense tidak error
        $html .= '<p>Selamat datang, <b>' . Auth::user()->name . '</b> (' . Auth::user()->role . ')</p>';
        $html .= '<form action="/admin/logout" method="POST">'.csrf_field().'<button type="submit">Logout</button></form><hr>';
        // Menu Khusus Super Admin
        if (Auth::user()->role === 'SUPER_ADMIN') {
            $html .= '<div style="background: #e3f2fd; padding: 10px; border-radius: 5px; margin-bottom: 20px;">';
            $html .= '<b>Panel Super Admin:</b> ';
            $html .= '<a href="/superadmin/kelola-admin"><button style="cursor:pointer;">👥 Kelola Akun Admin</button></a> ';
            $html .= '<a href="/superadmin/log-aktivitas"><button style="cursor:pointer;">📊 Log Aktivitas Admin</button></a>';
            $html .= '</div>';
        }
        $html .= '<hr>';
        $html .= '<h3>Daftar Pengajuan Masuk</h3>';
        $html .= '<table border="1" cellpadding="10" cellspacing="0">';
        $html .= '<tr><th>Kode</th><th>NIM</th><th>Nama</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>';
        
        foreach($pengajuan as $p) {
            $html .= '<tr>';
            $html .= '<td>' . $p->kode_pengajuan . '</td>';
            $html .= '<td>' . $p->nim . '</td>';
            $html .= '<td>' . $p->mahasiswa->nama_lengkap . '</td>';
            $html .= '<td>' . $p->status . '</td>';
            $html .= '<td>' . $p->created_at->format('d M Y') . '</td>';
            $html .= '<td><a href="/admin/pengajuan/' . $p->id . '"><button style="cursor:pointer;">Periksa Berkas</button></a></td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></div>';
        
        return $html;
    }

    // 2. Menampilkan Halaman Detail Pemeriksaan (Menambahkan 'string' agar Intelephense tidak error)
    public function show(string $id)
    {
        $pengajuan = PengajuanYudisium::with(['mahasiswa', 'validasi', 'dokumen.jenisDokumen'])->findOrFail($id);

        $html = '<div style="font-family: sans-serif; padding: 20px;">';
        $html .= '<a href="/admin/dashboard" style="text-decoration:none;">⬅ Kembali ke Dashboard</a>';
        $html .= '<h2>Pemeriksaan Berkas: ' . $pengajuan->mahasiswa->nama_lengkap . '</h2>';
        $html .= '<p>NIM: ' . $pengajuan->nim . ' | Kode: ' . $pengajuan->kode_pengajuan . '</p><hr>';

        // Mengarahkan tombol submit ke route verifikasi (yang akan kita buat nanti)
        $html .= '<form action="/admin/pengajuan/'.$pengajuan->id.'/verifikasi" method="POST">';
        $html .= csrf_field();

        $html .= '<h3>1. Validasi Data Inputan</h3>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Data (Field)</th><th>Isi dari Mahasiswa</th><th>Status</th><th>Catatan Revisi Admin</th></tr>';
        
        foreach($pengajuan->validasi as $v) {
            $field = $v->field_key;
            $isiData = $pengajuan->$field ?? $pengajuan->mahasiswa->$field ?? '-';

            $html .= '<tr>';
            $html .= '<td><b>' . strtoupper(str_replace('_', ' ', $field)) . '</b></td>';
            $html .= '<td>' . $isiData . '</td>';
            $html .= '<td>
                        <select name="validasi['.$v->id.'][status]">
                            <option value="PENDING" '.($v->status_validasi=='PENDING'?'selected':'').'>⏳ PENDING</option>
                            <option value="DISETUJUI" '.($v->status_validasi=='DISETUJUI'?'selected':'').'>✅ DISETUJUI</option>
                            <option value="REVISI" '.($v->status_validasi=='REVISI'?'selected':'').'>❌ REVISI</option>
                        </select>
                      </td>';
            $html .= '<td><input type="text" name="validasi['.$v->id.'][feedback]" value="'.$v->feedback.'" placeholder="Tulis alasan jika revisi..." style="width:100%;"></td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '<h3>2. Validasi Dokumen Upload</h3>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Persyaratan Dokumen</th><th>File PDF</th><th>Status</th><th>Catatan Revisi Admin</th></tr>';
        
        foreach($pengajuan->dokumen as $d) {
            $html .= '<tr>';
            $html .= '<td>' . $d->jenisDokumen->nama_dokumen . '</td>';
            $html .= '<td><a href="/admin/file/'.$d->id.'" target="_blank" style="color:blue;">Lihat Berkas 📄</a></td>';
            $html .= '<td>
                        <select name="dokumen['.$d->id.'][status]">
                            <option value="PENDING" '.($d->status_validasi=='PENDING'?'selected':'').'>⏳ PENDING</option>
                            <option value="DISETUJUI" '.($d->status_validasi=='DISETUJUI'?'selected':'').'>✅ DISETUJUI</option>
                            <option value="REVISI" '.($d->status_validasi=='REVISI'?'selected':'').'>❌ REVISI</option>
                        </select>
                      </td>';
            $html .= '<td><input type="text" name="dokumen['.$d->id.'][feedback]" value="'.$d->feedback.'" placeholder="Tulis alasan jika revisi..." style="width:100%;"></td>';
            $html .= '</tr>';
        }
        $html .= '</table><br><br>';
        
        $html .= '<button type="submit" style="padding: 12px 24px; background: darkgreen; color: white; border:none; cursor:pointer;">Simpan Hasil Verifikasi</button>';
        $html .= '</form></div>';

        return $html;
    }

    // 3. Fungsi untuk membuka file privat (diperbaiki menggunakan Storage Laravel)
    public function viewFile(string $id_dokumen)
    {
        $dokumen = PengajuanDokumen::findOrFail($id_dokumen);
        
        // Cek apakah file ada di disk default local Laravel (storage/app)
        if (!Storage::exists($dokumen->file_path)) {
            abort(404, 'File PDF tidak ditemukan di server. Path DB: ' . $dokumen->file_path);
        }

        // Response file menggunakan Storage::path
        return response()->file(Storage::path($dokumen->file_path));
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

        if ($adaRevisi) {
            // Jika ada minimal 1 saja yang salah, statusnya jadi PERLU REVISI
            $pengajuan->update(['status' => 'PERLU_REVISI']);
        } elseif (!$masihPendingField && !$masihPendingDokumen) {
            // Jika semua sudah diperiksa dan tidak ada revisi, berarti TERVERIFIKASI
            $pengajuan->update(['status' => 'TERVERIFIKASI', 'verified_at' => now()]);
        } else {
            // Jika admin baru memeriksa sebagian (masih ada yang PENDING)
            $pengajuan->update(['status' => 'VERIFIKASI_ADMIN']);
        }

        // Redirect kembali ke halaman detail
        return redirect('/admin/pengajuan/'.$id);
    }
}