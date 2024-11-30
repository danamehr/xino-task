<?php

namespace App\Modules\Shared\Http\V1\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAcceptRequestHeaderParameterToApplicationJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (str($request->getRequestUri())->startsWith('/v1')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
