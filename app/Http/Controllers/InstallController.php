<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InstallController extends Controller
{
    /**
     * Show the installation wizard (admin user + optional company config).
     */
    public function index()
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('install.wizard');
    }

    /**
     * Store the initial admin user and optional company configuration.
     */
    public function store(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // Optional company step
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_gst_number' => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_email' => ['nullable', 'email', 'max:255'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (! empty(trim($validated['company_name'] ?? ''))) {
            Company::create([
                'name' => $validated['company_name'],
                'gst_number' => $validated['company_gst_number'] ?? null,
                'address' => $validated['company_address'] ?? null,
                'phone' => $validated['company_phone'] ?? null,
                'email' => $validated['company_email'] ?? null,
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'Installation complete. Sign in with your new admin account.');
    }
}
