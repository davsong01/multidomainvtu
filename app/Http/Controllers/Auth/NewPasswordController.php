<?php

namespace App\Http\Controllers\Auth;

// use Illuminate\View\View;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'email' => 'required|email|exists:users,email,merchant_id,' . request()->merchant_id,
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')->where(['email' => $request->email,'token' => $request->token])->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $password = Hash::make($request->password);
        User::where('email', $request->email)->update(['password' => $password]);
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        $body = 'Hello <br>Your password has been changed on your ' .request()->store_name. ' account at: ' . Carbon::now() . '.<br> If this was not you, please get in touch with us as quick as possible';

        logEmails($request->email, 'Password Changed on '.request()->store_name, $body);

        return redirect()->route('login', $request->array)->with('message', 'Your password has been changed!');
    }
}
