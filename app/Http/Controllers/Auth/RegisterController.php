<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\KycData;
use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Auth\VerifyEmailController;


class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // return back()->with('error', 'Registration is currently not available, please try again');
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:' . User::class],
            'email' => [
                'required','string','lowercase', 'email', 'max:255', Rule::unique('users')->where(function ($query) {
                    $query->where('merchant_id', request()->merchant_id);
                })
            ],
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        if(!empty(getSettings()->terms_and_conditions_link)){
            $request->validate([
                'privacy' => ['required'],
            ]);
        }

        $user = $this->createUser($request);
        // $user->update([
        //     'api_key' =>  strrev(md5($user->username)),
        // ]);
        
        $customer = $this->createCustomer($user);
        $this->createKycData($user, $customer);
        
        $this->sendWelcomeVerificationEmail($user);
        Auth::login($user);
        
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', request()->array));
        }

        return view('auth.verify-email', request()->array);

    }

    public function createUser($request){
        $user = User::create([
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone' => $request->phone,
            'username' => $request->username,
            'referral' => $request->referral ?? '',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'merchant_id' => request()->merchant_id,
        ]);

        return $user;
    }

    public function createKycData($user, $customer)
    {
        $data = KycData::create([
            'key' => 'PHONE_NUMBER',
            'value' => $user->phone,
            'status' => 'unverified',
            'customer_id' => $customer->id,
            'merchant_id' => request()->merchant_id
        ]);

        return $data;
    }

    public function createCustomer($user){
        $customer = Customer::create([
            'user_id' => $user->id,
            'wallet' => 0,
            'referal_wallet' => 0,
            'customer_level' => request()->merchant_settings['default_customer_level_id'] ?? null,
            'merchant_id' => request()->merchant_id
        ]);

        return $customer;
    }

    public function sendWelcomeVerificationEmail($user){
        $verifyController = new VerifyEmailController();
        $token = $verifyController->generateVerificationToken($user);
        $reset_link = route('verification.verify', array_merge(request()->array, ['token' => $token]));

        $settings = request()->merchant_settings;

        $body = '<p style="line-height: 2.0;">Hello! '.$user->first_name. ' <br>Thank you for signing up to '.request()->store_name. '! We are excited to have you join our community. <br> Please verify your email address by clicking the link below: <br><br> <a target="_blank" href="' . $reset_link . '" style="position: margin:5px; relative;margin-bottom:50px;color: ' . $settings['menu_text_color'] . ';font-weight: 500;padding: 8px 20px;font-size: 13px;line-height: 24px;letter-spacing: 0.01em;border-radius: 4px;border: 1px solid;transition: all .4s ease;background-color:' . $settings['menu_background_color'] . '!important;text-align: center;white-space: nowrap;vertical-align: middle;text-decoration:none">VERIFY EMAIL</a><br><br>If you have any questions or need assistance, feel free to reach out to our support team.<br><br>Warm Regards. <br>' . request()->store_name . '<br/></p>';
        
        sendAuthEmails($user->email, 'Welcome to '.request()->store_name, $body);
    }
}
