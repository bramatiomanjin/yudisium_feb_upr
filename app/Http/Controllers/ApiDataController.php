<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanYudisium;
use App\Models\PengajuanDokumen;
use App\Models\ValidasiField;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiDataController extends Controller
{
    // 1. Get a single submission
    public function getSubmission(string $id)
    {
        $pengajuan = PengajuanYudisium::with(['mahasiswa'])->findOrFail($id);
        
        $data = [
            'id' => $pengajuan->id,
            'nim' => $pengajuan->nim,
            'nama' => $pengajuan->mahasiswa->nama_lengkap ?? '',
            'jurusan' => $pengajuan->mahasiswa->jurusan ?? '',
            'email' => $pengajuan->mahasiswa->email ?? '',
            'no_whatsapp' => $pengajuan->mahasiswa->no_whatsapp ?? '',
            'status' => strtolower($pengajuan->status),
            'kode_pengajuan' => $pengajuan->kode_pengajuan,
            'tahun_angkatan' => $pengajuan->mahasiswa->tahun_angkatan ?? '',
            'jalur_masuk' => $pengajuan->mahasiswa->jalur_masuk ?? '',
            'jenis_karya_tulis' => $pengajuan->mahasiswa->karya_tulis ?? '',
            'judul_karya_tulis' => $pengajuan->mahasiswa->judul_karya_tulis ?? '',
            'tanggal_ujian' => $pengajuan->mahasiswa->tanggal_ujian ?? '',
            'nilai_angka' => $pengajuan->mahasiswa->nilai_angka ?? '',
            'nilai_huruf' => $pengajuan->mahasiswa->nilai_huruf ?? '',
            'created_at' => $pengajuan->created_at?->toISOString(),
            'updated_at' => $pengajuan->updated_at?->toISOString(),
            'revision_count' => ValidasiField::where('pengajuan_id', $id)->where('status_validasi', 'REVISI')->count() + PengajuanDokumen::where('pengajuan_id', $id)->where('status_validasi', 'REVISI')->count()
        ];

        return response()->json($data);
    }

    // 2. Get documents for a submission
    public function getDocuments(string $id)
    {
        $dokumens = PengajuanDokumen::where('pengajuan_id', $id)->get();
        
        $data = $dokumens->map(function($doc) {
            return [
                'id' => $doc->id,
                'type' => $doc->jenis_dokumen, // map to expected type if needed
                'name' => str_replace('_', ' ', strtoupper($doc->jenis_dokumen)),
                'status' => strtolower($doc->status_validasi ?? 'pending'),
                'feedback' => $doc->feedback,
                'url' => '/admin/file/' . $doc->id,
            ];
        });

        return response()->json($data);
    }

    // 3. Get verification result/items
    public function getVerificationResult(string $id)
    {
        $fields = ValidasiField::where('pengajuan_id', $id)->get();
        
        $items = $fields->map(function($field) {
            return [
                'type' => 'field',
                'key' => $field->nama_field,
                'status' => strtolower($field->status_validasi),
                'feedback' => $field->feedback,
                'updatedAt' => $field->updated_at?->toISOString()
            ];
        });

        return response()->json([
            'items' => $items
        ]);
    }

    // 4. Verify submission
    public function verifySubmission(Request $request, string $id)
    {
        $pengajuan = PengajuanYudisium::findOrFail($id);
        $items = $request->input('items', []);
        
        $adaRevisi = false;

        foreach ($items as $item) {
            $status = strtoupper($item['decision'] == 'approved' ? 'VALID' : 'REVISI');
            $feedback = $item['feedback'] ?? null;

            if ($status == 'REVISI') {
                $adaRevisi = true;
            }

            if ($item['type'] == 'field') {
                ValidasiField::where('pengajuan_id', $id)
                    ->where('nama_field', $item['key'])
                    ->update([
                        'status_validasi' => $status,
                        'feedback' => $feedback,
                        'checked_by' => Auth::id(),
                        'checked_at' => now(),
                    ]);
            } else if ($item['type'] == 'document') {
                if (is_numeric($item['key'])) {
                    PengajuanDokumen::where('id', $item['key'])
                        ->update([
                            'status_validasi' => $status,
                            'feedback' => $feedback,
                            'checked_by' => Auth::id(),
                            'checked_at' => now(),
                        ]);
                } else {
                    PengajuanDokumen::where('pengajuan_id', $id)
                        ->where('jenis_dokumen', $item['key'])
                        ->update([
                            'status_validasi' => $status,
                            'feedback' => $feedback,
                            'checked_by' => Auth::id(),
                            'checked_at' => now(),
                        ]);
                }
            }
        }

        $masihPendingField = ValidasiField::where('pengajuan_id', $id)->where('status_validasi', 'PENDING')->exists();
        $masihPendingDokumen = PengajuanDokumen::where('pengajuan_id', $id)->where('status_validasi', 'PENDING')->exists();

        if ($adaRevisi) {
            $pengajuan->update(['status' => 'PERLU_REVISI']);
        } elseif (!$masihPendingField && !$masihPendingDokumen) {
            $pengajuan->update(['status' => 'TERVERIFIKASI', 'verified_at' => now()]);
        } else {
            $pengajuan->update(['status' => 'VERIFIKASI_ADMIN']);
        }

        return response()->json(['success' => true]);
    }

    // 5. Get history
    public function getHistory(Request $request)
    {
        $query = ValidasiField::with('pengajuan.mahasiswa')
            ->join('users', 'validasi_field.checked_by', '=', 'users.id')
            ->select('validasi_field.*', 'users.name as nama_admin', 'users.username as admin_username')
            ->whereNotNull('checked_by')
            ->orderBy('checked_at', 'desc')
            ->limit(100);

        if ($request->has('admin_username')) {
            $query->where('users.username', $request->admin_username);
        }

        $logs = $query->get();
        
        $data = $logs->map(function($log) {
            return [
                'id' => $log->id,
                'submissionId' => $log->pengajuan_id,
                'studentName' => $log->pengajuan->mahasiswa->nama_lengkap ?? '',
                'nim' => $log->pengajuan->mahasiswa->nim ?? '',
                'department' => $log->pengajuan->mahasiswa->jurusan ?? '',
                'submissionCode' => $log->pengajuan->kode_pengajuan ?? '',
                'adminName' => $log->nama_admin,
                'adminUsername' => $log->admin_username,
                'action' => 'VERIFICATION',
                'actionLabel' => 'Verifikasi',
                'note' => $log->feedback,
                'status_baru' => strtolower($log->pengajuan->status ?? ''),
                'created_at' => $log->checked_at?->toISOString(),
            ];
        });

        return response()->json($data);
    }
}
