<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/admin/dashboard');
        }
        
        // Force a new token for login form
        Session::regenerateToken();
        
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // Skip CSRF validation for login
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tìm người dùng với email và mật khẩu không mã hóa
        $user = User::where('email', $request->email)
                    ->where('password', $request->password)
                    ->first();
        
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            Session::regenerateToken();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->except('password'));
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Skip CSRF validation for logout
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
