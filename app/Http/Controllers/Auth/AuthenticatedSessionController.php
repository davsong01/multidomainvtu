<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = auth()->user();
        if ($user->status != 'active') {
            auth()->logout();
            if ($user->status == 'deleted') {
                return back()->with('error', 'Sorry, this account was deleted! Please contact support.');
                // return back()->with('error', );
            } else {
                return back()->with('error', 'Sorry, this account was suspended! Please contact support.');
            }
        }
        
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', ['store_slug' => getMerchantDetails()->store_slug]));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
