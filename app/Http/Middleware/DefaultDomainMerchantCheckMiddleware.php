<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Merchant;
use App\Services\TenantManager;
use App\Services\MerchantManager;
use Illuminate\Support\Facades\Request;


class DefaultDomainMerchantCheckMiddleware
{
    protected $merchantManager;

    public function __construct(MerchantManager $merchantManager){
        $this->merchantManager = $merchantManager;
    }

    public function handle($request, Closure $next)
    {
        
        if ($this->merchantManager->loadMerchant($request->route('merchant'))) {
            $request->route()->forgetParameter('merchant');
            return $next($request);
        }

        throw new NotFoundHttpException;
    }
}
