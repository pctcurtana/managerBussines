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
            
            // Set domain for cookies - without leading dot to fix issues
            Config::set('session.domain', $domain);
            
            // Set SameSite attribute to lax for better compatibility
            Config::set('session.same_site', 'lax');
            
            // Force secure cookies on production
            Config::set('session.secure', true);
            
            // Set longer session lifetime - 30 days
            Config::set('session.lifetime', 43200); // 30 days (43200 minutes)
        }

        // Tăng thời gian sống của cookie
        Config::set('session.expire_on_close', false);
        
        return $next($request);
    }
} 