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
            'pengumuman_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('pengumuman_image')) {
            $file = $request->file('pengumuman_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan ke public/uploads/pengumuman
            $file->move(public_path('uploads/pengumuman'), $filename);

            $setting = \App\Models\Setting::firstOrCreate(
                ['key' => 'pengumuman_image_path'],
                ['value' => '']
            );
            
            // Hapus gambar lama jika ada
            if ($setting->value && file_exists(public_path($setting->value))) {
                @unlink(public_path($setting->value));
            }

            $setting->value = 'uploads/pengumuman/' . $filename;
            $setting->save();
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil diunggah.');
    }

    public function hapusPengumuman(Request $request)
    {
        $setting = \App\Models\Setting::where('key', 'pengumuman_image_path')->first();
        if ($setting) {
            if ($setting->value && file_exists(public_path($setting->value))) {
                @unlink(public_path($setting->value));
            }
            $setting->value = '';
            $setting->save();
        }

        return redirect()->back()->with('success', 'Gambar pengumuman berhasil dihapus.');
    }
}
