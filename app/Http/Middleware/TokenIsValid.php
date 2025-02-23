<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;

class TokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Prevent processing more than once
        if (session()->has('middleware_passed')) {
            return $next($request);
        }

        // Allow static files without validation
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.ico')) {
            return $next($request);
        }

        $token = $request->bearerToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'Token no proporcionado.');
        }

        $personalToken = PersonalAccessToken::findToken($token);
        $user = $personalToken ? $personalToken->tokenable : null;

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token inválido.');
        }

        Auth::login($user);

        // Mark that the middleware has passed
        session(['middleware_passed' => true]);

        return $next($request);
    }

}