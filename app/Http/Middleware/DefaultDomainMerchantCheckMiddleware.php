<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Merchant;
use App\Services\MerchantPrefixService;
use Illuminate\Support\Facades\Request;


class DefaultDomainMerchantCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        $merchantPrefix = new MerchantPrefixService($request);
        $merchant = $merchantPrefix->getMerchantCustomerInfo($request);
        $prefix = $merchant['store_slug'];
        
        if(empty($prefix)){
            return redirect(route('default.page'));  
        }
        
        return $next($request);
    }
}
