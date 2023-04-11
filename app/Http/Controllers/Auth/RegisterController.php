<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator as FacadesValidator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Str;


class RegisterController extends Controller
{
    function index()
    {
        return view('auth/register');
    }

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    // use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest');
    // }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    // protected function validator(array $data)
    // {
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'type' => ['nullable']
    //     ]);
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //         'type' => $data['type'],
    //         'image' => $data['image'],
    //         'contact' => $data['contact']
    //     ]);
    //     Mail::to('admin@mydomain.com')->send(new UserRegistered($user));
    //     return User;
    // }

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

        Mail::send('auth/template/account_verify_user', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Email Verification Mail');
        });

        return redirect()->route('userLogin')->with('success', 'A Email verify link has been sent to your email.');
    }

    public function createorg(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact' => $data['contact_number'],
            'password' => Hash::make($data['password']),
            'type' => 0
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

        return redirect()->route('userLogin')->with('success', $message);
    }
}
