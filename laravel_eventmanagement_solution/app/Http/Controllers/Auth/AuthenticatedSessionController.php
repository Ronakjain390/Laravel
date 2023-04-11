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
    public function create()
    {
        if(Auth::check()) {
            if(Auth::user()->type == 0){
                return redirect()->route('superAdmin')->with('success', 'Login as a Admin');
            }
            if(Auth::user()->type == 2) {
                return redirect()->route('orgnizerdashboard')->with('success', 'Login as a Event Orgnizer');
            } 
            if(Auth::user()->type == 1){
                return redirect()->route('home')->with('success', 'Login as a User');
            }
        }
        else {
            return view('auth.login');
        }
    }

    public function superAdmin(): View
    {
        if(Auth::user()) {
            if(Auth::user()->type == 0){
                return redirect()->route('superAdmin')->with('success', 'Login as a Admin');
            }
            if(Auth::user()->type == 2) {
                return redirect()->route('orgnizerdashboard')->with('success', 'Login as a Event Orgnizer');
            } 
            if(Auth::user()->type == 1){
                return redirect()->route('home')->with('success', 'Login as a User');
            }
        }
        else {
            return view('auth/SuperAdmin.login');
        }
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password', 'type');

        if (Auth::attempt($credentials)) {
            if (auth()->user()->type == 1) {
                // print_r(' user'. auth()->user()->type); die;
                return redirect()->route('home')->with('success', "You are succesfully logged in.");
            } elseif (auth()->user()->type == 0) {
                return redirect()->route('superAdmin')->with('success', "Email and Password is not valid.");
            } else {
                print_r(' user1'. auth()->user()->type); die;
                return redirect()->back();
            }
        }
        return redirect()->back();
    }




    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('error', 'You have been successfully Logout');
    }
}
