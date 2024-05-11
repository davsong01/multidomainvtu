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
        $request = Request::createFrom($request);
        
        $merchantPrefix = new MerchantPrefixService($request);
        $merchant = $merchantPrefix->getMerchantCustomerInfo($request);
        
        if ($exception instanceof TokenMismatchException) {
            // Handle CSRF token mismatch (419 error) here
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            // Handle 404 errors here
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            // Handle Method Not Allowed errors here
            return response()->view('errors.404', ['merchant' => $merchant, 'exception' => $exception], 405);
        }

        return parent::render($request, $exception);
    }

    // ...
}