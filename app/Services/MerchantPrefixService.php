<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantPrefixService
{
    public function getMerchantCustomerInfo(Request $request){
        $prefix = $request->segment(1);
        $details = [];
        
        if (!empty($prefix)) {
            $merchantDetails = Merchant::with('setting', 'default_customer_level')->where('store_slug', $prefix)->first();
            
            if (!empty($merchantDetails)) {
                if($merchantDetails->status == 'inactive'){
                    
                }

                if ($merchantDetails->subscription_end < Carbon::now()) {
                    
                }
                
                $settings = $merchantDetails->setting->toArray();
                $settings['default_customer_level_id'] = $merchantDetails->default_customer_level->id ?? null;
                
                $request['merchant_settings'] = $settings;
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
