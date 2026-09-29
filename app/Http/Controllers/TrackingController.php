<?php

namespace App\Http\Controllers;

use App\Enums\PengajuanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PengajuanYudisium;
use App\Models\Mahasiswa;
use App\Models\ValidasiField;
use App\Models\PengajuanDokumen;
use App\Models\RiwayatRevisi;
use App\Support\DocumentUploadValidator;
use App\Support\StudentYudisiumNotifier;

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

    public function search(Request $request)
{
    $nim = trim((string) $request->input('nim', ''));

    $kode = strtoupper(
        trim(
            (string) (
                $request->input('kode_sk')
                ?? $request->input('kode_pengajuan')
                ?? ''
            )
        )
    );

    if ($nim === '' && $kode === '') {
        return response()->json([
            'success' => false,
            'message' => 'Masukkan NIM atau Kode SK Yudisium.',
        ], 422);
    }

    $query = PengajuanYudisium::with('mahasiswa');

    if ($nim !== '') {
        $query->where('nim', $nim);
    }

    if ($kode !== '') {
        $query->where('kode_pengajuan', $kode);
    }

    $pengajuan = $query
        ->latest('created_at')
        ->first();

    if (!$pengajuan) {
        return response()->json([
            'success' => false,
            'message' => 'Data pengajuan tidak ditemukan.',
        ], 404);
    }

    $jumlahRevisiField = ValidasiField::where(
        'pengajuan_id',
        $pengajuan->id
    )
        ->where('status_validasi', 'REVISI')
        ->count();

    $jumlahRevisiDokumen = PengajuanDokumen::where(
        'pengajuan_id',
        $pengajuan->id
    )
        ->where('status_validasi', 'REVISI')
        ->count();

    return response()->json([
        'success' => true,

        'data' => [
            'id' => $pengajuan->id,

            'kode_sk' => $pengajuan->kode_pengajuan,

            'kode_pengajuan' => $pengajuan->kode_pengajuan,

            'nim' => $pengajuan->nim,

            'nama' => $pengajuan->mahasiswa->nama_lengkap ?? '-',

            'jurusan' => $pengajuan->mahasiswa->jurusan ?? '-',

            'status' => $pengajuan->status,

            'tanggal_pengajuan' => (
                $pengajuan->submitted_at
                ?? $pengajuan->created_at
            )
                ->timezone('Asia/Jakarta')
                ->format('d F Y'),

            'updated_at' => $pengajuan
                ->updated_at
                ->timezone('Asia/Jakarta')
                ->format('d F Y, H:i')
                . ' WIB',

            'revision_count' =>
                $jumlahRevisiField
                + $jumlahRevisiDokumen,

            'revision_access_required' => in_array(
                $pengajuan->status,
                PengajuanStatus::revisionAccessValues(),
                true
            ),
        ],
    ]);
}

    public function revisionAccess(Request $request)
    {
        $nim = trim((string) $request->input('nim', ''));
        $kode = strtoupper(trim((string) $request->input('kode_pengajuan', '')));

        if ($nim === '' || $kode === '') {
            return response()->json([
                'success' => false,
                'message' => 'NIM dan Kode SK Yudisium wajib diisi untuk membuka revisi.',
            ], 422);
        }

        $pengajuan = PengajuanYudisium::query()
            ->where('nim', $nim)
            ->where('kode_pengajuan', $kode)
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'NIM dan Kode SK Yudisium tidak cocok.',
            ], 404);
        }

        if (! in_array($pengajuan->status, PengajuanStatus::revisionAccessValues(), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak sedang berada pada alur revisi.',
            ], 422);
        }

        $revisionToken = $pengajuan->getOrCreateRevisionAccessToken();

        return response()->json([
            'success' => true,
            'data' => [
                'revision_url' => route('tracking.revisi', [
                    'kode_pengajuan' => $pengajuan->kode_pengajuan,
                    'token' => $revisionToken,
                ], false),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }


    // =========================================================
    // 3. HALAMAN REVISI
    // =========================================================

    public function revisiPage(
        Request $request,
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

        abort_unless(
            $pengajuan->hasValidRevisionAccessToken($request->query('token')),
            403
        );


        if (
            !in_array(
                $pengajuan->status,
                PengajuanStatus::revisionAccessValues(),
                true
            )
        ) {

            return redirect(
                '/detail_tracking'
            );
        }


        return response()
            ->view(
                'mahasiswa.revisi',
                [
                    'pengajuan' =>
                        $pengajuan
                ]
            )
            ->header('Cache-Control', 'no-store, private')
            ->header('Referrer-Policy', 'no-referrer');
    }


    // =========================================================
    // 4. PROSES REVISI
    // =========================================================

    public function prosesRevisi(
        Request $request,
        string $kode_pengajuan,
        DocumentUploadValidator $documentUploadValidator,
        StudentYudisiumNotifier $notifier
    ) {
        $pengajuan =
            PengajuanYudisium::where(
                'kode_pengajuan',
                $kode_pengajuan
            )
                ->firstOrFail();

        $revisionToken = $request->query('token');
        $statusSebelumnya = (string) $pengajuan->status;

        abort_unless(
            $pengajuan->hasValidRevisionAccessToken($revisionToken),
            403
        );


        $mahasiswa =
            Mahasiswa::where(
                'nim',
                $pengajuan->nim
            )
                ->firstOrFail();

        $dokumenRevisi = PengajuanDokumen::with('jenisDokumen')
            ->where('pengajuan_id', $pengajuan->id)
            ->where('status_validasi', 'REVISI')
            ->get();

        foreach ($dokumenRevisi as $dokumen) {
            $documentUploadValidator->validate(
                $request,
                'revisi_dokumen.'.$dokumen->id,
                $dokumen->jenisDokumen,
                (bool) $dokumen->jenisDokumen->wajib
            );
        }


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

                            'yudisium/'
                            .
                            $mahasiswa->nim,

                            $namaFileStorage,

                            'local'
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

                $pengajuan->transitionTo(PengajuanStatus::PERLU_REVISI);


                $pesan =
                    'Sebagian revisi tersimpan, namun masih ada data yang belum Anda perbaiki!';

            } else {

                $pengajuan->transitionTo(PengajuanStatus::REVISI_DIKIRIM);
                
                \App\Models\RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'status' => PengajuanStatus::REVISI_DIKIRIM->value,
                    'catatan' => 'Mahasiswa telah mengirimkan seluruh perbaikan revisi',
                    'changed_by' => null
                ]);


                $pesan =
                    'Seluruh revisi berhasil dikirim ke Admin Akademik!';
            }


            DB::commit();
            $notifier->statusChanged(
                $pengajuan,
                $statusSebelumnya,
                PengajuanStatus::from((string) $pengajuan->status)
            );


            return redirect()->route(
                'tracking.revisi',
                [
                    'kode_pengajuan' => $kode_pengajuan,
                    'token' => $revisionToken,
                ]
            )
                ->with(
                    'pesan',
                    $pesan
                );

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('Gagal memproses revisi mahasiswa.', [
                'pengajuan_id' => $pengajuan->id,
                'exception' => $e,
            ]);


            return response(
                'Revisi belum dapat disimpan. Silakan coba lagi.',
                500
            );
        }
    }
}
