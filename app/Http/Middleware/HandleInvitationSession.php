<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleInvitationSession
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
        $response = $next($request);

        // Keep invitation data in session during registration process
        if ($request->is('register') || $request->is('login')) {
            // Don't clear invitation session on these routes
            return $response;
        }

        return $response;
    }
}