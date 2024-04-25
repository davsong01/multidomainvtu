<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // return route('login', ['merchant' => 'merchant1']);
        return $request->expectsJson() ? null : route('login', getMerchantDetails()->store_slug);
    }
}
