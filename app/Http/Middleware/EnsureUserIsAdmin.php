<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Allow the request only for logged-in administrators.
     * Use it AFTER the 'auth' middleware: ['auth', 'admin'].
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'This area is restricted to administrators.');
        }

        return $next($request);
    }
}
