<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $onboardingRoute = 'filament.admin.pages.onboarding';

        if (auth()->user()->needs_aws_credentials && $request->route()->getName() != $onboardingRoute) {
            return redirect()->route($onboardingRoute);
        }

        return $next($request);
    }
}
