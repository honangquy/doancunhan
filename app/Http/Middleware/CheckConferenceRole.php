<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckConferenceRole
{
    /**
     * Handle an incoming request.
     * 
     * Check if the authenticated user has required role for specific conference.
     * Usage: Route::middleware(['auth', 'conference.role:CHAIR'])->group(...)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  Role code (CHAIR, REVIEWER, etc.)
     * @param  string|null  $conferenceParam  Route parameter name for conference ID (default: 'conferenceId')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role, ?string $conferenceParam = 'conferenceId'): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        // Get conference ID from route parameter
        $conferenceId = $request->route($conferenceParam);
        
        if (!$conferenceId) {
            abort(400, 'Conference ID không được tìm thấy trong route.');
        }

        // Check if user has required role for this conference
        if ($user->hasRole($role, $conferenceId)) {
            return $next($request);
        }

        // Also allow ADMIN users (they have global access)
        if ($user->hasRole('ADMIN')) {
            return $next($request);
        }

        // User doesn't have required role for this conference
        abort(403, "Bạn không có quyền {$role} cho hội thảo này.");
    }
}