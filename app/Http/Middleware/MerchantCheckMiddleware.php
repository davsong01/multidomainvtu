<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MerchantCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $checkMerchant = $this->checkMerchantExist();

        if ($checkMerchant['success'] == true && !empty($checkMerchant['data']) && $checkMerchant['data']['merchantr']) {
            Session::put('merchant_id', $checkMerchant['data']['merchant_id']);
            Session::put('merchant', $checkMerchant['data']['merchant']);
            $merchant = $checkMerchant['data']['merchant'];

            return view('auth.login', compact('merchant'));
        } else {
            return view('auth.login');
        }
        return $next($request);
    }

    public function checkMerchantExist()
    {

        // $sub_domain = explode('.', $_SERVER['SERVER_NAME'])[0] ?? '';
        $domain = $_SERVER['SERVER_NAME'];
        
        if (env('ENT') == 'local') {
            $domain = 'portal.airtime2cash.com';
        }
        
        // if($domain 4== )
        dd($domain);
        dd('hole', $domain);
        if ($domain != 'gradelab') {
            $school = $this->setDb($domain);

            $status = $school->status ?? '';

            if (is_null($school)) {
                $message = 'School not configured for domain: ' . $domain . ', Please contact admin';
            } else {
                if (isset($school) && $status != 1) {
                    $message = 'School not active, Please contact admin';
                }

                if (!empty(auth()->user()) && auth()->user()->school_id != $school->id && auth()->user()->role_id != 1) {
                    $message = 'User not found for school, Please contact admin';
                }

                if ($school->status != 1) {
                    $message = 'School not enabled, Please contact admin';
                }
            }
        } else {
            $message = 'Not a valid domain';
        }

        if (isset($message) && !empty($message)) {
            $data = [
                'success' => false,
                'message' => $message,
            ];
        } else {
            $data = [
                'success' => true,
                'data' => [
                    'school' => $school,
                    'school_id' => $school->id
                ],
            ];
        }

        return $data;
    }
}
