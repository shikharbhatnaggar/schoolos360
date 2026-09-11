<?php

namespace App\Http\Middleware;

use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        // Share globally to all views
        view()->share('currentSchool', $school);
        view()->share('currentBranch', $branch);
        view()->share('availableBranches', TenantContext::availableBranches());
        view()->share('availableSchools', TenantContext::availableSchools());
        view()->share('currencySymbol', TenantContext::currencySymbol());

        return $next($request);
    }
}