<?php

namespace App\Http\Controllers;

use App\Models\PengajuanYudisium;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupDokumenController extends Controller
{
    public function download()
    {
        /*
         * Pastikan ekstensi ZIP tersedia.
         */
        if (!class_exists(ZipArchive::class)) {
            return response(
                'Ekstensi PHP ZIP belum aktif. Aktifkan extension=zip pada php.ini.',
                500
            );
        }

        /*
         * Ambil seluruh pengajuan beserta mahasiswa
         * dan dokumen terbaru yang tersimpan.
         */
        $pengajuanList = PengajuanYudisium::with([
            'mahasiswa',
            'dokumen.jenisDokumen',
        ])
            ->orderBy('nim', 'asc')
            ->get();

        if ($pengajuanList->isEmpty()) {
            return response(
                'Belum ada data pengajuan yang dapat dibackup.',
                404
            );
        }

        /*
         * Folder sementara untuk menyimpan ZIP.
         */
        Storage::makeDirectory('temp');

        $namaZip =
            'Backup_Dokumen_Yudisium_' .
            now()
                ->timezone('Asia/Jakarta')
                ->format('Y-m-d_His') .
            '.zip';

        $zipPath =
            Storage::path(
                'temp/' . $namaZip
            );

        /*
         * Kalau file dengan nama yang sama kebetulan ada,
         * hapus dulu.
         */
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        $zip = new ZipArchive();

        $hasilOpen = $zip->open(
            $zipPath,
            ZipArchive::CREATE |
            ZipArchive::OVERWRITE
        );

        if ($hasilOpen !== true) {
            return response(
                'Gagal membuat file backup ZIP.',
                500
            );
        }

        $jumlahFile = 0;

        foreach ($pengajuanList as $pengajuan) {

            $mahasiswa =
                $pengajuan->mahasiswa;

            /*
             * Kalau relasi mahasiswa tidak ditemukan,
             * tetap gunakan NIM agar folder tidak hilang.
             */
            $namaMahasiswa =
                $mahasiswa?->nama_lengkap
                ?? 'Mahasiswa';

            $nim =
                $pengajuan->nim
                ?? 'Tanpa NIM';

            /*
             * Bersihkan karakter yang tidak aman
             * untuk nama folder ZIP/Windows.
             */
            $folderMahasiswa =
                $this->sanitizeName(
                    $namaMahasiswa .
                    ' - ' .
                    $nim
                );

            $folderDalamZip =
                'Yudisium/' .
                $folderMahasiswa .
                '/';

            /*
             * Pastikan folder tetap ada meskipun
             * suatu saat mahasiswa belum memiliki file.
             */
            $zip->addEmptyDir(
                rtrim(
                    $folderDalamZip,
                    '/'
                )
            );

            $namaFileTerpakai = [];

            foreach (
                $pengajuan->dokumen
                as $dokumen
            ) {

                if (
                    empty($dokumen->file_path) ||
                    !Storage::exists(
                        $dokumen->file_path
                    )
                ) {
                    continue;
                }

                $pathAsli =
                    Storage::path(
                        $dokumen->file_path
                    );

                /*
                 * Ambil ekstensi dari file asli.
                 */
                $extension =
                    strtolower(
                        pathinfo(
                            $dokumen->nama_file_asli
                                ?: $pathAsli,
                            PATHINFO_EXTENSION
                        )
                    );

                /*
                 * Nama file di backup menggunakan
                 * nama jenis dokumen agar rapi.
                 */
                $namaDokumen =
                    $dokumen
                        ->jenisDokumen
                        ?->nama_dokumen
                    ?? 'Dokumen';

                $namaFile =
                    $this->sanitizeName(
                        $namaDokumen
                    );

                if ($extension !== '') {
                    $namaFile .=
                        '.' .
                        $extension;
                }

                /*
                 * Hindari bentrok apabila dua dokumen
                 * menghasilkan nama file yang sama.
                 */
                $namaFile =
                    $this->uniqueFileName(
                        $namaFile,
                        $namaFileTerpakai
                    );

                $namaFileTerpakai[] =
                    strtolower(
                        $namaFile
                    );

                $pathDalamZip =
                    $folderDalamZip .
                    $namaFile;

                if (
                    is_file($pathAsli)
                ) {
                    $zip->addFile(
                        $pathAsli,
                        $pathDalamZip
                    );

                    $jumlahFile++;
                }
            }
        }

        $zip->close();

        /*
         * Jangan kirim ZIP kosong.
         */
        if ($jumlahFile === 0) {

            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            return response(
                'Tidak ada file dokumen yang ditemukan di storage.',
                404
            );
        }

        /*
         * Kirim ZIP ke browser lalu hapus file
         * sementara setelah download selesai.
         */
        return response()
            ->download(
                $zipPath,
                $namaZip,
                [
                    'Content-Type' =>
                        'application/zip',
                ]
            )
            ->deleteFileAfterSend(true);
    }

    private function sanitizeName(
        string $value
    ): string {

        $value =
            trim(
                preg_replace(
                    '/[\\\\\/:*?"<>|]+/',
                    '-',
                    $value
                )
            );

        $value =
            preg_replace(
                '/\s+/',
                ' ',
                $value
            );

        $value =
            trim(
                $value,
                ". \t\n\r\0\x0B"
            );

        return $value !== ''
            ? $value
            : 'Tanpa Nama';
    }

    private function uniqueFileName(
        string $filename,
        array $usedNames
    ): string {

        $candidate =
            $filename;

        $counter =
            2;

        $extension =
            pathinfo(
                $filename,
                PATHINFO_EXTENSION
            );

        $baseName =
            pathinfo(
                $filename,
                PATHINFO_FILENAME
            );

        while (
            in_array(
                strtolower($candidate),
                $usedNames,
                true
            )
        ) {

            $candidate =
                $baseName .
                ' (' .
                $counter .
                ')';

            if ($extension !== '') {
                $candidate .=
                    '.' .
                    $extension;
            }

            $counter++;
        }

        return $candidate;
    }
}