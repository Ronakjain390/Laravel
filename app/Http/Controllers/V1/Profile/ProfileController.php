<?php

namespace App\Http\Controllers\V1\Profile;

use App\Models\User;
use App\Models\Order;
use App\Services\PDFServices\PDFGeneratorService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;


class ProfileController extends Controller
{
    public function index(Request $request)
    {
        // $orders = Order::where('user_id',  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords', 'plan', 'user')->latest()->first();
        // // dd($orders);
        // $pdfGenerator = new PDFGeneratorService();
        // $response = $pdfGenerator->planInvoicePDF($orders);
        // // dd($response);

        // $response = (array) $response->getData();

        // $demo = $response['pdf_url'];
 
        $id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $profile = User::where('id', $id)->with('emailVerification', 'phoneVerification')->first();
        if ($profile) {
            return response()->json(['profile' => $profile]);
        } else {
            return response()->json(['message' => 'Profile not found'], 404);
        }
    }

    public function indexVerification(Request $request)
    {
        // $orders = Order::where('user_id',  Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->with('featureUsageRecords', 'additionalFeatureUsageRecords', 'featureTopupUsageRecords', 'additionalFeatureTopupUsageRecords', 'plan', 'user')->latest()->first();
        // // dd($orders);
        // $pdfGenerator = new PDFGeneratorService();
        // $response = $pdfGenerator->planInvoicePDF($orders);
        // // dd($response);

        // $response = (array) $response->getData();

        // $demo = $response['pdf_url'];
// dd($demo);
        $id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $profile = User::where('id', $id)->with('emailVerication', 'phoneVerication')->first();
        if ($profile) {
            return response()->json(['profile' => $profile]);
        } else {
            return response()->json(['message' => 'Profile not found'], 404);
        }
    }

    public function create(Request $request, $id)
    {

        // Validate and create a new user profile
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'nullable',
            'company_name' => 'required',
            'gst' => 'nullable|regex:/^[a-zA-Z0-9]{15}$/',
            'pancard' => 'nullable|regex:/^[a-zA-Z0-9]{10}$/',
            'pincode' => 'required',
            'state' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ]);

        $profile = User::updateOrCreate([
            'user_id' => $id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'company_name' => $request->input('company_name'),
            'gst' => $request->input('gst'),
            'pincode' => $request->input('pincode'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'pancard' => $request->input('pancard'),
            'phone' => $request->input('phone'),
        ]);


        return response()->json(['message' => 'Profile created successfully', 'profile' => $profile], 201);
    }

    public function importUser($request)
    {

        // dd($request);
        // if ($request->email) {
        $user = User::create([
            'special_id' => $request->special_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'gst_number' => $request->gst_number,
            'pancard' => $request->pancard,
            'state' => $request->state,
            'city' => $request->city,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'bank_account_no' => $request->bank_account_no,
            'added_by' => $request->added_by,
            'ifsc_code' => $request->ifsc_code,
            'tan' => $request->tan,
            // 'remember_token' => $request->remember_token,
            'status' => 'active',
            // 'email_verified_at' => $request->email_verified_at,
            'first_time' => 1,
            // 'created_at' => $request->created_at,
            // 'updated_at' => $request->updated_at,
            // 'added_by' => $request->added_by
        ]);
        // }

        if ($user) {
            // $users = User::get();


            $panelSeriesNumberController = new PanelSeriesNumberController;

            $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
            $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->subDay();
            $combinations = [
                ['panel_id' => 1, 'section_id' => 1],
                ['panel_id' => 2, 'section_id' => 1],
                ['panel_id' => 3, 'section_id' => 2],
                ['panel_id' => 4, 'section_id' => 2],
            ];

            foreach ($combinations as $combination) {
                $panelSeriesData = [
                    'series_number' => substr($user->name, 0, 4) . Str::random(4),
                    'panel_id' => $combination['panel_id'],
                    'user_id' => $user->id,
                    'section_id' => $combination['section_id'],
                    'status' => 'active',
                    'valid_from' => $currentFinancialYearStart,
                    'valid_till' => $nextFinancialYearEnd,
                    'default' => '1',
                ];

                $panelSeriesRequest = new Request($panelSeriesData);
                $panelSeriesResponse = $panelSeriesNumberController->importStore($panelSeriesRequest);
            }

            // Create Order
            $ordeController = new OrdersController;
            $planCombinations = [
                ['plan_ids' => 3,],
                ['plan_ids' => 11,],
                ['plan_ids' => 12,],
                ['plan_ids' => 13,],
            ];

            foreach ($planCombinations as $planCombination) {
                $orderData = [
                    'user_id' => $user->id,
                    'plan_ids' => $planCombination,
                ];

                $orderRequest = new Request($orderData);
                $orderResponse = $ordeController->importStore($orderRequest);
            }
        }

        return response()->json(['message' => 'Profile created successfully', 'profile' => "true"], 200);
    }

    public function planUser()
    {

        // if ($request->email) {
        $users = User::get();

        foreach ($users as $user) {
            if ($user) {
                if ($user) {

                    $panelSeriesNumberController = new PanelSeriesNumberController;

                    $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
                    $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->subDay();
                    $combinations = [
                        ['panel_id' => 1, 'section_id' => 1],
                        ['panel_id' => 2, 'section_id' => 1],
                        ['panel_id' => 3, 'section_id' => 2],
                        ['panel_id' => 4, 'section_id' => 2],
                    ];

                    foreach ($combinations as $combination) {
                        $panelSeriesData = [
                            'series_number' => substr($user->name, 0, 4) . Str::random(4),
                            'panel_id' => $combination['panel_id'],
                            'user_id' => $user->id,
                            'section_id' => $combination['section_id'],
                            'status' => 'active',
                            'valid_from' => $currentFinancialYearStart,
                            'valid_till' => $nextFinancialYearEnd,
                            'default' => '1',
                        ];

                        $panelSeriesRequest = new Request($panelSeriesData);
                        $panelSeriesResponse = $panelSeriesNumberController->importStore($panelSeriesRequest);
                    }

                    // Create Order
                    $ordeController = new OrdersController;
                    $planCombinations = [
                        ['plan_ids' => 3,],
                        ['plan_ids' => 11,],
                        ['plan_ids' => 12,],
                        ['plan_ids' => 13,],
                    ];

                    foreach ($planCombinations as $planCombination) {
                        $orderData = [
                            'user_id' => $user->id,
                            'plan_ids' => $planCombination,
                        ];

                        $orderRequest = new Request($orderData);
                        $orderResponse = $ordeController->importStore($orderRequest);
                    }
                }


                $panelSeriesNumberController = new PanelSeriesNumberController;

                $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
                $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->subDay();
                $combinations = [
                    ['panel_id' => 1, 'section_id' => 1],
                    ['panel_id' => 2, 'section_id' => 1],
                    ['panel_id' => 3, 'section_id' => 2],
                    ['panel_id' => 4, 'section_id' => 2],
                ];

                foreach ($combinations as $combination) {
                    $panelSeriesData = [
                        'series_number' => substr($user->name, 0, 4) . Str::random(4),
                        'user_id' => $user->id,
                        'panel_id' => $combination['panel_id'],
                        'section_id' => $combination['section_id'],
                        'status' => 'active',
                        'valid_from' => $currentFinancialYearStart,
                        'valid_till' => $nextFinancialYearEnd,
                        'default' => '1',
                    ];

                    $panelSeriesRequest = new Request($panelSeriesData);
                    $panelSeriesResponse = $panelSeriesNumberController->importStore($panelSeriesRequest);
                }

                // Create Order
                $ordeController = new OrdersController;
                $planCombinations = [
                    ['plan_ids' => 3,],
                    ['plan_ids' => 11,],
                    ['plan_ids' => 12,],
                    ['plan_ids' => 13,],
                ];

                foreach ($planCombinations as $planCombination) {
                    $orderData = [
                        'user_id' => $user->id,
                        'plan_ids' => $planCombination,
                    ];

                    $orderRequest = new Request($orderData);
                    $orderResponse = $ordeController->importStore($orderRequest);
                }
            }
        }


        return response()->json(['message' => 'Profile created successfully', 'profile' => "true"], 200);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'email' => 'required|email',
            'address' => 'nullable',
            'gst_number' => 'nullable|regex:/^[a-zA-Z0-9]{15}$/',
            'pancard' => 'nullable|regex:/^[a-zA-Z0-9]{10}$/',
            'company_name' => 'nullable',
            'state' => 'nullable',
            'city' => 'nullable',
            'bank_name' => 'nullable',
            'branch_name' => 'nullable',
            'bank_account_number' => 'nullable',
            'ifsc_code' => 'nullable',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        $user = User::find($id);
        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->company_name = $request->company_name;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->gst_number = $request->gst_number;
            $user->pincode = $request->pincode;
            $user->address = $request->address;
            $user->pancard = $request->pancard;
            $user->branch_name = $request->branch_name;
            $user->bank_name = $request->bank_name;
            $user->ifsc_code = $request->ifsc_code;
            $user->bank_account_no = $request->bank_account_no;
            $user->save();
        }
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'status_code' => 200,
        ]);
    }
}
