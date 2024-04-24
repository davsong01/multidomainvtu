<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Merchant;
use Illuminate\Support\Facades\Request;


class DefaultDomainMerchantCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        $prefix = $request->segment(1);
        if(!empty($prefix)){
            $merchantDetails = Merchant::where('store_slug', $prefix)->where('status', 'active')->first();
            if(!empty($merchantDetails)){
                $request['merchant_id'] = $merchantDetails->id;
                $request['store_slug'] = $merchantDetails->store_slug;
            }else{
                return redirect(route('default.page'));
            }
        }else{
            return redirect(route('default.page'));
        }
        
        return $next($request);
    }
}
