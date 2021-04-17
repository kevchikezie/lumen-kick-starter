<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your email address is not verified.'
            ], 403);
        }

        return $next($request);
    }
}
