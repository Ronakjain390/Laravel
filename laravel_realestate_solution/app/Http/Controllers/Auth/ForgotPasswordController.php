<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    // use SendsPasswordResetEmails;

    function forgotpassword(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('auth/template/forgot_password', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset Link');
        });

        return redirect()->back()->with('success', 'Password Change link has been sent to your email.');
    }


    function verifypassword($token)
    {
        return view('auth/template/forgotpasswordverification', compact('token'));
    }

    function changepassword(Request $request, $token)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect()->route('adminLogin')->with('success', 'Your password has been changed succesfully!!! Now you can Login.');
    }


    function forgotpassword1(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('auth/template/forgot_password1', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset Link');
        });

        return redirect()->back()->with('success', 'Password Change link has been sent to your email.');
    }


    function verifypassword1($token)
    {
        return view('auth/template/forgotpasswordverification1', compact('token'));
    }

    function changepassword1(Request $request, $token)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect()->route('agentLogin')->with('success', 'Your password has been changed succesfully!!! Now you can Login.');
    }


    function forgotpassword2(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('auth/template/forgot_password2', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset Link');
        });

        return redirect()->back()->with('success', 'Password Change link has been sent to your email.');
    }


    function verifypassword2($token)
    {
        return view('auth/template/forgot_pass_user', compact('token'));
    }

    function changepassword2(Request $request, $token)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect()->route('userLogin')->with('success', 'Your password has been changed succesfully!!! Now you can Login.');
    }
}
