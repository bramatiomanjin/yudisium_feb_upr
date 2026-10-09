<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $setting = \App\Models\Setting::firstOrCreate(
            ['key' => 'is_pengajuan_open'],
            ['value' => '1']
        );
        
        $setting->value = $setting->value === '1' ? '0' : '1';
        $setting->save();
        
        return response()->json([
            'success' => true, 
            'is_open' => $setting->value === '1'
        ]);
    }

    public function uploadPengumuman(Request $request)
    {
        $request->validate([
            'pengumuman_images' => 'required|array|max:5',
            'pengumuman_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('pengumuman_images')) {
            $setting = \App\Models\Setting::firstOrCreate(
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

            $paths = [];
            foreach ($request->file('pengumuman_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pengumuman'), $filename);
                $paths[] = 'uploads/pengumuman/' . $filename;
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
            'pengumuman_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('pengumuman_images')) {
            $setting = \App\Models\Setting::firstOrCreate(
                ['key' => 'pengumuman_image_path'],
                ['value' => '[]']
            );
            
            $paths = json_decode($setting->value, true) ?: [];

            foreach ($request->file('pengumuman_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pengumuman'), $filename);
                $paths[] = 'uploads/pengumuman/' . $filename;
            }
            
            // Limit to 10 images maybe, or whatever. Let's just limit to 10
            if (count($paths) > 10) {
                $paths = array_slice($paths, 0, 10);
            }

            $setting->value = json_encode($paths);
            $setting->save();
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil ditambahkan.');
    }

    public function reorderPengumuman(Request $request)
    {
        $newOrder = $request->input('order'); // array of indices
        if (!is_array($newOrder)) {
            return response()->json(['success' => false]);
        }

        $setting = \App\Models\Setting::where('key', 'pengumuman_image_path')->first();
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
                if (!in_array($index, $newOrder)) {
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
        $setting = \App\Models\Setting::where('key', 'pengumuman_image_path')->first();
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
        $setting = \App\Models\Setting::where('key', 'pengumuman_image_path')->first();
        if ($setting) {
            $oldImages = json_decode($setting->value, true) ?: [];
            if (!is_array($oldImages) && $setting->value) {
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
