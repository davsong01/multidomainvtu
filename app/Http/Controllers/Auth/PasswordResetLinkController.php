<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(Request $request): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'email' => 'required|email|exists:users,email,merchant_id,'.request()->merchant_id,
        ]);
        
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateorInsert(['email' => $request->email],[
            'email' => $request->email,
            'token' => $token,
            'merchant_id' => request()->merchant_id,
            'created_at' => Carbon::now()
        ]);
        
        $reset_link = route('password.reset', array_merge(request()->array, ['token' => $token]));
      
        $settings = request()->merchant_settings;
        
        $body = '<p style="line-height: 2.0;">Hello! <br>You are receiving this email because we received a password reset request for your account on  ' . request()->store_name . '. Please click the button below to reset your password. <br><br> <a class="btn btn-info" target="_blank" href="' . $reset_link . '" style="position: margin:5px; relative;margin-bottom:50px;color: ' . $settings['menu_text_color'] . ';font-weight: 500;padding: 8px 20px;font-size: 13px;line-height: 24px;letter-spacing: 0.01em;border-radius: 4px;border: 1px solid;transition: all .4s ease;background:'. $settings['menu_background_color'] .';text-align: center;white-space: nowrap;vertical-align: middle;text-decoration:none">RESET PASSWORD</a><br/><br><strong>This password reset link will expire in 60 minutes.</strong><hr/>Warm Regards. (' . request()->store_name . ')<br/></p>';

        sendAuthEmails($request->email, 'Reset Password', $body);

        return back()->with('message', 'We have e-mailed your password reset link!');
    }

}
