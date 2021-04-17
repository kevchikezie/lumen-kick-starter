<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been deactivated.'
            ], 403);
        }

        return $next($request);
    }
}
