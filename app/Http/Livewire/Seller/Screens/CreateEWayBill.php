<?php

namespace App\Http\Livewire\Seller\Screens;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PDFServices\PDFGeneratorService;
use Illuminate\Support\Facades\Http;


class CreateEWayBill extends Component
{
    public $columnId;
    public $message;
    public $ewayBillData;
    public $sellerUser;
    public $buyerUser;
    public $email = 'jainronak390@gmail.com';
    public $username;
    public $password;
    public $client_id = 'e69d0f45-07c9-41d2-992b-5fc06c99fd83';
    public $client_secret = '39283097-3e2f-4e15-b1a8-d55a973407b7';
    public $gstin;
    public $ip_address = '152.59.101.65';

    public $states = [
        1 => 'JAMMU AND KASHMIR',
        2 => 'HIMACHAL PRADESH',
        3 => 'PUNJAB',
        4 => 'CHANDIGARH',
        5 => 'UTTARAKHAND',
        6 => 'HARYANA',
        7 => 'DELHI',
        8 => 'RAJASTHAN',
        9 => 'UTTAR PRADESH',
        10 => 'BIHAR',
        11 => 'SIKKIM',
        12 => 'ARUNACHAL PRADESH',
        13 => 'NAGALAND',
        14 => 'MANIPUR',
        15 => 'MIZORAM',
        16 => 'TRIPURA',
        17 => 'MEGHALAYA',
        18 => 'ASSAM',
        19 => 'WEST BENGAL',
        20 => 'JHARKHAND',
        21 => 'ORISSA',
        22 => 'CHHATTISGARH',
        23 => 'MADHYA PRADESH',
        24 => 'GUJARAT',
        25 => 'DAMAN AND DIU',
        26 => 'DADAR AND NAGAR HAVELI',
        27 => 'MAHARASTRA',
        29 => 'KARNATAKA',
        30 => 'GOA',
        31 => 'LAKSHADWEEP',
        32 => 'KERALA',
        33 => 'TAMIL NADU',
        34 => 'PUDUCHERRY',
        35 => 'ANDAMAN AND NICOBAR',
        36 => 'TELANGANA',
        37 => 'ANDHRA PRADESH',
        97 => 'OTHER TERRITORY',
        96 => 'OTHER COUNTRY',
    ];
    public function updatedEwayBillDataActFromStateCode($value)
    {
        $this->ewayBillData['fromStateCode'] = $value;
    }

    public function updatedEwayBillDataActToStateCode($value)
    {
        $this->ewayBillData['toStateCode'] = $value;
    }

    public function mount()
    {
        // Retrieve columnId from session
        $this->columnId = session('columnId');
        $invoice = Invoice::find($this->columnId);
        $invoice = $invoice->load('orderDetails', 'orderDetails.columns');
        $this->sellerUser = User::find($invoice->seller_id);
        $this->buyerUser = User::find($invoice->buyer_id);

        // Determine if the transaction is intra-state or inter-state
        $isIntraState = strtoupper($this->sellerUser->state) == strtoupper($this->buyerUser->state);
        // dd($isIntraState, $this->sellerUser->state, $this->buyerUser->state, $invoice);
        // Initialize blank dataset array
        $this->ewayBillData = [
            "supplyType" => "",
            "subSupplyType" => "",
            "subSupplyDesc" => "",
            "docType" => "INV",
            "docNo" => $invoice->invoice_series . '-' . $invoice->series_num,
            "docDate" => $invoice->invoice_date,
            "fromGstin" => "",
            "fromTrdName" => $invoice->seller,
            "fromAddr1" => $this->sellerUser->address,
            "fromPlace" => "",
            "actFromStateCode" => "",
            "fromPincode" => $this->sellerUser->pincode,
            "fromStateCode" => "",
            "toGstin" => "",
            "toTrdName" => $invoice->buyer,
            "toAddr1" => $this->buyerUser->address,
            "toPlace" => "",
            "toPincode" => $this->buyerUser->pincode,
            "actToStateCode" => "",
            "toStateCode" => "",
            "transactionType" => "",
            "dispatchFromGSTIN" => "",
            "dispatchFromTradeName" => "",
            "shipToGSTIN" => "",
            "shipToTradeName" => "",
            "totalValue" => $invoice->total,
            "cgstValue" => 0,
            "sgstValue" => 0,
            "igstValue" => 0,
            "totInvValue" => "",
            "transMode" => "",
            "transDistance" => "0",
            "vehicleNo" => "",
            "vehicleType" => "",
            "transporterId" => "",
            "itemList" => []
        ];

        foreach ($invoice->orderDetails as $orderDetail) {
            $cgstValue = 0;
            $sgstValue = 0;
            $igstValue = 0;
            $taxRate = $orderDetail->tax / 100; // Assuming tax is a percentage

            // Calculate GST Amount
            $gstAmount = $orderDetail->total_amount - ($orderDetail->total_amount * (100 / (100 + $orderDetail->tax)));

            if ($isIntraState) {
                // Intra-state: Split GST amount equally between CGST and SGST
                $cgstValue = $gstAmount / 2;
                $sgstValue = $gstAmount / 2;
            } else {
                // Inter-state: Apply GST amount as IGST
                $igstValue = $gstAmount;
            }

            // Accumulate CGST, SGST, and IGST values
            $this->ewayBillData['cgstValue'] += $cgstValue;
            $this->ewayBillData['sgstValue'] += $sgstValue;
            $this->ewayBillData['igstValue'] += $igstValue;
        }
       // Initialize a flag to check if the values have been set
        $valuesSet = false;

        // Initialize variables to store the values
        $productName = '';
        $hsnCode = '';
        $quantity = '';
        $qtyUnit = '';
        $taxableAmount = '';
        $sgstRate = null;
        $cgstRate = null;
        $igstRate = '';
        $cessRate = '';

        // Iterate through orderDetails and their columns
        foreach ($invoice->orderDetails as $orderDetail) {
            foreach ($orderDetail->columns as $column) {
                // Set the values based on the column name
                if ($column->column_name == 'Article') {
                    $productName = $column->column_value;
                }
                if ($column->column_name == 'Hsn') {
                    $hsnCode = $column->column_value;
                }
            }

            // Set the other values from orderDetail
            $quantity = $orderDetail->qty;
            $qtyUnit = $orderDetail->unit;
            $taxableAmount = $orderDetail->total_amount;
            $sgstRate = $orderDetail->sgst;
            $cgstRate = $orderDetail->cgst;
            $igstRate = $orderDetail->igst;
            $cessRate = '';

            // Check if the values have already been set
            if (!$valuesSet) {
                $this->ewayBillData['itemList'][] = [
                    "productName" => $productName,
                    "productDesc" => "",
                    "hsnCode" => $hsnCode,
                    "quantity" => $quantity,
                    "qtyUnit" => $qtyUnit,
                    "taxableAmount" => $taxableAmount,
                    "sgstRate" => $sgstRate,
                    "cgstRate" => $cgstRate,
                    "igstRate" => $igstRate,
                    "cessRate" => $cessRate
                ];
                // Set the flag to true after the first set of values is added
                $valuesSet = true;
            }
        }

        // Calculate total invoice value including taxes
        $this->ewayBillData['totInvValue'] = $this->ewayBillData['totalValue'] + $this->ewayBillData['cgstValue'] + $this->ewayBillData['sgstValue'] + $this->ewayBillData['igstValue'];
        // Set initial state values
        $this->fromState = $this->ewayBillData['actFromStateCode'];
        $this->toState = $this->ewayBillData['actToStateCode'];
        // dd($this->ewayBillData);
    }

    public function submitForm()
    {
        // dd($this->ewayBillData);

        $url = 'https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=jainronak390%40gmail.com';
        $headers = [
            'Content-Type: application/json',
            'ip_address: 152.59.101.65',
            'client_id: e69d0f45-07c9-41d2-992b-5fc06c99fd83',
            'client_secret: 39283097-3e2f-4e15-b1a8-d55a973407b7',
            'gstin: 05AAACH6188F1ZM'
        ];

        $data = $this->ewayBillData;

        // Convert relevant fields to appropriate data types
        $data['actFromStateCode'] = (int) $data['actFromStateCode'];
        $data['fromStateCode'] = (int) $data['fromStateCode'];
        $data['actToStateCode'] = (int) $data['actToStateCode'];
        $data['toStateCode'] = (int) $data['toStateCode'];
        $data['transactionType'] = (int) $data['transactionType'];
        $data['totalValue'] = (float) $data['totalValue'];
        $data['docDate'] = date('d/m/Y', strtotime($data['docDate']));
        $data['toPincode'] = (int) $data['toPincode']; // Convert toPincode to a number

        // Ensure GSTIN fields match the expected pattern
        $gstinPattern = '/^[0-9]{2}[A-Z0-9]{13}$/';
        $fieldsToValidate = ['fromGstin', 'toGstin', 'transporterId', 'shipToGSTIN', 'dispatchFromGSTIN'];
        foreach ($fieldsToValidate as $field) {
            if (isset($data[$field]) && !preg_match($gstinPattern, $data[$field])) {
                unset($data[$field]);
            }
        }

        // Validate and adjust docType based on supplyType
        switch ($data['supplyType']) {
            case 'O': // Outward Supply
                // dd($data['docType']);
                if (!in_array($data['docType'], ['INV', 'BIL', 'CHL'])) {
                    $data['docType'] = 'INV'; // Default to Invoice if invalid
                }
                break;
            case 'I': // Inward Supply
                if (!in_array($data['docType'], ['BIL', 'BOE'])) {
                    $data['docType'] = 'BIL'; // Default to Bill if invalid
                }
                break;
            default:
                // Handle other supply types if any
                $data['docType'] = 'INV'; // Fallback to a safe default
                break;
        }

        // Ensure itemList has correct data types and required fields
        foreach ($data['itemList'] as &$item) {
            $item['hsnCode'] = (int) $item['hsnCode'];
            $item['quantity'] = (float) $item['quantity'];
            $item['taxableAmount'] = (float) $item['taxableAmount'];
            $item['sgstRate'] = isset($item['sgstRate']) ? (float) $item['sgstRate'] : 0;
            $item['cgstRate'] = isset($item['cgstRate']) ? (float) $item['cgstRate'] : 0;
            $item['igstRate'] = isset($item['igstRate']) ? (float) $item['igstRate'] : 0;
            $item['cessRate'] = isset($item['cessRate']) ? (float) $item['cessRate'] : 0;
            $item['qtyUnit'] = isset($item['qtyUnit']) && strlen($item['qtyUnit']) >= 3 ? $item['qtyUnit'] : 'PCS'; // Ensure qtyUnit has a minimum length of 3

            if (empty($item['productName']) || empty($item['hsnCode']) || empty($item['quantity']) || empty($item['taxableAmount'])) {
                $item['productName'] = $item['productName'] ?? 'Unknown';
                $item['hsnCode'] = $item['hsnCode'] ?? 0;
                $item['quantity'] = $item['quantity'] ?? 0;
                $item['taxableAmount'] = $item['taxableAmount'] ?? 0;
            }
        }

        // Convert the data to JSON
        $jsonData = json_encode($data);
        // dd($jsonData);
        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Disable SSL verification (not recommended for production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            dd('cURL error: ' . $error_msg);
        }

        // Close cURL session
        curl_close($ch);

        // Decode the response
        $responseData = json_decode($response, true);
        // dd($responseData);
        // Handle the response
        if ($responseData['status_cd'] == '1') {
            // Success: Store the eWay Bill number in the database
            $ewayBillNo = $responseData['data']['ewayBillNo'];
            // Assuming you have a model named Invoice
            $invoice = Invoice::find($this->columnId);
            $invoice->eway_bill_no = $ewayBillNo;
            $invoice->save();

            // Fetch the invoice details with related data
            $invoice = Invoice::where('id', $this->columnId)
                ->with('buyerUser', 'sellerUser', 'orderDetails', 'orderDetails.columns', 'statuses')
                ->first();

            // Generate the PDF for the Invoice using PDFGenerator class
            $pdfGenerator = new PDFGeneratorService();
            $response = $pdfGenerator->generateInvoicePDF($invoice);

            // Handle the response from PDFGenerator
            $response = (array) $response->getData();
            if ($response['status_code'] === 200) {
                // PDF generated successfully
                $invoice->eway_bill_pdf_url = $response['pdf_url'];
                $invoice->save();
            }

            // Show success message to the user
            session()->flash('success', 'EWAYBILL request succeeded. EWay Bill No: ' . $ewayBillNo);
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', 'E-Way Bill generated successfully.');
        } else {
            // Error: Show error message to the user
            $errorMessage = $responseData['error']['message'] ?? 'Unknown error';
            session()->flash('error', 'EWAYBILL request failed: ' . $errorMessage);
        }
    }




    // public function submitForm()
    // {
    //     // dd($this->ewayBillData);
    //     $response = Http::withHeaders([
    //         'email' => $this->email,
    //         'ip_address' => $this->ip_address,
    //         'client_id' => $this->client_id,
    //         'client_secret' => $this->client_secret,
    //         'gstin' => $this->ewayBillData['toGstin'], // Use the GST number entered by the user
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //     ])->withoutVerifying()->post('https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=jainronak390@gmail.com', $this->ewayBillData);
    //         // dd($response);
    //     if ($response->successful()) {
    //         // dd('E-Way Bill generated successfully.');
    //         // Handle successful response
    //         session()->flash('message', 'E-Way Bill generated successfully.');
    //     } else {
    //         // Handle error response
    //         // dd('Failed to generate E-Way Bill.');
    //         session()->flash('error', 'Failed to generate E-Way Bill.');
    //     }
    // }
    public function render()
    {
        return view('livewire.seller.screens.create-e-way-bill', [
            'columnId' => $this->columnId,
        ]);
    }
}
