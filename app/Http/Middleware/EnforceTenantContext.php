<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceTenantContext
{
    /**
     * Middleware to ensure every request has a valid tenant context.
     * This prevents accidental cross-tenant data exposure.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip middleware for unauthenticated routes
        if (!$user) {
            return $next($request);
        }

        // Superadmin bypass - they can switch tenants
        if ($user->hasRole('superadmin')) {
            // If not in a mess context, don't block - they're navigating
            return $next($request);
        }

        // For regular users, ensure they have an approved mess
        $activeMess = $user->messes()
            ->where('status', 'approved')
            ->first();

        if (!$activeMess) {
            // User has no approved mess, redirect to mess selection
            if (!$request->is('mess/selection', 'mess/create', 'mess/join*', 'profile*')) {
                return redirect()->route('mess.selection')
                    ->with('error', 'Please select or create a mess to continue.');
            }
        }

        // Set the tenant context for the entire request
        $request->setAttribute('tenant_mess_id', $activeMess?->id);

        return $next($request);
    }
}
