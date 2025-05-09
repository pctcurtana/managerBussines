<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class ConfigureSessionCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get domain from request
        $host = $request->getHost();
        
        // Set session domain for production environments like Railway
        if (app()->environment('production')) {
            // Remove port number if present
            $domain = preg_replace('/:\d+$/', '', $host);
            
            // Set domain for cookies
            Config::set('session.domain', '.' . $domain);
            
            // Set SameSite attribute to None for cross-domain requests
            Config::set('session.same_site', 'none');
            
            // Force secure cookies on production
            Config::set('session.secure', true);
        }
        
        return $next($request);
    }
} 