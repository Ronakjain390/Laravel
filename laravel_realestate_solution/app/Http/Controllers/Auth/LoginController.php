<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator as FacadesValidator;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */


    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function userlogin()
    {
        return view('auth/userlogin');
    }


    public function adminlogin()
    {
        return view('auth/adminlogin');
    }

    public function agentlogin()
    {
        return view('auth/agentlogin');
    }

    public function customAdminLogin(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $remember = $request->remember;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            if ($request->user()->type == 1) {
                $request->session()->regenerate();
                return redirect()->route('adminIndex')->with('success', "You have been succesfully loggedin.");
            }
            return back()->with('error', "You are not authorized User.");
        }

        return back()->with('error', 'Email or UserName or Password may be wrong !!');

        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     if (auth()->user()->type == 1) {
        //         return redirect()->route('adminIndex')->with('success', "You are succesfully logged in.");
        //     } else {
        //         return redirect()->route('adminLogin')->with('error', "Email and Password is not valid.");
        //     }
        // }
        // return redirect()->route("adminLogin")->with('success', 'Email and Password is not valid');
    }


    public function customAgentLogin(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $remember = $request->remember;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            if ($request->user()->type == 2) {
                $request->session()->regenerate();
                return redirect()->route('agentHome')->with('success', "You have been succesfully loggedin.");
            }
            return back()->with('error', "You are not authorized User.");
        }

        return back()->with('error', 'Email or UserName or Password may be wrong !!');

        // $request->validate([
        //     'email' => 'required',
        //     'password' => 'required',
        // ]);

        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     if (auth()->user()->type == 2) {
        //         return redirect()->route('agentHome')->with('success', "You are succesfully logged in.");
        //     } else {
        //         return redirect()->route('agentLogin')->with('success', "Email and Password is not valid.");
        //     }
        // }

        // return redirect()->route("agentLogin")->with('success', 'Email and Password is not valid.');
    }

    public function customUserLogin(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $remember = $request->remember;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            if ($request->user()->type == 0) {
                $request->session()->regenerate();
                return redirect()->route('home')->with('success', "You have been succesfully loggedin.");
            }
            return back()->with('error', "You are not authorized User.");
        }

        return back()->with('error', 'Email or UserName or Password may be wrong !!');

        // $request->validate([
        //     'email' => 'required',
        //     'password' => 'required',
        // ]);

        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     if (auth()->user()->type == 0) {
        //         return redirect()->route('home')->with('success', "You are succesfully logged in.");
        //     } else {
        //         return redirect()->route('userLogin')->with('success', "Email and Password is not valid.");
        //     }
        // }

        // return redirect()->route("userLogin")->withSuccess('Login details are not valid');
    }
}
