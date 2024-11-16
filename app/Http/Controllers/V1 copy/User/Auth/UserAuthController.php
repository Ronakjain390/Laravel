<?php

namespace App\Http\Controllers\Web\User\Auth;

use passport;
use App\Models\Otp;
use App\Models\User;
use App\Models\Feature;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class UserAuthController extends Controller
{

    public function sendOTP(Request $request)
    {
        // Validate the input phone number
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|numeric',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if the user already exists
        $userExists = User::where('phone', $request->phone_number)->exists();

        if ($userExists) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists',
            ]);
        }

        // Generate OTP
        $otp = mt_rand(1000, 9999);

        // Check if the phone number exists in the OTP records
        $existingOTP = Otp::where('phone_number', $request->phone_number)->first();

        if ($existingOTP) {
            // Update the existing OTP record with the new OTP and expiry time
            $existingOTP->otp = Hash::make($otp);
            $existingOTP->expires_at = now()->addMinutes(5);
            $existingOTP->save();
        } else {
            // Create a new OTP record if the phone number does not exist
            $otpModel = Otp::create([
                'phone_number' => $request->phone_number,
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5)
            ]);
        }

        // SMS API - Textlocal
        $apiKey = 'etVEU+y8WgE-lNrXaKDbdAjborAW26zsIxf9brZrLO';
        $sender = 'TPARCH';
        $message = "{$otp} is your OTP for new registration at www.theparchi.com. Enter this OTP to verify your mobile number.";
        $numbers = [$request->phone_number];

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // Send OTP via Textlocal SMS API
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresponse = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP status code
        curl_close($ch);

        if ($status === 200) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    public function validateOTP(Request $request)
    {
        // Validate the input phone number and OTP
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|numeric',
            'otp' => 'required|numeric',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or OTP',
                'errors' => $validator->errors(),
            ], 200);
        }

        // Check if the user already exists
        User::where('phone', $request->phone_number)->delete();

        // Retrieve the OTP record from the database
        $storedOTP = Otp::where('phone_number', $request->phone_number)->first();

        // Verify the provided OTP against the stored OTP
        if ($storedOTP && Hash::check($request->otp, $storedOTP->otp)) {
            // Create a new user with the phone number
            $user = new User();
            $user->phone = $request->phone_number;
            $user->save();
            $storedOTP->delete();

            // Generate a personal access token for the user
            $token = $user->createToken('user', ['user'])->accessToken;

            // Clear the session data
            Session::forget('otp');
            Session::forget('phone_number');

            // Return success response with the user and the token
            return response()->json([
                'success' => true,
                'message' => 'OTP validation successful',
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            // OTP validation failed
            return response()->json([
                'success' => false,
                'message' => 'OTP validation failed',
            ]);
        }
    }

    public function completeRegistration(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Generate a 10-digit alphanumeric special ID
        $specialId = Str::random(10);

        // Get the authenticated user
        $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

        // Update the user details
        $user->name = $request->name;
        $user->email = $request->email;
        // Assign the special ID to the user's special_id attribute
        $user->special_id = $specialId;
        $user->password = Hash::make($request->password);
        $user->save();


        // Return success response with the updated user and the token
        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'user' => $user,
        ]);
    }

    public function sendOTPForLogin(Request $request)
    {
        // Validate the input email or phone number
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or phone number',
                'errors' => $validator->errors(),
            ], 200);
        }

        // Check if the email or phone number exists in the user records
        $user = User::where('email', $request->email_or_phone)
            ->orWhere('phone', $request->email_or_phone)
            ->first();

        // If the user does not exist, return error response
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist',
            ], 200);
        }

        // Generate OTP
        $otp = mt_rand(1000, 9999);

        // Store OTP in the database
        // Check if the phone number exists in the OTP records
        $existingOTP = Otp::where('phone_number', $request->email_or_phone)->first();

        if ($existingOTP) {
            // Update the existing OTP record with the new OTP and expiry time
            $existingOTP->otp = Hash::make($otp);
            $existingOTP->expires_at = now()->addMinutes(5);
            $existingOTP->save();
        } else {
            // Create a new OTP record if the phone number does not exist
            $otpModel = Otp::create([
                'phone_number' => $request->email_or_phone,
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5)
            ]);
        }

        // Send the OTP to the user's email
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Throwable $exception) {
            // Log the exception
            Log::channel('emaillog')->error($exception->getMessage());

            // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
        }

        // Send the OTP to the user's phone number
        $apiKey = urlencode('etVEU+y8WgE-lNrXaKDbdAjborAW26zsIxf9brZrLO');
        $numbers = [$user->phone];
        $sender = urlencode('TPARCH');
        $message = "{$otp} is your OTP for login to www.theparchi.com. This OTP will expire within 2 mins.";

        try {


            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

            // Send OTP via Textlocal SMS API
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $smsresponse = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP status code
            curl_close($ch);

            if ($status === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                ]);
            } else {
                // Log the error response
                Log::channel('otplog')->error($status);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP',
                ], 500);
            }
        } catch (\Throwable $exception) {
            // Log the exception
            Log::channel('otplog')->error($exception->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
            ], 500);
        }
    }

    public function validateOTPForLogin(Request $request)
    {
        // Validate the input OTP
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|numeric',
            'otp' => 'required|numeric',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Retrieve the OTP record from the database
        $storedOTP = Otp::where('phone_number', $request->phone_number)->first();

        // Verify the provided OTP against the stored OTP
        if ($storedOTP && Hash::check($request->otp, $storedOTP->otp)) {
            // Generate authentication token or session for the user
            $user = User::where('phone', $storedOTP->phone_number)->first();
            Auth::loginUsingId($user->id);
            $token = $user->createToken('user', ['user'])->accessToken;
            $storedOTP->delete();

            return response()->json([
                'success' => true,
                'message' => 'OTP validation successful',
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP',
        ], 401);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Generate a 10-digit alphanumeric special ID
        $specialId = Str::random(10);

        // Assign the special ID to the user's special_id attribute
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'special_id' => $specialId,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'user registered successfully', 'user' => $user]);
    }

    public function updateSpecialId()
    {
        // Get all users with null special_id
        $usersWithoutSpecialId = User::whereNull('special_id')->get();

        foreach ($usersWithoutSpecialId as $user) {
            // Generate a unique special_id
            do {
                $specialId = Str::random(10);
                $existingUser = User::where('special_id', $specialId)->first();
            } while ($existingUser);

            // Update the special_id for the user
            $user->update(['special_id' => $specialId]);
        }

        return true;
    }

    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Determine if the input is an email or phone number
        $field = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Attempt to authenticate the user
        $credentials = [
            $field => $request->email_or_phone,
            'password' => $request->password,
        ];

        if (!Auth::guard('user')->attempt($credentials)) {
            // Authentication failed
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Authentication successful, generate token or session
        $user = Auth::guard('user')->user();
        $token = $user->createToken('user', ['user'])->accessToken;

        // Return success response with user details and token
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function user_details(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

            // Retrieve the active plan with panels and features
            $userWithActivePlan = User::with('plans.panel')->first();

            $userId = $userWithActivePlan->id;
            foreach ($userWithActivePlan->plans as $key => $plan) {
                $plan->panel->features = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                    ->where('features.panel_id', $plan->panel->id)
                    ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
                        $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
                            ->where('plan_orders.user_id', $userId)
                            ->where('plan_orders.status', 'active');
                    })
                    ->leftJoin('feature_topup_usage_records', function ($join) use ($userId) {
                        $join->on('feature_topup_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as topup_orders', 'feature_topup_usage_records.order_id', '=', 'topup_orders.id')
                            ->where('topup_orders.user_id', $userId)
                            ->where('topup_orders.status', 'active');
                    })
                    ->groupBy('features.id', 'features.feature_type_id', 'features.feature_name', 'features.template_id', 'features.status', 'features.panel_id')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_count) AS total_usage_count')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit) AS total_usage_limit')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit - plan_feature_usage_records.usage_count) AS total_available_usage')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_count) AS total_usage_count_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit) AS total_usage_limit_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit - feature_topup_usage_records.usage_count) AS total_available_usage_topup')
                    ->with('template')
                    ->get();
            }

            // Return the user data
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'user' => $userWithActivePlan,
            ]);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            // 'phone' => 'required',
        ]);

        $response = Password::sendResetLink($request->only(['email']));

        if ($response === Password::RESET_LINK_SENT) {
            // Password reset link sent successfully
            return response()->json([
                'success' => true,
                'message' => 'Reset password OTP has been sent successfully',
            ]);
        } else {
            // Failed to send reset link
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset password OTP',
            ], 400);
        }
    }

    public function validateOtpAndResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $response = Password::reset($request->only(['email', 'password', 'password_confirmation', 'token']), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });

        if ($response === Password::PASSWORD_RESET) {
            // Password reset successful
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully',
            ]);
        } else {
            // Failed to reset password
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
            ], 400);
        }
    }
    public function user_logout(Request $request): Response
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
            $accessToken = Auth::guard(Auth::getDefaultDriver())->user()->token();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
            $accessToken->revoke();
            Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->delete();

            return Response(['data' => 'Unauthorized', 'message' => 'User logout successfully.'], 200);
        }
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }
}
