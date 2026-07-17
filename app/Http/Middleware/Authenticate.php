<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route("login");
    }
    public function handle($request, Closure $next, ...$guards)
    {
        $sanctum = $request->cookie("onerdftoken") ?: $request->header("Token");

        $sanctum
            ? $request->headers->set("Authorization", "Bearer " . $sanctum)
            : null;

        $this->authenticate($request, $guards);

        $user = $request->user();
        $requestedById = null;

        if ($user && $user->currentAccessToken()) {
            $tokenName = $user->currentAccessToken()->name;
            $abilities = $user->currentAccessToken()->abilities ?? [];

            if ($tokenName === "SupportImpersonationToken") {
                foreach ($abilities as $ability) {
                    if (str_starts_with($ability, "requested_by:")) {
                        $requestedById = explode(":", $ability)[1];
                        break;
                    }
                }
            }

            $request->merge([
                "is_support_impersonation" =>
                    $tokenName === "SupportImpersonationToken",
                "requested_by" => $requestedById,
            ]);
        }

        return $next($request);
    }
}
