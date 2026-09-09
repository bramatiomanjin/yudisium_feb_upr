<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            'password' => 'required'
        ]);

        // Cek kecocokan di database
        if (Auth::attempt($credentials)) {
            if (Auth::user()->status !== 'ACTIVE') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda masih berstatus PENDING atau INACTIVE. Silakan tunggu persetujuan Super Admin.',
                ]);
            }
            
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard'); // Jika benar, arahkan ke dashboard
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'ADMIN',
            'status' => 'PENDING',
        ]);

        return redirect('/admin/login')->with('success', 'Registrasi berhasil! Akun Anda menunggu persetujuan Super Admin.');
    }
}