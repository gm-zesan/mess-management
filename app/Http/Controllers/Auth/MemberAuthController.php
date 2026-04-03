<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberAuthController extends Controller
{
    /**
     * Show the member login form.
     */
    public function showLogin()
    {
        return view('auth.member-login');
    }

    /**
     * Handle member login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        \Log::info('Member login attempt', ['email' => $credentials['email']]);

        if (Auth::guard('member')->attempt($credentials)) {
            \Log::info('Member login successful', ['email' => $credentials['email']]);
            $request->session()->regenerate();
            return redirect()->route('member.dashboard')
                ->with('success', 'Welcome ' . Auth::guard('member')->user()->name);
        }

        \Log::warning('Member login failed', ['email' => $credentials['email']]);
        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password');
    }

    /**
     * Handle member logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login')
            ->with('success', 'You have been logged out');
    }

    /**
     * Show member dashboard (view-only).
     */
    public function dashboard()
    {
        $member = Auth::guard('member')->user();
        
        return view('member.dashboard', [
            'member' => $member,
        ]);
    }
}
