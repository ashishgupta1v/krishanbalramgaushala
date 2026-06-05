<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevoteeSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('gaushala_devotee_id')) {
            return redirect()->route('register');
        }
        return $next($request);
    }
}
