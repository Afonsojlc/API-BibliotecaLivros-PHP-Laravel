<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Enforce specific user role requirement on protected route groups.
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! $request->user() || $request->user()->role !== $role) {
            return response()->json([
                'message' => 'Access denied: Insufficient role permissions.'
            ], 403);
        }

        return $next($request);
    }
}
