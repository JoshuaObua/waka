<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('auth_token')) {
            return redirect()->route('login');
        }

        $token = session('auth_token');
        
        // Dynamically decode JWT payload or set fallback mock user info
        if (!session()->has('user_email')) {
            if ($token === 'mock_offline_token') {
                session([
                    'user_email' => 'admin@acme.com',
                    'user_roles' => ['Super Admin'],
                ]);
            } else {
                $parts = explode('.', $token);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                    if (is_array($payload)) {
                        session([
                            'user_email' => $payload['email'] ?? '',
                            'user_roles' => $payload['roles'] ?? [],
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
