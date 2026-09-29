<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentStoragePathMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        Schema::create('pengajuan_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('file_path', 500);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('pengajuan_dokumen');

        parent::tearDown();
    }

    public function test_migration_moves_and_verifies_existing_file_before_removing_source(): void
    {
        $legacyPath = 'private/yudisium/2301110001/document.pdf';
        $normalizedPath = 'yudisium/2301110001/document.pdf';
        $contents = '%PDF-1.4 migration test';
        Storage::disk('local')->put($legacyPath, $contents);

        $id = DB::table('pengajuan_dokumen')->insertGetId(['file_path' => $legacyPath]);
        $migration = $this->migration();

        $migration->up();

        $this->assertSame($normalizedPath, DB::table('pengajuan_dokumen')->find($id)->file_path);
        Storage::disk('local')->assertExists($normalizedPath);
        Storage::disk('local')->assertMissing($legacyPath);
        $this->assertSame($contents, Storage::disk('local')->get($normalizedPath));

        $migration->down();

        $this->assertSame($legacyPath, DB::table('pengajuan_dokumen')->find($id)->file_path);
        Storage::disk('local')->assertExists($legacyPath);
        Storage::disk('local')->assertMissing($normalizedPath);
        $this->assertSame($contents, Storage::disk('local')->get($legacyPath));
    }

    public function test_migration_does_not_overwrite_a_different_destination_file(): void
    {
        $legacyPath = 'private/yudisium/2301110001/document.pdf';
        $normalizedPath = 'yudisium/2301110001/document.pdf';
        Storage::disk('local')->put($legacyPath, 'legacy contents');
        Storage::disk('local')->put($normalizedPath, 'different destination contents');

        $id = DB::table('pengajuan_dokumen')->insertGetId(['file_path' => $legacyPath]);

        $this->migration()->up();

        $this->assertSame($legacyPath, DB::table('pengajuan_dokumen')->find($id)->file_path);
        $this->assertSame('legacy contents', Storage::disk('local')->get($legacyPath));
        $this->assertSame('different destination contents', Storage::disk('local')->get($normalizedPath));
    }

    public function test_migration_is_safe_when_the_legacy_file_is_missing(): void
    {
        $legacyPath = 'private/yudisium/2301110001/missing.pdf';
        $normalizedPath = 'yudisium/2301110001/missing.pdf';
        $id = DB::table('pengajuan_dokumen')->insertGetId(['file_path' => $legacyPath]);

        $this->migration()->up();

        $this->assertSame($normalizedPath, DB::table('pengajuan_dokumen')->find($id)->file_path);
        Storage::disk('local')->assertMissing($legacyPath);
        Storage::disk('local')->assertMissing($normalizedPath);
    }

    private function migration(): object
    {
        return require database_path(
            'migrations/2026_09_29_020000_normalize_yudisium_document_paths.php'
        );
    }
}
