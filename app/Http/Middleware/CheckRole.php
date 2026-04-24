<?php

// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! $request->user() || $request->user()->role !== $role) {
            return response()->json([
                'message' => 'Acesso negado. Permissões insuficientes.'
            ], 403);
        }

        return $next($request);
    }
}

