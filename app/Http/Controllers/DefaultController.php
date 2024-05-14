<?php

namespace App\Http\Controllers;

use Session;
use Carbon\Carbon;
use App\Models\API;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\KycData;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Settings;
use App\Models\BillerLog;
use App\Models\BlackList;
use App\Models\Variation;
use Illuminate\Support\Str;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\CustomerLevel;
use App\Models\RolePermission;
use App\Models\TransactionLog;
use App\Models\EmailApiSetting;
use App\Scopes\MerchantIdScope;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DefaultController extends Controller
{
    
    public function createDefaultDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'store_name' => 'required',
            'store_slug' => 'required|unique:merchants',
            'currency' => 'required',
            'official_email' => 'nullable',
            'whatsapp_number'=> 'nullable',
            'email' => 'required|unique:users',
            'custom_domain' => 'nullable|unique:merchants',
            'merchant_name' => 'required',

            'store_location' => 'nullable',
            'subscription_start' => 'nullable',
            'subscription_end' => 'nullable',
            'api_key' => 'nullable',
            // 'secret_key' => 'required',
            // 'public_key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->all()], 200);
        }

        try {
            DB::beginTransaction();
            // Create Merchant
            $merchant = Merchant::updateOrCreate([
                'store_slug' => $request->store_slug,
            ],[
                'store_slug' => $request->store_slug,
                'merchant_name' => $request->merchant_name,
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
                'merchant_id' => $merchant->id,
                'email_verified_at' => Carbon::now()
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

            API::Create([
                "name" => 'KingsVTU',
                "warning_threshold_status" => 'inactive',
                "warning_threshold" => null,
                "api_key" => $request->api_key,
                // "secret_key" => $request->secret_key,
                // "public_key" => $request->public_key,
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

            DB::commit();

            \Log::info(['info' => $merchant]);
            return response()->json(['status' => 'success', 'message' => 'Operation successful', 'merchant' => $merchant], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'failed', 'message' => $th->getMessage()], 412);
        }
        
    }

    public function createPermissions($merchant, $admin){
        $ids = RolePermission::where('status', 'active')->pluck('id')->toArray();
        
        $role = Role::updateOrCreate(['name' => 'Admin', 'status' => 'active', 'merchant_id' => $merchant->id], [
            'name' => 'Admin',
            'permissions' => implode(",", $ids),
            'status' => 'active',
            'role_type' => 'superadmin',
            'merchant_id' => $merchant->id
        ]);

        $admin->update(['permissions' => $role->id]);

        return;
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

        return;
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
        $transactions = TransactionLog::where('merchant_id', $id)->count();
        if($transactions > 0){
            return response()->json(['status' => 'failed', 'message' => 'We could not delete shop with transactions'], 200);
        }

        // Delete announcement
        Announcement::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        //Delete roles
        Role::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        // Delete api
        API::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        // Delete admin
        Admin::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        
        // Delete others
        User::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Customer::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Settings::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        CustomerLevel::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        BlackList::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Category::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Product::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Merchant::withoutGlobalScope(new MerchantIdScope())->where('id', $id)->delete();
        EmailApiSetting::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        KycData::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();
        Variation::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Operation successful'], 200);
    }

    public function updateMerchantShop(Request $request, $id)
    {
        $merchant = Merchant::where('id', $request->merchant_id)->first();

        if (!empty($merchant)) {
            $merchant->update([
                'store_slug' => $request['store_slug'] ?: $merchant->store_slug,
                'store_name' => $request['store_name'] ?: $merchant->store_name,
                'subscription_start' => $request['subscription_start'] ?: $merchant->subscription_start,
                'subscription_end' => $request['subscription_end'] ?: $merchant->subscription_end,
                'custom_domain' => $request['custom_domain'] ?: $merchant->custom_domain
            ]);

            // Settings::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $id)->update();
        }
        return response()->json(['status' => 'success', 'message' => 'Operation successful from destination server'], 200);
    }

    public function accessMerchantShop(Request $request){
        $merchant = Merchant::where('id', $request->merchant_id)->first();
        // Admin
        if($merchant){
            $user = User::withoutGlobalScope(new MerchantIdScope())->where('type', 'admin')->where('merchant_id',$request->merchant_id)->where('status', 'active')->first();

            if($user){
                Auth::login($user);
                Session::put('loginId', $user->id);
                return redirect()->intended(route('dashboard', $merchant->store_slug));
            }
        }     
    }

}
