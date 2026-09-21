<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisDokumen;

class SettingController extends Controller
{
    public function pengaturanDokumen()
    {
        $dokumen = JenisDokumen::all();
        return view('admin.pengaturan_dokumen', compact('dokumen'));
    }

    public function updatePengaturanDokumen(Request $request)
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
}
