<?php

namespace App\Http\Controllers;

use App\Models\PengajuanYudisium;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ExcelExportController extends Controller
{
    public function export(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $pengajuanList =
            PengajuanYudisium::with([
                'mahasiswa'
            ])
                ->orderBy(
                    'created_at',
                    'asc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | BUAT SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new Spreadsheet();


        $sheet =
            $spreadsheet
                ->getActiveSheet();


        $sheet->setTitle(
            'Data Yudisium'
        );


        /*
        |--------------------------------------------------------------------------
        | JUDUL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A1:N1'
        );


        $sheet->setCellValue(
            'A1',
            'DATA PENGAJUAN YUDISIUM'
        );


        $sheet->mergeCells(
            'A2:N2'
        );


        $sheet->setCellValue(
            'A2',
            'FAKULTAS EKONOMI DAN BISNIS'
        );


        $sheet->mergeCells(
            'A3:N3'
        );


        $sheet->setCellValue(
            'A3',
            'UNIVERSITAS PALANGKA RAYA'
        );


        /*
        |--------------------------------------------------------------------------
        | HEADER TABEL
        |--------------------------------------------------------------------------
        */

        $headers = [

            'A5' =>
                'No',

            'B5' =>
                'Kode Pengajuan',

            'C5' =>
                'NIM',

            'D5' =>
                'Nama Mahasiswa',

            'E5' =>
                'Program Studi',

            'F5' =>
                'Tahun Angkatan',

            'G5' =>
                'Jenis Karya Tulis',

            'H5' =>
                'Judul Karya Tulis',

            'I5' =>
                'Tanggal Ujian',

            'J5' =>
                'Nilai Angka',

            'K5' =>
                'Nilai Huruf',

            'L5' =>
                'Status Pengajuan',

            'M5' =>
                'Tanggal Pengajuan',

            'N5' =>
                'Tanggal Verifikasi',
        ];


        foreach (
            $headers as $cell => $text
        ) {

            $sheet->setCellValue(
                $cell,
                $text
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ISI DATA
        |--------------------------------------------------------------------------
        */

        $row =
            6;


        $no =
            1;


        foreach (
            $pengajuanList as $pengajuan
        ) {

            $mahasiswa =
                $pengajuan->mahasiswa;


            /*
             * NO
             */
            $sheet->setCellValue(
                'A' . $row,
                $no
            );


            /*
             * KODE PENGAJUAN
             */
            $sheet->setCellValue(
                'B' . $row,
                $pengajuan
                    ->kode_pengajuan
                ?? '-'
            );


            /*
             * NIM
             */
            $sheet->setCellValueExplicit(
                'C' . $row,
                (string) (
                    $mahasiswa
                        ?->nim
                    ??
                    $pengajuan
                        ->nim
                    ??
                    '-'
                ),
                DataType::TYPE_STRING
            );


            /*
             * NAMA
             */
            $sheet->setCellValue(
                'D' . $row,
                $mahasiswa
                    ?->nama_lengkap
                ?? '-'
            );


            /*
             * PROGRAM STUDI
             */
            $sheet->setCellValue(
                'E' . $row,
                $mahasiswa
                    ?->jurusan
                ?? '-'
            );


            /*
             * ANGKATAN
             */
            $sheet->setCellValue(
                'F' . $row,
                $mahasiswa
                    ?->tahun_angkatan
                ?? '-'
            );


            /*
             * JENIS KARYA TULIS
             */
            $sheet->setCellValue(
                'G' . $row,
                $pengajuan
                    ->karya_tulis
                ?? '-'
            );


            /*
             * JUDUL
             */
            $sheet->setCellValue(
                'H' . $row,
                $pengajuan
                    ->judul_karya_tulis
                ?? '-'
            );


            /*
             * TANGGAL UJIAN
             */
            $this->setExcelDate(
                $sheet,
                'I' . $row,
                $pengajuan
                    ->tanggal_ujian
            );


            /*
             * NILAI ANGKA
             */
            if (
                $pengajuan
                    ->nilai_angka !== null &&
                $pengajuan
                    ->nilai_angka !== ''
            ) {

                $sheet->setCellValue(
                    'J' . $row,
                    (float) $pengajuan
                        ->nilai_angka
                );

            } else {

                $sheet->setCellValue(
                    'J' . $row,
                    '-'
                );
            }


            /*
             * NILAI HURUF
             */
            $sheet->setCellValue(
                'K' . $row,
                $pengajuan
                    ->nilai_huruf
                ?? '-'
            );


            /*
             * STATUS
             */
            $status =
                strtoupper(
                    trim(
                        (string) $pengajuan
                            ->status
                    )
                );


            $sheet->setCellValue(
                'L' . $row,
                $this->statusLabel(
                    $status
                )
            );


            /*
             * TANGGAL PENGAJUAN
             */
            $this->setExcelDate(
                $sheet,
                'M' . $row,
                $pengajuan
                    ->created_at
            );


            /*
             * TANGGAL VERIFIKASI
             */
            $this->setExcelDate(
                $sheet,
                'N' . $row,
                $pengajuan
                    ->verified_at
            );


            /*
             * WARNA TEKS STATUS
             */
            $this->applyStatusTextStyle(
                $sheet,
                'L' . $row,
                $status
            );


            /*
             * ZEBRA ROW
             */
            if (
                $no % 2 ===
                0
            ) {

                $sheet
                    ->getStyle(
                        'A' .
                        $row .
                        ':N' .
                        $row
                    )
                    ->getFill()
                    ->setFillType(
                        Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setARGB(
                        'FFF7F9FB'
                    );
            }


            /*
             * Tinggi baris
             */
            $sheet
                ->getRowDimension(
                    $row
                )
                ->setRowHeight(
                    30
                );


            $row++;
            $no++;
        }


        /*
        |--------------------------------------------------------------------------
        | BARIS TERAKHIR
        |--------------------------------------------------------------------------
        */

        $lastRow =
            max(
                5,
                $row - 1
            );


        /*
        |--------------------------------------------------------------------------
        | STYLE JUDUL
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A1:N1'
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'size' =>
                        18,

                    'color' => [

                        'argb' =>
                            Color::COLOR_WHITE,
                    ],
                ],

                'fill' => [

                    'fillType' =>
                        Fill::FILL_SOLID,

                    'startColor' => [

                        'argb' =>
                            'FF123A63',
                    ],
                ],

                'alignment' => [

                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],
            ]);


        $sheet
            ->getStyle(
                'A2:N2'
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'size' =>
                        12,

                    'color' => [

                        'argb' =>
                            'FF123A63',
                    ],
                ],

                'fill' => [

                    'fillType' =>
                        Fill::FILL_SOLID,

                    'startColor' => [

                        'argb' =>
                            'FFF2F6FA',
                    ],
                ],

                'alignment' => [

                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],
            ]);


        $sheet
            ->getStyle(
                'A3:N3'
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'size' =>
                        10,

                    'color' => [

                        'argb' =>
                            'FF5F6B78',
                    ],
                ],

                'alignment' => [

                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | STYLE HEADER TABEL
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A5:N5'
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'color' => [

                        'argb' =>
                            Color::COLOR_WHITE,
                    ],
                ],

                'fill' => [

                    'fillType' =>
                        Fill::FILL_SOLID,

                    'startColor' => [

                        'argb' =>
                            'FF1F4E78',
                    ],
                ],

                'alignment' => [

                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,

                    'wrapText' =>
                        true,
                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [

                            'argb' =>
                                'FFB8C6D1',
                        ],
                    ],
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | STYLE DATA
        |--------------------------------------------------------------------------
        */

        if (
            $lastRow >=
            6
        ) {

            $sheet
                ->getStyle(
                    'A6:N' .
                    $lastRow
                )
                ->applyFromArray([

                    'font' => [

                        'size' =>
                            10,

                        'color' => [

                            'argb' =>
                                'FF27313D',
                        ],
                    ],

                    'alignment' => [

                        'vertical' =>
                            Alignment::VERTICAL_CENTER,

                        'wrapText' =>
                            true,
                    ],

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                Border::BORDER_THIN,

                            'color' => [

                                'argb' =>
                                    'FFE3E8ED',
                            ],
                        ],
                    ],
                ]);


            /*
             * Center columns
             */
            foreach (
                [
                    'A',
                    'C',
                    'F',
                    'G',
                    'I',
                    'J',
                    'K',
                    'L',
                    'M',
                    'N',
                ] as $column
            ) {

                $sheet
                    ->getStyle(
                        $column .
                        '6:' .
                        $column .
                        $lastRow
                    )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );
            }


            /*
             * Format nilai angka
             */
            $sheet
                ->getStyle(
                    'J6:J' .
                    $lastRow
                )
                ->getNumberFormat()
                ->setFormatCode(
                    '0.00'
                );


            /*
             * Kolom status background seragam
             */
            $sheet
                ->getStyle(
                    'L6:L' .
                    $lastRow
                )
                ->getFill()
                ->setFillType(
                    Fill::FILL_SOLID
                )
                ->getStartColor()
                ->setARGB(
                    'FFF8FAFC'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | LEBAR KOLOM
        |--------------------------------------------------------------------------
        */

        $columnWidths = [

            'A' =>
                6,

            'B' =>
                19,

            'C' =>
                18,

            'D' =>
                25,

            'E' =>
                24,

            'F' =>
                15,

            'G' =>
                20,

            'H' =>
                48,

            'I' =>
                17,

            'J' =>
                13,

            'K' =>
                13,

            'L' =>
                22,

            'M' =>
                18,

            'N' =>
                18,
        ];


        foreach (
            $columnWidths as
            $column => $width
        ) {

            $sheet
                ->getColumnDimension(
                    $column
                )
                ->setWidth(
                    $width
                );
        }


        /*
        |--------------------------------------------------------------------------
        | TINGGI BARIS
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getRowDimension(
                1
            )
            ->setRowHeight(
                30
            );


        $sheet
            ->getRowDimension(
                2
            )
            ->setRowHeight(
                22
            );


        $sheet
            ->getRowDimension(
                3
            )
            ->setRowHeight(
                20
            );


        $sheet
            ->getRowDimension(
                5
            )
            ->setRowHeight(
                38
            );


        /*
        |--------------------------------------------------------------------------
        | FREEZE PANE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'D6'
        );


        /*
        |--------------------------------------------------------------------------
        | AUTO FILTER
        |--------------------------------------------------------------------------
        */

        $sheet->setAutoFilter(
            'A5:N' .
            $lastRow
        );


        /*
        |--------------------------------------------------------------------------
        | PRINT SETUP
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getPageSetup()
            ->setOrientation(
                PageSetup::ORIENTATION_LANDSCAPE
            );


        $sheet
            ->getPageSetup()
            ->setPaperSize(
                PageSetup::PAPERSIZE_A4
            );


        $sheet
            ->getPageSetup()
            ->setFitToWidth(
                1
            );


        $sheet
            ->getPageSetup()
            ->setFitToHeight(
                0
            );


        $sheet
            ->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd(
                5,
                5
            );


        $sheet
            ->getPageMargins()
            ->setTop(
                0.5
            );


        $sheet
            ->getPageMargins()
            ->setBottom(
                0.5
            );


        $sheet
            ->getPageMargins()
            ->setLeft(
                0.35
            );


        $sheet
            ->getPageMargins()
            ->setRight(
                0.35
            );


        $sheet
            ->getPageSetup()
            ->setHorizontalCentered(
                true
            );


        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        $fileName =
            'Data_Yudisium_FEB_UPR_' .
            now()->format(
                'Y-m-d_H-i-s'
            ) .
            '.xlsx';


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $writer =
            new Xlsx(
                $spreadsheet
            );


        $tempFile =
            tempnam(
                sys_get_temp_dir(),
                'yudisium_'
            );


        $writer->save(
            $tempFile
        );


        return response()
            ->download(
                $tempFile,
                $fileName,
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            )
            ->deleteFileAfterSend(
                true
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EXCEL DATE
    |--------------------------------------------------------------------------
    */

    private function setExcelDate(
        $sheet,
        string $cell,
        $value
    ): void {

        if (
            empty(
                $value
            )
        ) {

            $sheet->setCellValue(
                $cell,
                '-'
            );

            return;
        }


        try {

            $date =
                $value instanceof \DateTimeInterface
                    ? $value
                    : Carbon::parse(
                        $value
                    );


            $sheet->setCellValue(
                $cell,
                Date::PHPToExcel(
                    $date
                )
            );


            $sheet
                ->getStyle(
                    $cell
                )
                ->getNumberFormat()
                ->setFormatCode(
                    'dd-mm-yyyy'
                );

        } catch (\Throwable $e) {

            $sheet->setCellValue(
                $cell,
                '-'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS LABEL
    |--------------------------------------------------------------------------
    */

    private function statusLabel(
        ?string $status
    ): string {

        $status =
            strtoupper(
                trim(
                    (string) $status
                )
            );


        $labels = [

            'DIAJUKAN' =>
                'Diajukan',

            'VERIFIKASI_ADMIN' =>
                'Verifikasi Admin',

            'PERLU_REVISI' =>
                'Perlu Revisi',

            'REVISI_DIKIRIM' =>
                'Revisi Dikirim',

            'TERVERIFIKASI' =>
                'Terverifikasi',

            'PEMBUATAN_SK' =>
                'Pembuatan SK',

            'TTD_WAKIL_DEKAN' =>
                'TTD Wakil Dekan',

            'TTD_DEKAN' =>
                'TTD Dekan',

            'SK_TERBIT' =>
                'SK Terbit',

            'SK_SIAP_DIAMBIL' =>
                'SK Siap Diambil',
        ];


        return
            $labels[
                $status
            ]
            ??
            ucwords(
                strtolower(
                    str_replace(
                        '_',
                        ' ',
                        $status
                    )
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS TEXT STYLE
    |--------------------------------------------------------------------------
    */

    private function applyStatusTextStyle(
        $sheet,
        string $cell,
        string $status
    ): void {

        /*
         * Hanya warna teks yang berbeda.
         * Background semua status dibuat seragam.
         */

        $colors = [

            'DIAJUKAN' =>
                'FF475569',

            'VERIFIKASI_ADMIN' =>
                'FF0369A1',

            'PERLU_REVISI' =>
                'FFB91C1C',

            'REVISI_DIKIRIM' =>
                'FFC2410C',

            'TERVERIFIKASI' =>
                'FF1D4ED8',

            'PEMBUATAN_SK' =>
                'FFB45309',

            'TTD_WAKIL_DEKAN' =>
                'FFB45309',

            'TTD_DEKAN' =>
                'FFB45309',

            'SK_TERBIT' =>
                'FF15803D',

            'SK_SIAP_DIAMBIL' =>
                'FF15803D',
        ];


        $color =
            $colors[
                $status
            ]
            ??
            'FF334155';


        $sheet
            ->getStyle(
                $cell
            )
            ->getFont()
            ->setBold(
                true
            )
            ->getColor()
            ->setARGB(
                $color
            );
    }
}