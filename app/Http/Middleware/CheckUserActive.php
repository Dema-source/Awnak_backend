<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only check active status for system_admin, organization_admin, opportunity_manager,
        //  volunteer_coordinator, performance_evaluator and volunteer roles
        if ($user && ($user->hasRole('system_admin') ||
            $user->hasRole('organization_admin') ||
            $user->hasRole('opportunity_manager') ||
            $user->hasRole('volunteer_coordinator') ||
            $user->hasRole('performance_evaluator') ||
            $user->hasRole('volunteer'))) {
            if ($user->status !== 'active') {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not active. Please contact super administrator.',
                    'data' => null
                ], 403);
            }
        }

        return $next($request);
    }
}
