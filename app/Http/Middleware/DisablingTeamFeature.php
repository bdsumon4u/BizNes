<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DisablingTeamFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return 'App\\Policies\\App\\'.class_basename($modelClass).'Policy';
            // Return the name of the policy class for the given model...
        });

        config(['permission.teams' => false]);
        $response = $next($request);
        config(['permission.teams' => true]);

        return $response;
    }
}
