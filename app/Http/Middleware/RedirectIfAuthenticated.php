<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->homeFor($guard));
            }
        }

        return $next($request);
    }

    private function homeFor(?string $guard): string
    {
        return match ($guard) {
            'technician' => route('technician.dashboard'),
            'client' => route('client.dashboard'),
            default => RouteServiceProvider::HOME,
        };
    }
}
