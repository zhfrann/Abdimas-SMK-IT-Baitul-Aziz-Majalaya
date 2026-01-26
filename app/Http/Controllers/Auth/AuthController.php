<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // 1) attempt dulu
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $remember)) {

            // 2) cek active
            if (!Auth::user()->is_active) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun Anda nonaktif. Silakan hubungi admin.',
                ])->onlyInput('username');
            }

            // 3) lanjut normal
            $request->session()->regenerate();

            if (Auth::user()->hasRole('Super Admin')) {
                return redirect()->intended('/dashboard');
            }
            if (Auth::user()->hasRole('Bagain Akademik')) {
                return redirect()->intended('/dashboard/akademik');
            }
            if (Auth::user()->hasRole('Guru Mapel')) {
                return redirect()->intended('/dashboard/guru-mapel');
            }
            if (Auth::user()->hasRole('Wali Kelas')) {
                return redirect()->intended('/dashboard/wali-kelas');
            }
            if (Auth::user()->hasRole('Kepala Sekolah')) {
                return redirect()->intended('/dashboard/kepala-sekolah');
            }
        }

        return back()->withErrors([
            'username' => 'Kredensial salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
