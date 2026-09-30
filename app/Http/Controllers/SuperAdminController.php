<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ValidasiField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    // 1. Menampilkan Halaman Kelola Admin
    public function index(): View
    {
        $admins = User::orderBy('created_at', 'desc')->get();

        $counts = (object) [
            'pending' => $admins->where('status', 'PENDING')->count(),
            'active' => $admins->where('status', 'ACTIVE')->count(),
            'inactive' => $admins->where('status', 'INACTIVE')->count(),
            'total' => $admins->count(),
        ];

        return view('admin.kelola_admin', compact('admins', 'counts'));
    }

    // 2. Memproses Penambahan Admin
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:ADMIN,SUPER_ADMIN',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('superadmin.kelola-admin');
    }

    // 3. Memproses Hapus / Nonaktifkan Admin
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($request->user()->is($user)) {
            return redirect()
                ->route('superadmin.kelola-admin')
                ->withErrors('Tidak bisa menonaktifkan akun Anda sendiri.');
        }

        $user->update(['status' => 'INACTIVE']);

        return redirect()->route('superadmin.kelola-admin');
    }

    // 4. Menyetujui Admin (PENDING → ACTIVE)
    public function approve(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'ACTIVE']);

        return redirect()->route('superadmin.kelola-admin');
    }

    // 5. Menolak Admin (PENDING → INACTIVE)
    public function reject(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'INACTIVE']);

        return redirect()->route('superadmin.kelola-admin');
    }

    // 6. Mengaktifkan Kembali Admin (INACTIVE → ACTIVE)
    public function activate(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'ACTIVE']);

        return redirect()->route('superadmin.kelola-admin');
    }

    // 7. Menampilkan History/Log Aktivitas Admin
    public function logAktivitas(): View
    {
        // Mengambil 50 aktivitas pengecekan field terbaru beserta nama Admin dan nama Mahasiswa
        $logs = ValidasiField::with('pengajuan.mahasiswa')
            ->join('users', 'validasi_field.checked_by', '=', 'users.id')
            ->select('validasi_field.*', 'users.name as nama_admin')
            ->whereNotNull('checked_by')
            ->orderBy('checked_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.history', compact('logs'));
    }
}
