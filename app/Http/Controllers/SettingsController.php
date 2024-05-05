<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Settings $settings)
    {
        $settings = Settings::first();
        if (!$settings) {
            $settings = Settings::create([
                'logo' => '',
                'favicon' => '',
                'currency' => '&#8358;',
                'official_email' => '',
                'whatsapp_number' => '',
                // 'google_ad_code' => '',
                'seo_title' => '',
                'seo_description' => '',
                'support_link' => '',
            ]);
        }

        $currencies = [
            '₦',
            '$'
        ];

        return view('admin.settings', compact('settings', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Settings $settings)
    {
        $settings = Settings::first();
        $data = $request->except(['_token', 'logo', 'favicon', 'ip', 'merchant_settings', 'merchant_id', 'store_slug', 'store_name', 'array']);
        
        if (!empty($request->logo)) {
            $data['logo'] = $this->uploadFile($request->logo, 'site');
        }

        if (!empty($request->dashboard_logo)) {
            $data['dashboard_logo'] = $this->uploadFile($request->dashboard_logo, 'site');
        }

        if (!empty($request->favicon)) {
            $data['favicon'] = $this->uploadFile($request->favicon, 'site');
        }

        if (!empty($request->payment_gateway)) {
            PaymentGateway::where('merchant_id', request()->merchant_id)->update(['status' => 'inactive']);
            
            if($request->payment_gateway == 'monnify'){
                $paymentgateway = PaymentGateway::updateOrCreate(['merchant_id' => request()->merchant_id, 'name' => 'Monnify'], [
                    'name' => 'Monnify',
                    'base_url' => 'https://api.monnify.com/api/v1',
                    'status' => 'active',
                    'charge' => 1.5,
                    'slug' => 'monnify',
                    'reserved_account_payment_charge' => 1.5
                ]);
            }

            if ($request->payment_gateway == 'squad') {
                PaymentGateway::updateOrCreate(['merchant_id' => request()->merchant_id, 'name' => 'Squad'], [
                    'name' => 'Squad',
                    'base_url' => 'https://sandbox-api-d.squadco.com/',
                    'status' => 'active',
                    'charge' => 0,
                    'slug' => 'squad',
                    'reserved_account_payment_charge' => 0
                ]);
            }
            
            $data['payment_gateway'] = $request->payment_gateway;
        }

        $settings->update($data);

        return back()->with('message', 'Operation successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
