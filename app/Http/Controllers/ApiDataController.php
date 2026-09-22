<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PengajuanYudisium;
use App\Models\PengajuanDokumen;
use App\Models\ValidasiField;
use App\Models\RiwayatRevisi;
use App\Models\RiwayatStatus;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiDataController extends Controller
{
    // =========================================================
    // 1. GET SINGLE SUBMISSION
    // =========================================================

    public function getSubmission(Request $request, string $id)
    {
        $pengajuan =
            PengajuanYudisium::with([
                'mahasiswa'
            ])
                ->findOrFail($id);

        if (!Auth::check() && $request->query('kode_sk') !== $pengajuan->kode_pengajuan) {
            abort(403, 'Unauthorized');
        }

        $data = [

            'id' =>
                $pengajuan->id,

            'nim' =>
                $pengajuan->nim,


            // Mahasiswa
            'nama' =>
                $pengajuan->mahasiswa
                    ->nama_lengkap ?? '',

            'jurusan' =>
                $pengajuan->mahasiswa
                    ->jurusan ?? '',

            'email' =>
                $pengajuan->mahasiswa
                    ->email ?? '',

            'no_whatsapp' =>
                $pengajuan->mahasiswa
                    ->no_whatsapp ?? '',

            'tahun_angkatan' =>
                $pengajuan->mahasiswa
                    ->tahun_angkatan ?? '',

            'jalur_masuk' =>
                $pengajuan->mahasiswa
                    ->jalur_masuk ?? '',


            // Pengajuan
            'status' =>
                strtolower(
                    $pengajuan->status
                ),

            'kode_pengajuan' =>
                $pengajuan->kode_pengajuan,

            'jenis_karya_tulis' =>
                $pengajuan->karya_tulis ?? '',

            'judul_karya_tulis' =>
                $pengajuan->judul_karya_tulis ?? '',

            'tanggal_ujian' =>
                $pengajuan->tanggal_ujian ?? '',

            'nilai_angka' =>
                $pengajuan->nilai_angka ?? '',

            'nilai_huruf' =>
                $pengajuan->nilai_huruf ?? '',


            'created_at' =>
                $pengajuan->created_at
                    ?->toISOString(),

            'updated_at' =>
                $pengajuan->updated_at
                    ?->toISOString(),


            'revision_count' =>
                ValidasiField::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->count()
                +
                PengajuanDokumen::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->count()
        ];


        return response()->json(
            $data
        );
    }


    // =========================================================
    // 2. DOCUMENTS
    // =========================================================

    public function getDocuments(Request $request, string $id)
    {
        $pengajuan = PengajuanYudisium::findOrFail($id);
        
        if (!Auth::check() && $request->query('kode_sk') !== $pengajuan->kode_pengajuan) {
            abort(403, 'Unauthorized');
        }

        $dokumens =
            PengajuanDokumen::with(
                'jenisDokumen'
            )
                ->where(
                    'pengajuan_id',
                    $id
                )
                ->get();


        $data =
            $dokumens->map(
                function ($doc) {

                    return [

                        'id' =>
                            $doc->id,

                        'type' =>
                            $doc->jenis_dokumen_id,

                        'title' =>
                            $doc->jenisDokumen
                                ->nama_dokumen
                                ?? 'Dokumen',

                        'label' =>
                            $doc->jenisDokumen
                                ->nama_dokumen
                                ?? 'Dokumen',

                        'name' =>
                            $doc->jenisDokumen
                                ->nama_dokumen
                                ?? 'Dokumen',

                        'filename' =>
                            $doc->nama_file_asli
                                ?? '',

                        'status' =>
                            strtolower(
                                $doc->status_validasi
                                ?? 'PENDING'
                            ),

                        'feedback' =>
                            $doc->feedback,

                        'url' =>
                            '/admin/file/' .
                            $doc->id,
                    ];
                }
            );


        return response()->json(
            $data
        );
    }


    // =========================================================
    // 3. VERIFICATION RESULT
    // =========================================================

    public function getVerificationResult(
        Request $request,
        string $id
    ) {
        $pengajuan = PengajuanYudisium::findOrFail($id);
        
        if (!Auth::check() && $request->query('kode_sk') !== $pengajuan->kode_pengajuan) {
            abort(403, 'Unauthorized');
        }

        $fields =
            ValidasiField::where(
                'pengajuan_id',
                $id
            )
                ->get();


        $items =
            $fields->map(
                function ($field) {

                    return [

                        'type' =>
                            'field',

                        'key' =>
                            $field->field_key,

                        'status' =>
                            strtolower(
                                $field->status_validasi
                            ),

                        'feedback' =>
                            $field->feedback,

                        'updatedAt' =>
                            $field->updated_at
                                ?->toISOString()
                    ];
                }
            );


        return response()->json([
            'items' =>
                $items
        ]);
    }


    // =========================================================
    // 4. VERIFY SUBMISSION
    // =========================================================

    public function verifySubmission(
        Request $request,
        string $id
    ) {
        $pengajuan =
            PengajuanYudisium::findOrFail(
                $id
            );


        /*
         * Simpan status sebelum verifikasi.
         *
         * Nilai ini akan digunakan untuk
         * History / Audit perubahan status.
         */
        $statusSebelumnya =
            strtoupper(
                trim(
                    (string) $pengajuan->status
                )
            );


        $items =
            $request->input(
                'items',
                []
            );


        DB::beginTransaction();


        try {

            $adaRevisi =
                false;


            foreach (
                $items as $item
            ) {

                $status =
                    $item['decision'] ===
                    'approved'
                        ? 'DISETUJUI'
                        : 'REVISI';


                $feedback =
                    $item['feedback']
                    ?? null;


                if (
                    $status ===
                    'REVISI'
                ) {

                    $adaRevisi =
                        true;
                }


                if (
                    $item['type'] ===
                    'field'
                ) {

                    ValidasiField::where(
                        'pengajuan_id',
                        $id
                    )
                        ->where(
                            'field_key',
                            $item['key']
                        )
                        ->update([

                            'status_validasi' =>
                                $status,

                            'feedback' =>
                                $feedback,

                            'checked_by' =>
                                Auth::id(),

                            'checked_at' =>
                                now(),
                        ]);
                }


                elseif (
                    $item['type'] ===
                    'document'
                ) {

                    PengajuanDokumen::where(
                        'id',
                        $item['key']
                    )
                        ->where(
                            'pengajuan_id',
                            $id
                        )
                        ->update([

                            'status_validasi' =>
                                $status,

                            'feedback' =>
                                $feedback,

                            'checked_by' =>
                                Auth::id(),

                            'checked_at' =>
                                now(),
                        ]);
                }
            }


            $masihPendingField =
                ValidasiField::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'PENDING'
                    )
                    ->exists();


            $masihPendingDokumen =
                PengajuanDokumen::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'PENDING'
                    )
                    ->exists();


            /*
             * Tentukan status akhir hasil verifikasi.
             */
            if (
                $adaRevisi
            ) {

                $statusBaru =
                    'PERLU_REVISI';


                $pengajuan->update([
                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        null
                ]);

            } elseif (
                !$masihPendingField &&
                !$masihPendingDokumen
            ) {

                $statusBaru =
                    'TERVERIFIKASI';


                $pengajuan->update([
                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        now()
                ]);

            } else {

                $statusBaru =
                    'VERIFIKASI_ADMIN';


                $pengajuan->update([
                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        null
                ]);
            }


            /*
             * =================================================
             * CATAT HISTORY VERIFIKASI
             * =================================================
             *
             * Satu kali submit verifikasi Admin
             * menghasilkan SATU event History.
             *
             * Jika ada item yang membutuhkan revisi,
             * aktivitas dikategorikan sebagai REVISI.
             *
             * Jika tidak, aktivitas dikategorikan
             * sebagai VERIFIKASI.
             */
            if (
                $statusBaru ===
                'PERLU_REVISI'
            ) {

                $catatanHistory =
                    'REVISI: ' .
                    $statusSebelumnya .
                    ' → ' .
                    $statusBaru;

            } else {

                $catatanHistory =
                    'VERIFIKASI: ' .
                    $statusSebelumnya .
                    ' → ' .
                    $statusBaru;
            }


            RiwayatStatus::create([

                'pengajuan_id' =>
                    $pengajuan->id,

                'status' =>
                    $statusBaru,

                'catatan' =>
                    $catatanHistory,

                'changed_by' =>
                    Auth::id(),
            ]);


            DB::commit();


            return response()->json([
                'success' =>
                    true,

                'status' =>
                    strtolower(
                        $pengajuan
                            ->fresh()
                            ->status
                    )
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Gagal menyimpan hasil verifikasi.',

                'error' =>
                    $e->getMessage()

            ], 500);
        }
    }


    // =========================================================
    // 5. GET REVISION SUBMISSION
    // =========================================================

    public function getRevisionSubmission(
        string $id
    ) {
        $pengajuan =
            PengajuanYudisium::with([
                'mahasiswa'
            ])
                ->findOrFail(
                    $id
                );


        /*
         * Ambil revisi paling baru untuk setiap
         * field / dokumen.
         */
        $riwayat =
            RiwayatRevisi::where(
                'pengajuan_id',
                $id
            )
                ->orderBy(
                    'id',
                    'desc'
                )
                ->get();


        $seen =
            [];


        $items =
            [];


        foreach (
            $riwayat as $r
        ) {

            if (
                $r->jenis_revisi ===
                'FIELD'
            ) {

                $uniqueKey =
                    'FIELD:' .
                    $r->field_key;

            } else {

                $uniqueKey =
                    'DOKUMEN:' .
                    $r->jenis_dokumen_id;
            }


            if (
                isset(
                    $seen[
                        $uniqueKey
                    ]
                )
            ) {
                continue;
            }


            $seen[
                $uniqueKey
            ] =
                true;


            // =============================================
            // FIELD
            // =============================================

            if (
                $r->jenis_revisi ===
                'FIELD'
            ) {

                $currentField =
                    ValidasiField::where(
                        'pengajuan_id',
                        $id
                    )
                        ->where(
                            'field_key',
                            $r->field_key
                        )
                        ->first();


                if (
                    !$currentField ||
                    $currentField->status_validasi !== 'PENDING'
                ) {
                    continue;
                }


                $items[] = [

                    'type' =>
                        'field',

                    'key' =>
                        $r->field_key,

                    'label' =>
                        $this
                            ->fieldLabel(
                                $r->field_key
                            ),

                    'oldValue' =>
                        $r->nilai_lama,

                    'newValue' =>
                        $r->nilai_baru,

                    'feedback' =>
                        $r->feedback_admin,

                    'revisionNumber' =>
                        $r->revisi_ke
                ];
            }


            // =============================================
            // DOKUMEN
            // =============================================

            else {

                $dokumen =
                    PengajuanDokumen::with(
                        'jenisDokumen'
                    )
                        ->where(
                            'pengajuan_id',
                            $id
                        )
                        ->where(
                            'jenis_dokumen_id',
                            $r->jenis_dokumen_id
                        )
                        ->first();


                if (
                    !$dokumen ||
                    $dokumen->status_validasi !== 'PENDING'
                ) {
                    continue;
                }


                $items[] = [

                    'type' =>
                        'document',

                    'key' =>
                        (string) $dokumen->id,

                    'jenisDokumenId' =>
                        $r->jenis_dokumen_id,

                    'label' =>
                        $dokumen
                            ->jenisDokumen
                            ->nama_dokumen
                            ?? 'Dokumen',

                    'oldFile' =>
                        $r->nilai_lama,

                    'newFile' => ['name' => $r->nilai_baru, 'url' => '/admin/file/' . $dokumen->id],

                    'feedback' =>
                        $r->feedback_admin,

                    'revisionNumber' =>
                        $r->revisi_ke
                ];
            }
        }


        return response()->json([

            'submission' => [

                'id' =>
                    $pengajuan->id,

                'code' =>
                    $pengajuan
                        ->kode_pengajuan,

                'nim' =>
                    $pengajuan
                        ->nim,

                'name' =>
                    $pengajuan
                        ->mahasiswa
                        ->nama_lengkap ?? '',

                'department' =>
                    $pengajuan
                        ->mahasiswa
                        ->jurusan ?? '',

                'status' =>
                    strtolower(
                        $pengajuan
                            ->status
                    )
            ],

            'items' =>
                array_values(
                    $items
                )
        ]);
    }


    // =========================================================
    // 6. REVIEW REVISION
    // =========================================================

    public function reviewRevision(
        Request $request,
        string $id
    ) {
        $pengajuan =
            PengajuanYudisium::findOrFail(
                $id
            );


        /*
         * Status sebelum Admin melakukan
         * review terhadap revisi mahasiswa.
         */
        $statusSebelumnya =
            strtoupper(
                trim(
                    (string) $pengajuan->status
                )
            );


        $items =
            $request->input(
                'items',
                []
            );


        DB::beginTransaction();


        try {

            $adaRevisiLagi =
                false;


            foreach (
                $items as $item
            ) {

                $decision =
                    $item['decision']
                    ?? null;


                $feedback =
                    $item['feedback']
                    ?? null;


                $status =
                    $decision ===
                    'approved'
                        ? 'DISETUJUI'
                        : 'REVISI';


                if (
                    $status ===
                    'REVISI'
                ) {

                    $adaRevisiLagi =
                        true;
                }


                // =========================================
                // FIELD
                // =========================================

                if (
                    $item['type'] ===
                    'field'
                ) {

                    ValidasiField::where(
                        'pengajuan_id',
                        $id
                    )
                        ->where(
                            'field_key',
                            $item['key']
                        )
                        ->update([

                            'status_validasi' =>
                                $status,

                            'feedback' =>
                                $feedback,

                            'checked_by' =>
                                Auth::id(),

                            'checked_at' =>
                                now(),
                        ]);


                    RiwayatRevisi::where(
                        'pengajuan_id',
                        $id
                    )
                        ->where(
                            'jenis_revisi',
                            'FIELD'
                        )
                        ->where(
                            'field_key',
                            $item['key']
                        )
                        ->latest(
                            'id'
                        )
                        ->first()
                        ?->update([
                            'feedback_admin' =>
                                $feedback
                        ]);
                }


                // =========================================
                // DOKUMEN
                // =========================================

                elseif (
                    $item['type'] ===
                    'document'
                ) {

                    $dokumen =
                        PengajuanDokumen::where(
                            'pengajuan_id',
                            $id
                        )
                            ->where(
                                'id',
                                $item['key']
                            )
                            ->first();


                    if ($dokumen) {

                        $dokumen->update([

                            'status_validasi' =>
                                $status,

                            'feedback' =>
                                $feedback,

                            'checked_by' =>
                                Auth::id(),

                            'checked_at' =>
                                now(),
                        ]);


                        RiwayatRevisi::where(
                            'pengajuan_id',
                            $id
                        )
                            ->where(
                                'jenis_revisi',
                                'DOKUMEN'
                            )
                            ->where(
                                'jenis_dokumen_id',
                                $dokumen
                                    ->jenis_dokumen_id
                            )
                            ->latest(
                                'id'
                            )
                            ->first()
                            ?->update([
                                'feedback_admin' =>
                                    $feedback
                            ]);
                    }
                }
            }


            $masihRevisiField =
                ValidasiField::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->exists();


            $masihRevisiDokumen =
                PengajuanDokumen::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'REVISI'
                    )
                    ->exists();


            $masihPendingField =
                ValidasiField::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'PENDING'
                    )
                    ->exists();


            $masihPendingDokumen =
                PengajuanDokumen::where(
                    'pengajuan_id',
                    $id
                )
                    ->where(
                        'status_validasi',
                        'PENDING'
                    )
                    ->exists();


            /*
             * Tentukan status akhir setelah
             * Admin mereview hasil revisi.
             */
            if (
                $adaRevisiLagi ||
                $masihRevisiField ||
                $masihRevisiDokumen
            ) {

                $statusBaru =
                    'PERLU_REVISI';


                $pengajuan->update([

                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        null
                ]);
            }


            elseif (
                !$masihPendingField &&
                !$masihPendingDokumen
            ) {

                $statusBaru =
                    'TERVERIFIKASI';


                $pengajuan->update([

                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        now()
                ]);
            }


            else {

                $statusBaru =
                    'VERIFIKASI_ADMIN';


                $pengajuan->update([

                    'status' =>
                        $statusBaru,

                    'verified_at' =>
                        null
                ]);
            }


            /*
             * =================================================
             * CATAT HISTORY REVIEW REVISI
             * =================================================
             *
             * Satu kali Admin menyelesaikan review revisi
             * menghasilkan SATU event History.
             */
            RiwayatStatus::create([

                'pengajuan_id' =>
                    $pengajuan->id,

                'status' =>
                    $statusBaru,

                'catatan' =>
                    'REVIEW REVISI: ' .
                    $statusSebelumnya .
                    ' → ' .
                    $statusBaru,

                'changed_by' =>
                    Auth::id(),
            ]);


            DB::commit();


            return response()->json([

                'success' =>
                    true,

                'status' =>
                    strtolower(
                        $pengajuan
                            ->fresh()
                            ->status
                    )
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([

                'success' =>
                    false,

                'message' =>
                    $e->getMessage()

            ], 500);
        }
    }


    // =========================================================
    // 7. HISTORY
    // =========================================================

    public function getHistory(
        Request $request
    ) {
        /*
         * History menggunakan tabel riwayat_status.
         *
         * Satu perubahan status = satu aktivitas.
         */
        $query =
            RiwayatStatus::with([
                'pengajuan.mahasiswa',
                'changedBy'
            ])
                ->orderByDesc(
                    'created_at'
                )
                ->orderByDesc(
                    'id'
                );


        /*
         * ADMIN hanya boleh melihat aktivitas
         * yang dilakukan akun sendiri.
         *
         * SUPER_ADMIN dapat melihat semuanya.
         */
        $user =
            Auth::user();


        if (
            $user &&
            strtoupper(
                (string) $user->role
            ) !== 'SUPER_ADMIN'
        ) {

            $query->where(
                'changed_by',
                $user->id
            );
        }


        $logs =
            $query
                ->limit(100)
                ->get();


        $data =
            $logs->map(
                function ($log) {

                    $catatan =
                        trim(
                            (string) (
                                $log->catatan
                                ?? ''
                            )
                        );


                    $previousStatus =
                        null;


                    $newStatus =
                        strtoupper(
                            trim(
                                (string) (
                                    $log->status
                                    ?? ''
                                )
                            )
                        );


                    /*
                     * Contoh:
                     *
                     * VERIFIKASI:
                     * DIAJUKAN → TERVERIFIKASI
                     *
                     * REVISI:
                     * DIAJUKAN → PERLU_REVISI
                     *
                     * REVIEW REVISI:
                     * REVISI_DIKIRIM → TERVERIFIKASI
                     *
                     * PROSES SK:
                     * TERVERIFIKASI → PEMBUATAN_SK
                     */
                    if (
                        preg_match(
                            '/([A-Z0-9_]+)\s*(?:→|->)\s*([A-Z0-9_]+)/u',
                            strtoupper(
                                $catatan
                            ),
                            $matches
                        )
                    ) {

                        $previousStatus =
                            $matches[1]
                            ?? null;


                        $newStatus =
                            $matches[2]
                            ?? $newStatus;
                    }


                    /*
                     * Tentukan kategori aktivitas.
                     */
                    $action =
                        'VERIFICATION';


                    $actionLabel =
                        'Verifikasi Pengajuan';


                    if (
                        str_starts_with(
                            strtoupper(
                                $catatan
                            ),
                            'PROSES SK:'
                        )
                    ) {

                        $action =
                            'UPDATE_SK_STATUS';


                        $actionLabel =
                            'Proses SK';
                    }


                    elseif (
                        str_starts_with(
                            strtoupper(
                                $catatan
                            ),
                            'REVISI:'
                        )
                        ||
                        str_starts_with(
                            strtoupper(
                                $catatan
                            ),
                            'REVIEW REVISI:'
                        )
                    ) {

                        $action =
                            'REVISION';


                        $actionLabel =
                            str_starts_with(
                                strtoupper(
                                    $catatan
                                ),
                                'REVIEW REVISI:'
                            )
                                ? 'Review Revisi'
                                : 'Meminta Revisi';
                    }


                    $admin =
                        $log->changedBy;


                    $pengajuan =
                        $log->pengajuan;


                    $mahasiswa =
                        $pengajuan
                            ?->mahasiswa;


                    return [

                        'id' =>
                            $log->id,


                        'submissionId' =>
                            $log->pengajuan_id,


                        'submissionCode' =>
                            $pengajuan
                                ?->kode_pengajuan
                            ?? '',


                        'studentName' =>
                            $mahasiswa
                                ?->nama_lengkap
                            ?? '',


                        'nim' =>
                            $mahasiswa
                                ?->nim
                            ??
                            $pengajuan
                                ?->nim
                            ??
                            '',


                        'department' =>
                            $mahasiswa
                                ?->jurusan
                            ?? '',


                        'adminUsername' =>
                            $admin
                                ?->email
                            ??
                            $admin
                                ?->name
                            ??
                            '-',


                        'adminName' =>
                            $admin
                                ?->name
                            ?? 'Sistem',


                        'adminRole' =>
                            $admin
                                ?->role
                            ?? 'SYSTEM',


                        'action' =>
                            $action,


                        'actionLabel' =>
                            $actionLabel,


                        'previousStatus' =>
                            $previousStatus
                                ? strtolower(
                                    $previousStatus
                                )
                                : null,


                        'newStatus' =>
                            $newStatus
                                ? strtolower(
                                    $newStatus
                                )
                                : null,


                        'note' =>
                            $catatan !== ''
                                ? $catatan
                                : null,


                        'createdAt' =>
                            $log->created_at
                                ?->toISOString(),


                        /*
                         * Alias kompatibilitas frontend.
                         */
                        'status_sebelumnya' =>
                            $previousStatus
                                ? strtolower(
                                    $previousStatus
                                )
                                : null,


                        'status_baru' =>
                            $newStatus
                                ? strtolower(
                                    $newStatus
                                )
                                : null,


                        'created_at' =>
                            $log->created_at
                                ?->toISOString(),
                    ];
                }
            );


        return response()->json(
            $data
        );
    }


    // =========================================================
    // HELPER LABEL
    // =========================================================

    private function fieldLabel(
        string $key
    ): string {

        $labels = [

            'nama_lengkap' =>
                'Nama Lengkap',

            'email' =>
                'Email',

            'no_whatsapp' =>
                'Nomor WhatsApp',

            'tahun_angkatan' =>
                'Tahun Angkatan',

            'jalur_masuk' =>
                'Jalur Masuk',

            'jurusan' =>
                'Jurusan',

            'karya_tulis' =>
                'Karya Tulis Ilmiah',

            'judul_karya_tulis' =>
                'Judul Skripsi / Artikel',

            'tanggal_ujian' =>
                'Tanggal Ujian',

            'nilai_angka' =>
                'Nilai Ujian',

            'nilai_huruf' =>
                'Nilai Huruf',
        ];


        return
            $labels[$key]
            ??
            ucwords(
                str_replace(
                    '_',
                    ' ',
                    $key
                )
            );
    }
}