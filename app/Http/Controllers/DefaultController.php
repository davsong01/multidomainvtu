<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Merchant;
use App\Models\Settings;
use Illuminate\Support\Str;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\CustomerLevel;
use App\Models\RolePermission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DefaultController extends Controller
{
    public function createDefaultDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'merchant_name' => 'required',
            'store_name' => 'required',
            'store_location' => 'nullable',
            'store_slug' => 'required|unique:merchants',
            'custom_domain' => 'nullable|unique:merchants',
            'subscription_start' => 'nullable',
            'subscription_end' => 'nullable',
            'api_key' => 'nullable',
            'secret_key' => 'required',
            'public_key' => 'required',
            'currency' => 'required',
            'official_email' => 'nullable',
            'whatsapp_number'=> 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->all()], 200);
        }

        try {
            DB::transaction(function () use ($request) {
                // Create Merchant
                $merchant = Merchant::updateOrCreate([
                    'store_slug' => $request->store_slug,
                ],[
                    'store_slug' => $request->store_slug,
                    'name' => $request->merchant_name,
                    'store_name' => $request->store_name,
                    'location' => $request->location,
                    'status' => 'active',
                    'subscription_start' => $request->subscription_start,
                    'subscription_end' => $request->subscription_end,
                ]);

                // Create default Admin
                $user = User::updateOrcreate(['email' => $request->email],[
                    'firstname' => $request->first_name,
                    'lastname' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'status' => 'active',
                    'type' => 'admin',
                    'username' => Str::slug($request->first_name . '-' . $request->last_name),
                    'merchant_id' => $merchant->id
                ]);

                $admin = Admin::create([
                    'user_id' => $user->id,'merchant_id' => $merchant->id
                ]);

                // Create api details
                if (env('ENT') == 'local') {
                    $sandbox = env('KINGSVTU_API_LOCAL_SANDBOX_BASE_URL');
                    $live = env('KINGSVTU_API_REMOTE_SANDBOX_BASE_URL');
                } else {
                    $sandbox = env('KINGSVTU_API_REMOTE_SANDBOX_BASE_URL');
                    $live = env('KINGSVTU_API_REMOTE_LIVE_BASE_URL');
                }

                Api::Create([
                    "name" => 'KingsVTU',
                    "warning_threshold_status" => 'inactive',
                    "warning_threshold" => null,
                    "api_key" => $request->api_key,
                    "secret_key" => $request->secret_key,
                    "public_key" => $request->public_key,
                    "sandbox_base_url" => $sandbox,
                    "live_base_url" => $live,
                    'merchant_id' => $merchant->id
                ]);

                Settings::create([
                    'logo' => '',
                    'favicon' => '',
                    'currency' => $request->currency,
                    'official_email' => $request->official_email,
                    'whatsapp_number' => $request->official_whatsapp_number,
                    'google_ad_code' => '',
                    'seo_title' => '',
                    'seo_description' => '',
                    'support_link' => '',
                    'terms_and_conditions_link' => '',
                    'merchant_id' => $merchant->id
                ]);

                $this->createPermissions($merchant, $admin);
                // Create Annoucement 
                $this->defaultAnnoucement($merchant);
                $this->defaultCustomerLevel($merchant);
            });

            return response()->json(['status' => 'success', 'message' => 'Operation successful'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 200);
        }
        
    }

    public function createPermissions($merchant, $admin){
        $ids = RolePermission::where('status', 'active')->pluck('id')->toArray();
        
        $role = Role::updateOrCreate(['name' => 'Admin', 'status' => 'active', 'merchant_id' => $merchant->id], [
            'name' => 'Admin',
            'permissions' => implode(",", $ids),
            'status' => 'active',
            'merchant_id' => $merchant->id
        ]);

        $admin->update(['permissions' => $role->id]);
    }

    public function defaultAnnoucement($merchant)
    {
        Announcement::updateOrCreate(['merchant_id' => $merchant->id, 'type' => 'popup'],[
            'title' => 'Pop-up Announcement',
            'status' => 'inactive',
            'type' => 'popup',
            'message' => '<p>Welcome to </p>'.$merchant->store_name,
        ]);
        Announcement::updateOrCreate(['merchant_id' => $merchant->id, 'type' => 'scroll'], [
            'title' => 'Pop-up Announcement',
            'status' => 'inactive',
            'type' => 'scroll',
            'message' => '<p>Welcome to </p>' . $merchant->store_name,
        ]);
    }

    public function defaultCustomerLevel($merchant)
    {
        $level = CustomerLevel::updateOrCreate(['merchant_id' => $merchant->id, 'name' => 'Customer'], [
            'name' => 'Customer',
            'make_default' => 'yes',
            'order' => 1,
            'upgrade_amount' => 0,
            'merchant_id' => $merchant->id
        ]);

        return $level;
    }
    

    public function deleteMerchantShop($id){
        // Delete announcement
        Announcement::where('merchant_id', $id)->delete();
        //Delete roles
        Role::where('merchant_id', $id)->delete();
        // Delete api
        Api::where('merchant_id', $id)->delete();
        // Delete admin
        Admin::where('merchant_id', $id)->delete();
        // Delete admin
        User::where('merchant_id', $id)->delete();
        Settings::where('merchant_id', $id)->delete();
        CustomerLevel::where('merchant_id', $id)->delete();
        Merchant::where('id', $id)->delete();
        
        return response()->json(['status' => 'success', 'message' => 'Operation successful'], 200);

    }

}
