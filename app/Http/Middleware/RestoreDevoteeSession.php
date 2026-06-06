<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Domain\Devotee\Devotee;

class RestoreDevoteeSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('gaushala_devotee_id')) {
            $devoteeId = $request->cookie('gaushala_devotee_remember');
            if ($devoteeId) {
                $devotee = Devotee::where('id', $devoteeId)->where('status', 'active')->first();
                if ($devotee) {
                    session(['gaushala_devotee_id' => $devotee->id]);
                }
            }
        }

        return $next($request);
    }
}
