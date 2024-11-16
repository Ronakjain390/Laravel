<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:2',
        ]);

        $user = auth()->user();
        $wallet = $user->wallet;

        $wallet->credit($request->amount);

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Wallet topped up successfully',
                'balance' => $wallet->balance
            ]
        ], 200);
    }
}
