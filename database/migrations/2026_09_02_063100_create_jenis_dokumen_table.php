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
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 100)->unique();
            $table->string('nama_dokumen', 255);
            $table->enum('jurusan', ['EKONOMI PEMBANGUNAN', 'MANAJEMEN', 'AKUNTANSI'])->nullable();
            $table->boolean('wajib')->default(true);
            $table->integer('max_size_mb')->default(1);
            $table->string('allowed_extensions', 100)->default('pdf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumen');
    }
};
