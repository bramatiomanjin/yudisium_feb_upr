<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUser = $request->user();

        if ($authenticatedUser === null) {
            return $next($request);
        }

        $user = User::query()->find($authenticatedUser->getAuthIdentifier());

        if ($user === null || strtoupper((string) $user->status) !== 'ACTIVE') {
            Auth::logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun tidak aktif.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi Super Admin.',
            ]);
        }

        Auth::setUser($user);

        return $next($request);
    }
}
