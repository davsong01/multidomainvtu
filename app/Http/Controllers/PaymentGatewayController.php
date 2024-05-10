<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $paymentgateway = PaymentGateway::orderBy('created_at', 'DESC')->get();
        return view('admin.paymentgateway.index', compact('paymentgateway'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.paymentgateway.create');
    }

    public function edit($merchant,  $paymentgateway)
    {
        $paymentgateway = PaymentGateway::where('id', $paymentgateway)->first();
        return view('admin.paymentgateway.edit', compact('paymentgateway'));
    }

    public function update(Request $request, $merchant, $paymentgateway)
    {        
        $this->validate($request, [
            "name" => "required",
            "password" => "nullable",
            "api_key" => "nullable",
            "secret_key" => "nullable",
            "public_key" => "nullable",
            "contract_id" => "nullable",
            "merchant_email" => "nullable",
            "base_url" => "nullable",
            "status" => "nullable",
            // "charge" => "required",
            "reserved_account_payment_charge" => "nullable",
            "reserved_account_payment_charge_type" => "nullable",
        ]);

        $paymentgateway = PaymentGateway::where('id', $paymentgateway)->first();

        $paymentgateway->update([
            "name" => $request->name,
            "password" => $request->password,
            "api_key" => $request->api_key,
            "secret_key" => $request->secret_key,
            "public_key" => $request->public_key,
            "contract_id" => $request->contract_id,
            "merchant_email" => $request->merchant_email,
            "base_url" => $request->base_url,
            "status" => $request->status,
            // "charge" => $request->charge,
            "reserved_account_payment_charge" => $request->reserved_account_payment_charge,
            "reserved_account_payment_charge_type" => $request->reserved_account_payment_charge_type,

        ]);

        return back()->with('message', 'Updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentGateway $paymentGateway)
    {
        //
    }
}
