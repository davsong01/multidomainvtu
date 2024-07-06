<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionLog;
use App\Models\ReservedAccount;
use App\Models\ReservedAccountNumber;
use App\Http\Controllers\PaymentProcessors\SquadController;
use App\Http\Controllers\PaymentProcessors\MonnifyController;

class ReservedAccountNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $numbers = ReservedAccountNumber::with('transactions')->orderBy('customer_id')->get();
        return view('admin.customers.reserved_account_numbers', compact('numbers'));
    }

    public function delete($merchant, $account)
    {
        $account = ReservedAccountNumber::where('id',$account)->first();
        
        $slug = $account->gateway->slug ?? null;
        $provider = $account->payment_gateway;
        
        if(!empty($slug)){
            if ($slug == 'monnify') {
                $monnify = new MonnifyController($provider);
                $delete = $monnify->deleteReservedAccount($account->account_reference);
            }

            if ($slug == 'squad') {
                $squad = new SquadController($provider);
                $delete = $squad->deleteReservedAccount($account->account_reference);
            }

            if ($delete['status'] == 'success') {
                return back()->with('message', 'Reserved Account Deleted successfully');
            } else {
                return back()->with('error', $delete['data']);
            }

        }else{
            return back()->with('message', 'Invalid Payment gateway selected');
        }
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
    public function show($merchant, $account)
    {
        $account = ReservedAccountNumber::where('id', $account)->first();

        $transactions = TransactionLog::where('account_number', $account->account_number)->orderBy('created_at', 'DESC')->get();
        
        return view('admin.customers.reserved_account_number_transactions', compact('transactions','account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReservedAccount $reservedAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReservedAccount $reservedAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReservedAccount $reservedAccount)
    {
        //
    }
}
