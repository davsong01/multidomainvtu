<?php

namespace App\Http\Controllers\PaymentProcessors;


use App\Models\KycData;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Scopes\MerchantIdScope;
use App\Http\Controllers\Controller;
use App\Models\ReservedAccountNumber;

class SquadController extends Controller
{
    private $api;

    public function __construct($provider = null)
    {
        if (!empty(request()->merchant_id)) {
            $this->api = PaymentGateway::where('slug', 'squad')->first();
        } else {
            $this->api = $provider;
        }
    }
    
    public function verifyTransaction($reference)
    {
        $url = $this->api->base_url . 'virtual-account/merchant/transactions/all?transactionReference=' . $reference;

        if (env('ENT') == 'local') {
            $response = [
                "status" => 200,
                "success" => true,
                "message" => "Success",
                "data" => [
                    "count" => 15,
                    "rows" => [
                        [
                            "transaction_reference" => $reference,
                            "virtual_account_number" => "6725149329",
                            "principal_amount" => "50.00",
                            "settled_amount" => "50.00",
                            "fee_charged" => "0.00",
                            "transaction_date" => "2022-10-07T00:00:00.000Z",
                            "transaction_indicator" => "C",
                            "remarks" => "Transfer FROM Sample | [CCC1234334] TO Sample Name",
                            "currency" => "NGN",
                            "alerted_merchant" => false,
                            "merchant_settlement_date" => "2022-10-07T12:04:11.635Z",
                            "frozen_transaction" => null,
                            "customer" => [
                                "customer_identifier" => "5UMKKK3R"
                            ]
                        ],
                        [
                            "transaction_reference" => $reference,
                            "virtual_account_number" => "6725149329",
                            "principal_amount" => "50.00",
                            "settled_amount" => "50.00",
                            "fee_charged" => "0.00",
                            "transaction_date" => "2022-10-07T00:00:00.000Z",
                            "transaction_indicator" => "C",
                            "remarks" => "Transfer FROM Sample | [CCC1234334] TO Sample Name",
                            "currency" => "NGN",
                            "alerted_merchant" => false,
                            "merchant_settlement_date" => "2022-10-07T12:04:11.635Z",
                            "frozen_transaction" => null,
                            "customer" => [
                                "customer_identifier" => "5UMKKK3R"
                            ]
                        ],
                    ],
                    "query" => []
                ]
            ];
        } else {
            $response = $this->makeCall($url, [], [], 'GET');
        }

        if (isset($response['success']) && isset($response['message']) && $response['success'] == true && $response['message'] == 'Success') {
            $real = [
                'status' => 'success',
                'data' => $response['data']['rows'][0],
            ];
        } else {
            $real = [
                'status' => 'failed',
                'data' => $response['message'] ?? 'Something went wrong',
            ];
        }

        return $real;
    }

    public function createReservedAccount(array $data, int $admin_id = null)
    {
        $url = $this->api->base_url . 'virtual-account';
        // Get customer and kyc data
        $customer = Customer::with('multiplekycdata')->where('id', $data['customer_id'])->first();
        $kycData = $customer->multiplekycdata->toArray();

        $kycData = extractKeyValuesFromMultiDimensionalArray('key', 'value', $kycData);
        $gender = "1";
        if (isset($kycData['GENDER'])) {
            if ($kycData['GENDER'] == 'male') {
                $gender = "1";
            } else {
                $gender = "2";
            }
        }

        $payload = [
            "customer_identifier" => request()->store_slug . '-' . $data['customer_id'],
            "first_name" => $kycData['FIRST_NAME'] ?? NULL,
            "last_name" => $kycData['LAST_NAME'] ?? NULL,
            "mobile_num" => $kycData['PHONE_NUMBER'],
            "email" => $data["customerEmail"],
            "bvn" => $kycData['BVN'],
            "dob" => $kycData['DATE_OF_BIRTH'] ?? '19/09/1980',
            "address" => $kycData['DATE_OF_BIRTH'] ?? 'Lagos',
            "gender" => $gender,
        ];

        $response = $this->makeCall($url, $payload);

        if (
            isset($response) && $response['success'] == true &&
            $response['message'] == 'Success'
        ) {
            if (isset($response['data'])) {
                $dataX = $response['data'];

                ReservedAccountNumber::updateOrCreate([
                    'customer_id' => $data["customer_id"] ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                ], [
                    'customer_id' => $data["customer_id"] ?? null,
                    'admin_id' => $admin_id ?? null,
                    'account_reference' => $dataX['customer_identifier'],
                    'account_number' => $dataX['virtual_account_number'] ?? null,
                    'account_name' => $dataX['first_name'],
                    'bank_name' => 'GT Bank',
                    'bank_code' => $dataX['bank_code'],

                    'paymentgateway_id' => $this->api->id,
                    'status' => 'active',
                    'purpose' => 'WALLET-FUNDING',
                    'bvn' =>  $kycData['BVN'],
                    'response' => json_encode($response),
                    'merchant_id' => request()->merchant_id,
                ]);

                $res = [
                    'status' => 'success',
                    'data' => '',
                ];
            } else {
                $res = [
                    'status' => 'failed',
                    'data' => $response['message'] ?? 'No Accounts set by Provider',
                ];
            }
        } else {
            $res = [
                'status' => 'failed',
                'data' => $response['message'] ?? 'no-response',
            ];
        }

        return $res;
    }

    public function makeCall($url, $payload, $method = 'POST')
    {
        $curl = curl_init();

        if ($method == 'POST') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',

                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key . ""
                ),
            ));
        }

        if ($method == 'GET') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $this->api->secret_key . ""
                    // 'Authorization: Bearer sandbox_sk_789945f4301f0b0dbd60a2f0f9bf40bc2d006fa45653'
                ),
            ));
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

//     'transaction_reference' => 'REFC8XVG7L891715795301940_1',
//     'virtual_account_number' => '6725149329',
//     'principal_amount' => '2500.00',
//     'settled_amount' => '2493.75',
//     'fee_charged' => '6.25',
//     'transaction_date' => '2024-05-15T18:48:22+01:00',
//     'customer_identifier' => 'davsong-10',
//     'transaction_indicator' => 'C',
//     'remarks' => 'Transfer FROM First Cus | [davsong-10] TO sandbox sandbox',
//     'currency' => 'NGN',
//     'channel' => 'virtual-account',
//     'sender_name' => 'First Cus',
//     'meta' => 
//     array (
//       'freeze_transaction_ref' => NULL,
//       'reason_for_frozen_transaction' => NULL,
//     ),
//     'encrypted_body' => '8zttY2ZEsfmYS0ERV/ATD132eWmhQMav5YcJaXtFs5xKBN9WaecNswDDVVajWWmodsaEBdtyAoBvUV04AJyW86HEg1ZFDzlhpPmol/nZrAE+qiPsshUrMZwcAo+Ri+NyWpVCZIRENN7VHjOzUbjYobJP61P2ge53h7D1yyVa5yjrq2MP8i6i8Olm2VMiJolpzHbtj3R4dtP/qAU4h/Fkxvbp+0h+rIyxiMEQp1SggkpegOJ6LM9uEgY2yR813AYR1PgZC8z3coWXTLTOx6wj3hsvSEUOVBJIdn3gDjq0kGLx9jaEap0BpVd8taQgNAQyBLXhvxTGl3mqHG8/Cp/lN8he8op46ZdLDIYNS4kmngCBir/1c0A1F/jkGK1mhBPFOg/9WyGY+s36w/YXAMk19LmB/AdU4f8L31OAuKz/0R+AUiGKD0e4jN/2ShTrbd0QFp7jts+wAU1KZXRLbi3V0Wjdultd3AaL00NW8I64bBeQQNA8E/Oq1a7HBlIgGN/pbAcVp0oLbA8tbKQDI0b8fNzS6DdCREyjP0mnVzQHgMfUkNREYa16LOkBA7k8dB/Kr8V+NWn9haLnZl6D7qr6ac3mreMfOMrR0uhQ78tR+evZRK+HhB1unEozlMzl8TJko0mXpbi2tvL6U3nN/5hgKBFHpq3IiAJAB4JyhkQXrr0=',
//   ),
}
