<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        // UI Login Sementara
        return '
        <div style="font-family: sans-serif; padding: 50px; text-align: center;">
            <h2>Login Admin Yudisium FEB</h2>
            <form action="/admin/login" method="POST">
                '.csrf_field().'
                Email: <input type="email" name="email" value="superadmin@feb.upr.ac.id"><br><br>
                Password: <input type="password" name="password" value="password123"><br><br>
                <button type="submit" style="padding: 10px 20px; background: darkred; color: white; border: none;">Login Admin</button>
            </form>
        </div>
        ';
    }

    public function loginProses(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek kecocokan di database
        if (Auth::attempt($credentials)) {
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
}