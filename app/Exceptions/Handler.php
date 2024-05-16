<?php

namespace App\Exceptions;

use Exception;
use App\Services\MerchantPrefixService;
use Illuminate\Support\Facades\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    // ...

    public function render($request, \Throwable $exception)
    {
        // dd($exception);
        $request = Request::createFrom($request);
        
        $merchantPrefix = new MerchantPrefixService($request);
        $merchant = $merchantPrefix->getMerchantCustomerInfo($request);
        
        if ($exception instanceof TokenMismatchException) {
            // Handle CSRF token mismatch (419 error) here
            \Log::error(['419 Error' => $exception->getMessage(), 'Merchant' => $merchant]);
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 419);
        }else

        if ($exception instanceof NotFoundHttpException) {
            // Handle 404 errors here
            \Log::error(['404 Error' => $exception->getMessage(), 'Merchant' => $merchant]);
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 404);
        }else

        if ($exception instanceof MethodNotAllowedHttpException) {
            // Handle Method Not Allowed errors here
            \Log::error(['405 Error' => $exception->getMessage(), 'Merchant' => $merchant]);
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 405);
        }

        else{
            \Log::error($exception->getMessage());
        }

        return parent::render($request, $exception);
    }

    // ...
}