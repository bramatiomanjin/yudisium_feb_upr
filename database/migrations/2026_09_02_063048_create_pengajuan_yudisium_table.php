<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_yudisium', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 30)->unique();
            $table->string('nim', 20)->unique();
            $table->enum('karya_tulis', ['SKRIPSI', 'ARTIKEL']);
            $table->text('judul_karya_tulis');
            $table->date('tanggal_ujian');
            $table->decimal('nilai_angka', 5, 2);
            $table->enum('nilai_huruf', ['A', 'A-', 'A/B', 'B+', 'B', 'B-']);
            $table->enum('status', ['DIAJUKAN', 'VERIFIKASI_ADMIN', 'PERLU_REVISI', 'REVISI_DIKIRIM', 'TERVERIFIKASI', 'PEMBUATAN_SK', 'TTD_WAKIL_DEKAN', 'TTD_DEKAN', 'SK_TERBIT'])->default('DIAJUKAN');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
    
            $table->foreign('nim')->references('nim')->on('mahasiswa')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_yudisium');
    }
};
