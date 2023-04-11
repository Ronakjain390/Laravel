<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator as FacadesValidator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\UserVerify;
use Str;


class AgentRegisterController extends Controller
{

    public function index()
    {
        return view('auth/agentregister');
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'contact_number' => 'required | digits_between:8,12',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $data = $request->post();
        $createUser = $this->createorg($data);
        // dd($createUser->toArray());

        $token = Str::random(64);

        UserVerify::create([
            'user_id' => $createUser->id,
            'token' => $token
        ]);

        Mail::send('auth/template/account_verify', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Email Verification Mail');
        });

        return redirect()->route('agentLogin')->with('success', 'A Email verify link has been sent to your email, please confirm for login.');
    }

    public function createorg(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'contact' => $data['contact_number'],
            'type' => 2
        ]);
    }

    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';

        if (!is_null($verifyUser)) {
            $user = $verifyUser->user;

            if (!$user->email_verified_at) {
                $verifyUser->user->email_verified_at = 1;
                $verifyUser->user->save();
                $message = "Your e-mail is verified. You can now login with your credentials.";
            } else {
                $message = "Your e-mail is already verified. You can login.";
            }
        }

        return redirect()->route('agentLogin')->with('success', $message);
    }
}
