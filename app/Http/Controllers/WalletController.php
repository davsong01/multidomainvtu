<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function getWalletBalance($user)
    {
        return $user->customer->wallet ?? 0.00;
    }

    public function getReferralBalance($user)
    {
        return $user->customer->referal_wallet ?? 0;
    }

    public function logWallet($data)
    {
        $wallet = Wallet::create([
            'customer_id' => $data['customer_id'] ?? auth()->user()->customer->id,
            'amount' => $data['total_amount'],
            'type' => $data['type'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'reason' => $data['reason'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'merchant_id' => $data['merchant_id'] ?? request()->merchant_id,
        ]);

        return $wallet;
    }

    public function updateCustomerWallet($user, $amount, $type)
    {
        $customer = !empty($user->customer) ? $user->customer : $user->cron_customer;
        
        if ($type == 'credit') {
            $customer->update([
                'wallet' => $customer->wallet + $amount,
            ]);
        } else {
            $customer->update([
                'wallet' => $customer->wallet - $amount,
            ]);
        }
        
        return;
    }

    public function updateReferralWallet($user, $amount, $type)
    {
        if ($type == 'credit') {
            $user->customer->update([
                'referal_wallet' => $user->customer->referal_wallet + $amount,
            ]);
        } else {
            $user->customer->update([
                'referal_wallet' => $user->customer->referal_wallet - $amount,
            ]);
        }

        return;
    }

    public function updatea2CashWallet($user, $amount, $type)
    {
        if ($type == 'credit') {
            $user->customer->update([
                'referal_wallet' => $user->customer->referal_wallet + $amount,
            ]);
        } else {
            $user->customer->update([
                'referal_wallet' => $user->customer->referal_wallet - $amount,
            ]);
        }

        return;
    }
}
