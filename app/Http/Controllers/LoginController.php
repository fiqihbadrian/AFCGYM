<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginBackend()
    {
        return view('auth.login', [
            'judul' => 'Login',
        ]);
    }

    public function authenticateBackend(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->status == 0) {
                Auth::logout();

                return back()->with('error', 'Akun belum aktif');
            }

            $request->session()->regenerate();

            // Arahkan sesuai role: 1 = Admin, 0 = Member
            if (Auth::user()->role == 1) {
                return redirect()->intended(route('backend.beranda'));
            }

            return redirect()->intended(route('member.beranda'));
        }

        return back()->with('error', 'Login gagal, email atau password salah');
    }

    public function logoutBackend(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('backend.login'));
    }
}
