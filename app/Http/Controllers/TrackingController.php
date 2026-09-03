<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanYudisium;
use App\Models\Mahasiswa;
use App\Models\ValidasiField;
use App\Models\PengajuanDokumen;
use App\Models\RiwayatRevisi;

class TrackingController extends Controller
{
    // 1. Halaman Pencarian Status
    public function index()
    {
        return '
        <div style="font-family: sans-serif; padding: 50px; text-align: center;">
            <h2>Cek Status & Revisi Yudisium</h2>
            <form action="/tracking" method="POST">
                '.csrf_field().'
                <label>NIM Mahasiswa:</label><br>
                <input type="text" name="nim" required placeholder="Contoh: 2300000001"><br><br>
                
                <label>Kode Pengajuan:</label><br>
                <input type="text" name="kode_pengajuan" required placeholder="Contoh: YDS-2026-0001"><br><br>
                
                <button type="submit" style="padding: 10px 20px; background: #0056b3; color: white; border: none;">Cari Pengajuan</button>
            </form>
        </div>
        ';
    }

    // 2. Memproses Pencarian
    public function search(Request $request)
    {
        $pengajuan = PengajuanYudisium::where('nim', $request->nim)
                        ->where('kode_pengajuan', $request->kode_pengajuan)
                        ->first();

        if (!$pengajuan) {
            return "Pengajuan tidak ditemukan. Pastikan NIM dan Kode Pengajuan benar.";
        }

        return redirect()->route('tracking.revisi', $pengajuan->kode_pengajuan);
    }

    // 3. Menampilkan Halaman Status & Form Revisi (Jika Ada)
    public function revisiPage(string $kode_pengajuan)
    {
        $pengajuan = PengajuanYudisium::with(['mahasiswa', 'validasi', 'dokumen.jenisDokumen'])
                        ->where('kode_pengajuan', $kode_pengajuan)->firstOrFail();

        $html = '<div style="font-family: sans-serif; padding: 20px;">';
        $html .= '<h2>Tracking Pengajuan: ' . $pengajuan->kode_pengajuan . '</h2>';
        $html .= '<p>Nama: <b>' . $pengajuan->mahasiswa->nama_lengkap . '</b></p>';
        $html .= '<p>Status Saat Ini: <b style="color: '.($pengajuan->status == 'PERLU_REVISI' ? 'red' : 'green').';">' . str_replace('_', ' ', $pengajuan->status) . '</b></p>';
        
        $html .= '<hr>';

        // Jika statusnya PERLU REVISI, buka form. Jika tidak, hanya tampilkan info.
        if ($pengajuan->status == 'PERLU_REVISI') {
            $html .= '<div style="background: #ffe6e6; padding: 15px; border-left: 5px solid red;">';
            $html .= '<b>Perhatian:</b> Ada data/dokumen yang ditolak oleh Admin. Silakan perbaiki pada form di bawah ini.</div><br>';
            
            $html .= '<form action="/revisi/'.$kode_pengajuan.'" method="POST" enctype="multipart/form-data">';
            $html .= csrf_field();
        }

        $html .= '<h3>1. Data Identitas & Akademik</h3>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Data (Field)</th><th>Status</th><th>Aksi / Input Revisi</th></tr>';
        
        foreach($pengajuan->validasi as $v) {
            $field = $v->field_key;
            $isiData = $pengajuan->$field ?? $pengajuan->mahasiswa->$field ?? '-';

            $html .= '<tr>';
            $html .= '<td>' . strtoupper(str_replace('_', ' ', $field)) . '</td>';
            
            if ($v->status_validasi == 'REVISI') {
                $html .= '<td style="color:red;">❌ Revisi<br><small><i>Catatan: '.$v->feedback.'</i></small></td>';
                // Munculkan input untuk mengedit data
                $html .= '<td><input type="text" name="revisi_field['.$field.']" value="'.$isiData.'" style="width:100%; border:1px solid red; padding:5px;"></td>';
            } else {
                $icon = ($v->status_validasi == 'DISETUJUI') ? '✅ Disetujui' : '⏳ Menunggu';
                $html .= '<td>'.$icon.'</td>';
                // Data terkunci
                $html .= '<td>🔒 <i>'.$isiData.'</i></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '<h3>2. Dokumen Persyaratan</h3>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Nama Dokumen</th><th>Status</th><th>Aksi / Upload Ulang</th></tr>';
        
        foreach($pengajuan->dokumen as $d) {
            $html .= '<tr>';
            $html .= '<td>' . $d->jenisDokumen->nama_dokumen . '</td>';
            
            if ($d->status_validasi == 'REVISI') {
                $html .= '<td style="color:red;">❌ Revisi<br><small><i>Catatan: '.$d->feedback.'</i></small></td>';
                // Munculkan input file untuk upload ulang
                $html .= '<td><input type="file" name="revisi_dokumen['.$d->id.']" accept=".pdf" style="border:1px solid red; padding:5px;"></td>';
            } else {
                $icon = ($d->status_validasi == 'DISETUJUI') ? '✅ Disetujui' : '⏳ Menunggu';
                $html .= '<td>'.$icon.'</td>';
                // Dokumen terkunci
                $html .= '<td>🔒 <i>File sudah tersimpan</i></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table><br>';

        if ($pengajuan->status == 'PERLU_REVISI') {
            $html .= '<button type="submit" style="padding: 12px 24px; background: darkgreen; color: white; border:none; cursor:pointer;">Kirim Perbaikan Data</button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }

    // 4. Memproses Update Revisi Mahasiswa (Sudah Disempurnakan)
    public function prosesRevisi(Request $request, string $kode_pengajuan)
    {
        $pengajuan = PengajuanYudisium::where('kode_pengajuan', $kode_pengajuan)->firstOrFail();
        $mahasiswa = Mahasiswa::where('nim', $pengajuan->nim)->firstOrFail();

        // Daftar kolom yang berada di tabel Mahasiswa
        $kolomMahasiswa = ['nama_lengkap', 'email', 'no_whatsapp', 'tahun_angkatan', 'jalur_masuk', 'jurusan'];

        DB::beginTransaction();
        try {
            // A. UPDATE FIELD TEKS (Hanya jika teksnya benar-benar berubah)
            if ($request->has('revisi_field')) {
                foreach ($request->revisi_field as $field => $newValue) {
                    
                    // Ambil nilai lama
                    $oldValue = in_array($field, $kolomMahasiswa) ? $mahasiswa->$field : $pengajuan->$field;

                    // CEK LOGIKA: Apakah nilai baru BERBEDA dengan nilai lama?
                    if ($oldValue != $newValue) {
                        
                        // Catat ke Riwayat Revisi
                        RiwayatRevisi::create([
                            'pengajuan_id' => $pengajuan->id,
                            'jenis_revisi' => 'FIELD',
                            'field_key' => $field,
                            'nilai_lama' => $oldValue,
                            'nilai_baru' => $newValue,
                        ]);

                        // Update nilai baru ke tabel
                        if (in_array($field, $kolomMahasiswa)) {
                            $mahasiswa->update([$field => $newValue]);
                        } else {
                            $pengajuan->update([$field => $newValue]);
                        }

                        // Karena sudah diubah, kembalikan statusnya menjadi PENDING
                        ValidasiField::where('pengajuan_id', $pengajuan->id)
                                     ->where('field_key', $field)
                                     ->update(['status_validasi' => 'PENDING', 'feedback' => null]);
                    }
                }
            }

            // B. UPDATE DOKUMEN (Jika ada file yang diupload ulang)
            if ($request->hasFile('revisi_dokumen')) {
                foreach ($request->file('revisi_dokumen') as $dokumen_id => $file) {
                    $dokumenLama = PengajuanDokumen::findOrFail($dokumen_id);
                    $jenisDokumen = $dokumenLama->jenisDokumen;

                    // Catat ke Riwayat Revisi
                    RiwayatRevisi::create([
                        'pengajuan_id' => $pengajuan->id,
                        'jenis_revisi' => 'DOKUMEN',
                        'jenis_dokumen_id' => $jenisDokumen->id,
                        'nilai_lama' => $dokumenLama->nama_file_asli,
                        'nilai_baru' => $file->getClientOriginalName(),
                    ]);

                    $namaFileStorage = time() . '_revisi_' . $jenisDokumen->kode . '.' . $file->extension();
                    $path = $file->storeAs('private/yudisium/' . $mahasiswa->nim, $namaFileStorage);

                    // Update data di database
                    $dokumenLama->update([
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'nama_file_storage' => $namaFileStorage,
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'ukuran_file' => $file->getSize(),
                        'status_validasi' => 'PENDING',
                        'feedback' => null
                    ]);
                }
            }

            // C. LOGIKA STATUS AKHIR PENGAJUAN
            // Cek apakah di database masih ada data yang berstatus REVISI
            $sisaRevisiField = ValidasiField::where('pengajuan_id', $pengajuan->id)->where('status_validasi', 'REVISI')->exists();
            $sisaRevisiDokumen = PengajuanDokumen::where('pengajuan_id', $pengajuan->id)->where('status_validasi', 'REVISI')->exists();

            if ($sisaRevisiField || $sisaRevisiDokumen) {
                // Jika masih ada yang salah/belum diubah, tahan statusnya di PERLU REVISI
                $pengajuan->update(['status' => 'PERLU_REVISI']);
                $pesan = 'Sebagian revisi tersimpan, namun masih ada data yang belum Anda perbaiki!';
            } else {
                // Jika semua yang ditolak sudah diperbaiki, ubah jadi REVISI DIKIRIM
                $pengajuan->update(['status' => 'REVISI_DIKIRIM']);
                $pesan = 'Seluruh revisi berhasil dikirim ke Admin Akademik!';
            }
            
            DB::commit();
            return redirect('/revisi/'.$kode_pengajuan)->with('pesan', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return response('Terjadi Kesalahan saat merevisi: ' . $e->getMessage(), 500);
        }
    }
}