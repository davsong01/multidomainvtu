<?php

namespace App\Http\Controllers\PaymentProcessors;


use App\Models\KycData;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\ReservedAccountNumber;

class SquadController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = PaymentGateway::where('slug', 'squad')->first();
    }


    public function verifyTransaction($reference)
    {
        $url = $this->api->base_url . 'v2/transactions/' . urlencode($reference);
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api->secret_key,
        ];

        $response = $this->makeCall($url, [], $headers, 'GET');
        dd($response);
        if (
            isset($response['responseCode']) && isset($response['responseBody']) && $response['responseCode'] == 0 && $response['responseBody']['paymentStatus'] == 'PAID' &&
            $response['responseMessage'] == 'success'
        ) {
            $real = [
                'status' => 'success',
                'data' => $response['responseBody'],
            ];
        } else {
            $real = [
                'status' => 'failed',
                'data' => $response['responseMessage'],
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
        
        $kycData = extractKeyValuesFromMultiDimensionalArray('key','value', $kycData);
        $gender = "1";
        if (isset($kycData['GENDER'])) {
            if ($kycData['GENDER'] == 'male') {
                $gender = "1";
            } else {
                $gender = "2";
            }
        }
        
        $payload = [
            "customer_identifier" => request()->store_slug.'-'.$data['customer_id'],
            "first_name" => $kycData['FIRST_NAME'] ?? NULL,
            "last_name" => $kycData['LAST_NAME'] ?? NULL,
            "mobile_num" => $kycData['PHONE_NUMBER'], 
            "email" => $data["customerEmail"],
            "bvn" => $kycData['BVN'],
            "dob" => $kycData['DATE_OF_BIRTH'] ?? '19/09/1980',
            "address" => $kycData['DATE_OF_BIRTH'] ?? 'Lagos',
            "gender" => $gender,
        ];

        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $this->api->secret_key . "",
        ];

        $response = $this->makeCall($url, $payload, $headers);
        
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
            }else{
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

    public function makeCall($url, $payload, $headers, $method = 'POST'){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $this->api->secret_key
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
