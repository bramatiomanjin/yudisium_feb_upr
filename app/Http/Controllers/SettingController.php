<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function pengaturanDokumen(): View
    {
        $dokumen = JenisDokumen::all();

        return view('admin.pengaturan_dokumen', compact('dokumen'));
    }

    public function updatePengaturanDokumen(Request $request): RedirectResponse
    {
        $activeIds = $request->input('active_docs', []);

        // Set all to 0
        JenisDokumen::query()->update(['is_active' => 0]);

        // Set selected to 1
        if (count($activeIds) > 0) {
            JenisDokumen::whereIn('id', $activeIds)->update(['is_active' => 1]);
        }

        return redirect()->back()->with('success', 'Pengaturan dokumen berhasil diperbarui.');
    }

    public function togglePengajuanStatus(Request $request)
    {
        $setting = Setting::firstOrCreate(
            ['key' => 'is_pengajuan_open'],
            ['value' => '1']
        );

        $setting->value = $setting->value === '1' ? '0' : '1';
        $setting->save();

        return response()->json([
            'success' => true,
            'is_open' => $setting->value === '1',
        ]);
    }

    public function uploadPengumuman(Request $request)
    {
        $request->validate([
            'pengumuman_images' => 'required|array|max:5',
            'pengumuman_images.*' => 'image|mimes:jpeg,png,jpg,webp,jfif|max:10240',
        ]);

        if ($request->hasFile('pengumuman_images')) {
            $setting = Setting::firstOrCreate(
                ['key' => 'pengumuman_image_path'],
                ['value' => '[]']
            );

            // Hapus gambar lama jika ada
            $oldImages = json_decode($setting->value, true) ?: [];
            foreach ($oldImages as $oldImg) {
                if ($oldImg && file_exists(public_path($oldImg))) {
                    @unlink(public_path($oldImg));
                }
            }

            $targetDir = public_path('uploads/pengumuman');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            $paths = [];
            foreach ($request->file('pengumuman_images') as $file) {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
                $cleanName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $cleanName = substr($cleanName, 0, 40);
                $filename = time().'_'.uniqid().($cleanName ? '_'.$cleanName : '').'.'.$extension;

                $file->move($targetDir, $filename);
                $paths[] = 'uploads/pengumuman/'.$filename;
            }

            $setting->value = json_encode($paths);
            $setting->save();
        }

        return redirect()->back()->with('success', 'Semua gambar pengumuman berhasil diganti.');
    }

    public function tambahPengumuman(Request $request)
    {
        $request->validate([
            'pengumuman_images' => 'required|array',
            'pengumuman_images.*' => 'image|mimes:jpeg,png,jpg,webp,jfif|max:10240',
        ]);

        if ($request->hasFile('pengumuman_images')) {
            $setting = Setting::firstOrCreate(
                ['key' => 'pengumuman_image_path'],
                ['value' => '[]']
            );

            $paths = json_decode($setting->value, true) ?: [];

            $targetDir = public_path('uploads/pengumuman');
            if (! file_exists($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            foreach ($request->file('pengumuman_images') as $file) {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
                $cleanName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $cleanName = substr($cleanName, 0, 40);
                $filename = time().'_'.uniqid().($cleanName ? '_'.$cleanName : '').'.'.$extension;

                $file->move($targetDir, $filename);
                $paths[] = 'uploads/pengumuman/'.$filename;
            }

            // Limit to 10 images
            if (count($paths) > 10) {
                $paths = array_slice($paths, 0, 10);
            }

            $setting->value = json_encode($paths);
            $setting->save();
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil ditambahkan.');
    }

    /**
     * Endpoint untuk menyajikan gambar pengumuman dengan fallback aman
     * sehingga gambar tidak pernah broken / corrupt di browser.
     */
    public function showPengumumanImage(string $filename)
{
    // Pertahanan berlapis: buang semua komponen direktori,
    // apa pun bentuk input dari route tetap diperlakukan sebagai nama file polos.
    $safeFilename = basename(rawurldecode($filename));

    if ($safeFilename === '' || $safeFilename === '.' || $safeFilename === '..') {
        abort(404);
    }

    $candidates = [
        public_path('uploads/pengumuman/'.$safeFilename),
        storage_path('app/public/pengumuman/'.$safeFilename),
    ];

    foreach ($candidates as $candidate) {
        $real = realpath($candidate);
        $allowedRoot = realpath(dirname($candidate));

        if (
            $real !== false
            && $allowedRoot !== false
            && str_starts_with($real, $allowedRoot.DIRECTORY_SEPARATOR)
            && is_file($real)
        ) {
            $mime = mime_content_type($real) ?: 'image/jpeg';

            return response()->file($real, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }

    // Fallback: jika file di database terhapus (misal akibat rebuild container ephemeral),
    // sajikan poster default resmi agar pop-up tidak menampilkan gambar corrupt/rusak.
    $fallbackPaths = [
        public_path('images/default_pengumuman.jpg'),
        public_path('uploads/pengumuman/default.jpg'),
    ];

    foreach ($fallbackPaths as $fallback) {
        if (file_exists($fallback) && is_file($fallback)) {
            return response()->file($fallback, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'no-cache, private',
            ]);
        }
    }

    abort(404);
}

    public function reorderPengumuman(Request $request)
    {
        $newOrder = $request->input('order'); // array of indices
        if (! is_array($newOrder)) {
            return response()->json(['success' => false]);
        }

        $setting = Setting::where('key', 'pengumuman_image_path')->first();
        if ($setting) {
            $oldImages = json_decode($setting->value, true) ?: [];
            $newImages = [];
            foreach ($newOrder as $index) {
                if (isset($oldImages[$index])) {
                    $newImages[] = $oldImages[$index];
                }
            }
            // append any missing images just in case
            foreach ($oldImages as $index => $img) {
                if (! in_array($index, $newOrder)) {
                    $newImages[] = $img;
                }
            }

            $setting->value = json_encode($newImages);
            $setting->save();
        }

        return response()->json(['success' => true]);
    }

    public function hapusSatuPengumuman(Request $request)
    {
        $indexToRemove = $request->input('index');
        $setting = Setting::where('key', 'pengumuman_image_path')->first();
        if ($setting) {
            $images = json_decode($setting->value, true) ?: [];
            if (isset($images[$indexToRemove])) {
                $img = $images[$indexToRemove];
                if ($img && file_exists(public_path($img))) {
                    @unlink(public_path($img));
                }
                array_splice($images, $indexToRemove, 1);
                $setting->value = json_encode($images);
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil dihapus.');
    }

    public function hapusPengumuman(Request $request)
    {
        $setting = Setting::where('key', 'pengumuman_image_path')->first();
        if ($setting) {
            $oldImages = json_decode($setting->value, true) ?: [];
            if (! is_array($oldImages) && $setting->value) {
                $oldImages = [$setting->value]; // backward compatibility
            }
            foreach ($oldImages as $oldImg) {
                if ($oldImg && file_exists(public_path($oldImg))) {
                    @unlink(public_path($oldImg));
                }
            }
            $setting->value = '[]';
            $setting->save();
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil dihapus.');
    }
}
