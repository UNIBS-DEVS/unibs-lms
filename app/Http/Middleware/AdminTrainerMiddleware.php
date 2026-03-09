<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminTrainerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array(Auth::user()->role, ['admin', 'trainer'])) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        return $next($request);
    }
}
