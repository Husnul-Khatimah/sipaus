<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User; // Pastikan ini diimpor dengan benar
use Illuminate\Support\Facades\Password; // Pastikan ini diimpor dengan benar
use Illuminate\Validation\ValidationException; // Pastikan ini diimpor dengan benar

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

        public function authenticate(Request $request)
        {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // --- DEBUGGING START ---
            // $authAttemptResult = Auth::attempt($credentials); // Baris ini sekarang dikomentari atau dihapus
            // dd('Credentials:', $credentials, 'Auth attempt result:', $authAttemptResult); // Baris ini sekarang dikomentari atau dihapus
            // --- DEBUGGING END ---

            // Jika otentikasi berhasil:
            if (Auth::attempt($credentials)) { // Gunakan Auth::attempt() secara langsung di sini
                $request->session()->regenerate();

                // Ubah role pengguna menjadi huruf kecil untuk perbandingan, agar tidak case-sensitive
                $userRole = strtolower(Auth::user()->role);

                // Mengalihkan berdasarkan role pengguna yang login
                if ($userRole === 'admin') { // Bandingkan dengan 'admin' (huruf kecil)
                    return redirect()->intended(route('admin.home'));
                } elseif ($userRole === 'pengguna') { // Bandingkan dengan 'pengguna' (huruf kecil)
                    return redirect()->intended(route('pengguna.home'));
                } elseif ($userRole === 'supplier') { // Bandingkan dengan 'supplier' (huruf kecil)
                    return redirect()->intended(route('suplier.home'));
                }
                // Jika role tidak dikenali atau tidak ada:
                return redirect()->route('login')->withErrors('Role pengguna tidak dikenal atau tidak memiliki halaman dashboard.');
            }

            return back()->withErrors([
                'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
            ])->onlyInput('email');
        }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }

    public function forgotPassword()
    {
        // Pastikan Anda memiliki view resources/views/auth/forgot_password.blade.php
        return view('auth.forgot_password');
    }

    /**
     * Tangani pengiriman link reset password.
     * Memerlukan konfigurasi mail di .env dan tabel password_resets di database.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Menggunakan fitur reset password bawaan Laravel
        // Ini akan mengirim email dengan link reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Menangani respons dari proses pengiriman link
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        // Jika pengiriman link gagal, lempar exception validasi
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function accessAccount()
    {
        // Pastikan Anda memiliki view resources/views/auth/access_account.blade.php
        return view('auth.access_account');
    }
}
