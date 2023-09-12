<?php

namespace UisIts\Oidc\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $introspectResponse = Socialite::driver('shib-oidc')
            ->introspect($request->bearerToken());

        if (! $introspectResponse['active']) {
            return new JsonResponse(['message' => 'Invalid Token!'], 401);
        }

        if (! empty($scopes)) {
            $this->checkScopes($introspectResponse['scope'], $scopes);
        }

        return $next($request);
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

    public static function getUserFromToken(string $bearerToken): array
    {
        return Socialite::driver('shib-oidc')->getUserByToken($bearerToken);
    }
}
