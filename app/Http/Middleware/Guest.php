<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Guest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect authenticated users to their respective routes
                if ($guard === 'user') {
                    return redirect()->route('dashboard');
                } elseif ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
            }
        }
        return $next($request);
    }
}
