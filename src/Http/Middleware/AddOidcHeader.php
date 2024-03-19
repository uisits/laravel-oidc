<?php

namespace UisIts\Oidc\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddOidcHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $user = auth()->user();
        if ($user) {
            $response->headers->set('X-Username', $user->email);
            $_SERVER['X-Username'] = $user->email;
        }

        return $response;
    }
}
