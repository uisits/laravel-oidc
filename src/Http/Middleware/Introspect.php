<?php

namespace UisIts\Oidc\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class Introspect
{
    /**
     * @param  \Closure(Request): (Response)  $next
     * @param  string  ...$scopes
     *
     * @throws \Throwable
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        if (! $request->hasHeader('Authorization')) {
            return new JsonResponse(['message' => 'Authorization Header not found!'], 403);
        }

        if (empty($request->bearerToken())) {
            return new JsonResponse(['message' => 'Token not set!'], 401);
        }

        if ($this->checkCache($request->bearerToken())) {
            return $next($request);
        }

        $introspectResponse = Socialite::driver('shib-oidc')
            ->introspect($request->bearerToken());

        if (! $introspectResponse['active']) {
            return new JsonResponse(['message' => 'Invalid Token!'], 401);
        }

        if (! empty($scopes)) {
            $this->checkScopes($introspectResponse['scope'], $scopes);
        }

        Cache::put('introspect.username', $introspectResponse['username']);
        Cache::put('introspect', encrypt($request->bearerToken()));

        return $next($request);
    }

    /**
     * Check if the token is already authorized
     */
    protected function checkCache($token): bool
    {
        // If token not in cache return
        if (! Cache::has('introspect')) {
            return false;
        }

        if ($this->isCachedTokenValid($token)) {
            return true;
        }

        return false;
    }

    /**
     * Check if cached token is valid
     */
    protected function isCachedTokenValid(string $token): bool
    {
        $cachedToken = decrypt(Cache::get('introspect'));

        // If token valid return
        if ($cachedToken === $token) {
            return true;
        }

        return false;
    }

    /**
     * Check the scopes of the token
     *
     * @throws \Throwable
     */
    public function checkScopes(string $newScopes, string|array $oldScopes): void
    {
        $oldScopes = collect($oldScopes);
        $newScopes = collect(explode(' ', $newScopes));
        $missingScopes = $oldScopes->diff($newScopes);

        if ($missingScopes->isNotEmpty()) {
            throw new \InvalidArgumentException("Missing scopes {$missingScopes->implode(',')}");
        }
    }
}
