<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_yudisium', function (Blueprint $table): void {
            $table->string('revision_access_token_hash', 64)
                ->nullable()
                ->after('kode_pengajuan');
            $table->string('revision_access_token_nonce', 64)
                ->nullable()
                ->after('revision_access_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_yudisium', function (Blueprint $table): void {
            $table->dropColumn([
                'revision_access_token_hash',
                'revision_access_token_nonce',
            ]);
        });
    }
};
