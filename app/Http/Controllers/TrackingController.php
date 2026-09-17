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
    // =========================================================
    // 1. TRACKING
    // =========================================================

    public function index()
    {
        return view(
            'mahasiswa.tracking'
        );
    }


    // =========================================================
    // 2. SEARCH TRACKING
    // =========================================================

    public function search(
        Request $request
    ) {
        $kode =
            $request->kode_sk
            ??
            $request->kode_pengajuan;


        $query =
            PengajuanYudisium::with(
                'mahasiswa'
            )
                ->where(
                    'nim',
                    $request->nim
                );

        if (!empty($kode)) {
            $query->where('kode_pengajuan', $kode);
        }

        $pengajuan = $query->latest('created_at')->first();


        if (!$pengajuan) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'NIM dan Kode SK Yudisium tidak ditemukan.'
            ], 404);
        }


        $jumlahRevisiField =
            ValidasiField::where(
                'pengajuan_id',
                $pengajuan->id
            )
                ->where(
                    'status_validasi',
                    'REVISI'
                )
                ->count();


        $jumlahRevisiDokumen =
            PengajuanDokumen::where(
                'pengajuan_id',
                $pengajuan->id
            )
                ->where(
                    'status_validasi',
                    'REVISI'
                )
                ->count();


        return response()->json([

            'success' =>
                true,

            'data' => [

                'id' =>
                    $pengajuan->id,

                'kode_sk' =>
                    $pengajuan
                        ->kode_pengajuan,

                'kode_pengajuan' =>
                    $pengajuan
                        ->kode_pengajuan,

                'nim' =>
                    $pengajuan
                        ->nim,

                'nama' =>
                    $pengajuan
                        ->mahasiswa
                        ->nama_lengkap ?? '-',

                'jurusan' =>
                    $pengajuan
                        ->mahasiswa
                        ->jurusan ?? '-',

                'status' =>
                    $pengajuan
                        ->status,

                'tanggal_pengajuan' =>
                    $pengajuan
                        ->created_at
                        ->timezone(
                            'Asia/Jakarta'
                        )
                        ->format(
                            'd F Y'
                        ),

                'updated_at' =>
                    $pengajuan
                        ->updated_at
                        ->timezone(
                            'Asia/Jakarta'
                        )
                        ->format(
                            'd F Y, H:i'
                        )
                    .
                    ' WIB',

                'revision_count' =>
                    $jumlahRevisiField
                    +
                    $jumlahRevisiDokumen
            ]
        ]);
    }


    // =========================================================
    // 3. HALAMAN REVISI
    // =========================================================

    public function revisiPage(
        string $kode_pengajuan
    ) {
        $pengajuan =
            PengajuanYudisium::with([
                'mahasiswa',
                'validasi',
                'dokumen.jenisDokumen'
            ])
                ->where(
                    'kode_pengajuan',
                    $kode_pengajuan
                )
                ->firstOrFail();


        if (
            !in_array(
                $pengajuan->status,
                [
                    'PERLU_REVISI',
                    'REVISI_DIKIRIM'
                ],
                true
            )
        ) {

            return redirect(
                '/detail_tracking'
            );
        }


        return view(
            'mahasiswa.revisi',
            [
                'pengajuan' =>
                    $pengajuan
            ]
        );
    }


    // =========================================================
    // 4. PROSES REVISI
    // =========================================================

    public function prosesRevisi(
        Request $request,
        string $kode_pengajuan
    ) {
        $pengajuan =
            PengajuanYudisium::where(
                'kode_pengajuan',
                $kode_pengajuan
            )
                ->firstOrFail();


        $mahasiswa =
            Mahasiswa::where(
                'nim',
                $pengajuan->nim
            )
                ->firstOrFail();


        $kolomMahasiswa = [

            'nama_lengkap',

            'email',

            'no_whatsapp',

            'tahun_angkatan',

            'jalur_masuk',

            'jurusan'
        ];


        DB::beginTransaction();


        try {

            // =================================================
            // A. FIELD
            // =================================================

            if (
                $request->has(
                    'revisi_field'
                )
            ) {

                foreach (
                    $request
                        ->revisi_field
                    as
                    $field
                    =>
                    $newValue
                ) {

                    $validasi =
                        ValidasiField::where(
                            'pengajuan_id',
                            $pengajuan->id
                        )
                            ->where(
                                'field_key',
                                $field
                            )
                            ->where(
                                'status_validasi',
                                'REVISI'
                            )
                            ->first();


                    if (!$validasi) {
                        continue;
                    }


                    if (
                        in_array(
                            $field,
                            $kolomMahasiswa,
                            true
                        )
                    ) {

                        $oldValue =
                            $mahasiswa
                                ->$field;

                    } else {

                        $oldValue =
                            $pengajuan
                                ->$field;
                    }


                    /*
                     * Nilai harus benar-benar berubah.
                     */
                    if (
                        (string) $oldValue ===
                        (string) $newValue
                    ) {
                        continue;
                    }


                    /*
                     * Tentukan revisi ke berapa.
                     */
                    $revisiKe =
                        RiwayatRevisi::where(
                            'pengajuan_id',
                            $pengajuan->id
                        )
                            ->where(
                                'jenis_revisi',
                                'FIELD'
                            )
                            ->where(
                                'field_key',
                                $field
                            )
                            ->max(
                                'revisi_ke'
                            );


                    $revisiKe =
                        ($revisiKe ?? 0)
                        +
                        1;


                    /*
                     * Simpan riwayat SEBELUM feedback
                     * dikosongkan.
                     */
                    RiwayatRevisi::create([

                        'pengajuan_id' =>
                            $pengajuan->id,

                        'jenis_revisi' =>
                            'FIELD',

                        'field_key' =>
                            $field,

                        'nilai_lama' =>
                            $oldValue,

                        'nilai_baru' =>
                            $newValue,

                        'feedback_admin' =>
                            $validasi
                                ->feedback,

                        'revisi_ke' =>
                            $revisiKe
                    ]);


                    if (
                        in_array(
                            $field,
                            $kolomMahasiswa,
                            true
                        )
                    ) {

                        $mahasiswa->update([
                            $field =>
                                $newValue
                        ]);

                    } else {

                        $pengajuan->update([
                            $field =>
                                $newValue
                        ]);
                    }


                    /*
                     * Revisi dikirim,
                     * menunggu review admin.
                     */
                    $validasi->update([

                        'status_validasi' =>
                            'PENDING',

                        'feedback' =>
                            null,

                        'checked_by' =>
                            null,

                        'checked_at' =>
                            null
                    ]);
                }
            }


            // =================================================
            // B. DOKUMEN
            // =================================================

            if (
                $request->hasFile(
                    'revisi_dokumen'
                )
            ) {

                foreach (
                    $request->file(
                        'revisi_dokumen'
                    )
                    as
                    $dokumen_id
                    =>
                    $file
                ) {

                    if ($file->getSize() > 1024 * 1024) {
                        DB::rollBack();
                        return response('Ukuran file ' . $file->getClientOriginalName() . ' terlalu besar. Maksimal 1 MB.', 400);
                    }

                    $dokumenLama =
                        PengajuanDokumen::where(
                            'id',
                            $dokumen_id
                        )
                            ->where(
                                'pengajuan_id',
                                $pengajuan->id
                            )
                            ->where(
                                'status_validasi',
                                'REVISI'
                            )
                            ->first();


                    if (!$dokumenLama) {
                        continue;
                    }


                    $jenisDokumen =
                        $dokumenLama
                            ->jenisDokumen;


                    $revisiKe =
                        RiwayatRevisi::where(
                            'pengajuan_id',
                            $pengajuan->id
                        )
                            ->where(
                                'jenis_revisi',
                                'DOKUMEN'
                            )
                            ->where(
                                'jenis_dokumen_id',
                                $jenisDokumen->id
                            )
                            ->max(
                                'revisi_ke'
                            );


                    $revisiKe =
                        ($revisiKe ?? 0)
                        +
                        1;


                    RiwayatRevisi::create([

                        'pengajuan_id' =>
                            $pengajuan->id,

                        'jenis_revisi' =>
                            'DOKUMEN',

                        'jenis_dokumen_id' =>
                            $jenisDokumen->id,

                        'nilai_lama' =>
                            $dokumenLama
                                ->nama_file_asli,

                        'nilai_baru' =>
                            $file
                                ->getClientOriginalName(),

                        'feedback_admin' =>
                            $dokumenLama
                                ->feedback,

                        'revisi_ke' =>
                            $revisiKe
                    ]);


                    $extension =
                        strtolower(
                            $file
                                ->getClientOriginalExtension()
                        );


                    $namaFileStorage =
                        $pengajuan->id
                        .
                        '_'
                        .
                        time()
                        .
                        '_revisi_'
                        .
                        $jenisDokumen->kode
                        .
                        '.'
                        .
                        $extension;


                    $path =
                        $file->storeAs(

                            'private/yudisium/'
                            .
                            $mahasiswa->nim,

                            $namaFileStorage
                        );


                    $dokumenLama->update([

                        'nama_file_asli' =>
                            $file
                                ->getClientOriginalName(),

                        'nama_file_storage' =>
                            $namaFileStorage,

                        'file_path' =>
                            $path,

                        'mime_type' =>
                            $file
                                ->getMimeType(),

                        'ukuran_file' =>
                            $file
                                ->getSize(),

                        'status_validasi' =>
                            'PENDING',

                        'feedback' =>
                            null,

                        'checked_by' =>
                            null,

                        'checked_at' =>
                            null
                    ]);
                }
            }


            // =================================================
            // C. STATUS AKHIR
            // =================================================

            $sisaRevisiField =
                ValidasiField::where(
                    'pengajuan_id',
                    $pengajuan->id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->exists();


            $sisaRevisiDokumen =
                PengajuanDokumen::where(
                    'pengajuan_id',
                    $pengajuan->id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->exists();


            if (
                $sisaRevisiField ||
                $sisaRevisiDokumen
            ) {

                $pengajuan->update([
                    'status' =>
                        'PERLU_REVISI'
                ]);


                $pesan =
                    'Sebagian revisi tersimpan, namun masih ada data yang belum Anda perbaiki!';

            } else {

                $pengajuan->update([
                    'status' =>
                        'REVISI_DIKIRIM'
                ]);
                
                \App\Models\RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'status' => 'REVISI_DIKIRIM',
                    'catatan' => 'Mahasiswa telah mengirimkan seluruh perbaikan revisi',
                    'changed_by' => null
                ]);


                $pesan =
                    'Seluruh revisi berhasil dikirim ke Admin Akademik!';
            }


            DB::commit();


            return redirect(
                '/revisi/'
                .
                $kode_pengajuan
            )
                ->with(
                    'pesan',
                    $pesan
                );

        } catch (\Throwable $e) {

            DB::rollBack();


            return response(
                'Terjadi Kesalahan saat merevisi: '
                .
                $e->getMessage(),
                500
            );
        }
    }
}