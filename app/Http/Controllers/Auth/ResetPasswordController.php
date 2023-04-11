<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator as FacadesValidator;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller

{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    // public function changePassword()
    // {
    //     return view('auth/passwords/change');
    // }

    // public function passwordChange(Request $request)
    // {
    //     $request->validate([
    //         'old_password' => ['required'],
    //         'new_password' => ['required'],
    //         'confirm_password' => ['same:new_password'],
    //     ]);
    //     if (!Hash::check($request->old_password, auth()->user()->password)) {
    //         return back()->with("error", "Old Password Doesn't match!");
    //         }
    //     User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
    //     return redirect()->route('profile.index')->withSuccess('Login details are not valid');


    function admin_chanegpassword(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:8',
            'confirmpasswrod' => 'required|same:newpassword',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        if (!Hash::check($request->oldpassword, Auth::user()->password)) {
            return redirect()->back()->with('error', 'Old Password doesnot match.');
        }
        $passwords = $request->confirmpasswrod;
        User::where('id', Auth::user()->id)->update([
            'password' => Hash::make($request->confirmpassword)
        ]);

        Mail::send('auth/template/passwordchanged', compact('passwords'), function ($message) {
            $message->to(Auth::user()->email);
            $message->subject('Password changed Mail');
        });

        return redirect()->back()->with('success', 'Password Changed Succesfully...');
    }
}
