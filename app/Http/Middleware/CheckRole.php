<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (!in_array($request->user()->role->value, $roles)) {
            return response()->json(['error' => 'Forbidden: insufficient permissions'], 403);
        }

        return $next($request);
    }
}
