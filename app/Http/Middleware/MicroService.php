<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class MicroService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $json = $request->json;
        if(empty($json)){
            return response()->json(['message' => 'No valid key', 'status' => 'failed'], 412);
        }
        if (!Hash::check(env('MULTI_SHOP_KEY'), $request->json)) {
            return response()->json(['message' => 'Key Failed', 'status' => 'faild'], 412);
        }else{
        }

        return $next($request);
    }
}
