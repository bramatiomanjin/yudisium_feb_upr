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

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil diunggah.');
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
