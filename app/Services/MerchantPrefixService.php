<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Settings;
use Illuminate\Http\Request;

class MerchantPrefixService
{
    public function getMerchantCustomerInfo(Request $request){
        $prefix = $request->segment(1);
        
        $details = [];

        if (!empty($prefix)) {
            $merchantDetails = Merchant::with('setting')->where('store_slug', $prefix)->where('status', 'active')->first();
            
            if (!empty($merchantDetails)) {
                $request['merchant_settings'] = $merchantDetails->setting->toArray();
                $request['merchant_id'] = $details['merchant_id'] = $merchantDetails->id;
                $request['store_slug'] = $details['store_slug'] = $merchantDetails->store_slug;
                $request['store_name'] = $details['store_name'] = $merchantDetails->store_name;
                $request['array'] = ['merchant' => $details['store_slug']];
            } 
            // else {
            //     return redirect(route('default.page'));
            // }
        // } else {
            // return redirect(route('default.page'));
        }

        return $details;
    }   

    public function setMerchantCustomerInfo()
    {
    }
}
