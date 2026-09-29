<?php

namespace App\Http\Controllers;

use App\Enums\PengajuanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\PengajuanYudisium;
use App\Models\RiwayatStatus;

class SkController extends Controller
{
    public function updateStatus(
        Request $request,
        string $id
    ) {
        /*
         * Frontend YudisiumAPI mengirim status
         * dalam format lowercase, misalnya:
         *
         * pembuatan_sk
         *
         * Sedangkan database menggunakan:
         *
         * PEMBUATAN_SK
         *
         * Karena itu normalisasi terlebih dahulu.
         */
        $requestedStatus =
            strtoupper(
                trim(
                    (string) $request->input(
                        'status',
                        ''
                    )
                )
            );


        $validator =
            Validator::make(
                [
                    'status' =>
                        $requestedStatus
                ],
                [
                    'status' => [
                        'required',
                        'string',
                        Rule::in(PengajuanStatus::skTargetValues()),
                    ]
                ],
                [
                    'status.required' =>
                        'Status proses SK wajib dipilih.',

                    'status.in' =>
                        'Status proses SK yang dipilih tidak valid.'
                ]
            );


        if (
            $validator->fails()
        ) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    $validator
                        ->errors()
                        ->first(
                            'status'
                        )
            ], 422);
        }


        $pengajuan =
            PengajuanYudisium::findOrFail(
                $id
            );


        $currentStatus = PengajuanStatus::tryFrom(
            strtoupper(trim((string) $pengajuan->status))
        );
        $nextStatus = PengajuanStatus::from($requestedStatus);

        /*
         * Mengizinkan lompatan tahapan (Loncat ke progres berikutnya)
         * asalkan status tujuan berada SETELAH status saat ini dalam urutan progres SK.
         */
        if ($currentStatus === null || ! in_array($currentStatus, PengajuanStatus::skFlow(), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Status pengajuan saat ini tidak dapat diproses lebih lanjut.'
            ], 422);
        }

        if (! $currentStatus->canTransitionTo($nextStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Perubahan status tidak valid. Hanya dapat maju atau loncat ke progres berikutnya.'
            ], 422);
        }


        DB::beginTransaction();


        try {

            /*
             * Simpan status baru pada pengajuan.
             */
            $pengajuan->transitionTo($nextStatus);


            /*
             * =================================================
             * CATAT HISTORY STATUS
             * =================================================
             *
             * Setiap perubahan proses SK menghasilkan
             * SATU baris di tabel riwayat_status.
             */
            RiwayatStatus::create([

                'pengajuan_id' =>
                    $pengajuan->id,

                'status' =>
                    $nextStatus->value,

                'catatan' =>
                    'Proses SK: ' .
                    $currentStatus->value .
                    ' → ' .
                    $nextStatus->value,

                'changed_by' =>
                    Auth::id(),
            ]);


            DB::commit();


            return response()->json([
                'success' =>
                    true,

                'message' =>
                    'Status proses SK berhasil diperbarui.',

                'data' => [

                    'id' =>
                        $pengajuan->id,

                    'kode_pengajuan' =>
                        $pengajuan
                            ->kode_pengajuan,

                    /*
                     * Response dikembalikan lowercase
                     * agar konsisten dengan frontend.
                     */
                    'status' =>
                        strtolower(
                            $pengajuan
                                ->status
                        ),

                    'updated_at' =>
                        $pengajuan
                            ->updated_at
                            ?->toISOString()
                ]
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();


            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Gagal memperbarui status proses SK.',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }
}
