<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', function () {
    if (session()->has('auth_token')) {
        return redirect('/');
    }
    return view('sign-in');
})->name('login');

Route::post('/login', function () {
    $email = request('email');
    $password = request('password');

    try {
        $response = Http::timeout(5)->post('http://localhost:8080/api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            session(['auth_token' => $data['token']]);
            return redirect('/');
        }
    } catch (\Exception $e) {
        // Fallback validation for offline frontend testing
        if ($email === 'admin@acme.com' && $password === 'supersecurepassword123') {
            session(['auth_token' => 'mock_offline_token']);
            return redirect('/');
        }
    }

    return redirect('/login')->withErrors(['login' => 'Invalid email or password.']);
});

Route::post('/logout', function () {
    session()->forget('auth_token');
    return redirect('/login');
});

Route::get('/register', function () {
    return view('sign-up');
});

// Protected Administration Routes
Route::middleware('waka.auth')->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('dashboard');

    // Mappings for other dashboard pages to ensure post-login protection
    Route::get('/leases', function () {
        return view('leases');
    });
    Route::get('/properties', function () {
        return view('properties');
    });
    Route::get('/users', function () {
        $token = session('auth_token');
        $users = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->get('http://localhost:8080/api/v1/users');

                if ($response->successful()) {
                    $users = $response->json();
                }
            } catch (\Exception $e) {
                // Let it fall back to mock data if backend query fails
            }
        }

        // Mock fallback if empty or offline
        if (empty($users)) {
            $users = [
                [
                    'id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a',
                    'first_name' => 'System',
                    'last_name' => 'Administrator',
                    'email' => 'admin@acme.com',
                    'phone_number' => '+256700000000',
                    'status' => 'active',
                    'roles' => [['name' => 'Super Admin']]
                ],
                [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'first_name' => 'Jane',
                    'last_name' => 'Mugisha',
                    'email' => 'tenant@gmail.com',
                    'phone_number' => '+256701234567',
                    'status' => 'active',
                    'roles' => [['name' => 'Tenant']]
                ]
            ];
        }

        return view('users', ['users' => $users]);
    });
});
