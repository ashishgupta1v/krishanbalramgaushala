<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function showLogin(): Response
    {
        if (session('admin_authenticated')) {
            return Inertia::location(route('admin.dashboard'));
        }
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Look up user in database
        $admin = AdminUser::where('username', $request->username)
            ->where('is_active', true)
            ->first();

        $valid = $admin && Hash::check($request->password, $admin->password);

        if (!$valid) {
            return back()->withErrors(['message' => 'Invalid username or password.']);
        }

        // Update last login timestamp
        $admin->update(['last_login_at' => now()]);

        session([
            'admin_authenticated' => true,
            'admin_username'      => $admin->username,
            'admin_name'          => $admin->name,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_authenticated', 'admin_username']);
        return redirect()->route('choose');
    }
}
