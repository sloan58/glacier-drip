<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    protected array $passThroughRoutes = [
        'filament.admin.auth.logout'
    ];

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRedirect($request)) {
            return redirect()->route('filament.admin.pages.onboarding');
        }

        return $next($request);
    }

    protected function shouldRedirect(Request $request): bool
    {
        $user = auth()->user();

        return $user
            && !$user->hasRole('admin')
            && $user->needs_aws_credentials
            && !$this->isPassThroughRoute($request);
    }

    protected function isPassThroughRoute(Request $request): bool
    {
        return in_array($request->route()?->getName(), $this->passThroughRoutes, true);
    }
}
