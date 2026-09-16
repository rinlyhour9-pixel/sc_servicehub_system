<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('technician') || $request->is('technician/*')) {
            return route('technician.login');
        }

        if ($request->is('client') || $request->is('client/*')) {
            return route('client.login');
        }

        return route('login');
    }
}
