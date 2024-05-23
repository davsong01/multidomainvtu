<?php

use App\Models\KycData;
use App\Models\Category;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\Merchant;
use App\Models\Settings;
use App\Mail\EmailMessages;
use Illuminate\Support\Arr;
use App\Models\Announcement;
use App\Models\PaymentGateway;
use App\Models\EmailApiSetting;
use App\Scopes\MerchantIdScope;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentProcessors\SquadController;
use App\Http\Controllers\PaymentProcessors\MonnifyController;

if (!function_exists("landingPageCategories")) {
    function landingPageCategories(): array {
        $data = [
            [
                "display_name" => "Airtime",
                "status" => "active",
                "slug" => "mtn-ussd",
                "description" => "KINGSVTU.NG: Buy Airtime",
                "icon" => "<svg class='icon' xmlns=\"http://www.w3.org/2000/svg\" fill=\"white\" height=\"24\" viewBox=\"0 -960 960 960\" width=\"24\"><path d=\"M760-480q0-117-81.5-198.5T480-760v-80q75 0 140.5 28.5t114 77q48.5 48.5 77 114T840-480h-80Zm-160 0q0-50-35-85t-85-35v-80q83 0 141.5 58.5T680-480h-80Zm198 360q-125 0-247-54.5T329-329Q229-429 174.5-551T120-798q0-18 12-30t30-12h162q14 0 25 9.5t13 22.5l26 140q2 16-1 27t-11 19l-97 98q20 37 47.5 71.5T387-386q31 31 65 57.5t72 48.5l94-94q9-9 23.5-13.5T670-390l138 28q14 4 23 14.5t9 23.5v162q0 18-12 30t-30 12ZM241-600l66-66-17-94h-89q5 41 14 81t26 79Zm358 358q39 17 79.5 27t81.5 13v-88l-94-19-67 67ZM241-600Zm358 358Z\"/></svg>",
                "unique_element" => "phone",
                "discount_type" => "percentage",
                'class' => 'ion',
            ],
            [
                "display_name" => "Data Subscriptions",
                "status" => "active",
                "slug" => "data",
                "description" => "KINGSVTU.NG: Buy Internet Data",
                "icon" => "<svg class='icon' xmlns=\"http://www.w3.org/2000/svg\" height=\"24\" viewBox=\"0 -960 960 960\" width=\"24\"><path d=\"M480-120q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM254-346l-84-86q59-59 138.5-93.5T480-560q92 0 171.5 35T790-430l-84 84q-44-44-102-69t-124-25q-66 0-124 25t-102 69ZM84-516 0-600q92-94 215-147t265-53q142 0 265 53t215 147l-84 84q-77-77-178.5-120.5T480-680q-116 0-217.5 43.5T84-516Z\"/></svg>",
                "unique_element" => "phone",
                "discount_type" => "flat",
            ],
            [
                "display_name" => "TV Subscriptions",
                "status" => "active",
                "slug" => "tv",
                "description" => "KINGSVTU.NG: Choose from a range of TV subscriptions for your entertainment. Easy payment, quick value delivery",
                "icon" => "<svg class='icon' xmlns=\"http://www.w3.org/2000/svg\" height=\"24\" viewBox=\"0 -960 960 960\" width=\"24\" fill=\"white\"><path d=\"M320-120v-80h80v-80H160q-33 0-56.5-23.5T80-360v-400q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v400q0 33-23.5 56.5T800-280H560v80h80v80H320ZM160-360h640v-400H160v400Zm0 0v-400 400Z\"/></svg>",
                "unique_element" => "iuc_number",
                "discount_type" => "percentage",
            ],
            [
                "display_name" => "Electricity",
                "status" => "active",
                "slug" => "electricity",
                "description" => "Recharge your meter",
                "icon" => "<svg class='icon' xmlns=\"http://www.w3.org/2000/svg\" height=\"24\" viewBox=\"0 -960 960 960\" width=\"24\"><path d=\"M480-80q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-200v-80h320v80H320Zm10-120q-69-41-109.5-110T180-580q0-125 87.5-212.5T480-880q125 0 212.5 87.5T780-580q0 81-40.5 150T630-320H330Zm24-80h252q45-32 69.5-79T700-580q0-92-64-156t-156-64q-92 0-156 64t-64 156q0 54 24.5 101t69.5 79Zm126 0Z\"/></svg>",
                "unique_element" => "meter_number",
                "discount_type" => "percentage",
            ],
            [
                "display_name" => "Education E-PINs",
                "status" => "active",
                "slug" => "education",
                "description" => "KINGSVTU.NG: Education E-PINs",
                "icon" => "<svg class='icon' xmlns=\"http://www.w3.org/2000/svg\" height=\"24\" fill=\"white\" viewBox=\"0 -960 960 960\" width=\"24\"><path d=\"M480-120 200-272v-240L40-600l440-240 440 240v320h-80v-276l-80 44v240L480-120Zm0-332 274-148-274-148-274 148 274 148Zm0 241 200-108v-151L480-360 280-470v151l200 108Zm0-241Zm0 90Zm0 0Z\"/></svg>",
                "unique_element" => "phone",
                "discount_type" => "flat",
            ]
        ];

        return $data;
    }
}

if (!function_exists("extractKeyValuesFromMultiDimensionalArray")) {
    function extractKeyValuesFromMultiDimensionalArray($search_column,$value_column, $arr): array
    {
        $modified = [];
        if(!empty($arr) && !empty($arr)){
            foreach($arr as $r){
                if(isset($r[$search_column]) && isset($r[$value_column])){
                    $modified[$r[$search_column]] = $r[$value_column];                
                }
            }
        }
        return $modified;
    }
}


if (!function_exists("getPaymentGateways")) {
    function getPaymentGateways()
    {
        $gateways = [
            [
                'name' => 'monnify',
                'status' => 'inactive',
            ],
            [
                'name' => 'squad',
                'status' => 'active',
            ] 
        ];

        return json_decode(json_encode($gateways), FALSE);
    }
}

if (!function_exists("getPaymentGatewayReservedAccountCharge")) {
    function getPaymentGatewayReservedAccountCharge($provider = null)
    {
        if(!empty($provider)){
            $gateway = PaymentGateway::withoutGlobalScope(new MerchantIdScope())->where('status', 'active')->where('id', $provider)->first();
        }else{
            $gateway = PaymentGateway::withoutGlobalScope(new MerchantIdScope())->where('status', 'active')->first();
        }
        
        if($gateway->reserved_account_payment_charge_type == 'flat'){
            $charge = $gateway->reserved_account_payment_charge;
            $display_value = isset(getSettings()->currency) ? getSettings()->currency . number_format($charge, 1) : number_format($charge, 1);
            $type = 'flat';
        }else{
            $charge = $gateway->reserved_account_payment_charge;
            $display_value = $charge.'%';
            $type = 'percentage';
        }

        return [
            'type' => $type,
            'value' => $charge,
            'display_value' => $display_value
        ];
    }
}

if (!function_exists("calculatePaymentGatewayReservedAccountCharge")) {
    function calculatePaymentGatewayReservedAccountCharge($data, $amount)
    {
        if ($data['type'] == 'flat') {
            $charge = $data['value'];
        } else {
            $charge =  $data['value'] / 100 * $amount;
        }
        return $charge;
    }
}

if (!function_exists("createReservedAccount")) {
    function createReservedAccount($data = null,$admin_id=null)
    {
        $paymentGateway = request()->merchant_settings['payment_gateway'];
        $provider = PaymentGateway::where('slug', $paymentGateway)->first();
        
        $reserved = null;
        if (!empty($paymentGateway)) {
            if ($paymentGateway == 'monnify') {
                $monnify = new MonnifyController($provider);
                $reserved = $monnify->createReservedAccount($data, $admin_id);
            }

            if ($paymentGateway == 'squad') {
                $squad = new SquadController($provider);
                $reserved = $squad->createReservedAccount($data, $admin_id);
            }
        }
        
        return $reserved;
    }
}

if (!function_exists("logEmails")) {
    function logEmails($email_to, $subject, $body)
    {
        try {
            EmailLog::create([
                'status' => 'pending',
                'subject' => $subject,
                'recipient' => $email_to,
                'content' => $body,
                'merchant_id' => request()->merchant_id
            ]);
           
        } catch (\Exception $e) {}
    }
}

if (!function_exists("sendEmails")) {
    function sendEmails($email_to, $subject, $body, $merchant_id = null)
    {
        $email_settings = [];
        try {
            if(!empty($merchant_id)){
                $provider = EmailApiSetting::withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $merchant_id)->first();
                $settings = Settings::with('merchant')->withoutGlobalScope(new MerchantIdScope())->where('merchant_id', $merchant_id)->first();

                $email_settings = [
                    'logo' => $settings->logo,
                    'merchant_id' => $settings->merchant->id,
                    'store_slug' => $settings->merchant->store_slug,
                    'store_name' => $settings->merchant->store_name,
                ];
            }else{
                $provider = EmailApiSetting::first();
                $email_settings = $request['merchant_settings'] ?? [];
            }
            
            config([
                'mail.driver' => $provider->MAIL_DRIVER ?? env('MAIL_DRIVER'),
                'mail.host' => $provider->MAIL_HOST ?? env('MAIL_HOST'),
                'mail.port' => $provider->MAIL_PORT ?? env('MAIL_PORT'),
                'mail.encryption' => $provider->MAIL_ENCRYPTION ?? env('MAIL_ENCRYPTION'),
                'mail.username' => $provider->MAIL_USERNAME ?? env('MAIL_USERNAME'),
                'mail.password' => $provider->MAIL_PASSWORD ?? env('MAIL_PASSWORD'),
                'mail.replyToName' => $provider->MAIL_REPLY_TO_NAME ?? env('MAIL_REPLY_TO_NAME'),
                'mail.replyToAddress' => $provider->MAIL_REPLY_TO_ADDRESS ?? env('MAIL_REPLY_TO_ADDRESS'),
                'mail.from' => [
                    'address' => $provider->MAIL_FROM_ADDRESS ?? env('MAIL_FROM_ADDRESS'),
                    'name' => $provider->MAIL_FROM_NAME ?? env('MAIL_FROM_NAME'),
                ],
            ]);

            $data = [
                'subject' => $subject,
                'body' => $body,
                'email_settings' => $email_settings ?? []
            ];

            Mail::to($email_to)->send(new EmailMessages($data));
            
            return [
                'message' => 'success',
            ];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return [
                'message' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists("sendAuthEmails")) {
    function sendAuthEmails($email_to, $subject, $body)
    {
        $data = [
            'subject' => $subject,
            'body' => $body,
        ];

        try {
            $provider = EmailApiSetting::first();
            config([
                'mail.driver' => $provider->MAIL_DRIVER ?? env('MAIL_DRIVER'),
                'mail.host' => $provider->MAIL_HOST ?? env('MAIL_HOST'),
                'mail.port' => $provider->MAIL_PORT ?? env('MAIL_PORT'),
                'mail.encryption' => $provider->MAIL_ENCRYPTION ?? env('MAIL_ENCRYPTION'),
                'mail.username' => $provider->MAIL_USERNAME ?? env('MAIL_USERNAME'),
                'mail.password' => $provider->MAIL_PASSWORD ?? env('MAIL_PASSWORD'),
                'mail.replyToName' => $provider->MAIL_REPLY_TO_NAME ?? env('MAIL_REPLY_TO_NAME'),
                'mail.replyToAddress' => $provider->MAIL_REPLY_TO_ADDRESS ?? env('MAIL_REPLY_TO_ADDRESS'),
                'mail.from' => [
                    'address' => $provider->MAIL_FROM_ADDRESS ?? env('MAIL_FROM_ADDRESS'),
                    'name' => $provider->MAIL_FROM_NAME ?? env('MAIL_FROM_NAME'),
                ],
            ]);

            $data = [
                'subject' => $subject,
                'body' => $body,
            ];
            
            // $current['provider'] = $provider->toArray();
            Mail::to($email_to)->send(new EmailMessages($data));
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        return true;
    }
}


if (!function_exists("getUniqueElements")) {
    function getUniqueElements()
    {
        return [
            'phone',
            'meter_number',
            'iuc_number',
            'account_id'
        ];
    }
}

if (!function_exists("verifiableUniqueElements")) {
    function verifiableUniqueElements()
    {
        return ['meter_number', 'iuc_number', 'profile_id'];
    }
}

if (!function_exists("getMerchantDetails")) {
    function getMerchantDetails(){
        $merchant = Merchant::where('id', Request::get('merchant_id'))->first();
        return $merchant;
    }
}

if (!function_exists("getCategories")) {
    function getCategories()
    {
        return Category::where('status', 'active')->where('type', 'general')->orderBy('order', 'ASC')->get();
    }
}

if (!function_exists("walletBalance")) {
    function walletBalance($user)
    {
        $wallet = new WalletController();
        return $wallet->getWalletBalance($user);
    }
}

if (!function_exists("referralBalance")) {
    function referralBalance($user)
    {
        $wallet = new WalletController();
        return $wallet->getReferralBalance($user);
    }
}

if (!function_exists("getSettings")) {
    function getSettings()
    {
        $settings = json_decode(json_encode(request()->merchant_settings), FALSE);
        return $settings;
    }
}

if (!function_exists("staffDefaultPassword")) {
    function staffDefaultPassword()
    {
        return '550523@'.request()->merchant_id;
    }
}

if (!function_exists("specialVerifiableVariations")) {
    function specialVerifiableVariations()
    {
        return $specialVerifiableVariations = [
            'utme-no-mock' => 'profile_id',
            'utme-mock' => 'profile_id',
            'de' => 'profile_id'
        ];
    }
}


if (!function_exists("getStates")) {
    function getStates()
    {
        $states = [
            "Abia",
            "Adamawa",
            "Akwa Ibom",
            "Anambra",
            "Bauchi",
            "Bayelsa",
            "Benue",
            "Borno",
            "Cross River",
            "Delta",
            "Ebonyi",
            "Edo",
            "Ekiti",
            "Enugu",
            "FCT - Abuja",
            "Gombe",
            "Imo",
            "Jigawa",
            "Kaduna",
            "Kano",
            "Katsina",
            "Kebbi",
            "Kogi",
            "Kwara",
            "Lagos",
            "Nasarawa",
            "Niger",
            "Ogun",
            "Ondo",
            "Osun",
            "Oyo",
            "Plateau",
            "Rivers",
            "Sokoto",
            "Taraba",
            "Yobe",
            "Zamfara"
        ];

        return $states;
    }
}

if (!function_exists("getLgas")) {
    function getLgas($state = null)
    {
        $states = [
            [
                "state" => "Adamawa",
                "alias" => "adamawa",
                "lgas" => [
                    "Demsa",
                    "Fufure",
                    "Ganye",
                    "Gayuk",
                    "Gombi",
                    "Grie",
                    "Hong",
                    "Jada",
                    "Larmurde",
                    "Madagali",
                    "Maiha",
                    "Mayo Belwa",
                    "Michika",
                    "Mubi North",
                    "Mubi South",
                    "Numan",
                    "Shelleng",
                    "Song",
                    "Toungo",
                    "Yola North",
                    "Yola South"
                ]
            ],

            [
                "state" => "FCT - Abuja",
                "alias" => "abuja",
                "lgas" => [
                    "Abaji LGA",
                    "Abuja Municipal Area Council",
                    "Bwari LGA",
                    "Gwagwalada LGA",
                    "Kwali LGA"
                ]
            ],

            [
                "state" => "Akwa Ibom",
                "alias" => "akwa_ibom",
                "lgas" => [
                    "Abak",
                    "Eastern Obolo",
                    "Eket",
                    "Esit Eket",
                    "Essien Udim",
                    "Etim Ekpo",
                    "Etinan",
                    "Ibeno",
                    "Ibesikpo Asutan",
                    "Ibiono-Ibom",
                    "Ikot Abasi",
                    "Ika",
                    "Ikono",
                    "Ikot Ekpene",
                    "Ini",
                    "Mkpat-Enin",
                    "Itu",
                    "Mbo",
                    "Nsit-Atai",
                    "Nsit-Ibom",
                    "Nsit-Ubium",
                    "Obot Akara",
                    "Okobo",
                    "Onna",
                    "Oron",
                    "Udung-Uko",
                    "Ukanafun",
                    "Oruk Anam",
                    "Uruan",
                    "Urue-Offong/Oruko",
                    "Uyo"
                ]
            ],
            [
                "state" => "Anambra",
                "alias" => "anambra",
                "lgas" => [
                    "Aguata",
                    "Anambra East",
                    "Anaocha",
                    "Awka North",
                    "Anambra West",
                    "Awka South",
                    "Ayamelum",
                    "Dunukofia",
                    "Ekwusigo",
                    "Idemili North",
                    "Idemili South",
                    "Ihiala",
                    "Njikoka",
                    "Nnewi North",
                    "Nnewi South",
                    "Ogbaru",
                    "Onitsha North",
                    "Onitsha South",
                    "Orumba North",
                    "Orumba South",
                    "Oyi"
                ]
            ],
            [
                "state" => "Ogun",
                "alias" => "ogun",
                "lgas" => [
                    "Abeokuta North",
                    "Abeokuta South",
                    "Ado-Odo/Ota",
                    "Egbado North",
                    "Ewekoro",
                    "Egbado South",
                    "Ijebu North",
                    "Ijebu East",
                    "Ifo",
                    "Ijebu Ode",
                    "Ijebu North East",
                    "Imeko Afon",
                    "Ikenne",
                    "Ipokia",
                    "Odeda",
                    "Obafemi Owode",
                    "Odogbolu",
                    "Remo North",
                    "Ogun Waterside",
                    "Shagamu"
                ]
            ],
            [
                "state" => "Ondo",
                "alias" => "ondo",
                "lgas" => [
                    "Akoko North-East",
                    "Akoko North-West",
                    "Akoko South-West",
                    "Akoko South-East",
                    "Akure North",
                    "Akure South",
                    "Ese Odo",
                    "Idanre",
                    "Ifedore",
                    "Ilaje",
                    "Irele",
                    "Ile Oluji/Okeigbo",
                    "Odigbo",
                    "Okitipupa",
                    "Ondo West",
                    "Ose",
                    "Ondo East",
                    "Owo"
                ]
            ],
            [
                "state" => "Rivers",
                "alias" => "rivers",
                "lgas" => [
                    "Abua/Odual",
                    "Ahoada East",
                    "Ahoada West",
                    "Andoni",
                    "Akuku-Toru",
                    "Asari-Toru",
                    "Bonny",
                    "Degema",
                    "Emuoha",
                    "Eleme",
                    "Ikwerre",
                    "Etche",
                    "Gokana",
                    "Khana",
                    "Obio/Akpor",
                    "Ogba/Egbema/Ndoni",
                    "Ogu/Bolo",
                    "Okrika",
                    "Omuma",
                    "Opobo/Nkoro",
                    "Oyigbo",
                    "Port Harcourt",
                    "Tai"
                ]
            ],
            [
                "state" => "Bauchi",
                "alias" => "bauchi",
                "lgas" => [
                    "Alkaleri",
                    "Bauchi",
                    "Bogoro",
                    "Damban",
                    "Darazo",
                    "Dass",
                    "Gamawa",
                    "Ganjuwa",
                    "Giade",
                    "Itas/Gadau",
                    "Jama'are",
                    "Katagum",
                    "Kirfi",
                    "Misau",
                    "Ningi",
                    "Shira",
                    "Tafawa Balewa",
                    "Toro",
                    "Warji",
                    "Zaki"
                ]
            ],
            [
                "state" => "Benue",
                "alias" => "benue",
                "lgas" => [
                    "Agatu",
                    "Apa",
                    "Ado",
                    "Buruku",
                    "Gboko",
                    "Guma",
                    "Gwer East",
                    "Gwer West",
                    "Katsina-Ala",
                    "Konshisha",
                    "Kwande",
                    "Logo",
                    "Makurdi",
                    "Obi",
                    "Ogbadibo",
                    "Ohimini",
                    "Oju",
                    "Okpokwu",
                    "Oturkpo",
                    "Tarka",
                    "Ukum",
                    "Ushongo",
                    "Vandeikya"
                ]
            ],
            [
                "state" => "Bornu",
                "alias" => "bornu",
                "lgas" => [
                    "Abadam",
                    "Askira/Uba",
                    "Bama",
                    "Bayo",
                    "Biu",
                    "Chibok",
                    "Damboa",
                    "Dikwa",
                    "Guzamala",
                    "Gubio",
                    "Hawul",
                    "Gwoza",
                    "Jere",
                    "Kaga",
                    "Kala/Balge",
                    "Konduga",
                    "Kukawa",
                    "Kwaya Kusar",
                    "Mafa",
                    "Magumeri",
                    "Maiduguri",
                    "Mobbar",
                    "Marte",
                    "Monguno",
                    "Ngala",
                    "Nganzai",
                    "Shani"
                ]
            ],
            [
                "state" => "Bayelsa",
                "alias" => "bayelsa",
                "lgas" => [
                    "Brass",
                    "Ekeremor",
                    "Kolokuma/Opokuma",
                    "Nembe",
                    "Ogbia",
                    "Sagbama",
                    "Southern Ijaw",
                    "Yenagoa"
                ]
            ],
            [
                "state" => "Cross River",
                "alias" => "cross_river",
                "lgas" => [
                    "Abi",
                    "Akamkpa",
                    "Akpabuyo",
                    "Bakassi",
                    "Bekwarra",
                    "Biase",
                    "Boki",
                    "Calabar Municipal",
                    "Calabar South",
                    "Etung",
                    "Ikom",
                    "Obanliku",
                    "Obubra",
                    "Obudu",
                    "Odukpani",
                    "Ogoja",
                    "Yakuur",
                    "Yala"
                ]
            ],
            [
                "state" => "Delta",
                "alias" => "delta",
                "lgas" => [
                    "Aniocha North",
                    "Aniocha South",
                    "Bomadi",
                    "Burutu",
                    "Ethiope West",
                    "Ethiope East",
                    "Ika North East",
                    "Ika South",
                    "Isoko North",
                    "Isoko South",
                    "Ndokwa East",
                    "Ndokwa West",
                    "Okpe",
                    "Oshimili North",
                    "Oshimili South",
                    "Patani",
                    "Sapele",
                    "Udu",
                    "Ughelli North",
                    "Ukwuani",
                    "Ughelli South",
                    "Uvwie",
                    "Warri North",
                    "Warri South",
                    "Warri South West"
                ]
            ],
            [
                "state" => "Ebonyi",
                "alias" => "ebonyi",
                "lgas" => [
                    "Abakaliki",
                    "Afikpo North",
                    "Ebonyi",
                    "Afikpo South",
                    "Ezza North",
                    "Ikwo",
                    "Ezza South",
                    "Ivo",
                    "Ishielu",
                    "Izzi",
                    "Ohaozara",
                    "Ohaukwu",
                    "Onicha"
                ]
            ],
            [
                "state" => "Edo",
                "alias" => "edo",
                "lgas" => [
                    "Akoko-Edo",
                    "Egor",
                    "Esan Central",
                    "Esan North-East",
                    "Esan South-East",
                    "Esan West",
                    "Etsako Central",
                    "Etsako East",
                    "Etsako West",
                    "Igueben",
                    "Ikpoba Okha",
                    "Orhionmwon",
                    "Oredo",
                    "Ovia North-East",
                    "Ovia South-West",
                    "Owan East",
                    "Owan West",
                    "Uhunmwonde"
                ]
            ],
            [
                "state" => "Ekiti",
                "alias" => "ekiti",
                "lgas" => [
                    "Ado Ekiti",
                    "Efon",
                    "Ekiti East",
                    "Ekiti South-West",
                    "Ekiti West",
                    "Emure",
                    "Gbonyin",
                    "Ido Osi",
                    "Ijero",
                    "Ikere",
                    "Ilejemeje",
                    "Irepodun/Ifelodun",
                    "Ikole",
                    "Ise/Orun",
                    "Moba",
                    "Oye"
                ]
            ],
            [
                "state" => "Enugu",
                "alias" => "enugu",
                "lgas" => [
                    "Awgu",
                    "Aninri",
                    "Enugu East",
                    "Enugu North",
                    "Ezeagu",
                    "Enugu South",
                    "Igbo Etiti",
                    "Igbo Eze North",
                    "Igbo Eze South",
                    "Isi Uzo",
                    "Nkanu East",
                    "Nkanu West",
                    "Nsukka",
                    "Udenu",
                    "Oji River",
                    "Uzo Uwani",
                    "Udi"
                ]
            ],
            [
                "state" => "Federal Capital Territory",
                "alias" => "abuja",
                "lgas" => [
                    "Abaji",
                    "Bwari",
                    "Gwagwalada",
                    "Kuje",
                    "Kwali",
                    "Municipal Area Council"
                ]
            ],
            [
                "state" => "Gombe",
                "alias" => "gombe",
                "lgas" => [
                    "Akko",
                    "Balanga",
                    "Billiri",
                    "Dukku",
                    "Funakaye",
                    "Gombe",
                    "Kaltungo",
                    "Kwami",
                    "Nafada",
                    "Shongom",
                    "Yamaltu/Deba"
                ]
            ],
            [
                "state" => "Jigawa",
                "alias" => "jigawa",
                "lgas" => [
                    "Auyo",
                    "Babura",
                    "Buji",
                    "Biriniwa",
                    "Birnin Kudu",
                    "Dutse",
                    "Gagarawa",
                    "Garki",
                    "Gumel",
                    "Guri",
                    "Gwaram",
                    "Gwiwa",
                    "Hadejia",
                    "Jahun",
                    "Kafin Hausa",
                    "Kazaure",
                    "Kiri Kasama",
                    "Kiyawa",
                    "Kaugama",
                    "Maigatari",
                    "Malam Madori",
                    "Miga",
                    "Sule Tankarkar",
                    "Roni",
                    "Ringim",
                    "Yankwashi",
                    "Taura"
                ]
            ],
            [
                "state" => "Oyo",
                "alias" => "oyo",
                "lgas" => [
                    "Afijio",
                    "Akinyele",
                    "Atiba",
                    "Atisbo",
                    "Egbeda",
                    "Ibadan North",
                    "Ibadan North-East",
                    "Ibadan North-West",
                    "Ibadan South-East",
                    "Ibarapa Central",
                    "Ibadan South-West",
                    "Ibarapa East",
                    "Ido",
                    "Ibarapa North",
                    "Irepo",
                    "Iseyin",
                    "Itesiwaju",
                    "Iwajowa",
                    "Kajola",
                    "Lagelu",
                    "Ogbomosho North",
                    "Ogbomosho South",
                    "Ogo Oluwa",
                    "Olorunsogo",
                    "Oluyole",
                    "Ona Ara",
                    "Orelope",
                    "Ori Ire",
                    "Oyo",
                    "Oyo East",
                    "Saki East",
                    "Saki West",
                    "Surulere Oyo State"
                ]
            ],
            [
                "state" => "Imo",
                "alias" => "imo",
                "lgas" => [
                    "Aboh Mbaise",
                    "Ahiazu Mbaise",
                    "Ehime Mbano",
                    "Ezinihitte",
                    "Ideato North",
                    "Ideato South",
                    "Ihitte/Uboma",
                    "Ikeduru",
                    "Isiala Mbano",
                    "Mbaitoli",
                    "Isu",
                    "Ngor Okpala",
                    "Njaba",
                    "Nkwerre",
                    "Nwangele",
                    "Obowo",
                    "Oguta",
                    "Ohaji/Egbema",
                    "Okigwe",
                    "Orlu",
                    "Orsu",
                    "Oru East",
                    "Oru West",
                    "Owerri Municipal",
                    "Owerri North",
                    "Unuimo",
                    "Owerri West"
                ]
            ],
            [
                "state" => "Kaduna",
                "alias" => "kaduna",
                "lgas" => [
                    "Birnin Gwari",
                    "Chikun",
                    "Giwa",
                    "Ikara",
                    "Igabi",
                    "Jaba",
                    "Jema'a",
                    "Kachia",
                    "Kaduna North",
                    "Kaduna South",
                    "Kagarko",
                    "Kajuru",
                    "Kaura",
                    "Kauru",
                    "Kubau",
                    "Kudan",
                    "Lere",
                    "Makarfi",
                    "Sabon Gari",
                    "Sanga",
                    "Soba",
                    "Zangon Kataf",
                    "Zaria"
                ]
            ],
            [
                "state" => "Kebbi",
                "alias" => "kebbi",
                "lgas" => [
                    "Aleiro",
                    "Argungu",
                    "Arewa Dandi",
                    "Augie",
                    "Bagudo",
                    "Birnin Kebbi",
                    "Bunza",
                    "Dandi",
                    "Fakai",
                    "Gwandu",
                    "Jega",
                    "Kalgo",
                    "Koko/Besse",
                    "Maiyama",
                    "Ngaski",
                    "Shanga",
                    "Suru",
                    "Sakaba",
                    "Wasagu/Danko",
                    "Yauri",
                    "Zuru"
                ]
            ],
            [
                "state" => "Kano",
                "alias" => "kano",
                "lgas" => [
                    "Ajingi",
                    "Albasu",
                    "Bagwai",
                    "Bebeji",
                    "Bichi",
                    "Bunkure",
                    "Dala",
                    "Dambatta",
                    "Dawakin Kudu",
                    "Dawakin Tofa",
                    "Doguwa",
                    "Fagge",
                    "Gabasawa",
                    "Garko",
                    "Garun Mallam",
                    "Gezawa",
                    "Gaya",
                    "Gwale",
                    "Gwarzo",
                    "Kabo",
                    "Kano Municipal",
                    "Karaye",
                    "Kibiya",
                    "Kiru",
                    "Kumbotso",
                    "Kunchi",
                    "Kura",
                    "Madobi",
                    "Makoda",
                    "Minjibir",
                    "Nasarawa",
                    "Rano",
                    "Rimin Gado",
                    "Rogo",
                    "Shanono",
                    "Takai",
                    "Sumaila",
                    "Tarauni",
                    "Tofa",
                    "Tsanyawa",
                    "Tudun Wada",
                    "Ungogo",
                    "Warawa",
                    "Wudil"
                ]
            ],
            [
                "state" => "Kogi",
                "alias" => "kogi",
                "lgas" => [
                    "Ajaokuta",
                    "Adavi",
                    "Ankpa",
                    "Bassa",
                    "Dekina",
                    "Ibaji",
                    "Idah",
                    "Igalamela Odolu",
                    "Ijumu",
                    "Kogi",
                    "Kabba/Bunu",
                    "Lokoja",
                    "Ofu",
                    "Mopa Muro",
                    "Ogori/Magongo",
                    "Okehi",
                    "Okene",
                    "Olamaboro",
                    "Omala",
                    "Yagba East",
                    "Yagba West"
                ]
            ],
            [
                "state" => "Osun",
                "alias" => "osun",
                "lgas" => [
                    "Aiyedire",
                    "Atakunmosa West",
                    "Atakunmosa East",
                    "Aiyedaade",
                    "Boluwaduro",
                    "Boripe",
                    "Ife East",
                    "Ede South",
                    "Ife North",
                    "Ede North",
                    "Ife South",
                    "Ejigbo",
                    "Ife Central",
                    "Ifedayo",
                    "Egbedore",
                    "Ila",
                    "Ifelodun",
                    "Ilesa East",
                    "Ilesa West",
                    "Irepodun",
                    "Irewole",
                    "Isokan",
                    "Iwo",
                    "Obokun",
                    "Odo Otin",
                    "Ola Oluwa",
                    "Olorunda",
                    "Oriade",
                    "Orolu",
                    "Osogbo"
                ]
            ],
            [
                "state" => "Sokoto",
                "alias" => "sokoto",
                "lgas" => [
                    "Gudu",
                    "Gwadabawa",
                    "Illela",
                    "Isa",
                    "Kebbe",
                    "Kware",
                    "Rabah",
                    "Sabon Birni",
                    "Shagari",
                    "Silame",
                    "Sokoto North",
                    "Sokoto South",
                    "Tambuwal",
                    "Tangaza",
                    "Tureta",
                    "Wamako",
                    "Wurno",
                    "Yabo",
                    "Binji",
                    "Bodinga",
                    "Dange Shuni",
                    "Goronyo",
                    "Gada"
                ]
            ],
            [
                "state" => "Plateau",
                "alias" => "plateau",
                "lgas" => [
                    "Bokkos",
                    "Barkin Ladi",
                    "Bassa",
                    "Jos East",
                    "Jos North",
                    "Jos South",
                    "Kanam",
                    "Kanke",
                    "Langtang South",
                    "Langtang North",
                    "Mangu",
                    "Mikang",
                    "Pankshin",
                    "Qua'an Pan",
                    "Riyom",
                    "Shendam",
                    "Wase"
                ]
            ],
            [
                "state" => "Taraba",
                "alias" => "taraba",
                "lgas" => [
                    "Ardo Kola",
                    "Bali",
                    "Donga",
                    "Gashaka",
                    "Gassol",
                    "Ibi",
                    "Jalingo",
                    "Karim Lamido",
                    "Kumi",
                    "Lau",
                    "Sardauna",
                    "Takum",
                    "Ussa",
                    "Wukari",
                    "Yorro",
                    "Zing"
                ]
            ],
            [
                "state" => "Yobe",
                "alias" => "yobe",
                "lgas" => [
                    "Bade",
                    "Bursari",
                    "Damaturu",
                    "Fika",
                    "Fune",
                    "Geidam",
                    "Gujba",
                    "Gulani",
                    "Jakusko",
                    "Karasuwa",
                    "Machina",
                    "Nangere",
                    "Nguru",
                    "Potiskum",
                    "Tarmuwa",
                    "Yunusari",
                    "Yusufari"
                ]
            ],
            [
                "state" => "Zamfara",
                "alias" => "zamfara",
                "lgas" => [
                    "Anka",
                    "Birnin Magaji/Kiyaw",
                    "Bakura",
                    "Bukkuyum",
                    "Bungudu",
                    "Gummi",
                    "Gusau",
                    "Kaura Namoda",
                    "Maradun",
                    "Shinkafi",
                    "Maru",
                    "Talata Mafara",
                    "Tsafe",
                    "Zurmi"
                ]
            ],
            [
                "state" => "Lagos",
                "alias" => "lagos",
                "lgas" => [
                    "Agege",
                    "Ajeromi-Ifelodun",
                    "Alimosho",
                    "Amuwo-Odofin",
                    "Badagry",
                    "Apapa",
                    "Epe",
                    "Eti Osa",
                    "Ibeju-Lekki",
                    "Ifako-Ijaiye",
                    "Ikeja",
                    "Ikorodu",
                    "Kosofe",
                    "Lagos Island",
                    "Mushin",
                    "Lagos Mainland",
                    "Ojo",
                    "Oshodi-Isolo",
                    "Shomolu",
                    "Surulere Lagos State"
                ]
            ],
            [
                "state" => "Katsina",
                "alias" => "katsina",
                "lgas" => [
                    "Bakori",
                    "Batagarawa",
                    "Batsari",
                    "Baure",
                    "Bindawa",
                    "Charanchi",
                    "Danja",
                    "Dandume",
                    "Dan Musa",
                    "Daura",
                    "Dutsi",
                    "Dutsin Ma",
                    "Faskari",
                    "Funtua",
                    "Ingawa",
                    "Jibia",
                    "Kafur",
                    "Kaita",
                    "Kankara",
                    "Kankia",
                    "Katsina",
                    "Kurfi",
                    "Kusada",
                    "Mai'Adua",
                    "Malumfashi",
                    "Mani",
                    "Mashi",
                    "Matazu",
                    "Musawa",
                    "Rimi",
                    "Sabuwa",
                    "Safana",
                    "Sandamu",
                    "Zango"
                ]
            ],
            [
                "state" => "Kwara",
                "alias" => "kwara",
                "lgas" => [
                    "Asa",
                    "Baruten",
                    "Edu",
                    "Ilorin East",
                    "Ifelodun",
                    "Ilorin South",
                    "Ekiti Kwara State",
                    "Ilorin West",
                    "Irepodun",
                    "Isin",
                    "Kaiama",
                    "Moro",
                    "Offa",
                    "Oke Ero",
                    "Oyun",
                    "Pategi"
                ]
            ],
            [
                "state" => "Nasarawa",
                "alias" => "nasarawa",
                "lgas" => [
                    "Akwanga",
                    "Awe",
                    "Doma",
                    "Karu",
                    "Keana",
                    "Keffi",
                    "Lafia",
                    "Kokona",
                    "Nasarawa Egon",
                    "Nasarawa",
                    "Obi",
                    "Toto",
                    "Wamba"
                ]
            ],
            [
                "state" => "Niger",
                "alias" => "niger",
                "lgas" => [
                    "Agaie",
                    "Agwara",
                    "Bida",
                    "Borgu",
                    "Bosso",
                    "Chanchaga",
                    "Edati",
                    "Gbako",
                    "Gurara",
                    "Katcha",
                    "Kontagora",
                    "Lapai",
                    "Lavun",
                    "Mariga",
                    "Magama",
                    "Mokwa",
                    "Mashegu",
                    "Moya",
                    "Paikoro",
                    "Rafi",
                    "Rijau",
                    "Shiroro",
                    "Suleja",
                    "Tafa",
                    "Wushishi"
                ]
            ],
            [
                "state" => "Abia",
                "alias" => "abia",
                "lgas" => [
                    "Aba North",
                    "Arochukwu",
                    "Aba South",
                    "Bende",
                    "Isiala Ngwa North",
                    "Ikwuano",
                    "Isiala Ngwa South",
                    "Isuikwuato",
                    "Obi Ngwa",
                    "Ohafia",
                    "Osisioma",
                    "Ugwunagbo",
                    "Ukwa East",
                    "Ukwa West",
                    "Umuahia North",
                    "Umuahia South",
                    "Umu Nneochi"
                ]
            ]
        ];

        $lgas = null;
        if (!empty ($state)) {
            foreach ($states as $key => $value) {
                if (strtolower($state) == strtolower($value['state'])) {
                    $lgas = array_values($value['lgas']);
                }
            }
        }

        return $lgas;
    }
}

if (!function_exists("kycStatus")) {
    function kycStatus($key, $customer_id)
    {
        $data = KycData::where(['customer_id' => $customer_id, 'key' => $key])->first();

        if (!$data) {
            $data = collect([
                'key' => '',
                'value' => '',
                'status' => 'unverified'
            ]);
        }

        return $data;
    }
}


if (!function_exists("getFinalKycStatus")) {
    function getFinalKycStatus($customer_id)
    {
        return Customer::where(['id' => $customer_id])->value('kyc_status');
    }
}

if (!function_exists("starMiddle")) {
    function starMiddle($word, $a = 2, $b = 9, $c = 9, $d = 10)
    {
        return substr_replace($word, "*******", $a, $b) . substr($word, $c, $d);
    }
}

if (!function_exists("announcements")) {
    function announcements($type)
    {
        $ann = $ann = Announcement::all();

        if (count($ann)) {
            if ($type == 'scroll') {
                return $ann[1];
            } else {
                return $ann[0];
            }
        }
    }

    if (!function_exists("hasAccess")) {
        function hasAccess($route)
        {
            $routes = auth()->user()->admin->rolepermissions();
            
            if (in_array($route, $routes)|| in_array(1, auth()->user()->admin->roleIds())) {
                return true;
            }else{
                return false;
            }
        }
    }
}
