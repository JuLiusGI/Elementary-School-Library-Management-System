<?php

/**
 * AdminMiddleware
 *
 * Restricts access to routes for admin users only.
 * Redirects non-admin users to the dashboard with an error message.
 *
 * @package App\Http\Middleware
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Checks if the authenticated user has admin role.
     * If not, redirects to dashboard with an error message.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Access denied. Administrator privileges required.');
        }

        return $next($request);
    }
}
