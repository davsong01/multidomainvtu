<?php

namespace App\Http\Middleware;

use App\Services\MerchantPrefixService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $merchantPrefix = new MerchantPrefixService();
        $merchant = $merchantPrefix->getMerchantCustomerInfo($request);
       
        $request['merchant'] = $merchant['store_slug'];
        
        return $request->expectsJson() ? null : route('login', ['merchant' => $request['merchant']]);
    }
}
