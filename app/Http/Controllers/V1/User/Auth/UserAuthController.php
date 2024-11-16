<?php

namespace App\Http\Controllers\V1\User\Auth;

use passport;
use App\Models\Otp;
use App\Models\User;
use App\Models\Units;
use Carbon\Carbon;
use App\Models\TeamUser;
use App\Models\UserDetails;
use App\Models\CompanyLogo;
use App\Models\UserQuery;
use App\Models\Feature;
use App\Models\PhoneVerifications;
use App\Models\EmailVerifications;
use App\Mail\OtpMail;
use App\Mail\AcceptRejectOtpMail;
use App\Mail\MailVerification;
use App\Mail\UserRegistrationMail;
use Illuminate\Support\Str;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\V1\CompanyLogo\CompanyLogoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;


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
        // dd($status);
        curl_close($ch);

        if ($status === 200) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to Email',
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
            $token = $user->createToken(Auth::getDefaultDriver(), [Auth::getDefaultDriver()])->accessToken;

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

        // Create Default Series Number
        $panelSeriesNumberController = new PanelSeriesNumberController;

        // $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
        // $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->subDay();
        // $financialYear = (now()->year()) . '-' . (now()->year + 1);
        $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
        $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);
        $financialYear = ($currentFinancialYearStart->year) . '-' . ($nextFinancialYearEnd->year);

        // dd($currentFinancialYearStart, $nextFinancialYearEnd);
        $combinations = [
            ['prefix' => 'CH-', 'panel_id' => 1, 'section_id' => 1],
            ['prefix' => 'CH-', 'panel_id' => 2, 'section_id' => 1],
            ['prefix' => '', 'panel_id' => 3, 'section_id' => 2],
            ['prefix' => 'PO-', 'panel_id' => 4, 'section_id' => 2],
        ];

        $panelSeriesNumbers = [];

        foreach ($combinations as $combination) {
            $prefix = $combination['prefix'];
            $panel_id = $combination['panel_id'];
            $section_id = $combination['section_id'];

            // Generate the series number
            $series_number = $prefix . (strlen($user->company_name) >= 4
                ? substr($user->company_name, 0, 4)
                : substr($user->name, 0, 4)) . '-' . $financialYear;

            // Create panel series data
            $panelSeriesData = [
                'series_number' => $series_number,
                'panel_id' => $panel_id,
                'section_id' => $section_id,
                'status' => 'active',
                'valid_from' => $currentFinancialYearStart,
                'valid_till' => $nextFinancialYearEnd,
                'default' => '1',
            ];

            // Create a new Request instance
            $panelSeriesRequest = new Request($panelSeriesData);

            // Call the store method to create the panel series number
            $panelSeriesResponse = $panelSeriesNumberController->store($panelSeriesRequest);
        }


        // Create Order
        $ordeController = new OrdersController;
        $planCombinations = [
            ['plan_ids' => 3,],
            ['plan_ids' => 11,],
            ['plan_ids' => 19,],
            ['plan_ids' => 10,],
        ];

        foreach ($planCombinations as $planCombination) {
            $orderData = [
                'user_id' => $user->id,
                'plan_ids' => $planCombination,
            ];

            $orderRequest = new Request($orderData);
            $orderResponse = $ordeController->store($orderRequest);
        }
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
        // dd($user);
        // If the user does not exist, return error response
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist',
            ], 500);
        }

        // Generate OTP
        $otp = mt_rand(1000, 9999);

        // Store OTP in the database
        // Check if the phone number exists in the OTP records
        $existingOTP = Otp::where('phone_number', $user->phone)->first();

        if ($existingOTP) {
            // Update the existing OTP record with the new OTP and expiry time
            $existingOTP->otp = Hash::make($otp);
            $existingOTP->expires_at = now()->addMinutes(5);
            $existingOTP->save();
        } else {
            // Create a new OTP record if the phone number does not exist
            $otpModel = Otp::create([
                'phone_number' => $user->phone,
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5)
            ]);
        }

        // // Send the OTP to the user's email
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
        } catch (\Throwable $exception) {
            // Log the exception
            Log::channel('emaillog')->error($exception->getMessage());

            //     // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
        }

        // Send the OTP to the user's phone number
        $apiKey = urlencode('etVEU+y8WgE-lNrXaKDbdAjborAW26zsIxf9brZrLO');
        $numbers = [$user->phone];

        // dd($numbers);
        $sender = urlencode('TPARCH');
        $message = "{$otp} is your OTP for login to www.theparchi.com. This OTP will expire within 2 mins.";

        try {
            // Prepare data for POST request
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

            // dd($status);
            if ($status === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to Email',
                    'phone' => $user->phone
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

    public function sendOTPForAcceptReject(Request $request)
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
        // dd($user);
        // If the user does not exist, return error response
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist',
            ], 500);
        }

        // Generate OTP
        $otp = mt_rand(1000, 9999);

        // Store OTP in the database
        // Check if the phone number exists in the OTP records
        $existingOTP = Otp::where('phone_number', $user->phone)->first();

        if ($existingOTP) {
            // Update the existing OTP record with the new OTP and expiry time
            $existingOTP->otp = Hash::make($otp);
            $existingOTP->expires_at = now()->addMinutes(5);
            $existingOTP->save();
        } else {
            // Create a new OTP record if the phone number does not exist
            $otpModel = Otp::create([
                'phone_number' => $user->phone,
                'otp' => Hash::make($otp),
                'expires_at' => now()->addMinutes(5)
            ]);
        }

        // // Send the OTP to the user's email
        try {
            Mail::to($user->email)->send(new AcceptRejectOtpMail($otp));
        } catch (\Throwable $exception) {
            // Log the exception
            Log::channel('emaillog')->error($exception->getMessage());

            //     // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
        }

        // Send the OTP to the user's phone number
        $apiKey = urlencode('etVEU+y8WgE-lNrXaKDbdAjborAW26zsIxf9brZrLO');
        $numbers = [$user->phone];

        // dd($numbers);
        $sender = urlencode('TPARCH');
        $message = "{$otp} is your OTP for login to www.theparchi.com. This OTP will expire within 2 mins.";

        try {
            // Prepare data for POST request
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

            // dd($status);
            if ($status === 200) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to Email',
                    'phone' => $user->phone
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
                'status_code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Retrieve the OTP record from the database
        $storedOTP = Otp::where('phone_number', $request->phone_number)->first();

        // Verify the provided OTP against the stored OTP
        if ($storedOTP && Hash::check($request->otp, $storedOTP->otp)) {
            // Generate authentication token or session for the user
            $user = User::where('phone', $storedOTP->phone_number)->first();
            Auth::login($user);

            // $tokenForReset = $user->createToken(Auth::getDefaultDriver(), [Auth::getDefaultDriver()])->accessToken;
            $storedOTP->delete();

            return response()->json([
                'success' => true,
                'message' => 'OTP validation successful',
                // 'token' => $tokenForReset,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP',
            'status_code' => 400,
        ], 401);
    }

    public function validateOTPForResetPassword(Request $request)
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
            Auth::login($user);

            $tokenForReset = $user->createToken(Auth::getDefaultDriver(), [Auth::getDefaultDriver()])->accessToken;
            $storedOTP->delete();

            return response()->json([
                'success' => true,
                'message' => 'OTP validation successful',
                'tokenForReset' => $tokenForReset,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP',
            'status_code' => 400,
        ], 401);
    }

    public function register(Request $request)
    {

        // try {
        //     DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits:10|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);

        // Determine if the input is an email or phone number
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $field => $request->email,
            'password' => $request->password,
        ];

        if (Auth::guard(Auth::getDefaultDriver())->attempt($credentials)) {
            // Authentication successful, generate token or session
            $user = Auth::guard(Auth::getDefaultDriver())->user();
            $token = $user->createToken(Auth::getDefaultDriver(), [Auth::getDefaultDriver()])->accessToken;
            // Return success response with user details and token
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        }


        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Generate a 10-digit alphanumeric special ID
        $specialId = Str::random(10);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'special_id' => $specialId,
                'password' => Hash::make($request->password),
                'permissions' => json_encode([
                    'sender' => [
                        'whatsapp' => [
                            'sent_challan' => false,
                            'add_comment'  => false,
                            'sfp' => false,
                            'received_return_challan' => false,
                            'additional_number' => false,
                        ],
                        'email' => [
                            'sent_challan' => false,
                            'received_return_challan' => false,
                            'add_comment'  => false,
                            'sfp' => false,
                        ],
                    ],
                    'receiver' => [
                        'whatsapp' => [
                            'received_challan' => false,
                            'sent_return_challan' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                        'email' => [
                            'received_challan' => false,
                            'sent_return_challan' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                    ],
                    'seller' => [
                        'whatsapp' => [
                            'sent_invoice' => false,
                            'received_po' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                        'email' => [
                            'sent_invoice' => false,
                            'received_po' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                    ],
                    'buyer' => [
                        'whatsapp' => [
                            'received_invoice' => false,
                            'sent_po' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                        'email' => [
                            'received_invoice' => false,
                            'sent_po' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                    ],
                    'receipt_note' => [
                        'whatsapp' => [
                            'sent_receipt_note' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                        'email' => [
                            'sent_receipt_note' => false,
                            'sfp' => false,
                            'add_comment'  => false,
                        ],
                    ]
                ]),
            ]);
        // dd($user);
        if (Auth::guard(Auth::getDefaultDriver())->attempt($credentials)) {
            // Authentication successful, generate token or session
            $user = Auth::guard(Auth::getDefaultDriver())->user();
            $token = $user->createToken(Auth::getDefaultDriver(), [Auth::getDefaultDriver()])->accessToken;
            // Return success response with user details and token
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Login successful',
            //     'user' => $user,
            //     'token' => $token,
            // ]);
        }
        // Create Default Series Number
        $panelSeriesNumberController = new PanelSeriesNumberController;


        $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
        $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);

        // If the current date is before April 1st, consider it as part of the previous financial year
        if (now() < $currentFinancialYearStart) {
            $currentFinancialYearStart = $currentFinancialYearStart->subYear();
            $nextFinancialYearEnd = $nextFinancialYearEnd->subYear();
        }

        $financialYear = ($currentFinancialYearStart->year % 100) . '-' . ($nextFinancialYearEnd->year % 100);



        // dd($currentFinancialYearStart, $nextFinancialYearEnd, $financialYear);
        // dd($currentFinancialYearStart, $nextFinancialYearEnd);
        $combinations = [
            ['prefix' => 'CH-', 'panel_id' => 1, 'section_id' => 1],
            ['prefix' => 'CH-', 'panel_id' => 2, 'section_id' => 1],
            ['prefix' => '', 'panel_id' => 3, 'section_id' => 2],
            ['prefix' => 'PO-', 'panel_id' => 4, 'section_id' => 2],
            ['prefix' => 'GRN-', 'panel_id' => 5, 'section_id' => 1],
        ];

        $panelSeriesNumbers = [];

        foreach ($combinations as $combination) {
            $prefix = $combination['prefix'];
            $panel_id = $combination['panel_id'];
            $section_id = $combination['section_id'];

            // Generate the series number
            $series_number = strtoupper($prefix . (strlen($user->company_name) >= 4
                ? substr($user->company_name, 0, 4)
                : substr($user->name, 0, 4)) . '-' . $financialYear);

            // Create panel series data
            $panelSeriesData = [
                'series_number' => $series_number,
                'panel_id' => $panel_id,
                'section_id' => $section_id,
                'status' => 'active',
                'valid_from' => $currentFinancialYearStart,
                'valid_till' => $nextFinancialYearEnd,
                'default' => '1',
            ];

            // Create a new Request instance
            $panelSeriesRequest = new Request($panelSeriesData);

            // Call the store method to create the panel series number
            $panelSeriesResponse = $panelSeriesNumberController->store($panelSeriesRequest);
        }

        // Create Order
        $ordeController = new OrdersController;
        $planCombinations = [
            ['plan_ids' => 50,],
            ['plan_ids' => 11,],
            ['plan_ids' => 19,],
            ['plan_ids' => 10,],
            ['plan_ids' => 58,],
        ];

        foreach ($planCombinations as $planCombination) {
            $orderData = [
                'user_id' => $user->id,
                'plan_ids' => $planCombination,
            ];

            $orderRequest = new Request($orderData);
            $orderResponse = $ordeController->store($orderRequest);
        }

        $datas = [
            [
                'default' => '1',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Article',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Hsn',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Details',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_0',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '1',
                'section_id' => '1',
                'feature_id' => '1',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ],
            [
                'default' => '1',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Article',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Hsn',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Details',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_0',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '3',
                'section_id' => '2',
                'feature_id' => '12',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ],
            [
                'default' => '1',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Article',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Hsn',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '1',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => 'Details',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_0',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_1',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_2',
                'status' => 'active',
            ], [
                'default' => '0',
                'panel_id' => '4',
                'section_id' => '2',
                'feature_id' => '22',
                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                'panel_column_display_name' => '',
                'panel_column_default_name' => 'column_3',
                'status' => 'active',
            ],
        ];
        foreach ($datas as $data) {


            $request  = new Request;
            // $request->replace([]);
            $request->merge($data);
            // dd($request);
            $newChallanDesign = new PanelColumnsController;
            $response = $newChallanDesign->store($request);
        }
        $panelTypes = ['sender', 'receiver', 'seller', 'buyer', 'receipt_note'];
        foreach ($panelTypes as $panelType) {
        $units = [
            ['unit' => 'bags', 'short_name' => 'bag', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'dozens', 'short_name' => 'dzn', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'grammes', 'short_name' => 'gm', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'kilograms', 'short_name' => 'kg', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'litre', 'short_name' => 'ltr', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'meters', 'short_name' => 'mtr', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'pieces', 'short_name' => 'pcs', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'cartons', 'short_name' => 'ctn', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
            ['unit' => 'Militre', 'short_name' => 'ml', 'status' => 'active', 'is_default' => '1', 'user_id' => $user->id, 'panel_type' => $panelType],
        ];


            // Iterate over units
            foreach ($units as $unitData) {
                // Create a new Unit instance
                $unit = new Units($unitData);

                // Set the user_id for the unit
                $unit->user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

                // Save the unit to the database
                $unit->save();
            }
        }

            $companyLogo = new CompanyLogo();
            $companyLogo->user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $companyLogo->challan_stamp = 1;
            $companyLogo->invoice_stamp = 1;
            $companyLogo->po_stamp = 1;
            $companyLogo->return_challan_stamp = 1;
            $companyLogo->receipt_note_stamp = 1;
            $companyLogo->save();


                // $pdfEmailService = new PDFEmailService();
                // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id; // Replace with the actual recipient email address
                // // dd($recipientEmail);
                // $pdfEmailService->userRegistrationEmail($userId);
                 // // Send the OTP to the user's email
                 $userData = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user();
        try {
            Mail::to("contact@theparchi.com")->send(new UserRegistrationMail($userData));
            Mail::to($userData->email)->send(new NewUserRegistrationMail($userData));
        } catch (\Throwable $exception) {
            // Log the exception
            // dd('mot');
            Log::channel('emaillog')->error($exception->getMessage());

            //     // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
        }

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully, Pls Login',
            'user' => $user,
        ], 200);
        // } catch (\Exception $e) {
        //     // Something went wrong, rollback the transaction
        //     DB::rollback();

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An error occurred while registering the user',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
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

        // Check if the user exists
        $user = User::where($field, $request->email_or_phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User does not exist',
            ], 404);
        }

        // Attempt to authenticate the user
        $credentials = [
            $field => $request->email_or_phone,
            'password' => $request->password,
        ];

        if (!Auth::guard('user')->attempt($credentials)) {
            // Authentication failed, password is incorrect
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password',
            ], 401);
        }

        // Authentication successful, generate token or session
        $user = Auth::guard('user')->user();

        // Update the last login information
        $user->update([
            'last_login_at' => now(),
        ]);

        // Create and return the token
        $token = $user->createToken('user', ['user'])->accessToken;

        // Return success response with user details and token
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }


    public function apiLogin(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required',
        ]);
        // dd($request);
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
        // dd(Auth::guard(Auth::getDefaultDriver())->attempt($credentials), $credentials);

        if (!Auth::guard('user-api')->attempt($credentials)) {
            // Authentication failed
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Authentication successful, generate token or session
        $user = Auth::guard('user-api')->user();
        $token = $user->createToken('user-api', ['user-api'])->accessToken;

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
        try {
            // Check if the user has a valid token
            $currentUser = Auth::guard(Auth::getDefaultDriver())->user();
            if ($currentUser->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $userId = Auth::getDefaultDriver() == 'team-user' ? $currentUser->team_owner_user_id : $currentUser->id;

                // Retrieve the user with active plan and their features
                $userWithActivePlan = User::where('id', $userId)->with(['plans.panel', 'plans.panel.features' => function($query) use ($userId) {
                    $query->select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                        ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
                            $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
                                ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
                                ->where('plan_orders.user_id', $userId)
                                ->whereNotNull('plan_feature_usage_records.usage_count')
                                ->whereNotNull('plan_feature_usage_records.usage_limit')
                                ->where('plan_orders.status', 'active');
                        })
                        ->leftJoin('feature_topup_usage_records', function ($join) use ($userId) {
                            $join->on('feature_topup_usage_records.feature_id', '=', 'features.id')
                                ->join('orders as topup_orders', 'feature_topup_usage_records.order_id', '=', 'topup_orders.id')
                                ->where('topup_orders.user_id', $userId)
                                ->where('topup_orders.status', 'active');
                        })
                        ->groupBy('features.id', 'features.feature_type_id', 'features.feature_name', 'features.template_id', 'features.status', 'features.panel_id')
                        ->selectRaw('features.*,
                            SUM(plan_feature_usage_records.usage_count) AS total_usage_count,
                            SUM(plan_feature_usage_records.usage_limit) AS total_usage_limit,
                            SUM(plan_feature_usage_records.usage_limit - plan_feature_usage_records.usage_count) AS total_available_usage,
                            SUM(feature_topup_usage_records.usage_count) AS total_usage_count_topup,
                            SUM(feature_topup_usage_records.usage_limit) AS total_usage_limit_topup,
                            SUM(feature_topup_usage_records.usage_limit - feature_topup_usage_records.usage_count) AS total_available_usage_topup');
                }])->first();

                if (!$userWithActivePlan) {
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'User with active plan not found.',
                    ], 404);
                }

                $userWithActivePlan->team_user = null;

                if (Auth::getDefaultDriver() == "team-user") {
                    $teamUser = TeamUser::where('id', $currentUser->id)->with('permissions')->first();
                    $teamUser->permissions->permission = json_decode($teamUser->permissions->permission);
                    $userWithActivePlan->team_user = $teamUser;
                }

                // Return the user data
                return response()->json([
                    'success' => true,
                    'message' => 'User data retrieved successfully',
                    'user' => $userWithActivePlan,
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in user_details method: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::id(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving user details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function filterNullValues($data)
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->filterNullValues($value);
            }
            return $value;
        }, array_filter($data, function ($value) {
            return !is_null($value);
        }));
    }



    // User Expired Plan
    public function userExpiredPlan(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            // Retrieve the active plan with panels and features
            $userWithExpiredPlan = User::where('id', $user->id)->with('plansExpired.panel')->first();

            $userId = $userWithExpiredPlan->id;
            foreach ($userWithExpiredPlan->plans as $key => $plan) {

                // $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                //     ->where('features.panel_id', $plan->panel->id)
                $plan->panel->feature = Feature::select('features.id', 'features.feature_type_id', 'features.panel_id', 'features.feature_name', 'features.template_id', 'features.status')
                    ->where('features.panel_id', $plan->panel->id)

                    ->leftJoin('plan_feature_usage_records', function ($join) use ($userId) {
                        $join->on('plan_feature_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as plan_orders', 'plan_feature_usage_records.order_id', '=', 'plan_orders.id')
                            ->where('plan_orders.user_id', $userId)
                            ->where('plan_orders.id', $userId)
                            ->where('plan_feature_usage_records.usage_count', '!=', null)
                            ->where('plan_feature_usage_records.usage_limit', '!=', null)
                            ->distinct('plan_feature_usage_records.feature_id')
                            ->where('plan_orders.status', 'active')->limit(1);
                    })
                    ->leftJoin('feature_topup_usage_records', function ($join) use ($userId) {
                        $join->on('feature_topup_usage_records.feature_id', '=', 'features.id')
                            ->join('orders as topup_orders', 'feature_topup_usage_records.order_id', '=', 'topup_orders.id')
                            ->where('topup_orders.user_id', $userId)
                            ->where('topup_orders.status', 'active');
                    })
                    ->groupBy('features.id', 'features.feature_type_id', 'features.feature_name', 'features.template_id', 'features.status', 'features.panel_id', 'plan_feature_usage_records.usage_count', 'plan_feature_usage_records.usage_limit')
                    // ->select('plan_feature_usage_records.usage_count','plan_feature_usage_records.usage_limit')

                    ->selectRaw('SUM(plan_feature_usage_records.usage_count) AS total_usage_count')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit) AS total_usage_limit')
                    ->selectRaw('SUM(plan_feature_usage_records.usage_limit - plan_feature_usage_records.usage_count) AS total_available_usage')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_count) AS total_usage_count_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit) AS total_usage_limit_topup')
                    ->selectRaw('SUM(feature_topup_usage_records.usage_limit - feature_topup_usage_records.usage_count) AS total_available_usage_topup')
                    ->with('template', 'sentChallans')
                    ->get();
            }
            $userWithExpiredPlan->team_user = null;
            // dd($userWithExpiredPlan);
            if (Auth::getDefaultDriver() == "team-user") {
                $teamUser = TeamUser::where('id',Auth::guard(Auth::getDefaultDriver())->user()->id)->with('permissions')->first();
                $teamUser->permissions->permission = json_decode($teamUser->permissions->permission);
                $userWithExpiredPlan->team_user = $teamUser;
            }

            // Return the user data
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'user' => $userWithExpiredPlan,
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    public function userActivePlan(Request $request)
    {
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
            $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            // Retrieve the active plan with panels and features
            $activePlans = User::where('id', $user->id)->with('plans.panel','plans.plan','plans.featureUsageRecords.feature','plans.featureTopupUsageRecords.feature')->first();
            $userId = $activePlans->id;
            $userWithActivePlan = $activePlans->plans->groupBy('panel.panel_name');
            // dd($userWithActivePlan);


            $userWithActivePlan->team_user = null;
            if (Auth::getDefaultDriver() == "team-user") {
                $teamUser = TeamUser::find(Auth::guard(Auth::getDefaultDriver())->user()->id)->with('permissions')->first();
                $teamUser->permissions->permission = json_decode($teamUser->permissions->permission);
                $userWithActivePlan->team_user = $teamUser;
            }
            // dd($userWithActivePlan);

            // Return the user data
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'user' => $userWithActivePlan,
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    public function features(Request $request, $panelId)
    {
        $features = Feature::where('panel_id', $panelId)->with('template')->get();

        $features = $features->map(function ($feature) {
            return [
                'template_name' => $feature->template->template_name,
                'template_page_name' => $feature->template->template_page_name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Features retrieved successfully',
            'features' => $features,
        ]);
    }

    public function teamUsersData(Request $request)
    {
        $teamUsers =  TeamUser::where('team_owner_user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('permissions')->get();

        $teamUsers = $teamUsers->map(function ($user) {
            return [
                'permissions' => json_decode($user->permissions, true),
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'Team users data retrieved successfully',
            'team_users' => $teamUsers,
        ]);
    }


    public function sendResetOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $response = Password::sendResetLink($request->only(['email']));

            if ($response === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reset password OTP has been sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send reset password OTP',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error in sendResetOtp method: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function validateOtpAndResetPassword(Request $request)
{
    // Validate the request data as needed

    // $request->validate([
    //     'phone' => 'required',
    //     'password' => 'required|confirmed|min:8',
    //     'token' => 'required',
    // ]);

    $credentials = $request->only('phone', 'password', 'password_confirmation', 'token');

    // Retrieve the user by phone number
    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }

    // Update the user's password
    $user->forceFill([
        'password' => Hash::make($credentials['password']),
    ])->save();

    // If needed, you can update other fields here as well
    // dd($user);
    return response()->json([
        'success' => true,
        'message' => 'Password has been reset successfully',
    ]);

}


    public function user_logout(Request $request): Response
    {
        try {
            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $user = Auth::guard(Auth::getDefaultDriver())->user();
                Auth::guard(Auth::getDefaultDriver())->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return Response()->json(['success' => true, 'data' => 'Unauthorized', 'message' => 'User  logout successfully. '], 200);
            }
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Error in user_logout method: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // All Features Toggle
    public function userDashboardPanel(Request $request)
    {
        try {
            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $user = Auth::guard(Auth::getDefaultDriver())->user();
                // dd($request->all());
                $user->update([
                    'buyer' => $request->input('buyer', $user->buyer),
                    'seller' => $request->input('seller', $user->seller),
                    'receiver' => $request->input('receiver', $user->receiver),
                    'sender' => $request->input('sender', $user->sender),
                    'receipt_note' => $request->input('receipt_note', $user->receipt_note),
                ]);

                return response()->json([
                    'message' => 'Package updated successfully',
                    'success' => true,
                    'status' => 201,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the package.',
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    // Store User Details
    public function storeUserDetail(Request $request)
    {
        try {
            // Validate the incoming request data for storing the user detail
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'address' => 'nullable|string|max:255',
                'pincode' => 'nullable|integer',
                'phone' => 'nullable|string',
                'gst_number' => 'nullable|string|max:191',
                'state' => 'nullable|string|max:75',
                'city' => 'nullable|string|max:75',
                'bank_name' => 'nullable|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'bank_account_no' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:255',
                'tan' => 'nullable|string|max:15',
                // 'organisation_type' => 'nullable|string',
                'location_name' => 'nullable|string',
                'status' => 'nullable|in:active,pause,terminate',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Create the user detail
            $userDetail = UserDetails::create([
                'user_id' => $request->input('user_id'),
                'address' => $request->input('address'),
                'pincode' => $request->input('pincode'),
                'phone' => $request->input('phone'),
                'gst_number' => $request->input('gst_number'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'bank_name' => $request->input('bank_name'),
                'branch_name' => $request->input('branch_name'),
                'bank_account_no' => $request->input('bank_account_no'),
                'ifsc_code' => $request->input('ifsc_code'),
                'tan' => $request->input('tan'),
                // 'organisation_type' => $request->input('organisation_type'),
                'location_name' => $request->input('location_name'),
                'status' => $request->input('status', 'active'), // Default value: active
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'New Address Added',
                'user_detail' => $userDetail,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while storing user details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUserDetail(Request $request, $userDetailId)
    {
        try {
            // Validate the incoming request data for updating the user detail
            $validator = Validator::make($request->all(), [
                'address' => 'nullable|string|max:255',
                'pincode' => 'nullable|integer',
                'phone' => 'nullable|string',
                'gst_number' => 'nullable|string|max:191',
                'state' => 'nullable|string|max:75',
                'city' => 'nullable|string|max:75',
                'bank_name' => 'nullable|string|max :255',
                'branch_name' => 'nullable|string|max:255',
                'bank_account_no' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:255',
                'tan' => 'nullable|string|max:15',
                'location_name' => 'nullable|string',
                'status' => 'nullable|in:active,pause,terminate',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Find the user detail by ID
            $userDetail = UserDetails::findOrFail($userDetailId);
            $userDetail->update($request->only([
                'address',
                'pincode',
                'phone',
                'gst_number',
                'state',
                'city',
                'bank_name',
                'branch_name',
                'bank_account_no',
                'ifsc_code',
                'tan',
                'location_name',
                'status',
            ]));

            return response()->json([
                'status' => 200,
                'message' => 'User  detail updated successfully',
                'user_detail' => $userDetail,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating user details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $userDetail = UserDetails::find($id);

            if (!$userDetail) {
                return response()->json([
                    'data' => null,
                    'message' => 'User not found.',
                    'status_code' => 404,
                ], 404);
            }

            $userDetail->delete();

            return response()->json([
                'data' => null,
                'message' => 'User destroyed.',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user detail: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while deleting the user.',
                'status_code' => 500
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = User::query();

            if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name', Auth::getDefaultDriver())->exists()) {
                $query->where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
            } elseif ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $Users = $query->with(['details', 'emailVerification', 'phoneVerification' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->get();

            return response()->json([
                'data' => $Users,
                'message' => 'Success',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching users.',
                'status_code' => 500
            ], 500);
        }
    }


    // Method for storing Query from Landing Page Form
    public function userQuery(Request $request)
    {
        try {
            $userQuery = UserQuery::create([
                'phone' => $request->phone,
                'email' => $request->email,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thank You!! TheParchi team shall connect with you shortly',
                'user_detail' => $userQuery,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating user query: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing your query.',
                'status_code' => 500
            ], 500);
        }
    }

    public function sendOtpForVerification(Request $request, $verificationType)
{
    $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
    $otp = mt_rand(100000, 999999); // Generate a 6-digit OTP
    $expiresAt = now()->addMinutes(10); // OTP expiration time

    try {
        if ($verificationType === 'email') {
            $verification = EmailVerifications::updateOrCreate(
                ['user_id' => $user->id],
                ['otp' => Hash::make($otp), 'expires_at' => $expiresAt]
            );
            Mail::to($user->email)->send(new MailVerification($otp));
        } elseif ($verificationType === 'phone') {
            $verification = PhoneVerifications::updateOrCreate(
                ['user_id' => $user->id],
                ['otp' => Hash::make($otp), 'expires_at' => $expiresAt]
            );

            $apiKey = urlencode('etVEU+y8WgE-lNrXaKDbdAjborAW26zsIxf9brZrLO');
            $numbers = [$user->phone];
            $sender = urlencode('TPARCH');
            $message = "{$otp} is your OTP for login to www.theparchi.com. This OTP will expire within 2 mins.";
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $smsresponse = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status !== 200) {
                throw new \Exception('Failed to send OTP');
            }
        }

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'OTP sent successfully to Email',
            'otp' => $otp
        ], 200);
    } catch (\Throwable $exception) {
        Log::channel('emaillog')->error($exception->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP',
        ], 500);
    }
}


    public function verifyOTP($otp, $verificationType)
    {
        $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

        if ($verificationType === 'email') {
            $verification = EmailVerifications::where('user_id', $user->id)->latest()->first();
        } elseif ($verificationType === 'phone') {
            $verification = PhoneVerifications::where('user_id', $user->id)->latest()->first();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification type',
            ], 400);
        }

        // Check if verification exists
        if (!$verification) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'Verification record not found',
            ], 400);
        }

        // Check if OTP is expired
        if (Carbon::parse($verification->expires_at)->isPast()) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'OTP expired',
            ], 400);
        }

        // Check if OTP is valid
        if (!Hash::check($otp, $verification->otp)) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'Invalid OTP',
            ], 400);
        }

        // If OTP is valid and not expired, proceed to mark the verification as used or delete it
        $verification->verified_at = now(); // Set the current time as the verification time
        $verification->save();

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => 'OTP verified successfully',
        ], 200);
    }



    // Request For accoutnt Delete
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return Response::json(['message' => 'User not found'], 404);
        }

        try {
            // Delete user and any associated data
            $user->delete();

            // Log the user out
            Auth::logout();

            return Response::json(['message' => 'Your account has been deleted'], 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'Failed to delete account'], 500);
        }
    }

    // Store FCM Token
    public function storeFcmToken(Request $request)
    {
        Log::info('storeFcmToken method was called');

        // Get the user
        $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);

        // Store the FCM token
        $user->update([
            'device_token' => $request->input('fcm_token'),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'FCM token stored successfully',
            'user' => $user,
        ], 200);
    }

    // Update The permisssion of the user
    public function updatePermission(Request $request)
    {
        $user = User::find(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
    }

}
