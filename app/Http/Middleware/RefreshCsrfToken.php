<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ regenerate token khi không có sẵn hoặc session đang được khởi tạo mới
        if (!Session::has('_token') || !Session::has('last_token_refresh')) {
            Session::regenerateToken();
            // Set thời gian refresh token gần nhất
            Session::put('last_token_refresh', now()->timestamp);
        } else {
            // Kiểm tra thời gian từ lần refresh token gần nhất
            // Nếu đã hơn 12 giờ, refresh token
            $lastRefresh = Session::get('last_token_refresh');
            $hoursSinceRefresh = (now()->timestamp - $lastRefresh) / 3600;
            
            if ($hoursSinceRefresh > 12) {
                Session::regenerateToken();
                Session::put('last_token_refresh', now()->timestamp);
            }
        }

        return $response;
    }
} 