<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Shared\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
                ]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar dari sistem SAMSARA SIMS.');
    }

    protected function redirectBasedOnRole($user): RedirectResponse
    {
        $role = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom((string)$user->role);

        return match ($role) {
            UserRole::SUPERADMIN, UserRole::ADMIN_SAR => redirect()->intended(route('admin.index')),
            UserRole::KASIR => redirect()->intended(route('pos.index')),
            UserRole::PETUGAS_POS => redirect()->intended(route('fieldops.index')),
            default => redirect()->intended(route('home')),
        };
    }
}
