<?php

namespace App\Support;

use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Mime\MimeTypes;

class DocumentUploadValidator
{
    public function validate(
        Request $request,
        string $inputName,
        JenisDokumen $jenisDokumen,
        bool $required
    ): void {
        $extensions = collect(explode(',', (string) $jenisDokumen->allowed_extensions))
            ->map(fn (string $extension): string => strtolower(trim($extension)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $maxSizeMb = max(1, (int) $jenisDokumen->max_size_mb);
        $extensionList = implode(',', $extensions);
        $mimeTypes = collect($extensions)
            ->flatMap(fn (string $extension): array => MimeTypes::getDefault()->getMimeTypes($extension))
            ->unique()
            ->values()
            ->all();

        Validator::make(
            $request->all(),
            [
                $inputName => [
                    $required ? 'required' : 'nullable',
                    'file',
                    'extensions:'.$extensionList,
                    'mimetypes:'.implode(',', $mimeTypes),
                    'max:'.($maxSizeMb * 1024),
                ],
            ],
            [
                $inputName.'.required' => ':attribute wajib diunggah.',
                $inputName.'.file' => ':attribute harus berupa file yang valid.',
                $inputName.'.extensions' => ':attribute harus menggunakan extension: '.implode(', ', $extensions).'.',
                $inputName.'.mimetypes' => 'Isi file :attribute tidak sesuai dengan format yang diizinkan.',
                $inputName.'.max' => ':attribute maksimal berukuran '.$maxSizeMb.' MB.',
            ],
            [
                $inputName => $jenisDokumen->nama_dokumen,
            ]
        )->validate();
    }
}
