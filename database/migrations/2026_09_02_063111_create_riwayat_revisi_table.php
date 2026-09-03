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
        Schema::create('riwayat_revisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_yudisium')->cascadeOnDelete();
            $table->enum('jenis_revisi', ['FIELD', 'DOKUMEN']);
            $table->string('field_key', 100)->nullable();
            $table->foreignId('jenis_dokumen_id')->nullable()->constrained('jenis_dokumen')->nullOnDelete();
            $table->text('nilai_lama')->nullable();
            $table->text('nilai_baru')->nullable();
            $table->text('feedback_admin')->nullable();
            $table->unsignedInteger('revisi_ke')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_revisi');
    }
};
