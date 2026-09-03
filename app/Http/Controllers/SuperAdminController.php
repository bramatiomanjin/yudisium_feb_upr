<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminController extends Controller
{
    // 1. Menampilkan Halaman Kelola Admin
    public function index()
    {
        // Proteksi: Tendang jika bukan SUPER_ADMIN
        if (Auth::user()->role !== 'SUPER_ADMIN') {
            abort(403, 'Akses Ditolak: Halaman ini khusus Super Admin.');
        }

        // Ambil semua data user (Admin & Super Admin)
        $users = User::orderBy('created_at', 'desc')->get();

        $html = '<div style="font-family: sans-serif; padding: 20px;">';
        $html .= '<a href="/admin/dashboard" style="text-decoration:none;">⬅ Kembali ke Dashboard</a>';
        $html .= '<h2>Manajemen Akun Admin</h2>';
        $html .= '<hr>';
        
        // Form Tambah Admin Baru
        $html .= '<h3>Tambah Admin Baru</h3>';
        $html .= '<form action="/superadmin/tambah-admin" method="POST">';
        $html .= csrf_field();
        $html .= 'Nama Lengkap: <input type="text" name="name" required> ';
        $html .= 'Email: <input type="email" name="email" required> ';
        $html .= 'Password: <input type="password" name="password" required> ';
        $html .= 'Role: <select name="role"><option value="ADMIN">ADMIN (Staf)</option><option value="SUPER_ADMIN">SUPER ADMIN</option></select> ';
        $html .= '<button type="submit" style="background: blue; color: white; border: none; padding: 5px 10px; cursor: pointer;">Simpan Akun</button>';
        $html .= '</form><br>';

        // Tabel Daftar Admin
        $html .= '<h3>Daftar Pengguna Sistem</h3>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat Pada</th><th>Aksi</th></tr>';
        
        foreach($users as $u) {
            $html .= '<tr>';
            $html .= '<td>' . $u->name . '</td>';
            $html .= '<td>' . $u->email . '</td>';
            $html .= '<td><b>' . $u->role . '</b></td>';
            $html .= '<td>' . $u->created_at->format('d M Y') . '</td>';
            $html .= '<td>';
            if ($u->id === Auth::id()) {
                $html .= '<i>(Akun Anda)</i>';
            } else {
                $html .= '<form action="/superadmin/hapus-admin/'.$u->id.'" method="POST" onsubmit="return confirm(\'Yakin ingin menghapus akun ini?\');">';
                $html .= csrf_field();
                $html .= '<button type="submit" style="background: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Hapus</button>';
                $html .= '</form>';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></div>';
        return $html;
    }

    // 2. Memproses Penambahan Admin
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'SUPER_ADMIN') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:ADMIN,SUPER_ADMIN'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/superadmin/kelola-admin');
    }

    // 3. Memproses Hapus Admin
    public function destroy(string $id)
    {
        // Proteksi ganda
        if (Auth::user()->role !== 'SUPER_ADMIN') {
            abort(403);
        }

        // Cegah menghapus akun sendiri
        if ($id == Auth::id()) {
            return redirect('/superadmin/kelola-admin')->withErrors('Tidak bisa menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect('/superadmin/kelola-admin');
    }

    // 4. Menampilkan History/Log Aktivitas Admin
    public function logAktivitas()
    {
        if (Auth::user()->role !== 'SUPER_ADMIN') {
            abort(403);
        }

        // Mengambil 50 aktivitas pengecekan field terbaru beserta nama Admin dan nama Mahasiswa
        $logs = \App\Models\ValidasiField::with('pengajuan.mahasiswa')
            ->join('users', 'validasi_field.checked_by', '=', 'users.id')
            ->select('validasi_field.*', 'users.name as nama_admin')
            ->whereNotNull('checked_by')
            ->orderBy('checked_at', 'desc')
            ->limit(50)
            ->get();

        $html = '<div style="font-family: sans-serif; padding: 20px;">';
        $html .= '<a href="/admin/dashboard" style="text-decoration:none;">⬅ Kembali ke Dashboard</a>';
        $html .= '<h2>Riwayat Aktivitas Admin</h2>';
        $html .= '<p>Menampilkan 50 tindakan verifikasi data terakhir yang dilakukan oleh para Admin.</p><hr>';

        $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
        $html .= '<tr style="background:#f4f4f4;"><th>Waktu</th><th>Nama Admin</th><th>Mahasiswa (NIM)</th><th>Data yang Diperiksa</th><th>Keputusan</th></tr>';
        
        if ($logs->isEmpty()) {
            $html .= '<tr><td colspan="5" style="text-align:center;">Belum ada aktivitas verifikasi dari Admin.</td></tr>';
        } else {
            foreach($logs as $log) {
                // Menentukan warna badge status
                $warna = $log->status_validasi == 'DISETUJUI' ? 'green' : ($log->status_validasi == 'REVISI' ? 'red' : 'gray');
                
                $html .= '<tr>';
                $html .= '<td>' . \Carbon\Carbon::parse($log->checked_at)->format('d M Y, H:i') . '</td>';
                $html .= '<td><b>' . $log->nama_admin . '</b></td>';
                $html .= '<td>' . ($log->pengajuan->mahasiswa->nama_lengkap ?? 'Tidak Diketahui') . ' (' . ($log->pengajuan->nim ?? '-') . ')</td>';
                $html .= '<td>' . strtoupper(str_replace('_', ' ', $log->field_key)) . '</td>';
                $html .= '<td style="color:' . $warna . '; font-weight:bold;">' . $log->status_validasi . '</td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</table></div>';
        return $html;
    }
}