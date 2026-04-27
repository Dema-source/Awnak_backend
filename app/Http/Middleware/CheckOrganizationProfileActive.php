<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOrganizationProfileActive
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
        $user = Auth::user();

        // Check if user is authenticated and has organization_admin role
        if (!$user || !$user->hasRole('organization_admin')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Organization admin access required.',
                'data' => null
            ], 403);
        }

        // Check if organization profile exists and is active
        $organizationProfile = $user->organization_profile;
        
        if (!$organizationProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Organization profile is required. Please create your organization profile first.',
                'data' => null
            ], 403);
        }
        
        if ($organizationProfile->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Organization profile is inactive. Please contact administrator.',
                'data' => null
            ], 403);
        }

        return $next($request);
    }
}
