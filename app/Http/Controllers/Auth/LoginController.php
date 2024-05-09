<?php

namespace App\Http\Controllers\Auth;

use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\MerchantPrefixService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $merchantPrefix = new MerchantPrefixService($request);
        $merchant = $merchantPrefix->getMerchantCustomerInfo($request);
        $prefix = $merchant['store_slug'];
        
        if (!empty($prefix)) {
            return view('auth.login', compact('prefix'));
        } else {
            return route('/');
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // $this->ensureIsNotRateLimited();
        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'merchant_id' => $request->merchant_id))) {
            $user = auth()->user();
            
            if ($user->status != 'active') {
                if ($user->status == 'deleted') {
                    return back()->with('error', 'Sorry, this account has been deleted! Please contact support.');
                } else {
                    return back()->with('error', 'Sorry, this account has been suspended! Please contact support.');
                }

                if ($user->type == 'customer' && $user->status == 'active') {
                    if (getSettings()->login_email_notification == 'yes') {
                        $name = $user->first_name ?? $user->username;
                        $body = 'Dear ' . $name . '<br>A new sign in has occurred on your account at: ' . Carbon::now() . '.<br> If this was not you, please get in touch with us as quick as possible';
                        logEmails($user->email, 'New Login alert on ' . request()->store_name, $body);
                    }
                }
            }
            $request->session()->put('loginId', $user->id);
            return redirect()->intended(route('dashboard', $request->array));
        } else {
            return redirect()->route('login', $request->array)
            ->with('error', 'Invalid credentials.');
        }
    }

    
    public function logout(Request $request){
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();
        Auth::logout();

        return redirect()->route('welcome', $request->array); 
    }

    public function index()
    {
        return view('auth.login');
    }
    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('Signed in');
        }
        return redirect("login")->withSuccess('Login details are not valid');
    }
    public function registration()
    {
        return view('auth.register');
    }
    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data = $request->all();
        $check = $this->create($data);
        return redirect("dashboard")->withSuccess('You have signed-in');
    }
    
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }
        return redirect("login")->withSuccess('You are not allowed to access');
    }

    
    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }
}
