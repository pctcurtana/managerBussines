<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Các routes loại trừ CSRF verification
        'api/*',
        'webhook/*',
        // Tạm thời loại trừ các routes login/logout
        'login',
        'logout'
    ];
}
