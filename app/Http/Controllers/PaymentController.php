<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\TransactionLog;
use App\Scopes\MerchantIdScope;
use Illuminate\Support\Facades\DB;
use App\Models\ReservedAccountNumber;
use App\Models\ReservedAccountCallback;
use App\Http\Controllers\PaymentProcessors\SquadController;
use App\Http\Controllers\PaymentProccessors\MonnifyController;

class PaymentController extends Controller
{
    public function redirectToUrl(Request $request)
    {
        $provider = PaymentGateway::where('id', 1)->first();

        if (empty($provider)) {
            return false;
        }

        // Log Wallet
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());
        $reference = $this->generateRequestId();
        $extra_charge = getSettings()->card_funding_extra_charge > 0 ? getSettings()->card_funding_extra_charge : 0;
        $provider_charge = ($provider->charge / 100) * $request->amount;
        $provider_charge = $provider_charge + $extra_charge;

        $amount = $request->amount - $provider_charge;
        $original_amount = $request->amount;

        $request['type'] = 'credit';
        $request['customer_id'] = auth()->user()->customer->id;
        $request['request_id'] = $reference;
        $request['transaction_id'] = '';
        $request['payment_method'] = $provider->name;
        $request['balance_before'] = $balance;
        $request['ip_address'] = $this->getIpAddress();
        $request['domain_name'] = $this->getDomainName();
        $request['customer_email'] = auth()->user()->email;
        $request['customer_phone'] = auth()->user()->phone;
        $request['customer_name'] = auth()->user()->firstname;
        $request['reason'] = 'WALLET-FUNDING';
        $request['amount'] = $original_amount;
        $request['total_amount'] = $amount;
        $request['discount'] = 0;
        $request['unit_price'] =  $amount;
        $request['quantity'] = 1;
        $request['unique_element'] = 'WALLET-FUNDING';
        $request['provider_charge'] = $provider_charge;
        $request['wallet_funding_provider'] = $provider->id;
        $request['api_id'] = $provider->id;

        $transaction =  app('App\Http\Controllers\TransactionController')->logTransaction($request->all());

        $request['reference'] = $reference;
        $request['amount'] = $original_amount;
        $redirect_url = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->redirectToGateway($request, $transaction);

        if (isset($redirect_url) && $redirect_url['status'] == 'success') {
            return redirect()->away($redirect_url['url']);
        } else {
            return back()->with('error', 'We could not initiate this transaction, please try again');
        }
    }

    // {"transaction_reference":"REFC8XVG7L891715795301940_1","virtual_account_number":"6725149329","principal_amount":"2500.00","settled_amount":"2493.75","fee_charged":"6.25","transaction_date":"2024-05-15T18:48:22+01:00","customer_identifier":"davsong-10","transaction_indicator":"C","remarks":"Transfer FROM First Cus | [davsong-10] TO sandbox sandbox","currency":"NGN","channel":"virtual-account","sender_name":"First Cus","meta":{"freeze_transaction_ref":null,"reason_for_frozen_transaction":null},"encrypted_body":"8zttY2ZEsfmYS0ERV\/ATD132eWmhQMav5YcJaXtFs5xKBN9WaecNswDDVVajWWmodsaEBdtyAoBvUV04AJyW86HEg1ZFDzlhpPmol\/nZrAE+qiPsshUrMZwcAo+Ri+NyWpVCZIRENN7VHjOzUbjYobJP61P2ge53h7D1yyVa5yjrq2MP8i6i8Olm2VMiJolpzHbtj3R4dtP\/qAU4h\/Fkxvbp+0h+rIyxiMEQp1SggkpegOJ6LM9uEgY2yR813AYR1PgZC8z3coWXTLTOx6wj3hsvSEUOVBJIdn3gDjq0kGLx9jaEap0BpVd8taQgNAQyBLXhvxTGl3mqHG8\/Cp\/lN8he8op46ZdLDIYNS4kmngCBir\/1c0A1F\/jkGK1mhBPFOg\/9WyGY+s36w\/YXAMk19LmB\/AdU4f8L31OAuKz\/0R+AUiGKD0e4jN\/2ShTrbd0QFp7jts+wAU1KZXRLbi3V0Wjdultd3AaL00NW8I64bBeQQNA8E\/Oq1a7HBlIgGN\/pbAcVp0oLbA8tbKQDI0b8fNzS6DdCREyjP0mnVzQHgMfUkNREYa16LOkBA7k8dB\/Kr8V+NWn9haLnZl6D7qr6ac3mreMfOMrR0uhQ78tR+evZRK+HhB1unEozlMzl8TJko0mXpbi2tvL6U3nN\/5hgKBFHpq3IiAJAB4JyhkQXrr0="}

    public function dumpCallback(Request $request, $provider, $internal='no')
    {
        $gateway = PaymentGateway::withoutGlobalScope(new MerchantIdScope())->where('id', $provider)->first();
        $merchant = Merchant::where('id', $gateway->merchant_id)->first();

        if (!$gateway || !$merchant) {
            return;
        }

        if ($gateway->slug == 'monnify') {
            $account_number = $request['eventData']['destinationAccountInformation']['accountNumber'];
            $session_id = $request['eventData']['paymentSourceInformation'][0]['sessionId'];
            $transaction_reference = $request['eventData']['transactionReference'] ?? $request['eventData']['paymentReference'];
            $payment_method = $request['eventData']['paymentMethod'];
            $paid_on = $request['eventData']['paidOn'];
        }

        if ($gateway->slug == 'squad') {
            $account_number = $request['virtual_account_number'];
            $session_id = $request['transaction_reference'];
            $transaction_reference = $request['transaction_reference'];
            $payment_method = $request['channel'];
            $paid_on = Carbon::parse($request['transaction_date']);
        }

        $check = ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where(['session_id' => $session_id, 'transaction_reference' => $transaction_reference])->first();

        if (!$check) {
            ReservedAccountCallback::updateOrCreate(['session_id' => $session_id], [
                'raw' => json_encode($request->all()),
                'provider_id' => $provider,
                'paid_on' => $paid_on ?? null,
                'session_id' => $session_id,
                'account_number' => $account_number,
                'payment_method' => $payment_method,
                'merchant_id' => $merchant->id,
                'transaction_reference' => $transaction_reference,
            ]);

            if ($internal == 'yes') {
                return ['message' => 'success'];
            } else {
                return response()->json(['message' => 'success'], 200);
            }
        }else{
            if ($internal == 'yes') {
                return ['error' => 'Likely duplicate'];
            } else {
                return response()->json(['message' => 'success'], 200);
            }
        }
        
    }

    public function analyzeCallbackResponse()
    {
        try {
            $calls = ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where(['status' => 'pending'])->orderBy('id', 'ASC')
                ->take(5)
                ->get()
                ->toArray();

            if (count($calls) < 1) {
                return;
            }

            $tlk = 'PICKED-' . time();
            $ids = array_column($calls, 'id');

            ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->whereIn('id', $ids)->update(['status' => $tlk]);
            $calls = ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where(['status' => $tlk])->get();

            // dummy
            // $calls = ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where(['status' => 'pending'])->orderBy('id', 'ASC')->get();
            // end dummy

            foreach ($calls as $call) {
                DB::beginTransaction();

                $decodeCall = json_decode($call->raw, true);
                $account = ReservedAccountNumber::withoutGlobalScope(new MerchantIdScope())->with('cron_customer')->where('account_number', $call->account_number)->where('paymentgateway_id', $call->provider_id)->first();
                $provider = PaymentGateway::withoutGlobalScope(new MerchantIdScope())->where('id', $call->provider_id)->first();
                $settings = $call->merchant->setting;
                if (!$account) {
                    ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->whereIn('id', $ids)->update(['status' => 'processed']);
                    continue;
                }

                $customer = $account->cron_customer;
                $user = $account->cron_customer->cron_user;
                
                if ($provider->slug == 'monnify') {
                    $payment_type = $call->payment_method;

                    if ($payment_type === 'CARD') {
                        $extra_charge = $settings->card_funding_extra_charge > 0 ? $settings->card_funding_extra_charge : 0;

                        continue;
                    }
                    $monnify = new MonnifyController($provider);
                    $analyze = $monnify->verifyTransaction($call->transaction_reference);
                    
                    ReservedAccountCallback::where('id', $call->id)->update(['raw_requery' => json_encode($analyze['data'])]);

                    if (isset($analyze) && $analyze['status'] == 'success') {
                        $payment_method = $provider->name . '(' . $decodeCall['eventData']['paymentMethod'] . ')';
                        
                        $original_amount = $analyze['data']['amountPaid'] ?? $decodeCall['eventData']['amountPaid'];

                        $transaction_id = $analyze['data']['transactionReference'] ?? $decodeCall['eventData']['transactionReference'];
                    }else{
                        ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->whereIn('id', $ids)->update(['status' => 'processed']);
                    }
                }

                if ($provider->slug == 'squad') {
                    $squad = new SquadController($provider);
                    $analyze = $squad->verifyTransaction($call->transaction_reference);
                    
                    ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where('id', $call->id)->update(['raw_requery' => json_encode($analyze['data'])]);

                    if (isset($analyze) && $analyze['status'] == 'success') {
                        $payment_method = $provider->name . '(BANK_TRANSFER)';
                        
                        $original_amount = $analyze['data']['principal_amount'];

                        $transaction_id = $analyze['data']['transactionReference'] ?? $call->transaction_reference;
                    } else {
                        ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->whereIn('id', $ids)->update(['status' => 'processed']);
                    }
                }

                if (isset($analyze) && $analyze['status'] == 'success') {
                    // Log Transaction
                    $wallet = new WalletController();
                    $balance = $wallet->getWalletBalance($user);
                    $reference = $this->generateRequestId();

                    $provider_charge_setting = getPaymentGatewayReservedAccountCharge($provider->id) ?? 0;
                    $provider_charge = calculatePaymentGatewayReservedAccountCharge($provider_charge_setting, $original_amount) ?? 0;
                    
                    $amount = $original_amount - $provider_charge;

                    $request['type'] = 'credit';
                    $request['customer_id'] = $customer->id;
                    $request['request_id'] = $reference;
                    $request['transaction_id'] = $transaction_id;
                    $request['payment_method'] = $payment_method;
                    $request['balance_before'] = $balance;
                    $request['ip_address'] = $this->getIpAddress();
                    $request['domain_name'] = $this->getDomainName();
                    $request['customer_email'] = $user->email;
                    $request['customer_phone'] = $user->phone;
                    $request['customer_name'] = $user->firstname;
                    $request['reason'] = 'WALLET-FUNDING';
                    $request['amount'] = $original_amount;
                    $request['total_amount'] = $amount;
                    $request['discount'] = 0;
                    $request['unit_price'] =  $amount;
                    $request['quantity'] = 1;
                    $request['unique_element'] = 'WALLET-FUNDING';
                    $request['provider_charge'] = $provider_charge;
                    $request['wallet_funding_provider'] = $call->provider_id;
                    $request['api_id'] = $call->provider_id;
                    $request['account_number'] =  $call->account_number;
                    $request['merchant_id'] =  $call->merchant_id;

                    // $settings = $call->merchant->setting;
                    $settings->store_name = $call->merchant->store_name;

                    $transaction =  app('App\Http\Controllers\TransactionController')->logTransaction($request);
                    $transaction->update([
                        'balance_after' => $balance + $amount,
                        'status' => 'delivered',
                        'descr' => 'Wallet Funding Via Account: ' . $call->account_number . ' of ' . $settings->currency . number_format($amount, 2) . ' was successful',
                    ]);

                    $request['type'] = 'credit';
                    $request['total_amount'] = $amount;
                    $request['transaction_id'] = $transaction->transaction_id;
                    $request['reason'] = 'WALLET FUNDING Via Reserved account';

                    $wal = $wallet->logWallet($request);

                    // Update Customer Wallet
                    $wallet->updateCustomerWallet($user, $amount, $request['type']);
                    ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where('id', $call['id'])->update(['transaction_id' => $transaction_id]);

                    $this->sendTransactionEmail($transaction, $user, $settings);

                    ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where('id', $call->id)->update(['status' => 'analyzed']);
                    DB::commit();
                } else {
                    ReservedAccountCallback::withoutGlobalScope(new MerchantIdScope())->where('id', $call->id)->update(['status' => 'pending']);
                    DB::rollBack();
                    return response()->json([], 200);
                }

                return response()->json([], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage(), $th->getFile(), $th->getLine());
        }
    }

    public function analyzePaymentResponse(Request $request, $provider_id)
    {
        $wallet = new WalletController();
        $balance = $wallet->getWalletBalance(auth()->user());

        $reference_id = $request->paymentReference;
        $transaction = TransactionLog::where('reference_id', $reference_id)->first();
        if (!$transaction || !$reference_id) {
            return abort(404);
        }
        $providerDetails = PaymentGateway::where('id', $provider_id)->first();

        // Verify Transaction
        $verify = $this->verifyPayment($transaction->transaction_id, 1);

        if (isset($verify) && $verify['status'] == 'success') {
            $paid = $transaction->total_amount;

            try {
                DB::beginTransaction();
                // Log basic transaction
                $transaction->update([
                    'balance_after' => $balance + $paid,
                    'status' => 'delivered',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid, 2) . ' was successful',
                ]);
                // Log wallet
                $request['customer_id'] = auth()->user()->customer->id;
                $request['type'] = 'credit';
                $request['amount'] = $paid;
                $request['total_amount'] = $paid;
                $request['transaction_id'] = $transaction->transaction_id;
                $request['reason'] = 'WALLET FUNDING';

                $wal = $wallet->logWallet($request->all());

                // Update Customer Wallet
                $wallet->updateCustomerWallet(auth()->user(), $paid, $request['type']);

                DB::commit();

                $this->sendTransactionEmail($transaction, auth()->user());

                return redirect(route('transaction.status', $transaction->transaction_id));
            } catch (\Throwable $th) {
                DB::rollBack();
                $transaction->update([
                    'balance_after' => $balance,
                    'status' => 'attention-required',
                    'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid, 2) . ' failed. Transaction unverified',
                ]);
                // dd($th->getMessage(), $th->getLine(), $th->getFile());
                return redirect(route('transaction.status', $transaction->transaction_id));
            }
        } else {
            $transaction->update([
                'balance_after' => $balance,
                'status' => 'failed',
                'descr' => 'Wallet Funding of ' . getSettings()->currency . number_format($paid ?? $transaction->amount, 2) . ' failed. Transaction unverified',
            ]);

            return redirect(route('transaction.status', $transaction->transaction_id));
        }
    }

    public function verifyPayment($reference, $provider_id = null)
    {
        $provider = PaymentGateway::where('id', $provider_id)->first();

        if (empty($provider)) {
            return false;
        }
        
        $verify = app('App\Http\Controllers\PaymentProcessors\MonnifyController')->verifyTransaction($reference);

        return $verify;
    }

    public function callBackAnalysis()
    {
        $calls = ReservedAccountCallback::orderBy('status', 'DESC')->paginate();
        return view('admin.transaction.raw_callbacks', compact('calls'));
    }

    public function resetCallBackResponse($merchant, $callback)
    {
        $callback = ReservedAccountCallback::where('id', $callback)->first();
        $callback->status = 'pending';
        $callback->save();

        return back()->with('message', 'Operation Successful');
    }

}
