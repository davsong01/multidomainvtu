<?php

namespace App\Http\Middleware;

use Closure;


class CustomDomainMerchantCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function checkMerchantExist()
    {
        // $sub_domain = explode('.', $_SERVER['SERVER_NAME'])[5] ?? '';
        $domain = $_SERVER['SERVER_NAME'];
        dd($domain);
        if($domain == env('APP_BASE_URL')){
            \Session::put('slug', 'local');
            dd('sdsd', \Request::route()->getPrefix());
            $merchant_details = Merchant::where('slug', $slug)->first();
        }

        return $next($request);        
    }
}
