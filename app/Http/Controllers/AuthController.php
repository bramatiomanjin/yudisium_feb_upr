<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('admin.login');
    }

    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek kecocokan di database
        if (Auth::attempt($credentials)) {
            if (Auth::user()->status !== 'ACTIVE') {
                $status = Auth::user()->status;

                Auth::logout();

                return back()->withErrors([
                    'email' => $status === 'PENDING'
                        ? 'Akun sedang menunggu persetujuan Super Admin.'
                        : 'Akun tidak aktif. Silakan hubungi Super Admin atau Bagian IT.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard'); // Jika benar, arahkan ke dashboard
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function registerProses(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau hubungi Super Admin.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'ADMIN',
            'status' => 'PENDING',
        ]);

        return redirect('/admin/login')->with(
            'success',
            'Akun berhasil dibuat dan sedang menunggu persetujuan Super Admin.'
        );
    }
}
