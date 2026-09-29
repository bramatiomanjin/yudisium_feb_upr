<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const LEGACY_PREFIX = 'private/yudisium/';

    private const NORMALIZED_PREFIX = 'yudisium/';

    public function up(): void
    {
        $this->migratePaths(self::LEGACY_PREFIX, self::NORMALIZED_PREFIX);
    }

    public function down(): void
    {
        $this->migratePaths(self::NORMALIZED_PREFIX, self::LEGACY_PREFIX);
    }

    private function migratePaths(string $sourcePrefix, string $destinationPrefix): void
    {
        $disk = Storage::disk('local');

        DB::table('pengajuan_dokumen')
            ->where('file_path', 'like', $sourcePrefix.'%')
            ->orderBy('id')
            ->chunkById(100, function ($documents) use ($disk, $sourcePrefix, $destinationPrefix): void {
                foreach ($documents as $document) {
                    $source = (string) $document->file_path;
                    $destination = $destinationPrefix.substr($source, strlen($sourcePrefix));

                    if (!$disk->exists($source)) {
                        if (!$disk->exists($destination)) {
                            Log::warning('File dokumen tidak ditemukan saat normalisasi path storage.', [
                                'pengajuan_dokumen_id' => $document->id,
                                'source_path' => $source,
                                'destination_path' => $destination,
                            ]);
                        }

                        $this->updatePath($document->id, $source, $destination);

                        continue;
                    }

                    if ($disk->exists($destination)) {
                        if (!$this->filesMatch($disk->path($source), $disk->path($destination))) {
                            Log::error('Normalisasi path dokumen dilewati karena destination berisi file berbeda.', [
                                'pengajuan_dokumen_id' => $document->id,
                                'source_path' => $source,
                                'destination_path' => $destination,
                            ]);

                            continue;
                        }
                    } else {
                        $disk->makeDirectory(dirname($destination));

                        if (!$disk->copy($source, $destination)
                            || !$disk->exists($destination)
                            || !$this->filesMatch($disk->path($source), $disk->path($destination))) {
                            Log::error('Gagal menyalin atau memverifikasi file saat normalisasi path dokumen.', [
                                'pengajuan_dokumen_id' => $document->id,
                                'source_path' => $source,
                                'destination_path' => $destination,
                            ]);

                            continue;
                        }
                    }

                    $updated = $this->updatePath($document->id, $source, $destination);

                    if ($updated) {
                        $disk->delete($source);
                    }
                }
            });
    }

    private function updatePath(int $id, string $source, string $destination): bool
    {
        return DB::table('pengajuan_dokumen')
            ->where('id', $id)
            ->where('file_path', $source)
            ->update(['file_path' => $destination]) === 1;
    }

    private function filesMatch(string $source, string $destination): bool
    {
        return is_file($source)
            && is_file($destination)
            && filesize($source) === filesize($destination)
            && hash_file('sha256', $source) === hash_file('sha256', $destination);
    }
};
