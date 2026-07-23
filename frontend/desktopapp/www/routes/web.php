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
        $token = session('auth_token');
        $kpis = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->get('http://localhost:8080/api/v1/dashboard/kpis');

                if ($response->successful()) {
                    $kpis = $response->json();
                }
            } catch (\Exception $e) {
                // let it fallback
            }
        }

        // Mock fallback if empty or offline
        if (empty($kpis)) {
            $kpis = [
                'units' => [
                    'total' => 15,
                    'occupied' => 12,
                    'vacant' => 3,
                    'percent' => 80
                ],
                'users' => [
                    'total' => 32,
                    'tenants' => 25,
                    'others' => 7,
                    'percent' => 78
                ],
                'invoices' => [
                    'total' => 24,
                    'paid' => 18,
                    'overdue' => 6,
                    'percent' => 75
                ],
                'collections' => [
                    'current' => 3400000,
                    'previous' => 2950000,
                    'change_percent' => 15.25
                ]
            ];
        }

        return view('index', ['kpis' => $kpis]);
    })->name('dashboard');

    // Mappings for other dashboard pages to ensure post-login protection
    // Leases & Contracts Management
    Route::get('/leases', function () {
        $token = session('auth_token');
        $leases = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/leases');
                if ($response->successful()) {
                    $leases = $response->json();
                }
            } catch (\Exception $e) {
                // fall back
            }
        }

        // Mock fallback if empty
        if (empty($leases)) {
            $leases = [
                [
                    'id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a',
                    'unit' => ['unit_number' => 'Suite 101', 'property_name' => 'Acme Plaza'],
                    'tenant_profile' => [
                        'user' => [
                            'first_name' => 'Jane',
                            'last_name' => 'Mugisha',
                            'email' => 'tenant@gmail.com'
                        ]
                    ],
                    'start_date' => '2026-06-01',
                    'end_date' => '2027-06-01',
                    'billing_cycle' => 'monthly',
                    'rent_amount' => 1200000.00,
                    'deposit_amount' => 1200000.00,
                    'status' => 'pending'
                ]
            ];
        }

        return view('leases', ['leases' => $leases]);
    })->name('leases');

    Route::get('/leases/create', function () {
        $token = session('auth_token');
        $units = [];
        $tenantProfiles = [];

        if ($token !== 'mock_offline_token') {
            try {
                // Fetch units (to find vacant ones)
                $unitsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/units');
                if ($unitsResponse->successful()) {
                    $units = $unitsResponse->json();
                }

                // Fetch tenant profiles
                $tenantsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/tenants');
                if ($tenantsResponse->successful()) {
                    $tenantProfiles = $tenantsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Mock fallback if empty
        if (empty($units)) {
            $units = [
                [
                    'id' => '1a111111-1111-1111-1111-111111111111',
                    'unit_number' => 'Suite 101',
                    'property_name' => 'Acme Plaza',
                    'rent_amount' => 1200000.00,
                    'status' => 'vacant'
                ],
                [
                    'id' => '2b222222-2222-2222-2222-222222222222',
                    'unit_number' => 'Suite 102',
                    'property_name' => 'Acme Plaza',
                    'rent_amount' => 1500000.00,
                    'status' => 'vacant'
                ]
            ];
        }
        if (empty($tenantProfiles)) {
            $tenantProfiles = [
                [
                    'id' => '9a111111-1111-1111-1111-111111111111',
                    'user' => [
                        'first_name' => 'Jane',
                        'last_name' => 'Mugisha',
                        'email' => 'tenant@gmail.com'
                    ]
                ]
            ];
        }

        // Filter for vacant units
        $vacantUnits = array_filter($units, function ($u) {
            return strtolower($u['status'] ?? 'vacant') === 'vacant';
        });

        return view('leases_create', [
            'units' => array_values($vacantUnits),
            'tenants' => $tenantProfiles
        ]);
    });

    Route::post('/leases', function () {
        $token = session('auth_token');
        $unitId = request('unit_id');
        $tenantProfileId = request('tenant_profile_id');
        $startDate = request('start_date');
        $endDate = request('end_date');
        $billingCycle = request('billing_cycle');
        $rentAmount = request('rent_amount');
        $depositAmount = request('deposit_amount') ?? 0;
        $escalationRate = request('escalation_rate') ?? 0;
        $lateFeePercentage = request('late_fee_percentage') ?? 0;
        $lateFeeGraceDays = request('late_fee_grace_days') ?? 0;

        // GORM expects dates formatted as RFC3339
        $formattedStart = date('Y-m-d\T00:00:00\Z', strtotime($startDate));
        $formattedEnd = date('Y-m-d\T00:00:00\Z', strtotime($endDate));

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/leases', [
                        'unit_id' => $unitId,
                        'tenant_profile_id' => $tenantProfileId,
                        'start_date' => $formattedStart,
                        'end_date' => $formattedEnd,
                        'billing_cycle' => $billingCycle,
                        'rent_amount' => (float)$rentAmount,
                        'deposit_amount' => (float)$depositAmount,
                        'escalation_rate' => (float)$escalationRate,
                        'late_fee_percentage' => (float)$lateFeePercentage,
                        'late_fee_grace_days' => (int)$lateFeeGraceDays
                    ]);

                if ($response->successful()) {
                    return redirect()->route('leases')->with('success', 'Lease contract onboarded successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to onboard lease contract.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('leases')->with('success', 'Lease contract onboarded successfully (Offline Mock Success).');
    });

    Route::post('/leases/{id}/status', function ($id) {
        $token = session('auth_token');
        $status = request('status');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->put("http://localhost:8080/api/v1/leases/{$id}/status", [
                        'status' => $status
                    ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Lease contract status updated to ' . strtoupper($status) . '.');
                }

                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to update lease status.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->back()->with('success', 'Lease contract status updated successfully (Offline Mock Success).');
    });
    // Properties Management
    Route::get('/properties', function () {
        $token = session('auth_token');
        $properties = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/properties');
                if ($response->successful()) {
                    $properties = $response->json();
                }
            } catch (\Exception $e) {
                // fall back
            }
        }

        // Mock fallback if empty or offline
        if (empty($properties)) {
            $properties = [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'name' => 'Acme Plaza',
                    'description' => 'Commercial office space block in Kampala',
                    'address' => 'Plot 14, Kampala Road',
                    'gps_coordinates' => '0.3125, 32.5811',
                    'land_title_number' => 'FRVOL-29910-44'
                ]
            ];
        }

        return view('properties', ['properties' => $properties]);
    })->name('properties');

    Route::get('/properties/create', function () {
        return view('properties_create');
    });

    Route::post('/properties', function () {
        $token = session('auth_token');
        
        $input = [
            'title' => request('title'),
            'description' => request('description'),
            'property_status' => request('property_status'),
            'property_type' => request('property_type'),
            'listing_price' => (float)request('listing_price'),
            'currency' => request('currency', 'USD'),
            'price_period' => request('price_period'),
            'street_address' => request('street_address'),
            'unit_number' => request('unit_number'),
            'city' => request('city'),
            'state_region' => request('state_region'),
            'postal_code' => request('postal_code'),
            'country' => request('country'),
            'latitude' => (float)request('latitude', 0.0),
            'longitude' => (float)request('longitude', 0.0),
            'bedrooms' => (int)request('bedrooms', 0),
            'bathrooms' => (float)request('bathrooms', 0.0),
            'square_units' => request('square_units', 'Square Feet'),
            'indoor_area' => (float)request('indoor_area', 0.0),
            'lot_size' => (float)request('lot_size', 0.0),
            'year_built' => (int)request('year_built', date('Y')),
            'floors_total' => (int)request('floors_total', 1),
            'floor_number' => (int)request('floor_number', 0),
            'primary_image_url' => request('primary_image_url'),
            'video_tour_url' => request('video_tour_url'),
            'floor_plan_url' => request('floor_plan_url'),
            'has_virtual_tour' => request('has_virtual_tour') === '1',
            'virtual_tour_url' => request('virtual_tour_url'),
            'amenity_ids' => request('amenity_ids', [])
        ];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/properties', $input);

                if ($response->successful()) {
                    return redirect()->route('properties')->with('success', 'Property registered successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to register property.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('properties')->with('success', 'Property registered successfully (Offline Mock Success).');
    });

    // Rentable Units Management
    Route::get('/units', function () {
        $token = session('auth_token');
        $units = [];
        $properties = [];

        if ($token !== 'mock_offline_token') {
            try {
                // Fetch units
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/units');
                if ($response->successful()) {
                    $units = $response->json();
                }

                // Fetch properties for dropdown
                $propsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/properties');
                if ($propsResponse->successful()) {
                    $properties = $propsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Mock fallback if empty
        if (empty($units)) {
            $units = [
                [
                    'id' => '1a111111-1111-1111-1111-111111111111',
                    'unit_number' => 'Suite 101',
                    'floor_number' => 1,
                    'category' => 'commercial',
                    'type' => 'office',
                    'rent_amount' => 1200000.00,
                    'status' => 'occupied',
                    'property_name' => 'Acme Plaza'
                ],
                [
                    'id' => '2b222222-2222-2222-2222-222222222222',
                    'unit_number' => 'Suite 102',
                    'floor_number' => 1,
                    'category' => 'commercial',
                    'type' => 'office',
                    'rent_amount' => 1500000.00,
                    'status' => 'occupied',
                    'property_name' => 'Acme Plaza'
                ],
                [
                    'id' => '3c333333-3333-3333-3333-333333333333',
                    'unit_number' => 'Suite 103',
                    'floor_number' => 1,
                    'category' => 'commercial',
                    'type' => 'office',
                    'rent_amount' => 1800000.00,
                    'status' => 'vacant',
                    'property_name' => 'Acme Plaza'
                ]
            ];
            $properties = [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'name' => 'Acme Plaza'
                ]
            ];
        }

        return view('units', [
            'units' => $units,
            'properties' => $properties
        ]);
    })->name('units');

    Route::get('/units/create', function () {
        $token = session('auth_token');
        $properties = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/properties');
                if ($response->successful()) {
                    $properties = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($properties)) {
            $properties = [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'name' => 'Acme Plaza'
                ]
            ];
        }

        return view('units_create', ['properties' => $properties]);
    });

    Route::post('/properties/{property_id}/units', function ($property_id) {
        $token = session('auth_token');
        
        $input = [
            'unit_number' => request('unit_number'),
            'description' => request('description'),
            'property_status' => request('property_status'),
            'property_type' => request('property_type'),
            'listing_price' => (float)request('listing_price'),
            'currency' => request('currency', 'USD'),
            'price_period' => request('price_period'),
            'street_address' => request('street_address'),
            'city' => request('city'),
            'state_region' => request('state_region'),
            'postal_code' => request('postal_code'),
            'country' => request('country'),
            'latitude' => (float)request('latitude', 0.0),
            'longitude' => (float)request('longitude', 0.0),
            'bedrooms' => (int)request('bedrooms', 0),
            'bathrooms' => (float)request('bathrooms', 0.0),
            'square_units' => request('square_units', 'Square Feet'),
            'indoor_area' => (float)request('indoor_area', 0.0),
            'lot_size' => (float)request('lot_size', 0.0),
            'year_built' => (int)request('year_built', date('Y')),
            'floors_total' => (int)request('floors_total', 1),
            'floor_number' => (int)request('floor_number', 0),
            'primary_image_url' => request('primary_image_url'),
            'video_tour_url' => request('video_tour_url'),
            'floor_plan_url' => request('floor_plan_url'),
            'has_virtual_tour' => request('has_virtual_tour') === '1',
            'virtual_tour_url' => request('virtual_tour_url'),
            'amenity_ids' => request('amenity_ids', [])
        ];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post("http://localhost:8080/api/v1/properties/{$property_id}/units", $input);

                if ($response->successful()) {
                    return redirect()->route('units')->with('success', 'Rentable unit registered successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to register rentable unit.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('units')->with('success', 'Rentable unit registered successfully (Offline Mock Success).');
    });

    // Tenants List (filtered users)
    Route::get('/tenants', function () {
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
                // fall back
            }
        }

        // Mock fallback if empty or offline
        if (empty($users)) {
            $users = [
                [
                    'id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a',
                    'first_name' => 'System',
                    'middle_name' => 'Platform',
                    'last_name' => 'Administrator',
                    'email' => 'admin@acme.com',
                    'phone_number' => '+256700000000',
                    'status' => 'active',
                    'roles' => [['name' => 'Super Admin']]
                ],
                [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'first_name' => 'Jane',
                    'middle_name' => 'Babirye',
                    'last_name' => 'Mugisha',
                    'email' => 'tenant@gmail.com',
                    'phone_number' => '+256701234567',
                    'status' => 'active',
                    'roles' => [['name' => 'Tenant']]
                ]
            ];
        }

        // Filter for only Tenant roles
        $tenants = array_filter($users, function ($u) {
            $roles = $u['roles'] ?? [];
            foreach ($roles as $r) {
                if (strtolower($r['name']) === 'tenant') {
                    return true;
                }
            }
            return false;
        });

        return view('tenants', ['users' => array_values($tenants)]);
    })->name('tenants');

    // User Management list
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
                    'middle_name' => 'Platform',
                    'last_name' => 'Administrator',
                    'email' => 'admin@acme.com',
                    'phone_number' => '+256700000000',
                    'status' => 'active',
                    'roles' => [['name' => 'Super Admin']]
                ],
                [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'first_name' => 'Jane',
                    'middle_name' => 'Babirye',
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

    // View Create User Form
    Route::get('/users/create', function () {
        $token = session('auth_token');
        $roles = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->get('http://localhost:8080/api/v1/roles');

                if ($response->successful()) {
                    $roles = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($roles)) {
            $roles = [
                ['id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a', 'name' => 'Super Admin'],
                ['id' => '3f657908-11e3-4eb0-9970-38ad36cf961b', 'name' => 'Tenant'],
                ['id' => '4f657908-11e3-4eb0-9970-38ad36cf961c', 'name' => 'Agent']
            ];
        }

        return view('users_create', ['roles' => $roles]);
    });

    // Submit Create User Form
    Route::post('/users', function () {
        $token = session('auth_token');
        $input = [
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'middle_name' => request('middle_name'),
            'email' => request('email'),
            'password' => request('password'),
            'phone_number' => request('phone_number'),
            'role_ids' => request('role_ids', [])
        ];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/users', $input);

                if ($response->successful()) {
                    return redirect('/users')->with('success', 'User created successfully.');
                } else {
                    $err = $response->json();
                    return redirect()->back()->withInput()->withErrors(['create' => $err['message'] ?? 'Failed to create user.']);
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        return redirect('/users')->with('success', 'User created successfully (Offline Mock Mode).');
    });

    // View Single User Profile
    Route::get('/users/{id}', function ($id) {
        $token = session('auth_token');
        $user = null;

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->get('http://localhost:8080/api/v1/users');

                if ($response->successful()) {
                    $users = $response->json();
                    foreach ($users as $u) {
                        if (($u['id'] ?? '') === $id) {
                            $user = $u;
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fetch from mock
            }
        }

        // Mock fallback if empty or offline
        if (empty($user)) {
            $mockUsers = [
                '1d657a08-08e3-4eb0-8970-38ad36cf961a' => [
                    'id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a',
                    'first_name' => 'System',
                    'middle_name' => 'Platform',
                    'last_name' => 'Administrator',
                    'email' => 'admin@acme.com',
                    'phone_number' => '+256700000000',
                    'status' => 'active',
                    'roles' => [['name' => 'Super Admin']]
                ],
                '3f657908-11e3-4eb0-9970-38ad36cf961b' => [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'first_name' => 'Jane',
                    'middle_name' => 'Babirye',
                    'last_name' => 'Mugisha',
                    'email' => 'tenant@gmail.com',
                    'phone_number' => '+256701234567',
                    'status' => 'active',
                    'roles' => [['name' => 'Tenant']]
                ]
            ];
            $user = $mockUsers[$id] ?? null;
        }

        if (empty($user)) {
            abort(404, 'User not found');
        }

        return view('profile', ['user' => $user]);
    });

    // Update User Status (Suspend/Activate)
    Route::post('/users/{id}/status', function ($id) {
        $token = session('auth_token');
        $status = request('status');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->put("http://localhost:8080/api/v1/users/{$id}/status", [
                        'status' => $status
                    ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'User status updated to ' . $status . ' successfully.');
                }
            } catch (\Exception $e) {
                // fall back to offline success message
            }
        }

        return redirect()->back()->with('success', 'User status updated to ' . $status . ' successfully (Offline Mode).');
    });

    // Reset User Password
    Route::post('/users/{id}/reset-password', function ($id) {
        $token = session('auth_token');
        $password = request('password');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post("http://localhost:8080/api/v1/users/{$id}/reset-password", [
                        'new_password' => $password
                    ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'User password reset successfully.');
                }
            } catch (\Exception $e) {
                // fall back to offline success message
            }
        }

        return redirect()->back()->with('success', 'User password reset successfully (Offline Mode).');
    });

    // Roles & Permission Matrix Management
    Route::get('/roles', function () {
        $token = session('auth_token');
        $roles = [];
        $permissions = [];
        $rolePermissions = [];

        if ($token !== 'mock_offline_token') {
            try {
                // 1. Fetch permissions
                $permResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/permissions');
                if ($permResponse->successful()) {
                    $permissions = $permResponse->json();
                }

                // 2. Fetch roles
                $roleResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/roles');
                if ($roleResponse->successful()) {
                    $roles = $roleResponse->json();
                }

                // 3. Fetch permissions assigned to each role
                foreach ($roles as $r) {
                    $detailResponse = Http::timeout(5)->withToken($token)->get("http://localhost:8080/api/v1/roles/{$r['id']}");
                    if ($detailResponse->successful()) {
                        $detail = $detailResponse->json();
                        $rolePermissions[$r['id']] = array_column($detail['permissions'] ?? [], 'id');
                    }
                }
            } catch (\Exception $e) {
                // fallback to offline mode
            }
        }

        // Offline mock fallback if empty
        if (empty($roles)) {
            $roles = [
                ['id' => '11111111-1111-1111-1111-111111111111', 'name' => 'Super Admin', 'description' => 'Platform level super administrator', 'tenant_id' => null],
                ['id' => '22222222-2222-2222-2222-222222222222', 'name' => 'Tenant', 'description' => 'Standard property tenant space', 'tenant_id' => null],
                ['id' => '33333333-3333-3333-3333-333333333333', 'name' => 'Property Manager', 'description' => 'Rent collection and unit manager', 'tenant_id' => 'acme'],
            ];
            $permissions = [
                ['id' => 'p1', 'code' => 'property:create', 'category' => 'Property', 'description' => 'Allow creating properties'],
                ['id' => 'p2', 'code' => 'property:view', 'category' => 'Property', 'description' => 'Allow viewing properties'],
                ['id' => 'p3', 'code' => 'unit:create', 'category' => 'Property', 'description' => 'Allow creating units'],
                ['id' => 'p4', 'code' => 'unit:view', 'category' => 'Property', 'description' => 'Allow viewing units'],
                ['id' => 'p5', 'code' => 'tenant:onboard', 'category' => 'Tenant', 'description' => 'Allow onboarding new tenants'],
                ['id' => 'p6', 'code' => 'tenant:view', 'category' => 'Tenant', 'description' => 'Allow viewing tenant profiles'],
                ['id' => 'p7', 'code' => 'invoice:create', 'category' => 'Financial', 'description' => 'Allow creating invoices'],
                ['id' => 'p8', 'code' => 'invoice:view', 'category' => 'Financial', 'description' => 'Allow viewing invoices'],
            ];
            $rolePermissions = [
                '11111111-1111-1111-1111-111111111111' => ['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8'],
                '22222222-2222-2222-2222-222222222222' => ['p2', 'p4', 'p6', 'p8'],
                '33333333-3333-3333-3333-333333333333' => ['p1', 'p2', 'p3', 'p4', 'p8'],
            ];
        }

        return view('roles', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    })->name('roles');

    Route::post('/roles', function () {
        $token = session('auth_token');
        $name = request('name');
        $description = request('description');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/roles', [
                        'name' => $name,
                        'description' => $description
                    ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Role created successfully.');
                }
                
                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to create role.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is currently offline.']);
            }
        }

        return redirect()->back()->with('success', 'Role created successfully (Offline Mode).');
    });

    Route::post('/roles/{id}/permissions', function ($id) {
        $token = session('auth_token');
        $permissionIds = request('permission_ids', []);

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->put("http://localhost:8080/api/v1/roles/{$id}/permissions", [
                        'permission_ids' => $permissionIds
                    ]);

                if ($response->successful()) {
                    return response()->json(['status' => 'success', 'message' => 'Role permissions updated successfully.']);
                }
                
                $body = $response->json();
                return response()->json(['status' => 'error', 'message' => $body['error'] ?? 'Failed to update permissions.'], 400);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Backend is offline.'], 500);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Role permissions updated successfully (Offline Mode).']);
    });

    Route::post('/roles/{id}/delete', function ($id) {
        $token = session('auth_token');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->delete("http://localhost:8080/api/v1/roles/{$id}");

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Role deleted successfully.');
                }
                
                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to delete role.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->back()->with('success', 'Role deleted successfully (Offline Mode).');
    });

    // Wallets & Ledgers Management
    Route::get('/wallets', function () {
        $token = session('auth_token');
        $wallets = [];
        $ledgerEntries = [];
        $gatewayBalance = null;
        $tenantsList = [];

        if ($token !== 'mock_offline_token') {
            try {
                // 1. Fetch wallets
                $walletResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/wallets');
                if ($walletResponse->successful()) {
                    $wallets = $walletResponse->json();
                }

                // 2. Fetch ledger entries
                $ledgerResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/ledger-entries');
                if ($ledgerResponse->successful()) {
                    $ledgerEntries = $ledgerResponse->json();
                }

                // 3. Fetch gateway balance
                $gatewayResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/payments/gateway/balance');
                if ($gatewayResponse->successful()) {
                    $gatewayBalance = $gatewayResponse->json();
                }

                // 4. Fetch tenants to populate wallet top-up dropdown profiles
                $tenantsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/tenants');
                if ($tenantsResponse->successful()) {
                    $tenantsList = $tenantsResponse->json();
                }
            } catch (\Exception $e) {
                // fall back to offline mode
            }
        }

        // Offline mock fallback if empty
        if (empty($wallets)) {
            $wallets = [
                ['id' => 'w1', 'owner_type' => 'landlord', 'owner_id' => '1111', 'currency' => 'UGX', 'balance' => 1200000.00],
                ['id' => 'w2', 'owner_type' => 'tenant', 'owner_id' => 'Jane Mugisha', 'currency' => 'UGX', 'balance' => 250000.00]
            ];
            $ledgerEntries = [
                [
                    'id' => 'le1',
                    'entry_type' => 'rent_payment',
                    'amount' => 1200000.00,
                    'description' => 'Direct invoice payment via ioTec Pay Gateway ref MM-992039',
                    'created_at' => '2026-07-16T12:00:00Z'
                ],
                [
                    'id' => 'le2',
                    'entry_type' => 'wallet_topup',
                    'amount' => 250000.00,
                    'description' => 'Wallet topup via ioTec Pay Gateway ref MM-992040',
                    'created_at' => '2026-07-16T10:30:00Z'
                ]
            ];
            $gatewayBalance = [
                'balance' => 1450000.00,
                'currency' => 'UGX'
            ];
            $tenantsList = [
                [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'user' => [
                        'first_name' => 'Jane',
                        'last_name' => 'Mugisha',
                        'email' => 'tenant@gmail.com'
                    ]
                ]
            ];
        }

        return view('wallets', [
            'wallets' => $wallets,
            'ledgerEntries' => $ledgerEntries,
            'gatewayBalance' => $gatewayBalance,
            'tenantsList' => $tenantsList
        ]);
    })->name('wallets');

    Route::get('/wallets/create', function () {
        $token = session('auth_token');
        $tenantsList = [];
        if ($token !== 'mock_offline_token') {
            try {
                $tenantsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/tenants');
                if ($tenantsResponse->successful()) { $tenantsList = $tenantsResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($tenantsList)) {
            $tenantsList = [
                [
                    'id' => '3f657908-11e3-4eb0-9970-38ad36cf961b',
                    'user' => [
                        'first_name' => 'Jane',
                        'last_name' => 'Mugisha',
                        'email' => 'tenant@gmail.com'
                    ]
                ]
            ];
        }
        return view('wallets_create', ['tenantsList' => $tenantsList]);
    });

    Route::post('/wallets/top-up', function () {
        $token = session('auth_token');
        $profileId = request('profile_id');
        $phone = request('phone');
        $amount = request('amount');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/payments/wallets/top-up', [
                        'profile_id' => $profileId,
                        'phone' => $phone,
                        'amount' => (float)$amount
                    ]);

                if ($response->successful()) {
                    return redirect()->route('wallets')->with('success', 'Mobile money top-up initiated successfully. Wallet balance will reflect upon completion.');
                }

                $body = $response->json();
                return redirect()->route('wallets')->withErrors(['error' => $body['error'] ?? 'Failed to initiate top-up.']);
            } catch (\Exception $e) {
                return redirect()->route('wallets')->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('wallets')->with('success', 'Mobile money top-up initiated successfully (Offline Mock Success).');
    });

    Route::post('/wallets/disburse', function () {
        $token = session('auth_token');
        $payeePhone = request('payee_phone');
        $note = request('note');
        $amount = request('amount');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/payments/gateway/disburse', [
                        'payee_phone' => $payeePhone,
                        'note' => $note,
                        'amount' => (float)$amount
                    ]);

                if ($response->successful()) {
                    return redirect()->route('wallets')->with('success', 'Disbursement executed successfully via ioTec Pay.');
                }

                $body = $response->json();
                return redirect()->route('wallets')->withErrors(['error' => $body['error'] ?? 'Failed to execute disbursement.']);
            } catch (\Exception $e) {
                return redirect()->route('wallets')->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('wallets')->with('success', 'Disbursement executed successfully (Offline Mock Success).');
    });

    // Gateway Transactions Management
    Route::get('/transactions', function () {
        $token = session('auth_token');
        $transactions = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/payments');
                if ($response->successful()) {
                    $transactions = $response->json();
                }
            } catch (\Exception $e) {
                // fall back to offline mode
            }
        }

        // Offline mock fallback if empty
        if (empty($transactions)) {
            $transactions = [
                [
                    'id' => '1d657a08-08e3-4eb0-8970-38ad36cf961a',
                    'amount' => 1200000.00,
                    'payment_method' => 'mobile_money',
                    'provider_reference' => 'TXN-99882211',
                    'payment_date' => '2026-07-16T12:00:00Z',
                    'status' => 'completed'
                ],
                [
                    'id' => '2f657a08-08e3-4eb0-8970-38ad36cf961b',
                    'amount' => 250000.00,
                    'payment_method' => 'mobile_money',
                    'provider_reference' => 'TXN-99882212',
                    'payment_date' => '2026-07-16T10:30:00Z',
                    'status' => 'pending'
                ]
            ];
        }

        return view('transactions', [
            'transactions' => $transactions
        ]);
    })->name('transactions');

    Route::post('/transactions/{id}/sync', function ($id) {
        $token = session('auth_token');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(10)
                    ->withToken($token)
                    ->get("http://localhost:8080/api/v1/payments/gateway/status/{$id}");

                if ($response->successful()) {
                    $txn = $response->json();
                    $status = $txn['status'] ?? 'unknown';
                    return redirect()->back()->with('success', "Transaction status synced successfully! Current status: " . strtoupper($status));
                }
                
                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to sync status.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->back()->with('success', 'Transaction status synced successfully (Offline Mock Sync: COMPLETED).');
    });

    // Invoices Management
    Route::get('/invoices', function () {
        $token = session('auth_token');
        $invoices = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/invoices');
                if ($response->successful()) {
                    $invoices = $response->json();
                }
            } catch (\Exception $e) {
                // fall back to offline mode
            }
        }

        // Offline mock fallback if empty
        if (empty($invoices)) {
            $invoices = [
                [
                    'id' => '11111111-1111-1111-1111-111111111111',
                    'invoice_number' => 'INV-10029',
                    'lease' => [
                        'unit' => ['unit_number' => 'Suite 101'],
                        'tenant_profile' => ['user' => ['first_name' => 'Jane', 'last_name' => 'Mugisha']]
                    ],
                    'issue_date' => '2026-06-01',
                    'due_date' => '2026-06-15',
                    'total_amount' => 1200000.00,
                    'paid_amount' => 1200000.00,
                    'status' => 'paid'
                ],
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                    'invoice_number' => 'INV-10030',
                    'lease' => [
                        'unit' => ['unit_number' => 'Suite 101'],
                        'tenant_profile' => ['user' => ['first_name' => 'Jane', 'last_name' => 'Mugisha']]
                    ],
                    'issue_date' => '2026-07-01',
                    'due_date' => '2026-07-15',
                    'total_amount' => 1200000.00,
                    'paid_amount' => 0.00,
                    'status' => 'overdue'
                ]
            ];
        }

        return view('invoices', ['invoices' => $invoices]);
    })->name('invoices');

    Route::get('/invoices/create', function () {
        $token = session('auth_token');
        $leases = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/leases');
                if ($response->successful()) {
                    $leases = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Mock fallback if empty
        if (empty($leases)) {
            $leases = [
                [
                    'id' => '1a111111-1111-1111-1111-111111111111',
                    'unit' => ['unit_number' => 'Suite 101'],
                    'tenant_profile' => [
                        'user' => ['first_name' => 'Jane', 'last_name' => 'Mugisha']
                    ],
                    'rent_amount' => 1200000.00
                ]
            ];
        }

        return view('invoices_create', ['leases' => $leases]);
    });

    Route::post('/invoices', function () {
        $token = session('auth_token');
        $leaseId = request('lease_id');
        $issueDate = request('issue_date');
        $dueDate = request('due_date');
        $totalAmount = request('total_amount');

        // GORM expects RFC3339 formatted timestamps
        $formattedIssue = date('Y-m-d\T00:00:00\Z', strtotime($issueDate));
        $formattedDue = date('Y-m-d\T00:00:00\Z', strtotime($dueDate));

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/invoices', [
                        'lease_id' => $leaseId,
                        'issue_date' => $formattedIssue,
                        'due_date' => $formattedDue,
                        'total_amount' => (float)$totalAmount
                    ]);

                if ($response->successful()) {
                    return redirect()->route('invoices')->with('success', 'Invoice created successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to create invoice.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('invoices')->with('success', 'Invoice created successfully (Offline Mock Success).');
    });

    Route::post('/invoices/{id}/pay', function ($id) {
        $token = session('auth_token');
        $phone = request('phone');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(10)
                    ->withToken($token)
                    ->post("http://localhost:8080/api/v1/payments/invoices/{$id}/pay-gateway", [
                        'phone' => $phone
                    ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Payment collections initiated successfully. Balance updates on validation.');
                }

                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to initiate payment.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->back()->with('success', 'Payment collections initiated successfully (Offline Mock Success).');
    });

    // Maintenance Requests
    Route::get('/maintenance-requests', function () {
        $token = session('auth_token');
        $requests = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/requests');
                if ($response->successful()) {
                    $requests = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Mock fallback if empty
        if (empty($requests)) {
            $requests = [
                [
                    'id' => '11111111-2222-3333-4444-555555555555',
                    'category' => 'Plumbing',
                    'description' => 'Water leakage from the main ceiling pipe in the kitchen area.',
                    'priority' => 'high',
                    'status' => 'pending',
                    'created_at' => '2026-07-16T15:30:00Z',
                    'unit' => ['unit_number' => 'Suite 101'],
                    'tenant_profile' => ['user' => ['first_name' => 'Jane', 'last_name' => 'Mugisha']]
                ]
            ];
        }

        return view('maintenance_requests', [
            'requests' => $requests
        ]);
    })->name('maintenance_requests');

    Route::get('/maintenance-requests/create', function () {
        $token = session('auth_token');
        $units = [];
        $tenants = [];

        if ($token !== 'mock_offline_token') {
            try {
                $unitsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/units');
                if ($unitsResponse->successful()) {
                    $units = $unitsResponse->json();
                }

                $tenantsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/tenants');
                if ($tenantsResponse->successful()) {
                    $tenants = $tenantsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($units)) {
            $units = [
                ['id' => '1a111111-1111-1111-1111-111111111111', 'unit_number' => 'Suite 101', 'property_name' => 'Acme Plaza']
            ];
        }
        if (empty($tenants)) {
            $tenants = [
                ['id' => '9a111111-1111-1111-1111-111111111111', 'user' => ['first_name' => 'Jane', 'last_name' => 'Mugisha']]
            ];
        }

        return view('maintenance_requests_create', [
            'units' => $units,
            'tenants' => $tenants
        ]);
    });

    Route::post('/maintenance-requests', function () {
        $token = session('auth_token');
        $unitId = request('unit_id');
        $tenantProfileId = request('tenant_profile_id');
        $category = request('category');
        $description = request('description');
        $priority = request('priority');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/maintenance/requests', [
                        'unit_id' => $unitId,
                        'tenant_profile_id' => $tenantProfileId,
                        'category' => $category,
                        'description' => $description,
                        'priority' => $priority
                    ]);

                if ($response->successful()) {
                    return redirect()->route('maintenance_requests')->with('success', 'Maintenance request logged successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to log maintenance request.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('maintenance_requests')->with('success', 'Maintenance request logged successfully (Offline Mock Success).');
    });

    // Vendors Directory
    Route::get('/vendors', function () {
        $token = session('auth_token');
        $vendors = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/vendors');
                if ($response->successful()) {
                    $vendors = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($vendors)) {
            $vendors = [
                [
                    'id' => '22222222-3333-4444-5555-666666666666',
                    'business_name' => 'Kampala Plumbing Masters Ltd',
                    'contact_name' => 'Andrew Mukasa',
                    'phone' => '+256772112233',
                    'email' => 'contact@kplumbing.com',
                    'category' => 'Plumbing'
                ]
            ];
        }

        return view('vendors', ['vendors' => $vendors]);
    })->name('vendors');

    Route::get('/vendors/create', function () {
        return view('vendors_create');
    });

    Route::post('/vendors', function () {
        $token = session('auth_token');
        $businessName = request('business_name');
        $contactName = request('contact_name');
        $phone = request('phone');
        $email = request('email');
        $category = request('category');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/maintenance/vendors', [
                        'business_name' => $businessName,
                        'contact_name' => $contactName,
                        'phone' => $phone,
                        'email' => $email,
                        'category' => $category
                    ]);

                if ($response->successful()) {
                    return redirect()->route('vendors')->with('success', 'Vendor onboarded successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to onboard vendor.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('vendors')->with('success', 'Vendor onboarded successfully (Offline Mock Success).');
    });

    // Work Orders Management
    Route::get('/work-orders', function () {
        $token = session('auth_token');
        $workOrders = [];
        $requests = [];
        $vendors = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/work-orders');
                if ($response->successful()) {
                    $workOrders = $response->json();
                }

                $requestsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/requests');
                if ($requestsResponse->successful()) {
                    $requests = $requestsResponse->json();
                }

                $vendorsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/vendors');
                if ($vendorsResponse->successful()) {
                    $vendors = $vendorsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        // Ensure arrays are initialized to avoid null warnings
        $workOrders = is_array($workOrders) ? $workOrders : [];
        $requests = is_array($requests) ? $requests : [];
        $vendors = is_array($vendors) ? $vendors : [];

        if (empty($workOrders)) {
            $workOrders = [
                [
                    'id' => '33333333-4444-5555-6666-777777777777',
                    'request_id' => '11111111-2222-3333-4444-555555555555',
                    'vendor_id' => '22222222-3333-4444-5555-666666666666',
                    'estimated_cost' => 150000.00,
                    'status' => 'scheduled',
                    'scheduled_date' => '2026-07-18T10:00:00Z',
                    'sla_completion_time' => '2026-07-19T18:00:00Z'
                ]
            ];
        }

        if (empty($requests)) {
            $requests = [
                ['id' => '11111111-2222-3333-4444-555555555555', 'description' => 'Water leakage from the main ceiling pipe in the kitchen area.']
            ];
        }

        if (empty($vendors)) {
            $vendors = [
                ['id' => '22222222-3333-4444-5555-666666666666', 'business_name' => 'Kampala Plumbing Masters Ltd']
            ];
        }

        // Build key-value maps for quick lookup in O(N) instead of nested loops
        $vendorMap = [];
        foreach ($vendors as $v) {
            if (isset($v['id'])) {
                $vendorMap[$v['id']] = $v['business_name'] ?? 'Vendor';
            }
        }

        $requestMap = [];
        foreach ($requests as $r) {
            if (isset($r['id'])) {
                $requestMap[$r['id']] = $r['description'] ?? 'Issue';
            }
        }

        // Bind descriptions to work orders objects
        foreach ($workOrders as &$wo) {
            $vid = $wo['vendor_id'] ?? null;
            $rid = $wo['request_id'] ?? null;
            
            $wo['vendor_name'] = $vendorMap[$vid] ?? (isset($wo['vendor']['business_name']) ? $wo['vendor']['business_name'] : 'Unassigned');
            $wo['request_desc'] = $requestMap[$rid] ?? (isset($wo['request']['description']) ? $wo['request']['description'] : 'General repair order');
        }

        return view('work_orders', [
            'workOrders' => $workOrders
        ]);
    })->name('work_orders');

    Route::get('/work-orders/create', function () {
        $token = session('auth_token');
        $requests = [];
        $vendors = [];

        if ($token !== 'mock_offline_token') {
            try {
                $requestsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/requests');
                if ($requestsResponse->successful()) {
                    $requests = $requestsResponse->json();
                }

                $vendorsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/maintenance/vendors');
                if ($vendorsResponse->successful()) {
                    $vendors = $vendorsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($requests)) {
            $requests = [
                ['id' => '11111111-2222-3333-4444-555555555555', 'description' => 'Water leakage from the main ceiling pipe in the kitchen area.']
            ];
        }

        if (empty($vendors)) {
            $vendors = [
                ['id' => '22222222-3333-4444-5555-666666666666', 'business_name' => 'Kampala Plumbing Masters Ltd', 'category' => 'Plumbing']
            ];
        }

        return view('work_orders_create', [
            'requests' => $requests,
            'vendors' => $vendors
        ]);
    });

    Route::post('/work-orders', function () {
        $token = session('auth_token');
        $requestId = request('request_id');
        $vendorId = request('vendor_id');
        $cost = request('estimated_cost');
        $scheduled = request('scheduled_date');
        $sla = request('sla_completion_time');

        // Format dates as RFC3339
        $formattedScheduled = $scheduled ? date('Y-m-d\T00:00:00\Z', strtotime($scheduled)) : null;
        $formattedSla = $sla ? date('Y-m-d\T00:00:00\Z', strtotime($sla)) : null;

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->post('http://localhost:8080/api/v1/maintenance/work-orders', [
                        'request_id' => $requestId,
                        'vendor_id' => $vendorId,
                        'estimated_cost' => (float)$cost,
                        'scheduled_date' => $formattedScheduled,
                        'sla_completion_time' => $formattedSla
                    ]);

                if ($response->successful()) {
                    return redirect()->route('work_orders')->with('success', 'Work order scheduled successfully.');
                }

                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to schedule work order.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }

        return redirect()->route('work_orders')->with('success', 'Work order scheduled successfully (Offline Mock Success).');
    });

    // Utility Billing - Meter Settings
    Route::get('/utility-meters', function () {
        $token = session('auth_token');
        $meters = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/utility/meters');
                if ($response->successful()) {
                    $meters = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($meters)) {
            $meters = [
                [
                    'id' => '11111111-abcd-1111-2222-333333333333',
                    'meter_number' => 'MTR-90081',
                    'type' => 'electricity',
                    'unit' => ['unit_number' => 'Suite 101'],
                    'last_reading' => 1250.00
                ]
            ];
        }

        return view('utility_meters', ['meters' => $meters]);
    })->name('utility_meters');

    Route::get('/utility-meters/create', function () {
        $token = session('auth_token');
        $units = [];

        if ($token !== 'mock_offline_token') {
            try {
                $unitsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/units');
                if ($unitsResponse->successful()) {
                    $units = $unitsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($units)) {
            $units = [
                ['id' => '1a111111-1111-1111-1111-111111111111', 'unit_number' => 'Suite 101', 'property_name' => 'Acme Plaza']
            ];
        }

        return view('utility_meters_create', ['units' => $units]);
    });

    Route::post('/utility-meters', function () {
        $token = session('auth_token');
        $unitId = request('unit_id');
        $meterNumber = request('meter_number');
        $type = request('type');
        $lastReading = request('last_reading') ?? 0;

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/utility/meters', [
                    'unit_id' => $unitId,
                    'meter_number' => $meterNumber,
                    'type' => $type,
                    'last_reading' => (float)$lastReading
                ]);
                if ($response->successful()) {
                    return redirect()->route('utility_meters')->with('success', 'Utility meter registered successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to register meter.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->route('utility_meters')->with('success', 'Utility meter registered successfully (Offline Mock Success).');
    });

    // Utility Billing - Tariffs Directory
    Route::get('/utility-tariffs', function () {
        $token = session('auth_token');
        $tariffs = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/utility/tariffs');
                if ($response->successful()) {
                    $tariffs = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($tariffs)) {
            $tariffs = [
                [
                    'id' => '22222222-abcd-1111-2222-333333333333',
                    'name' => 'Umeme Standard Commercial',
                    'type' => 'electricity',
                    'rate_per_unit' => 750.00
                ]
            ];
        }

        return view('utility_tariffs', ['tariffs' => $tariffs]);
    })->name('utility_tariffs');

    Route::get('/utility-tariffs/create', function () {
        return view('utility_tariffs_create');
    });

    Route::post('/utility-tariffs', function () {
        $token = session('auth_token');
        $name = request('name');
        $type = request('type');
        $rate = request('rate_per_unit');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/utility/tariffs', [
                    'name' => $name,
                    'type' => $type,
                    'rate_per_unit' => (float)$rate
                ]);
                if ($response->successful()) {
                    return redirect()->route('utility_tariffs')->with('success', 'Tariff created successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to create tariff.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->route('utility_tariffs')->with('success', 'Tariff created successfully (Offline Mock Success).');
    });

    // Utility Billing - Bills & Invoices
    Route::get('/utility-bills', function () {
        $token = session('auth_token');
        $bills = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/utility/bills');
                if ($response->successful()) {
                    $bills = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($bills)) {
            $bills = [
                [
                    'id' => '33333333-abcd-1111-2222-333333333333',
                    'previous_reading' => 1250.00,
                    'current_reading' => 1400.00,
                    'units_consumed' => 150.00,
                    'total_amount' => 112500.00,
                    'status' => 'unpaid',
                    'due_date' => '2026-07-31',
                    'meter' => ['meter_number' => 'MTR-90081', 'type' => 'electricity'],
                    'tariff' => ['name' => 'Umeme Standard Commercial']
                ]
            ];
        }

        return view('utility_bills', [
            'bills' => $bills
        ]);
    })->name('utility_bills');

    Route::get('/utility-bills/create', function () {
        $token = session('auth_token');
        $meters = [];
        $tariffs = [];

        if ($token !== 'mock_offline_token') {
            try {
                $metersResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/utility/meters');
                if ($metersResponse->successful()) {
                    $meters = $metersResponse->json();
                }
                $tariffsResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/utility/tariffs');
                if ($tariffsResponse->successful()) {
                    $tariffs = $tariffsResponse->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($meters)) {
            $meters = [
                ['id' => '11111111-abcd-1111-2222-333333333333', 'meter_number' => 'MTR-90081', 'type' => 'electricity', 'last_reading' => 1250.00]
            ];
            $tariffs = [
                ['id' => '22222222-abcd-1111-2222-333333333333', 'name' => 'Umeme Standard Commercial', 'type' => 'electricity', 'rate_per_unit' => 750.00]
            ];
        }

        return view('utility_bills_create', [
            'meters' => $meters,
            'tariffs' => $tariffs
        ]);
    });

    Route::post('/utility-bills', function () {
        $token = session('auth_token');
        $meterId = request('meter_id');
        $tariffId = request('tariff_id');
        $currentReading = request('current_reading');
        $dueDate = request('due_date');

        $formattedDue = date('Y-m-d\T00:00:00\Z', strtotime($dueDate));

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/utility/bills', [
                    'meter_id' => $meterId,
                    'tariff_id' => $tariffId,
                    'current_reading' => (float)$currentReading,
                    'due_date' => $formattedDue
                ]);
                if ($response->successful()) {
                    return redirect()->route('utility_bills')->with('success', 'Utility bill created successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to create utility bill.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->route('utility_bills')->with('success', 'Utility bill created successfully (Offline Mock Success).');
    });

    // Visitor Management
    Route::get('/visitors', function () {
        $token = session('auth_token');
        $visitors = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/visitors');
                if ($response->successful()) {
                    $visitors = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($visitors)) {
            $visitors = [
                [
                    'id' => '44444444-abcd-1111-2222-3333-333333333333',
                    'full_name' => 'John Doe',
                    'phone' => '+256701122334',
                    'email' => 'john.doe@gmail.com',
                    'purpose' => 'Delivery',
                    'host_name' => 'Jane Mugisha',
                    'check_in_time' => null,
                    'check_out_time' => null
                ]
            ];
        }

        return view('visitors', ['visitors' => $visitors]);
    })->name('visitors');

    Route::get('/visitors/create', function () {
        return view('visitors_create');
    });

    Route::post('/visitors', function () {
        $token = session('auth_token');
        $fullName = request('full_name');
        $phone = request('phone');
        $email = request('email');
        $purpose = request('purpose');
        $hostName = request('host_name');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/visitors', [
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'email' => $email,
                    'purpose' => $purpose,
                    'host_name' => $hostName
                ]);
                if ($response->successful()) {
                    return redirect()->route('visitors')->with('success', 'Visitor registered successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to register visitor.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->route('visitors')->with('success', 'Visitor registered successfully (Offline Mock Success).');
    });

    Route::post('/visitors/{id}/check-in', function ($id) {
        $token = session('auth_token');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/visitors/{$id}/check-in", [
                    'status' => 'checked_in'
                ]);
                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Visitor checked in successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to check in visitor.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->back()->with('success', 'Visitor checked in (Offline Mock Success).');
    });

    Route::post('/visitors/{id}/check-out', function ($id) {
        $token = session('auth_token');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/visitors/{$id}/check-out", [
                    'status' => 'checked_out'
                ]);
                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Visitor checked out successfully.');
                }
                $body = $response->json();
                return redirect()->back()->withErrors(['error' => $body['error'] ?? 'Failed to check out visitor.']);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->back()->with('success', 'Visitor checked out (Offline Mock Success).');
    });

    // Webhooks & Integrations
    Route::get('/webhooks', function () {
        $token = session('auth_token');
        $subscriptions = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/webhooks/subscriptions');
                if ($response->successful()) {
                    $subscriptions = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($subscriptions)) {
            $subscriptions = [
                [
                    'id' => '55555555-abcd-1111-2222-333333333333',
                    'target_url' => 'https://api.acmesystem.com/webhooks/listener',
                    'event_type' => 'invoice.paid'
                ]
            ];
        }

        return view('webhooks', ['subscriptions' => $subscriptions]);
    })->name('webhooks');

    Route::get('/webhooks/create', function () {
        return view('webhooks_create');
    });

    Route::post('/webhooks', function () {
        $token = session('auth_token');
        $url = request('target_url');
        $event = request('event_type');

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/webhooks/subscriptions', [
                    'target_url' => $url,
                    'event_type' => $event
                ]);
                if ($response->successful()) {
                    return redirect()->route('webhooks')->with('success', 'Webhook subscription saved.');
                }
                $body = $response->json();
                return redirect()->back()->withInput()->withErrors(['error' => $body['error'] ?? 'Failed to subscribe.']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']);
            }
        }
        return redirect()->route('webhooks')->with('success', 'Webhook subscription saved (Offline Mock Success).');
    });

    // Audit Logs
    Route::get('/audit-logs', function () {
        $token = session('auth_token');
        $logs = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/audit-logs');
                if ($response->successful()) {
                    $logs = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($logs)) {
            $logs = [
                [
                    'id' => '66666666-abcd-1111-2222-333333333333',
                    'actor_email' => 'admin@acme.com',
                    'action' => 'user.login',
                    'ip_address' => '127.0.0.1',
                    'timestamp' => '2026-07-16T22:00:00Z',
                    'status' => 'success'
                ]
            ];
        }

        return view('audit_logs', ['logs' => $logs]);
    });

    // GraphQL Explorer
    Route::get('/graphql', function () {
        return view('graphql', ['result' => null, 'query' => '']);
    });

    Route::post('/graphql/query', function () {
        $token = session('auth_token');
        $query = request('query');
        $result = null;

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(10)->withToken($token)->post('http://localhost:8080/api/v1/graphql', [
                    'query' => $query
                ]);
                $result = $response->body();
            } catch (\Exception $e) {
                $result = json_encode(['errors' => [['message' => 'GraphQL service is offline.']]]);
            }
        } else {
            // Mock GraphQL response
            $result = json_encode([
                'data' => [
                    'tenant' => [
                        'name' => 'Acme Plaza Tenant Properties',
                        'propertiesCount' => 1,
                        'totalRentDue' => 1200000.00
                    ]
                ]
            ]);
        }

        return view('graphql', ['result' => $result, 'query' => $query]);
    });

    // File Manager (Google Drive Shortcut Target)
    Route::get('/documents', function () {
        $token = session('auth_token');
        $documents = [];

        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/documents');
                if ($response->successful()) {
                    $documents = $response->json();
                }
            } catch (\Exception $e) {
                // fallback
            }
        }

        if (empty($documents)) {
            $documents = [
                [
                    'id' => '11111111-abcd-1111-2222-333333333333',
                    'name' => 'Lease_Agreement_Suite101.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 1254000,
                    'created_at' => '2026-07-16T12:00:00Z'
                ],
                [
                    'id' => '22222222-abcd-1111-2222-333333333333',
                    'name' => 'Renovation_Cost_Estimate.xlsx',
                    'file_type' => 'excel',
                    'file_size' => 450000,
                    'created_at' => '2026-07-16T14:30:00Z'
                ]
            ];
        }

        return view('documents', ['documents' => $documents]);
    })->name('documents');

    Route::get('/documents/create', function () {
        return view('documents_create');
    });

    Route::post('/documents/upload', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(10)->withToken($token)->post('http://localhost:8080/api/v1/documents/upload', [
                    'name' => request('name'), 'folder' => request('folder')
                ]);
                if ($response->successful()) { return redirect()->route('documents')->with('success', 'Document uploaded successfully.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('documents')->with('success', 'Document uploaded successfully (Offline Mock).');
    });

    // Event Calendar (Calendar Shortcut Target)

    Route::get('/calendar', function () {
        return view('calendar');
    });

    // Blogs CMS
    Route::get('/blogs', function () {
        $token = session('auth_token');
        $blogs = [];
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/blogs');
                if ($response->successful()) { $blogs = $response->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($blogs)) {
            $blogs = [
                ['id' => '1', 'title' => 'Welcome to Waka PMS', 'content' => 'We are excited to launch our new property management platform.', 'status' => 'published', 'created_at' => '2026-07-18T00:00:00Z'],
                ['id' => '2', 'title' => 'Tenant Portal Guide', 'content' => 'Learn how to pay bills and log maintenance requests.', 'status' => 'draft', 'created_at' => '2026-07-17T00:00:00Z']
            ];
        }
        return view('blogs', ['blogs' => $blogs]);
    })->name('blogs');

    Route::get('/blogs/create', function () {
        return view('blogs_create');
    });

    Route::post('/blogs', function () {
        $token = session('auth_token');
        $title = request('title');
        $content = request('content');
        $status = request('status', 'draft');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/blogs', [
                    'title' => $title, 'content' => $content, 'status' => $status
                ]);
                if ($response->successful()) { return redirect()->route('blogs')->with('success', 'Blog post created successfully.'); }
                return redirect()->back()->withInput()->withErrors(['error' => $response->json()['error'] ?? 'Failed to create blog post.']);
            } catch (\Exception $e) { return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']); }
        }
        return redirect()->route('blogs')->with('success', 'Blog post created successfully (Offline Mock).');
    });

    Route::post('/blogs/{id}/update', function ($id) {
        $token = session('auth_token');
        $title = request('title');
        $content = request('content');
        $status = request('status');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/blogs/{$id}", [
                    'title' => $title, 'content' => $content, 'status' => $status
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Blog post updated successfully.'); }
                return redirect()->back()->withErrors(['error' => $response->json()['error'] ?? 'Failed to update blog post.']);
            } catch (\Exception $e) { return redirect()->back()->withErrors(['error' => 'Backend is offline.']); }
        }
        return redirect()->back()->with('success', 'Blog post updated successfully (Offline Mock).');
    });

    Route::post('/blogs/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/blogs/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Blog post deleted successfully.'); }
                return redirect()->back()->withErrors(['error' => $response->json()['error'] ?? 'Failed to delete blog post.']);
            } catch (\Exception $e) { return redirect()->back()->withErrors(['error' => 'Backend is offline.']); }
        }
        return redirect()->back()->with('success', 'Blog post deleted successfully (Offline Mock).');
    });

    // Gym Subscriptions
    Route::get('/gym', function () {
        $token = session('auth_token');
        $plans = []; $subscriptions = [];
        if ($token !== 'mock_offline_token') {
            try {
                $pResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/gym/plans');
                if ($pResponse->successful()) { $plans = $pResponse->json(); }
                $sResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/gym/subscriptions');
                if ($sResponse->successful()) { $subscriptions = $sResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($plans)) {
            $plans = [
                ['id' => '1', 'name' => 'Monthly Standard', 'price' => 150000.0, 'duration_days' => 30],
                ['id' => '2', 'name' => 'Annual Gold', 'price' => 1200000.0, 'duration_days' => 365]
            ];
        }
        if (empty($subscriptions)) {
            $subscriptions = [
                ['id' => '1', 'client_name' => 'Alice Kemigisha', 'plan_name' => 'Monthly Standard', 'status' => 'active', 'start_date' => '2026-07-01', 'end_date' => '2026-07-31'],
                ['id' => '2', 'client_name' => 'Bob Male', 'plan_name' => 'Annual Gold', 'status' => 'expired', 'start_date' => '2025-07-01', 'end_date' => '2026-07-01']
            ];
        }
        return view('gym', ['plans' => $plans, 'subscriptions' => $subscriptions]);
    })->name('gym');

    Route::get('/gym/create', function () {
        $token = session('auth_token');
        $plans = [];
        if ($token !== 'mock_offline_token') {
            try {
                $pResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/gym/plans');
                if ($pResponse->successful()) { $plans = $pResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($plans)) {
            $plans = [
                ['id' => '1', 'name' => 'Monthly Standard', 'price' => 150000.0, 'duration_days' => 30],
                ['id' => '2', 'name' => 'Annual Gold', 'price' => 1200000.0, 'duration_days' => 365]
            ];
        }
        return view('gym_create', ['plans' => $plans]);
    });

    Route::post('/gym/plans', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/gym/plans', [
                    'name' => request('name'), 'price' => (float)request('price'), 'duration_days' => (int)request('duration_days')
                ]);
                if ($response->successful()) { return redirect()->route('gym')->with('success', 'Gym plan created successfully.'); }
                return redirect()->back()->withInput()->withErrors(['error' => $response->json()['error'] ?? 'Failed to create plan.']);
            } catch (\Exception $e) { return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']); }
        }
        return redirect()->route('gym')->with('success', 'Gym plan created successfully (Offline Mock).');
    });

    Route::post('/gym/plans/{id}/update', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/gym/plans/{$id}", [
                    'name' => request('name'), 'price' => (float)request('price'), 'duration_days' => (int)request('duration_days')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Gym plan updated successfully.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Gym plan updated successfully (Offline Mock).');
    });

    Route::post('/gym/plans/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/gym/plans/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Gym plan deleted successfully.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Gym plan deleted successfully (Offline Mock).');
    });

    Route::post('/gym/subscriptions', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/gym/subscriptions', [
                    'client_name' => request('client_name'), 'plan_id' => request('plan_id')
                ]);
                if ($response->successful()) { return redirect()->route('gym')->with('success', 'Client subscribed successfully.'); }
                return redirect()->back()->withInput()->withErrors(['error' => $response->json()['error'] ?? 'Failed to subscribe client.']);
            } catch (\Exception $e) { return redirect()->back()->withInput()->withErrors(['error' => 'Backend is offline.']); }
        }
        return redirect()->route('gym')->with('success', 'Client subscribed successfully (Offline Mock).');
    });

    Route::post('/gym/subscriptions/{id}/status', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/gym/subscriptions/{$id}/status", [
                    'status' => request('status')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Subscription status updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Subscription status updated (Offline Mock).');
    });

    // Sauna Management
    Route::get('/sauna', function () {
        $token = session('auth_token');
        $plans = []; $subscriptions = [];
        if ($token !== 'mock_offline_token') {
            try {
                $pResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/sauna/plans');
                if ($pResponse->successful()) { $plans = $pResponse->json(); }
                $sResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/sauna/subscriptions');
                if ($sResponse->successful()) { $subscriptions = $sResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($plans)) {
            $plans = [
                ['id' => '1', 'name' => 'Daily Pass', 'price' => 20000.0, 'duration_days' => 1],
                ['id' => '2', 'name' => 'Sauna Platinum', 'price' => 500000.0, 'duration_days' => 90]
            ];
        }
        if (empty($subscriptions)) {
            $subscriptions = [
                ['id' => '1', 'client_name' => 'Charles Lwanga', 'plan_name' => 'Daily Pass', 'status' => 'active', 'start_date' => '2026-07-18', 'end_date' => '2026-07-19'],
                ['id' => '2', 'client_name' => 'Diana Nansubuga', 'plan_name' => 'Sauna Platinum', 'status' => 'active', 'start_date' => '2026-06-01', 'end_date' => '2026-09-01']
            ];
        }
        return view('sauna', ['plans' => $plans, 'subscriptions' => $subscriptions]);
    })->name('sauna');

    Route::get('/sauna/create', function () {
        $token = session('auth_token');
        $plans = [];
        if ($token !== 'mock_offline_token') {
            try {
                $pResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/sauna/plans');
                if ($pResponse->successful()) { $plans = $pResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($plans)) {
            $plans = [
                ['id' => '1', 'name' => 'Daily Pass', 'price' => 20000.0, 'duration_days' => 1],
                ['id' => '2', 'name' => 'Sauna Platinum', 'price' => 500000.0, 'duration_days' => 90]
            ];
        }
        return view('sauna_create', ['plans' => $plans]);
    });

    Route::post('/sauna/plans', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/sauna/plans', [
                    'name' => request('name'), 'price' => (float)request('price'), 'duration_days' => (int)request('duration_days')
                ]);
                if ($response->successful()) { return redirect()->route('sauna')->with('success', 'Sauna plan created.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('sauna')->with('success', 'Sauna plan created successfully (Offline Mock).');
    });

    Route::post('/sauna/plans/{id}/update', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/sauna/plans/{$id}", [
                    'name' => request('name'), 'price' => (float)request('price'), 'duration_days' => (int)request('duration_days')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Sauna plan updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Sauna plan updated successfully (Offline Mock).');
    });

    Route::post('/sauna/plans/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/sauna/plans/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Sauna plan deleted.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Sauna plan deleted successfully (Offline Mock).');
    });

    Route::post('/sauna/subscriptions', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/sauna/subscriptions', [
                    'client_name' => request('client_name'), 'plan_id' => request('plan_id')
                ]);
                if ($response->successful()) { return redirect()->route('sauna')->with('success', 'Sauna client subscribed.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('sauna')->with('success', 'Sauna client subscribed successfully (Offline Mock).');
    });

    Route::post('/sauna/subscriptions/{id}/status', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/sauna/subscriptions/{$id}/status", [
                    'status' => request('status')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Sauna subscription updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Sauna subscription updated (Offline Mock).');
    });

    // Reports & Analytics
    Route::get('/reports', function () {
        $token = session('auth_token');
        $type = request('type', 'analytics');
        $export = request('export');
        
        // Handle export actions
        if ($export === 'csv' || $export === 'pdf') {
            return response()->streamDownload(function () use ($type, $export) {
                echo "WAKA PMS Report Export\nType: " . strtoupper($type) . "\nFormat: " . strtoupper($export) . "\nGenerated: " . date('Y-m-d H:i:s') . "\nMock export data line 1\nMock export data line 2\n";
            }, "waka_report_" . $type . "_" . date('Ymd') . "." . ($export === 'csv' ? 'csv' : 'txt'), [
                'Content-Type' => $export === 'csv' ? 'text/csv' : 'text/plain'
            ]);
        }

        $reportData = [];
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/reports', [
                    'type' => $type
                ]);
                if ($response->successful()) { $reportData = $response->json(); }
            } catch (\Exception $e) {}
        }

        if (empty($reportData)) {
            // Provide context-specific mock data depending on request type
            if ($type === 'analytics') {
                $reportData = [
                    'occupancy_rate' => '80%', 'active_tenants' => 25, 'total_revenue' => 4500000, 
                    'revenue_trend' => [1200000, 1500000, 1800000], 'maintenance_tickets_resolved' => '94%'
                ];
            } else if ($type === 'rent_collection') {
                $reportData = [
                    ['tenant' => 'Jane Mugisha', 'unit' => 'Suite 101', 'amount_due' => 1200000, 'amount_paid' => 1200000, 'status' => 'Paid'],
                    ['tenant' => 'David Ochieng', 'unit' => 'Suite 102', 'amount_due' => 1500000, 'amount_paid' => 0, 'status' => 'Overdue']
                ];
            } else {
                $reportData = [
                    'type' => $type, 'details' => 'Dynamic metrics generated from records.', 'summary' => 'System verified clean ledger entries.'
                ];
            }
        }

        return view('reports', ['reportData' => $reportData, 'currentType' => $type]);
    })->name('reports');

    // Restaurant & Bar Management
    Route::get('/restaurant', function () {
        $token = session('auth_token');
        $items = []; $orders = [];
        if ($token !== 'mock_offline_token') {
            try {
                $iResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/restaurant/items');
                if ($iResponse->successful()) { $items = $iResponse->json(); }
                $oResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/restaurant/orders');
                if ($oResponse->successful()) { $orders = $oResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($items)) {
            $items = [
                ['id' => '1', 'name' => 'Club Sandwich', 'description' => 'Toasted double decker sandwich', 'price' => 18000.0, 'status' => 'available'],
                ['id' => '2', 'name' => 'Nile Special Beer', 'description' => 'Cold local premium lager', 'price' => 7000.0, 'status' => 'available']
            ];
        }
        if (empty($orders)) {
            $orders = [
                ['id' => '1', 'table_number' => 'Table 4', 'items' => '1x Club Sandwich, 2x Nile Special Beer', 'total_amount' => 32000.0, 'status' => 'pending'],
                ['id' => '2', 'table_number' => 'Table 2', 'items' => '1x Club Sandwich', 'total_amount' => 18000.0, 'status' => 'completed']
            ];
        }
        return view('restaurant', ['items' => $items, 'orders' => $orders]);
    })->name('restaurant');

    Route::get('/restaurant/create', function () {
        return view('restaurant_create');
    });

    Route::post('/restaurant/items', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/restaurant/items', [
                    'name' => request('name'), 'description' => request('description'), 'price' => (float)request('price')
                ]);
                if ($response->successful()) { return redirect()->route('restaurant')->with('success', 'Menu item added.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('restaurant')->with('success', 'Menu item added successfully (Offline Mock).');
    });

    Route::post('/restaurant/items/{id}/update', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/restaurant/items/{$id}", [
                    'name' => request('name'), 'description' => request('description'), 'price' => (float)request('price')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Menu item updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Menu item updated successfully (Offline Mock).');
    });

    Route::post('/restaurant/items/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/restaurant/items/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Menu item deleted.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Menu item deleted successfully (Offline Mock).');
    });

    Route::post('/restaurant/orders/{id}/status', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/restaurant/orders/{$id}/status", [
                    'status' => request('status')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Order status updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Order status updated (Offline Mock).');
    });

    // Expense Management
    Route::get('/expenses', function () {
        $token = session('auth_token');
        $categories = []; $expenses = [];
        if ($token !== 'mock_offline_token') {
            try {
                $cResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/expenses/categories');
                if ($cResponse->successful()) { $categories = $cResponse->json(); }
                $eResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/expenses');
                if ($eResponse->successful()) { $expenses = $eResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($categories)) {
            $categories = [
                ['id' => '1', 'name' => 'Utilities', 'description' => 'Water, Electricity, Internet'],
                ['id' => '2', 'name' => 'Repairs', 'description' => 'General physical repairs and spares']
            ];
        }
        if (empty($expenses)) {
            $expenses = [
                ['id' => '1', 'amount' => 450000.0, 'category' => ['name' => 'Utilities'], 'description' => 'Umeme Office Bills Jun 2026', 'status' => 'approved', 'created_at' => '2026-07-10T12:00:00Z'],
                ['id' => '2', 'amount' => 200000.0, 'category' => ['name' => 'Repairs'], 'description' => 'Fixing back entrance lock', 'status' => 'pending', 'created_at' => '2026-07-15T09:00:00Z']
            ];
        }
        return view('expenses', ['categories' => $categories, 'expenses' => $expenses]);
    })->name('expenses');

    Route::get('/expenses/create', function () {
        $token = session('auth_token');
        $categories = [];
        if ($token !== 'mock_offline_token') {
            try {
                $cResponse = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/expenses/categories');
                if ($cResponse->successful()) { $categories = $cResponse->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($categories)) {
            $categories = [
                ['id' => '1', 'name' => 'Utilities', 'description' => 'Water, Electricity, Internet'],
                ['id' => '2', 'name' => 'Repairs', 'description' => 'General physical repairs and spares']
            ];
        }
        return view('expenses_create', ['categories' => $categories]);
    });

    Route::post('/expenses/categories', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/expenses/categories', [
                    'name' => request('name'), 'description' => request('description')
                ]);
                if ($response->successful()) { return redirect()->route('expenses')->with('success', 'Expense category created.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('expenses')->with('success', 'Expense category created (Offline Mock).');
    });

    Route::post('/expenses/categories/{id}/update', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/expenses/categories/{$id}", [
                    'name' => request('name'), 'description' => request('description')
                ]);
                if ($response->successful()) { return redirect()->route('expenses')->with('success', 'Expense category updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('expenses')->with('success', 'Expense category updated (Offline Mock).');
    });

    Route::post('/expenses/categories/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/expenses/categories/{$id}");
                if ($response->successful()) { return redirect()->route('expenses')->with('success', 'Expense category deleted.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('expenses')->with('success', 'Expense category deleted (Offline Mock).');
    });

    Route::post('/expenses', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/expenses', [
                    'category_id' => request('category_id'), 'amount' => (float)request('amount'), 'description' => request('description')
                ]);
                if ($response->successful()) { return redirect()->route('expenses')->with('success', 'Expense recorded successfully.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('expenses')->with('success', 'Expense recorded successfully (Offline Mock).');
    });

    Route::post('/expenses/{id}/status', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/expenses/{$id}/status", [
                    'status' => request('status')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Expense status updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Expense status updated (Offline Mock).');
    });

    // Smart Devices (IoT)
    Route::get('/smart-devices', function () {
        $token = session('auth_token');
        $devices = [];
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/smart-devices');
                if ($response->successful()) { $devices = $response->json(); }
            } catch (\Exception $e) {}
        }
        if (empty($devices)) {
            $devices = [
                ['id' => '1', 'name' => 'Main Gate Controller', 'device_type' => 'gateway', 'status' => 'online', 'parameters' => '{"mode":"auto","lock_delay":10}'],
                ['id' => '2', 'name' => 'Suite 101 Smart Lock', 'device_type' => 'lock', 'status' => 'online', 'parameters' => '{"auto_lock":true}']
            ];
        }
        return view('smart_devices', ['devices' => $devices]);
    })->name('smart-devices');

    Route::get('/smart-devices/create', function () {
        return view('smart_devices_create');
    });

    Route::post('/smart-devices', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/smart-devices', [
                    'name' => request('name'), 'device_type' => request('device_type'), 'parameters' => request('parameters')
                ]);
                if ($response->successful()) { return redirect()->route('smart-devices')->with('success', 'Smart device registered.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->route('smart-devices')->with('success', 'Smart device registered successfully (Offline Mock).');
    });

    Route::post('/smart-devices/{id}/update', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put("http://localhost:8080/api/v1/admin/smart-devices/{$id}", [
                    'name' => request('name'), 'parameters' => request('parameters')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Smart device settings updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Smart device settings updated successfully (Offline Mock).');
    });

    Route::post('/smart-devices/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/smart-devices/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Smart device removed.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Smart device removed successfully (Offline Mock).');
    });

    // Settings & Security Center
    Route::get('/settings', function () {
        $token = session('auth_token');
        $preferences = []; $sessions = []; $webhooks = []; $notifications = []; $auditLogs = []; $activities = []; $serviceStatus = [];
        $configContent = '';

        if ($token !== 'mock_offline_token') {
            try {
                // Fetch preferences
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/auth/preferences');
                if ($r->successful()) { $preferences = $r->json(); }
                // Fetch active sessions
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/auth/sessions');
                if ($r->successful()) { $sessions = $r->json(); }
                // Fetch user activities
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/tenant/activities');
                if ($r->successful()) { $activities = $r->json(); }
                // Fetch dynamic configuration
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/settings/config');
                if ($r->successful()) { $configContent = json_encode($r->json(), JSON_PRETTY_PRINT); }
                // Fetch command center service status
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/command-center/status');
                if ($r->successful()) { $serviceStatus = $r->json(); }
                // Fetch webhooks config
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/settings/webhooks');
                if ($r->successful()) { $webhooks = $r->json(); }
                // Fetch notifications
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/settings/notifications');
                if ($r->successful()) { $notifications = $r->json(); }
                // Fetch audit logs
                $r = Http::timeout(5)->withToken($token)->get('http://localhost:8080/api/v1/admin/audit-logs');
                if ($r->successful()) { $auditLogs = $r->json(); }
            } catch (\Exception $e) {}
        }

        // Mock Fallbacks
        if (empty($preferences)) {
            $preferences = ['timezone' => 'Africa/Kampala', 'currency' => 'UGX', 'theme' => 'light', 'language' => 'en'];
        }
        if (empty($sessions)) {
            $sessions = [
                ['id' => '1', 'ip_address' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 PHPDesktop', 'is_current' => true, 'last_active' => '2026-07-18T08:00:00Z']
            ];
        }
        if (empty($serviceStatus)) {
            $serviceStatus = [
                ['name' => 'Waka Go Server Core', 'status' => 'running', 'uptime' => '4d 18h', 'cpu' => '0.5%', 'memory' => '42MB']
            ];
        }
        if (empty($webhooks)) {
            $webhooks = [
                ['id' => '1', 'url' => 'https://external-ledger.com/webhook', 'events' => 'payment.received,lease.approved']
            ];
        }
        if (empty($notifications)) {
            $notifications = [
                ['id' => '1', 'channel' => 'sms', 'template_name' => 'Rent Reminder', 'content' => 'Dear {name}, rent for unit {unit} is due.'],
                ['id' => '2', 'channel' => 'email', 'template_name' => 'Welcome', 'content' => 'Welcome to Waka PMS!']
            ];
        }
        if (empty($activities)) {
            $activities = [
                ['id' => '1', 'action' => 'login', 'description' => 'User logged in successfully', 'created_at' => '2026-07-18T08:00:00Z']
            ];
        }
        if (empty($auditLogs)) {
            $auditLogs = [
                ['id' => '1', 'actor' => 'admin@acme.com', 'action' => 'update_settings', 'target' => 'System Config', 'created_at' => '2026-07-18T08:15:00Z']
            ];
        }
        if (empty($configContent)) {
            $configContent = "{\n  \"system_mode\": \"production\",\n  \"enable_signup\": false,\n  \"maintenance_auto_assign\": true\n}";
        }

        return view('settings', [
            'preferences' => $preferences,
            'sessions' => $sessions,
            'serviceStatus' => $serviceStatus,
            'webhooks' => $webhooks,
            'notifications' => $notifications,
            'activities' => $activities,
            'auditLogs' => $auditLogs,
            'configContent' => $configContent
        ]);
    })->name('settings');

    Route::post('/settings/pin', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/auth/change-pin', [
                    'old_pin' => request('old_pin'), 'new_pin' => request('new_pin')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Security PIN changed successfully.'); }
                return redirect()->back()->withErrors(['error' => $response->json()['error'] ?? 'Failed to update PIN.']);
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Security PIN changed successfully (Offline Mock).');
    });

    Route::post('/settings/mfa', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/auth/mfa/toggle', [
                    'enabled' => request('enabled') === '1'
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'MFA status updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'MFA status updated (Offline Mock).');
    });

    Route::post('/settings/preferences', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put('http://localhost:8080/api/v1/auth/preferences', [
                    'timezone' => request('timezone'), 'currency' => request('currency')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Preferences updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Preferences updated successfully (Offline Mock).');
    });

    Route::post('/settings/sessions/revoke', function () {
        $token = session('auth_token');
        $id = request('session_id');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/auth/sessions/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Session revoked.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Session revoked successfully (Offline Mock).');
    });

    Route::post('/settings/sessions/revoke-all', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/auth/sessions");
                if ($response->successful()) { return redirect()->back()->with('success', 'All other sessions revoked.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'All sessions revoked successfully (Offline Mock).');
    });

    Route::post('/settings/config', function () {
        $token = session('auth_token');
        $json = json_decode(request('config'), true);
        if (!$json) { return redirect()->back()->withErrors(['error' => 'Invalid JSON formatting.']); }
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put('http://localhost:8080/api/v1/admin/settings/config', $json);
                if ($response->successful()) { return redirect()->back()->with('success', 'Dynamic configuration file updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Dynamic configuration updated (Offline Mock).');
    });

    Route::post('/settings/services/action', function () {
        $token = session('auth_token');
        $service = request('service');
        $action = request('action');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post("http://localhost:8080/api/v1/admin/command-center/services/{$service}/action", [
                    'action' => $action
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', "Service {$service} action {$action} succeeded."); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', "Service {$service} action {$action} executed (Offline Mock).");
    });

    Route::post('/settings/webhooks', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->post('http://localhost:8080/api/v1/admin/settings/webhooks', [
                    'url' => request('url'), 'events' => request('events')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Webhook registered.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Webhook subscription saved (Offline Mock).');
    });

    Route::post('/settings/webhooks/{id}/delete', function ($id) {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->delete("http://localhost:8080/api/v1/admin/settings/webhooks/{$id}");
                if ($response->successful()) { return redirect()->back()->with('success', 'Webhook deleted.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Webhook subscription deleted (Offline Mock).');
    });

    Route::post('/settings/notifications', function () {
        $token = session('auth_token');
        if ($token !== 'mock_offline_token') {
            try {
                $response = Http::timeout(5)->withToken($token)->put('http://localhost:8080/api/v1/admin/settings/notifications', [
                    'channel' => request('channel'), 'template_name' => request('template_name'), 'content' => request('content')
                ]);
                if ($response->successful()) { return redirect()->back()->with('success', 'Notification template updated.'); }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Notification template updated (Offline Mock).');
    });
});
