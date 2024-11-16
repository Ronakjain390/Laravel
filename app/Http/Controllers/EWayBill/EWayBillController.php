<?php

namespace App\Http\Controllers\EWayBill;

use App\Http\Controllers\Controller;
use App\Services\EWayBillService;
use Illuminate\Http\Request;

class EWayBillController extends Controller
{
    protected $eWayBillService;

    public function __construct(EWayBillService $eWayBillService)
    {
        $this->eWayBillService = $eWayBillService;
    }

    public function authenticate()
    {
        $response = $this->eWayBillService->authenticate();

        if ($response) {
            // Handle the response, e.g., store in session or database
            return response()->json($response);
        }

        return response()->json(['error' => 'Authentication failed'], 401);
    }

    public function cancelEWayBill($ewayBillNo)
    {
        $response = $this->eWayBillService->cancelEWayBill($ewayBillNo);
        return response()->json($response);
    }

}
