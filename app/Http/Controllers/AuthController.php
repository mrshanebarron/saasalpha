<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        $users = \App\Models\User::select('id', 'name', 'email', 'role', 'job_title')->get();
        return view('auth.login', compact('users'));
    }

    public function login(Request $request)
    {
        if ($request->user_id) {
            Auth::loginUsingId($request->user_id);
            return redirect()->route('dashboard');
        }
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
