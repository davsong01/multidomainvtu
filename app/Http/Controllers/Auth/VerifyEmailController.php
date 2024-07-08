<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', request()->array));
        }
        
        $this->sendEmailVerificationNotification(auth()->user(), $request->type);

        if(!empty($request->type) && $request->type == 'resend'){
            $message = 'Email Verification link has been resent!';
            return redirect(route('verification.notice', request()->array))->with('message', $message);
        }else{
            $message = '';
        }

        return view('auth.verify-email', request()->array);
    }


    public function sendEmailVerificationNotification($user, $type = null)
    {
        $token = $this->generateVerificationToken($user,$type);
        $reset_link = route('verification.verify', array_merge(request()->array, ['token' => $token]));

        $settings = request()->merchant_settings;
        
        $body = '<p style="line-height: 2.0;">Hello! <br>Please verify your email on  ' . request()->store_name . ' with the link bellow: <br><br> <a target="_blank" href="' . $reset_link . '" style="position: margin:5px; relative;margin-bottom:50px;color: ' . $settings['menu_text_color'] . ';font-weight: 500;padding: 8px 20px;font-size: 13px;line-height: 24px;letter-spacing: 0.01em;border-radius: 4px;border: 1px solid;transition: all .4s ease;background-color:' . $settings['menu_background_color'] . '!important;text-align: center;white-space: nowrap;vertical-align: middle;text-decoration:none">VERIFY EMAIL</a><br><br>Warm Regards. <br>' . request()->store_name . '<br/></p>';
        
        sendAuthEmails($user->email, 'Email Verification Link', $body);
    }
    
    public function generateVerificationToken($user, $type = null){
        if (!empty($type) && $type == 'resend') {
            $verify = EmailVerification::where('user_id', $user->id)->first();
        } else {
            $token = Str::random(64);

            $verify = EmailVerification::updateOrCreate(['user_id' => $user->id], [
                'user_id' => $user->id,
                'token' => $token,
                'merchant_id' => request()->merchant_id
            ]);
        }
        
        return $verify->token;
    }

    public function confirmEmailVerification($merchant, $token)
    {
        $verifyUser = EmailVerification::with('user')->where('token', $token)->first();
        
        if (!empty($verifyUser)) {
            $user = $verifyUser->user;
            
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. Please login.";
            }
            $verifyUser->delete();

            return redirect()->route('dashboard', $merchant)->with('message', $message);

        }else{
            $message = 'Please login';
            return redirect()->route('login', $merchant)->with('message', $message);
        }

    }
}
