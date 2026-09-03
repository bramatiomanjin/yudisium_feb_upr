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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('nim', 20)->primary();
            $table->string('nama_lengkap', 150);
            $table->string('email', 150);
            $table->string('no_whatsapp', 20);
            $table->year('tahun_angkatan');
            $table->enum('jalur_masuk', ['RPL', 'REGULER']);
            $table->enum('jurusan', ['EKONOMI PEMBANGUNAN', 'MANAJEMEN', 'AKUNTANSI']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
