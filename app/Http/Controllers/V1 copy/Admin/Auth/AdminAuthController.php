<?php

namespace App\Http\Controllers\Web\Admin\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{

    //

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Admin registered successfully', 'admin' => $admin]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'success' => false,
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $admin = Auth::guard('admin')->user();

        // dd( Auth::guard('admin'));
        // dd($admin,$admin->createToken('admin')->accessToken);
        $token = $admin->createToken('admin', ['admin'])->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


    public function admin_details(Request $request)
    {
        if (Auth::guard('admin-api')->check()) {
            $user = Auth::guard('admin-api')->user();
            return response()->json(['data' => $user, 'data-type' => 'admin'], 200);
        }
        // return Response(['data' => 'Unauthorized'], 401);
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }

    public function admin_logout(): Response
    {
        if (Auth::guard('admin-api')->check()) {
            $accessToken = Auth::guard('admin-api')->user()->token();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
            $accessToken->revoke();
            Auth::guard('admin-api')->user()->tokens()->where('name','admin')->delete();


            return Response(['data' => 'Unauthorized', 'message' => 'User logout successfully.'], 200);
        }
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized',
        ], 401);
    }
}
