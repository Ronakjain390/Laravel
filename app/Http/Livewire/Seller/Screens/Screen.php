<?php

namespace App\Http\Livewire\Seller\Screens;

use App\Models\Buyer;
use App\Models\Invoice;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\PanelSeriesNumber;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\URL;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
// use App\Http\Livewire\Sender\Screens\sentInvoice;
use App\Http\Livewire\Sender\Screens\createInvoice;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

class Screen extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $mainUser;
    public $uploadFile;
    public $persistedTemplate;
    public $persistedActiveFeature;
    public $features = [];
    public $activeFeature;
    public $template;
    public $rate;
    public $data;
    public $quantity;
    public $totalAmount;
    public $discount;
    public $rows = [];
    protected $queryString = ['activeTab'];
    public $errorFileUrl;
    public $createInvoice;
    public $manuallyAdded;
    public $validationErrorsJson = [];
    public $buyerUserData;
    public $isTaxIncluded = false;
    public $createInvoiceInstance;
    public $showInputBoxes = true;
    public $name, $save;
    public $action = 'save';
    public $prevTax = null;
    public $savedInvoiceId; // New property to store the saved ID
    public $isEditing = false;
    public $isSendEnabled = false;
    public $sentInvoice, $challanFiltersData, $challanData, $selectedColumnName;
    public $autofillData = [];
    public $isLoading = true;
    public $ColumnDisplayNames, $invoiceFiltersData, $receivedColumnDisplayNames, $sentColumnDisplayNames, $assigned_to_name, $discountValue, $totalAmountWithoutTax,$panelUserColumnDisplayNames;
    protected $addBuyer, $storeInvoiceSeries;
    private $addBuyerCode, $selectedUserDetailsData;
    public $response, $buyerData, $seriesNoData, $newInvoiceDesign, $modifySentInvoiceData, $modifySentInvoice;
    public $newInvoiceSeriesNoController, $addInvoiceSeries, $buyerDatas, $buyerDetails, $gst, $showData, $invoiceId;
    public $buyer_name, $company_name, $from, $to, $isMobile, $buyerName, $buyerAddress, $email, $address, $pincode, $phone, $state, $city, $tan, $errorMessage, $success, $successMessage, $showManualBuyerTab, $buyer_special_id, $errors, $statusCode, $message, $PanelColumnData, $termsIndexData;
    public $products, $articles = [], $locations = [], $item_codes, $Article, $location, $item_code, $fromDate, $toDate;
    //sent invoice
    public $invoiceData;
    public $tableTdData, $currentPage = 1, $paginateLinks;
    public $purchaseOrderBuyerData;
    public $authUserState;
    // create invoice screen
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $selectedUser;
    public $selectedUserDetails = [];
    public $totalSales = 0;
    public $discountEntered = false;
    public $discountPercentage = 0;
    public $calculateTax = true;
    public $sameAsBilling = true;
    public $billTo;
    public $column1;
    public $column2;
    public $column3;
    public $disabledButtons = true;
    public $column4;
    public $fromStockRequest = [];
    public $selectedProductP_ids = [];
    public $sendButtonDisabled = true;
    public $updateForm = true;
    public $invoice_id;
    public $total_qty;
    public $invoice_series, $invoice_date, $series_num, $status, $seller_id, $seller, $buyer_id, $buyer, $comment,  $unit = [], $artical = [];

    public $addBuyerData = array(
        'buyer_name' => '',
        'company_name' => '',
        'email' => '',
        'address' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'buyer_special_id' => '',
    );
    public $sellerInvoiceSeriesData = array(
        'invoice_number' => '',
        'valid_from' => '',
        'valid_till' => '',
        'buyer_user_id' => '',
        'panel_id' => '3',
        'section_id' => '2',
        'assigned_to_b_id' => '',
        'assigned_to_name' => '',
        'default' => '0',
        'status' => 'active',
    );
    public $termsAndConditionsData = array(
        'content' => '',
        'panel_id' => '3',
        'section_id' => '2',
    );
    public $selectBuyer =  array(
        'id' => "",
        'added_by' => "",
        'buyer_name' => "",
        'status' => 'active',
        'details' => [
            [
                "id" => "",
                "buyer_id" => "",
                "address" => "",
                "pincode" => "",
                "phone" => "",
                "gst_number" => "",
                "state" => "",
                "city" => "",
                "bank_name" => "",
                "branch_name" => "",
                "bank_account_no" => "",
                "ifsc_code" => "",
                "tan" => "",
                'status' => 'active',
            ]
        ]
    );


    // public $updateInvoiceSeriesData = array(
    //     'series_id' => '',
    //     'invoice_number' => '',
    //     'valid_from' => '',
    //     'valid_till' => '',
    //     'buyer_user_id' => '',
    //     'panel_id' => '1',
    //     'section_id' => '1',
    //     'assigned_to_r_id' => '25',
    //     'assigned_to_name' => '',
    //     'default' => '1',
    //     'status' => 'active',

    // );
    // public $updateAllInvoiceData = [];
    public $updateInvoiceSeriesData = [];
    public $updateAllInvoiceData = array(
        'buyer_name' => '',
        "status" => "active",
        "details" => [
            [
                'address' => '',
                'pincode' => '',
                'phone' => '',
                'gst_number' => '',
                'state' => '',
                'city' => '',
                'bank_name' => '',
                'branch_name' => '',
                'bank_account_no' => '',
                'ifsc_code' => '',
                'tan' => '',
                'status' => 'active',
            ]
        ]
    );
    public $updateInvoiceSeriesTerms = [];

    public $create_invoice_request = array(
        'invoice_series' => '',
        'series_num' => '',
        'invoice_date' => '',
        'feature_id' => '',
        'buyer_id' => '',
        'buyer' => '',
        'comment' => '',
        'total_qty' => null,
        'calculate_tax' => null,
        'total' => '',
        'total_words' => '',
        'order_details' => [
            [
                'p_id' => '',
                'unit' => '',
                'rate' => null,
                'qty' => null,
                'round_off' => null,
                'discount' => null,
                'total_amount' => null,
                'tax_percentage' => null,
                'tax_amount' => null,
                'tax' => null,
                'total_with_tax' => null,
                'total_without_tax' => null,
                'toalTax' => null,
                'discount_amount' => null,
                'discount_total_amount' => null,
                'total_tax' => null,
                'toalTaxRate' => null,
                'totalSales' => null,
                'cgst_rate' => null,
                'sgst_rate' => null,
                'cgst' => null,
                'sgst' => null,
                'columns' => [
                    [
                        'column_name' => '',
                        'column_value' => '',
                    ]
                ],
            ],
        ],
        'statuses' => [
            [
                'comment' => ''
            ]
        ]
    );

    // create invoice screen
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'manualBuyerAdded' => 'handleManualBuyerAdded',
        'forceDelete' => 'deleteInvoiceSeries',
        'discountEntered' => 'handleDiscountEntered',
        'detailedSentInvoice' => 'handleDetailedSentInvoice',
        'deletedSentInvoice' => 'handleDeletedSentInvoice',
        'invoiceDesign' => 'handleInvoiceDesign',
        'invoiceTermsAndConditions' => 'handleInvoiceTermsAndConditions',
        'deleteInvoiceTerms' => 'handleDeleteInvoiceTerms',
        'detailedPurchaseOrder' => 'handleDetailedPurchaseOrder',
        'modifyInvoice' => 'handleModifyInvoice',
        'updateTotal' => 'updateTotal',
        'addFromStock' => 'addFromStock',
        'refreshComponent' =>  '$refresh',
        'seriesNumberUpdated' => 'updateSeriesNumber',
        'disabledInputs' => 'updateInputs',
    ];


    public function saveRows($requestData)
    {
        // dd($requestData);
        $this->emit('saveRowsToPoToInvoice', $requestData);
    }

    public function updateInputs($value)
    {
        $this->disabledButtons = false;
    }
    public $teamMembers;
    public $discount_total_amount;
    public $withoutTax;
    public $total = 0; // Total amount
    public $discountWithoutTax;
//     public function updatedCreateInvoiceRequest() {
//     $totalQuantity = 0;
//     $totalDiscount = 0;

//     foreach ($this->create_invoice_request['order_details'] as &$detail) {
//         // dd($detail);
//         if (isset($detail['discount'])) {
//             // Calculate discount as a percentage of the total amount without tax
//             $totalWithoutTax = $detail['rate'] * $detail['qty'];
//             $totalDiscount += $totalWithoutTax * ($detail['discount'] / 100);
//             $this->withoutTax = $totalWithoutTax;
//         }

//         $detail['total_amount'] = $this->calculateTotalAmount($detail['rate'], $detail['qty'], $detail['tax'], $detail['discount'], $detail['discount_total_amount'] ?? 0);
//         // dd($detail['total_amount']);
//         if (isset($detail['qty'])) {
//             $totalQuantity += (int) $detail['qty'];
//         }

//         $detail['total_tax'] = $this->calculateTotalTax($detail['total_amount'], $detail['tax']);
//     }

//     if (isset($detail['discount_total_amount'])) {
//         // dd($detail['discount_total_amount']);
//         // Calculate discount as a percentage of the total amount without tax
//         $totalWithoutTax = $detail['total_amount'];
//         $totalDiscount += $totalWithoutTax * ($detail['discount_total_amount'] / 100);

//         // dd($totalDiscount);
//     }

//     $this->create_invoice_request['total_qty'] = $totalQuantity;
//     $this->create_invoice_request['total_discount'] = $totalDiscount;
//     // $this->create_invoice_request['discount_total_amount'] = $totalDiscount;
//     $this->updateTotal();
// }



// public function updatedCalculateTax() {
//     $totalDiscount = 0;
//     // dd('2');
//     foreach ($this->create_invoice_request['order_details'] as &$detail) {
//         if (isset($detail['discount'])) {
//             // Calculate discount as a percentage of the total amount without tax
//             $totalWithoutTax = $detail['rate'] * $detail['qty'];
//             $totalDiscount += $totalWithoutTax * ($detail['discount'] / 100);
//         }
//         if (isset($detail['discount_total_amount'])) {
//             // Calculate discount as a percentage of the total amount without tax
//             $totalWithoutTax = $detail['rate'] * $detail['qty'];
//             $totalDiscount += $totalWithoutTax * ($detail['discount_total_amount'] / 100);
//         }
//         $detail['total_amount'] = $this->calculateTotalAmount($detail['rate'], $detail['qty'], $detail['tax'], $detail['discount'], $detail['discount_total_amount'] ?? 0);
//         $detail['total_tax'] = $this->calculateTotalTax($detail['total_amount'], $detail['tax']);
//     }

//     $this->create_invoice_request['total_discount'] = $totalDiscount;
//     $this->updateTotal();
// }
// private function calculateTotalAmount($rate, $qty, $tax, $discount, $discount_total_amount) {
//     $subtotal = (float)($rate) * (float)($qty);
//     $subtotalAfterDiscount = $subtotal - ($subtotal * $discount / 100); // Apply discount before tax

//     if ($this->calculateTax) {
//         $totalAmount = $subtotalAfterDiscount * (1 + ($tax / 100));
//     } else {
//         $totalAmount = (float)($subtotalAfterDiscount);
//     }

//     // Apply discount on total amount if $discount_total_amount is true
//     if ($discount_total_amount) {
//         $totalAmount = $totalAmount - (float)($totalAmount * $discount_total_amount / 100);
//         // dd($totalAmount);
//     }

//     return $totalAmount;
// }

//     private function updateTotal() {
//         // dd('4');
//         // Recalculate total
//         $this->total = 0;
//         $totalWithoutTax = 0;
//         $totalTax = 0;
//         foreach ($this->create_invoice_request['order_details'] as $item) {
//             $totalWithoutTax += $item['total_amount'] - $item['total_tax']; // Calculate total without tax
//             $totalTax += $item['total_tax']; // Calculate total tax
//         }
//         // Subtract total discount as a percentage from total without tax
//         if (isset($this->create_invoice_request['total_discount'])) {
//             $discountPercentage = $this->create_invoice_request['total_discount'] / 100;
//             $discountAmount = $totalWithoutTax * $discountPercentage;
//             // $totalWithoutTax -= $discountAmount;
//         }
//         if (isset($this->create_invoice_request['discount_total_amount'])) {
//             $discountPercentage = $this->create_invoice_request['discount_total_amount'] / 100;
//             $discountAmount = $totalWithoutTax * $discountPercentage;
//             // $totalWithoutTax -= $discountAmount;
//         }
//         // dd($totalWithoutTax);
//         $total = $totalWithoutTax + $totalTax; // Calculate total amount
//         // dd($this->total);
//         $this->create_invoice_request['total'] = $total;
//         $this->create_invoice_request['total_words'] = $this->numberToIndianRupees((float) $total);
//     }

// public function updatedCreateInvoiceRequest()
// {
//     $orderDetails = collect($this->create_invoice_request['order_details']);

//     $totalQuantity = $orderDetails->sum(function ($detail) {
//         return (float)$detail['qty'];
//     });


//     $totalWithoutTax = array_reduce($this->create_invoice_request['order_details'], function ($carry, $item) {
//         if (isset($item['total_amount']) && isset($item['total_tax'])) {
//             return $carry + $item['total_amount'] - $item['total_tax'];
//         }
//         return $carry;
//     }, 0);

//     $this->create_invoice_request['order_details'] = array_map(function ($item) {
//         $item['total_amount'] = $this->calculateTotalAmount(
//             $item['rate'],
//             $item['qty'],
//             $item['tax'] ?? 0, // Use null coalescing operator to provide a default value
//             $item['discount'] ?? 0,
//             $item['discount_total_amount'] ?? 0
//         );
//         $item['total_tax'] = isset($item['tax']) // Check if 'tax' key exists
//             ? $this->calculateTotalTax($item['total_amount'], $item['tax']) // If 'tax' key exists, calculate total tax
//             : 0; // If 'tax' key doesn't exist, set total tax to 0
//         return $item;
//     }, $this->create_invoice_request['order_details']);


//     $this->create_invoice_request['total_qty'] = $totalQuantity;
//     $this->create_invoice_request['total_without_tax'] = $totalWithoutTax;
//     // $this->create_invoice_request['total_discount'] = $totalDiscount;
//     // $this->updateTotal();
// }

    // public function updatedCalculateTax()
    // {
    //     $orderDetails = collect($this->create_invoice_request['order_details']);



    //     $this->create_invoice_request['order_details'] = array_map(function ($item) {
    //     $totalAmount = $this->calculateTotalAmount($item['rate'], $item['qty'], $item['tax'], $item['discount'] ?? 0);
    //     $item['total_amount'] = round($totalAmount, 2); // round to 2 decimal places
    //     $item['total_tax'] = $this->calculateTotalTax($item['total_amount'], $item['tax']);
    //     return $item;
    // }, $this->create_invoice_request['order_details']);

    //     // $this->create_invoice_request['total_discount'] = $totalDiscount;
    //     // $this->updateTotal();
    // }

    // private function calculateTotalAmount($rate, $qty, $tax, $discount)
    // {
    //     $subtotal = (float)($rate) * (float)($qty);
    //     $subtotalAfterDiscount = $subtotal - ($subtotal * $discount / 100); // Apply discount before tax

    //     if ($this->calculateTax) {

    //         $totalAmount = $subtotalAfterDiscount * (1 + ((float)$tax / 100));

    //     } else {

    //         $totalAmount = (float)($subtotalAfterDiscount);

    //     }


    //     // dd($this->discount_total_amount);
    //     // Format totalAmount with 2 decimal places
    //     $totalAmount = number_format($totalAmount, 2, '.', '');
    //     return $totalAmount;
    // }

    public $totalwithoutDiscount;
    public $amount;

    // public function updatedDiscountTotalAmount($value)
    // {
    //     $this->updateTotal();
    // }
    // public function updateTotal()
    // {
    //     $orderDetails = collect($this->create_invoice_request['order_details']);

    //     $totalWithoutTax = $orderDetails->sum(function ($item) {
    //         return $item['total_amount'] - $item['total_tax'];
    //     });

    //     $totalTax = $orderDetails->sum('total_tax');

    //     $totalDiscount = $this->create_invoice_request['total_discount'] ?? 0;

    //     $discountPercentage = $totalDiscount / 100;
    //     $discountAmount = $totalWithoutTax * $discountPercentage;

    //     $total = $totalWithoutTax + $totalTax - $discountAmount;
    //     $this->totalwithoutDiscount = $totalWithoutTax;
    //     // dd($this->discount_total_amount);
    //     // if ($this->discount_total_amount) {
    //     //     $total = $total - (float)($total * $this->discount_total_amount / 100);
    //     //     $this->discountWithoutTax = $total - (float)($total * $this->discount_total_amount / 100);
    //     // }
    //     // Apply discount on total amount without tax
    //     if ($this->discount_total_amount) {
    //         $totalWithoutTaxDiscount = $totalWithoutTax - (float)($totalWithoutTax * $this->discount_total_amount / 100);
    //         $total = $totalWithoutTaxDiscount + $totalTax;
    //         $this->discountWithoutTax = $totalWithoutTaxDiscount;
    //     }

    //     $this->create_invoice_request['total'] = number_format(floatval(str_replace(',', '', $total)), 2, '.', '');
    //     $this->create_invoice_request['total_words'] = $this->numberToIndianRupees((float) $total);
    // }
    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 3;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });

        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems);
            $this->panel = $item->panel;
            Session::put('panel', $this->panel);
        }

        // Store the activeFeature (which is the ID for po-to-invoice) in the session
        Session::put('temp_active_feature', $activeFeature);

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }

    public function loadData()
    {
        $this->isLoading = false;
    }
    public function hideDropdown()
    {
        $this->dispatchBrowserEvent('hide-dropdown');
    }
    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
        // dd($variable, $value);

        if($variable == 'invoice_sfp'){
            $this->sfpModal = true;
            $this->invoice_id = $value;
        }

        $request = request();

        if ($this->invoice_series != null) {
            // dump($this->invoice_series);
            $request->merge(['invoice_series' => $this->invoice_series]);
        }

         // // Filter by seller_id
         if ($this->seller_id != null) {
            $request->merge(['seller_id' => $this->seller_id]);
        }

        // Filter by buyer_id
        if ($this->buyer_id != null) {
            // dump($this->buyer_id);
            $request->merge(['buyer_id' => $this->buyer_id]);
        }
        // Filter by status
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        // Filter by state in ReceiverDetails
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        // Filter by date range
        if ($this->from != null && $this->to != null) {
            $request->merge([
                'from' => $this->from,
                'to' => $this->to,
            ]);
            $this->hideDropdown();
        }

        switch ($this->persistedTemplate) {
        //     case 'sent_invoice':
        //         $challanController = new InvoiceController();
        // $tableTdData = $challanController->index($request);
        // $this->tableTdData = $tableTdData->getData()->data->data;
        // $this->currentPage = $tableTdData->getData()->data->current_page;
        // $this->paginateLinks = $tableTdData->getData()->data->links;
        // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // break;
        case 'detailed_sent_invoice':
            $challanController = new InvoiceController();
            $tableTdData = $challanController->index($request);
            $this->tableTdData = $tableTdData->getData()->data->data;
            $this->currentPage = $tableTdData->getData()->data->current_page;
            $this->paginateLinks = $tableTdData->getData()->data->links;
            $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        break;
    }
    }

    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        // $this->dispatchBrowserEvent('inputsDisabledChanged', ['value' => false]);
    }

    // public function saveRows($requestData)
    // {
    //     dd($requestData, $this->create_invoice_request);
    //     $request = request();
    //     // Update the createChallanRequest with the new data
    //     $this->create_invoice_request['order_details'] = $requestData['order_details'];
    //     $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
    //     $this->create_invoice_request['total'] = $requestData['total'];
    //     $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];

    //     $request->merge($this->create_invoice_request);
    //     // dd($request);
    //     if($this->updateForm == false)
    //     {
    //         $request->merge($this->addBuyerData);
    //         // dd($request);
    //         if($request->phone || $request->email){
    //             // dd($request);

    //         $BuyersController = new BuyersController;
    //         $response = $BuyersController->addManualBuyer($request);
    //         $result = $response->getData();

    //         }

    //         $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->where('default', "1")
    //             ->where('panel_id', '3')
    //             ->first();
    //             $invoiceSeries = $series->series_number;
    //             $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
    //             ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             // ->max('series_num');
    //             ->get();
    //                 // dd($latestSeriesNum);
    //                 // if ($latestSeriesNum->isNotEmpty()) {
    //                     // Get the maximum 'series_num' from the collection
    //                     $maxSeriesNum = $latestSeriesNum->max('series_num');

    //                     // Now $maxSeriesNum contains the maximum 'series_num'
    //                     // You can use it as needed
    //                     // echo $maxSeriesNum;
    //                     // dd($maxSeriesNum);
    //                 // }
    //             // Increment the latestSeriesNum for the new challan
    //             $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;
    //             // dd($seriesNum);
    //             // $this->create_invoice_request['invoice_series'] = $invoiceSeries;
    //             // $this->create_invoice_request['buyer'] = $buyer;
    //             // $this->create_invoice_request['buyer_id'] = json_decode($selectedUserDetails)->buyer_user_id;
    //             // $this->create_invoice_request['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //             // $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;


    //             $this->create_invoice_request['invoice_series'] = $invoiceSeries;
    //             $this->create_invoice_request['series_num'] = $invoiceNum;
    //             if (isset($result->buyer->buyer_name)) {
    //                 $this->create_invoice_request['buyer'] = $result->buyer->buyer_name;
    //                 $this->create_invoice_request['buyer_id'] = $result->buyer->buyer_user_id;
    //             } elseif (isset($request->buyer_name)) {
    //                 $this->create_invoice_request['buyer'] = $request->buyer_name;
    //                 $this->create_invoice_request['buyer_id'] = null;
    //                 $this->sendButtonDisabled = false;
    //             } else {
    //                 $this->create_invoice_request['buyer'] = 'Others';
    //                 $this->create_invoice_request['buyer_id'] = null;
    //             }

    //             // $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //             // $this->createChallanRequest['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
    //             $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;

    //     }
    //     $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
    //     foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
    //         $this->create_invoice_request['order_details'][$index]['discount'] = $this->discount_total_amount;
    //     }
    //     $request->merge($this->create_invoice_request);
    //     // dd($request);
    //     $errors = false;
    //         foreach ($request->order_details as $index => $order_detail) {
    //             // Check if 'qty' is null
    //             if (is_null($order_detail['qty'])) {
    //                 $this->addError('qty.' . $index, 'Required.');
    //                 $errors = true;
    //             }
    //             // Check if 'article' is null
    //             if (isset($order_detail['columns'])) {
    //                 foreach ($order_detail['columns'] as $column) {
    //                     if ($column['column_name'] == 'Article' && empty($column['column_value'])) {
    //                         $this->addError('article.' . $index, 'Required.');
    //                         $errors = true;
    //                     }
    //                 }
    //             }
    //         }

    //         if ($errors) {
    //             return;
    //         }
    //     $invoiceController = new InvoiceController;
    //     $response = $invoiceController->store($request);
    //     $result = $response->getData();
    //     // Check the status code from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         $this->inputsResponseDisabled = false; // Adjust the condition as needed
    //         // dd($result);
    //         if($this->create_invoice_request['buyer_id'] == null){
    //             $this->successMessage = $result->message;
    //             // $this->innerFeatureRedirect('sent_invoice', '13');
    //             return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    //         }
    //         $this->invoiceId = $result->invoice_id;
    //         $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    // }

    public function invoiceModify(Request $request)
    {
        $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
        foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
            $this->create_invoice_request['order_details'][$index]['discount'] = $this->discount_total_amount;
        }

        $request->merge($this->create_invoice_request);
        // dd($request);
        // Create instances of necessary classes
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->update($request, $this->invoiceId);
        $result = $response->getData();
        // dd($request);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->invoiceId = $result->invoice_id;

            $this->reset([ 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }



    public function modifyInvoice(Request $request)
    {
        // dd($request);
        $id = session('persistedActiveFeature');
        $this->context = 'invoice';
        $challanController = new InvoiceController();
        $challanModifyData = $challanController->show($request, $id);
        // dd($challanModifyData);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Convert the stdClass to an array
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);
        // dd($modifiedDataArray);
        $filteredOrderDetails = [];
        foreach ($modifiedDataArray['order_details'] as $orderDetail) {
            if (isset($orderDetail['qty']) && $orderDetail['qty'] > 0) {
            // Update qty with qty
            $orderDetail['qty'] = $orderDetail['qty'];
            $filteredOrderDetails[] = $orderDetail; // Include this row in the filtered data
            }
        }
        // Update order_details with the filtered and modified data
        $modifiedDataArray['order_details'] = array_values($filteredOrderDetails);

        // Merge the existing create_invoice_request with the modified data
        $this->create_invoice_request = array_merge($this->create_invoice_request, $modifiedDataArray);

        $this->challanModifyData = json_encode($modifiedDataArray);
        // dd($this->create_invoice_request);
        $this->inputsDisabled = false;

        $PanelColumnsController = new PanelColumnsController;
        $request->merge([
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });

        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);

        $this->panelColumnDisplayNames = $panelColumnDisplayNames;
        $request->merge([
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);
        $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        $this->ColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        array_push($this->ColumnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');
        $this->initializeRows($modifiedDataArray);
        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;
        $products = new ProductController;
        $units = new UnitsController;
        $unitsCollection = $units->index('sender')->original;
        $this->units = $unitsCollection->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unit' => $unit->unit,
                'short_name' => $unit->short_name,
                'is_default' => $unit->is_default,
            ];
        })->toArray();
    }

    private function initializeRows($modifiedDataArray)
    {
        // Filter out empty values from panelUserColumnDisplayNames
        $filteredNames = array_filter($this->panelUserColumnDisplayNames, function($value) {
            return !empty($value);
        });

        // Initialize dynamic fields with filtered names
        $dynamicFields = array_fill_keys($filteredNames, '');

        // Define static fields
        $staticFields = [
            'item' => '',
            'quantity' => 0,
            'rate' => 0,
            'tax' => 0,
            'total' => 0,
            'calculateTax' => true
        ];

        // Initialize rows with data from modifiedDataArray
        $this->rows = array_map(function($orderDetail) use ($dynamicFields, $staticFields) {
            // Extract columns data
            $columnsData = [];
            if (isset($orderDetail['columns'])) {
                foreach ($orderDetail['columns'] as $column) {
                    $columnsData[$column['column_name']] = $column['column_value'];
                }
            }

            return array_merge($dynamicFields, $staticFields, $columnsData, [
                'item' => $orderDetail['item_code'] ?? '',
                'quantity' => $orderDetail['qty'] ?? 0,
                'rate' => $orderDetail['rate'] ?? 0,
                'tax' => $orderDetail['tax'] ?? 0,
                'total' => $orderDetail['total_amount'] ?? 0,
                'calculateTax' => $orderDetail['calculate_tax'] ?? true
            ]);
        }, $modifiedDataArray['order_details']);
    }

    private function getColumnDisplayNames($controller, $request, $userId)
    {
        $request->merge([
            'feature_id' => 1,
            'user_id' => $userId,
        ]);

        $response = $controller->index($request);
        $data = json_decode($response->content(), true);

        return collect($data['data'])->where('feature_id', 1)->pluck('panel_column_display_name')->all();
    }

    public function disableSaveButton()
    {
        $this->isSaveButtonDisabled = true;
    }

    public function saveInvoiceModify(Request $request)
    {
        $request->merge($this->create_invoice_request);
        // dd($request);

        // Create instances of necessary classes
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->isSaveButtonDisabled = true;
            $this->invoiceId = $result->invoice_id;

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }

    public function editRows($requestData)
    {
        // dd($requestData, $this->create_invoice_request, $this->invoiceId);
        $request = request();
         // Update the create_invoice_request with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];

        $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
        foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
            $this->create_invoice_request['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }

        $request->merge($this->create_invoice_request);
        // dd($request);
        // Create instances of necessary classes
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            // $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->invoiceId = $result->invoice_id;
            $this->save = $result->message;
            $this->inputsDisabled = true;

            $this->reset([ 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function invoiceUpdate($id)
    {
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed

        $request = request();
        $InvoiceController = new InvoiceController;
        // dd($id);
        $response = $InvoiceController->show($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            // dd($result);
            $PanelColumnsController = new PanelColumnsController;

            $columnsResponse = $PanelColumnsController->index($request);
            $columnsData = json_decode($columnsResponse->content(), true);

            $filteredColumns = array_filter($columnsData['data'], function ($column) {
                return $column['feature_id'] == 12;
            });
            $panelColumnDisplayNames = array_map(function ($column) {
                return $column['panel_column_display_name'];
            }, $filteredColumns);

            $this->panelColumnDisplayNames = $panelColumnDisplayNames;

            $billTo = new ReceiversController;
            $this->billTo = $billTo->index($request)->getData()->data;
            // dd($this->billTo);
            $request = request();
            $columnFilterDataset = [
                'feature_id' => 12
            ];
            $request->merge($columnFilterDataset);
            $PanelColumnsController = new PanelColumnsController;
            $columnsResponse = $PanelColumnsController->index($request);
            $columnsData = json_decode($columnsResponse->content(), true);
            $ColumnDisplayNames = array_map(function ($column) {
                return $column['panel_column_display_name'];
            }, $columnsData['data']);

            $this->ColumnDisplayNames = $ColumnDisplayNames;
            array_push($this->ColumnDisplayNames, 'item code', 'unit', 'qty', 'rate');

            $products = new ProductController;
            $response = $products->index($request);
            $productResult = $response->getData();
            $this->products = (array) $productResult->data;


            $result->data->total_words = $this->numberToIndianRupees((float) $result->data->total);

            // dd($this->create_invoice_request);
            $this->create_invoice_request = json_encode($result->data);
            // dd($result->data);
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // $this->innerFeatureRedirect('update_challan', '1');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // redirect()->route('sender');


    }


    public function invoiceEdit()
    {
        $this->action = 'edit';
        $this->inputsDisabled = false;
        $this->inputsResponseDisabled = true; // Adjust the condition as needed
    }

    public function sendInvoiceAfterSave()
    {
        if ($this->savedInvoiceId) {
            $this->sendInvoice($this->savedInvoiceId);
        }
        $request = request();
        $this->sentInvoice($this->currentPage);
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);


        // return view('components.panel.seller.sent_invoice');
        // $this->reset();
    }
    public $createInvoiceData = [
        'rate' => null,
        'quantity' => null,
        'totalAmount' => 0,
        'rows' => [],
    ];

    public function mount()
    {
        // $this->createInvoiceInstance = new createInvoice;
        // Retrieve the persisted value from the session, if available
        $sessionId = session()->getId();
        $this->poToInvoiceId = Session::get('po_to_invoice_id');
        // Session::forget('po_to_invoice_id'); // Clear the session after retrieving the ID
        $request = request();
        $template = request('template', 'index');
        session()->put('previous_url', url()->current());
        $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute
        if (!request()->query('activeTab')) {
            $this->activeTab = 'tab1';
        }
        $PanelColumnsController = new PanelColumnsController;
        $request->merge([
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });

        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);

        $this->panelColumnDisplayNames = $panelColumnDisplayNames;
        $request->merge([
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;
        if (view()->exists('components.panel.seller.' . $template)) {

            // $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            // $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template;

            $request = request();
            $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);
            $UserResource = new UserAuthController;
            $response = $UserResource->user_details($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->mainUser = json_encode($response->user);
                // $this->successMessage = $response->message;
                $this->reset(['errorMessage']);
            } else {
                $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
            }
            // dd("ff", $this->persistedActiveFeature, $this->persistedTemplate);
            switch ($this->persistedTemplate) {
                case 'create_invoice':
                    // $query = new TeamUserController;

                    // $query = $query->index();

                    // $status = $query->getStatusCode();
                    // $query = $query->getData();

                    // if ($status === 200) {
                    //     $this->teamMembers = $query->data;
                    // } else {
                    //     $this->errorMessage = json_encode($query->errors);
                    //     $this->reset(['status', 'successMessage']);
                    // }
                    // $this->createInvoice($request);
                    break;
                case 'sent_invoice':
                    // $query = new TeamUserController;

                    // $query = $query->index();

                    // $status = $query->getStatusCode();
                    // $query = $query->getData();

                    // if ($status === 200) {
                    //     $this->teamMembers = $query->data;
                    // } else {
                    //     $this->errorMessage = json_encode($query->errors);
                    //     $this->reset(['status', 'successMessage']);
                    // }
                    // $this->sentInvoice($this->currentPage);
                    break;
                case 'detailed_sent_invoice':
                    $this->detailedSentInvoice($this->currentPage);
                    break;
                case 'deleted_sent_invoice':
                    // $this->deletedSentInvoice($request);
                    break;
                case 'invoice_design':
                    $this->invoiceDesign($request);
                    break;
                case 'modify_sent_invoice':
                    $this->modifySentInvoice($request);
                    break;
                case 'bulk_create_invoice':
                    break;
                case 'received_invoice':
                    $query = new TeamUserController;

                    $query = $query->index();

                    $status = $query->getStatusCode();
                    $query = $query->getData();

                    if ($status === 200) {
                        $this->teamMembers = $query->data;
                    } else {
                        $this->errorMessage = json_encode($query->errors);
                        $this->reset(['status', 'successMessage']);
                    }
                    $this->receivedInvoice($request);
                    break;
                case 'add_buyer':
                    // $this->addBuyer = new addBuyer;
                    break;
                case 'modify_invoice':
                    $this->modifyInvoice($request);
                    $query = new TeamUserController;

                    $query = $query->index();

                    $status = $query->getStatusCode();
                    $query = $query->getData();

                    if ($status === 200) {
                        $this->teamMembers = $query->data;
                    } else {
                        $this->errorMessage = json_encode($query->errors);
                        $this->reset(['status', 'successMessage']);
                    }
                    break;
                case 'purchase_order_seller':
                    // dd('hallo');
                    // $query = new TeamUserController;

                    // $query = $query->index();

                    // $status = $query->getStatusCode();
                    // $query = $query->getData();

                    // if ($status === 200) {
                    //     $this->teamMembers = $query->data;
                    // } else {
                    //     $this->errorMessage = json_encode($query->errors);
                    //     $this->reset(['status', 'successMessage']);
                    // }
                    // $this->purchaseOrder($this->currentPage);
                    break;
                case 'detailed_purchase_order':
                    // dd('hallo');
                    $this->detailedPurchaseOrder($this->currentPage);
                    break;
                case 'invoice_terms_and_conditions';
                    $this->invoiceTermsAndConditions($request);
                    break;

                case 'invoice_series_no':
                    $filterDataset = [
                        'panel_id' => 3,
                        'section_id' => 2,
                    ];
                    $request->merge($filterDataset);
                    $newInvoiceSeriesIndex = new PanelSeriesNumberController;
                    $request->merge(['panel_id' => '3']);
                    $data = $newInvoiceSeriesIndex->index($request);
                    $this->seriesNoData = (array) $data->getData()->data;
                    $newBuyerController = new BuyersController;

                    $request->replace([]);
                    $response = $newBuyerController->index($request);
                    $buyerData = $response->getData();
                    $this->buyerDatas = $buyerData->data;
                    break;


            }
        } else {
            $this->persistedTemplate = 'index';
            $this->persistedActiveFeature = null;
        }
        // $this->emit('updateDynamicView', 'components.panel.seller.' . $this->persistedTemplate);
    }



    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    public function handleFeatureRoute($template, $activeFeature)
    {
        $viewPath = 'components.panel.seller.' . $template;
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        $routeParams = ['template' => $this->persistedTemplate];

        // If the template is 'po-to-invoice', include the ID in the query string
        if ($template === 'po-to-invoice') {
            $id = Session::get('temp_active_feature');
            Session::forget('temp_active_feature'); // Clear the temporary session variable
            if ($id) {
                $routeParams['id'] = $id;
            }
        }

        $url = URL::route('seller', $routeParams);
        return $this->redirect($url);
    }

    public function sentInvoice($page)
    {
        $request = new Request;
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 13,
            'panel_id' => 3,
        ];
        $request->merge($columnFilterDataset);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->ColumnDisplayNames = $ColumnDisplayNames;
        $id = ''; // You can pass the $id if needed

        request()->replace([]);

         // Filter by invoice_series
         if ($request->has('invoice_series')) {
            // Split the search term into series and number
            $searchTerm = $request->invoice_series;
            $searchParts = explode('-', $searchTerm);

            if (count($searchParts) == 2) {
                $series = $searchParts[0];
                $num = $searchParts[1];

                // Perform the search
                $query->where('invoice_series', $series)
                    ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by buyer_id
        if ($this->buyer_id != null) {
            // dump($this->buyer_id);
            $request->merge(['buyer_id' => $this->buyer_id]);
        }

        // Filter by Seller id
        if ($this->seller_id != null) {
            $request->merge(['seller_id' => $this->seller_id]);
        }

        // Filter by status
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        // Filter by state in BuyerDetails
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        // Filter by date range
        if ($this->fromDate != null && $this->toDate != null) {
           $request->merge([
               'from_date' => $this->fromDate,
               'to_date' => $this->toDate,
           ]);
        }

        $this->tableTdData = [];
        $request = new Request(['page' => $page, 'perPage' => $this->perPage]);
        $challanController = new InvoiceController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // dd($this->challanFiltersData);

        $this->emit('invoiceDataReceived', $tableTdData);
    }
    public $team_user_ids = [];
    public $page = 1;
    public $perPage = 100;
    public $maxPerPage = 100;

    public function sfpInvoice()
    {

        $request = request();
        $request->merge([
            'id' => $this->team_user_ids,
            'invoice_id' => $this->invoice_id,
            'comment' => $this->comment,
        ]);
        // dd($request);
        $invoiceController = new InvoiceController;

        $response = $invoiceController->invoiceSfpCreate($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            redirect()->route('seller', ['template' => 'sent_invoice'] )->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // $this->innerFeatureRedirect('sent_invoice', '13');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        // redirect()->route('seller', ['template' => 'sent_invoice'] )->with('message', $this->successMessage ?? $this->errorMessage);
    }

    // public function poToInvoice(Request $request)
    // {
    //     $id = session('persistedActiveFeature');
    //     $poId = session('po_id');
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //     $data = PurchaseOrder::where('id', $id)->with(['orderDetails', 'orderDetails.columns', 'sellerUser', 'buyerUser'])->first();
    //     // dd($data);
    //     $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');
    //     $this->purchase_order_series = $data->purchase_order_series;
    //     $sellerUser = $data->sellerUser;
    //     // dd($sellerUser);
    //     $buyerUser = $data->buyerUser;
    //     $buyerName = $buyerUser->seller;

    //     // Extract necessary data from $sellerUser
    //     $invoiceSeries = $data->invoice_series ?? 'Not Assigned';
    //     $address = $sellerUser->address ?? null;
    //     $email = $sellerUser->email ?? null;
    //     $this->phone = $sellerUser->phone ?? null;
    //     $phone = $sellerUser->phone ?? null;

    //     $gst = $sellerUser->gst_number ?? null;
    //     $this->state = $sellerUser->state ?? null;
    //     $state = $sellerUser->state ?? null;
    //     $this->pincode = $sellerUser->pincode ?? null;
    //     $pincode = $sellerUser->pincode ?? null;
    //     $buyer = $sellerUser->name ?? 'Select Buyer';
    //     $selectedUserDetails = json_encode($sellerUser);

    //     // Call selectUser method
    //     $this->selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $buyer, $selectedUserDetails);

    //     $this->challanModifyData = json_encode($data);
    //     $modifiedDataArray = json_decode(json_encode($data), true);

    //     // Update order_details with the filtered and modified data
    //     $this->create_invoice_request = array_merge($this->create_invoice_request, $modifiedDataArray);
    //     $this->challanModifyData = json_encode($this->create_invoice_request);

    //     $this->inputsDisabled = false;

    //     $PanelColumnsController = new PanelColumnsController;
    //     $request->merge([
    //         'feature_id' => 12,
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //     ]);
    //     $columnsResponse = $PanelColumnsController->index($request);
    //     $columnsData = json_decode($columnsResponse->content(), true);

    //     $filteredColumns = array_filter($columnsData['data'], function ($column) {
    //         return $column['feature_id'] == 12;
    //     });

    //     $panelColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $filteredColumns);

    //     $this->panelColumnDisplayNames = $panelColumnDisplayNames;
    //     $request->merge([
    //         'feature_id' => 12,
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //     ]);
    //     $columnsUserResponse = $PanelColumnsController->index($request);
    //     $columnsUserData = json_decode($columnsUserResponse->content(), true);

    //     $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
    //         return $column['feature_id'] == 12;
    //     });
    //     $panelUserColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $filteredUserColumns);

    //     $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;
    //     $PanelColumnsController = new PanelColumnsController;
    //     $columnsResponse = $PanelColumnsController->index($request);
    //     $columnsData = json_decode($columnsResponse->content(), true);
    //     $ColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $columnsData['data']);

    //     $this->ColumnDisplayNames = $ColumnDisplayNames;
    //     array_push($this->ColumnDisplayNames, 'item code', 'unit', 'qty', 'rate');
    //     $units = new UnitsController;
    //     $unitsCollection = $units->index('sender')->original;
    //     $this->units = $unitsCollection->map(function ($unit) {
    //         return [
    //             'id' => $unit->id,
    //             'unit' => $unit->unit,
    //             'short_name' => $unit->short_name,
    //             'is_default' => $unit->is_default,
    //         ];
    //     })->toArray();
    // }

    public function sfpPurchaseOrder()
    {

        $request = request();
        $request->merge([
            'id' => $this->team_user_id,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        $ChallanController = new PurchaseOrderController;

        $response = $ChallanController->returnPoSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('purchase_order_seller', '14');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('sender', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);

    }

    public function SfpAccept(Request $request, $sfpId)
    {
        $receiverScreen = new InvoiceController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept Invoice.');
        }
    }
    public function SfpReject(Request $request, $sfpId)
    {
        $receiverScreen = new InvoiceController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept Invoice.');
        }
    }

    public function SfpReAccept(Request $request, $sfpId)
    {
        $receiverScreen = new PurchaseOrderController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReReject(Request $request, $sfpId)
    {
        $receiverScreen = new PurchaseOrderController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    // DETAILED VIEW OF SENT INVOICE
    public function detailedSentInvoice($page)
    {
        $request = new Request;
        $this->ColumnDisplayNames = ['Invoice No',  'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn','Details', 'Unit', 'Quantity', 'Unit Price', 'Tax', 'Total Amount'];

        request()->replace([]);

        // Filter by invoice_series
        if ($request->has('invoice_series')) {
            // Split the search term into series and number
            $searchTerm = $request->invoice_series;
            $searchParts = explode('-', $searchTerm);

            if (count($searchParts) == 2) {
                $series = $searchParts[0];
                $num = $searchParts[1];

                // Perform the search
                $query->where('invoice_series', $series)
                    ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }

        if ($this->buyer_id != null) {
            $request->merge(['buyer_id' => $this->buyer_id]);
        }
        // Filter by Seller id
        if ($this->seller_id != null) {
           $request->merge(['seller_id' => $this->seller_id]);
        }
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        // Filter by date range
        if ($this->fromDate != null && $this->toDate != null) {
           $request->merge([
               'from_date' => $this->fromDate,
               'to_date' => $this->toDate,
           ]);
        }

        $this->tableTdData = [];
        $request = new Request(['page' => $page, 'perPage' => $this->perPage]);
        $challanController = new InvoiceController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        $this->emit('invoiceDataReceived', $tableTdData);
    }

    // DELETED INVOICE
    public function deletedSentInvoice(Request $request)
    {
        $this->ColumnDisplayNames = ['Invoice No', 'PO No.', 'Buyer', 'TIme', 'Date', 'Creator', 'Quantity', 'Amount', 'State', 'Status', 'SFP', 'Comment'];
        request()->replace([]);
        if ($this->invoice_series != null) {
            // dump($this->invoice_series);
            $request->merge(['invoice_series' => $this->invoice_series]);
        }
        if ($this->buyer_id != null) {
            $request->merge(['buyer_id' => $this->buyer_id]);
        }

        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }

        $challanController = new InvoiceController();
        $invoiceData = $challanController->deletedInvoice($request);
        $this->invoiceData = $invoiceData->getData()->data;
        $this->invoiceFiltersData = json_encode($invoiceData->getData());
        $this->emit('invoiceDataReceived', $invoiceData);
    }


    public $additionalInputs = 3;
    public $invoiceDesignData = array(
        [
            'panel_id' => '1',
            'section_id' => '1',
            'feature_id' =>  '11',
            'default' => '0',
            'status' => '',
            'panel_column_default_name' => '',
            'panel_column_display_name' => '',
            'user_id' => '',
        ]

    );
    public function invoiceDesign()
    {
        $request = new Request;

        $request->merge([
            'default' => '0',
            'panel_id' => '3',
            'section_id' => '2',
            'feature_id' => '12',
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $newChallanDesign = new PanelColumnsController;
        $response = $newChallanDesign->index($request);
        $this->invoiceDesignData = $response->getData()->data;
        $this->additionalInputs = 3;
        // $this->additionalInputs = count($this->invoiceDesignData);
        // }
        // dd($this->invoiceDesignData);
        // You can add any additional processing or redirection logic here

        // After processing, you might want to reset the input fields
        // $this->reset('invoiceDesignData');
    }

    public function createChallanDesign(){
        $request = new Request;
        // dd($this->additionalInputs);
        for ($i = 0; $i <= $this->additionalInputs; $i++) {
                    $inputKey = "$i"; // Assuming the input names are column3, column4, etc.
                    // dd($inputKey);
                    // dd($this->invoiceDesignData[$i]['panel_column_default_name']);
                    if (isset($this->invoiceDesignData[$i]['panel_column_display_name'])) {
                        $panelColumnDisplay = $this->invoiceDesignData[$i]['panel_column_display_name'];
                        $panelColumnDefault = "column_$i";

                        // Define the data array for the new record

                        if (isset($this->invoiceDesignData[$i]['id'])) {
                            $data = [
                                'id' => $this->invoiceDesignData[$i]['id'],
                                'panel_id' => '3',
                                'section_id' => '2',
                                'feature_id' => '12',
                                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                                'panel_column_display_name' => $panelColumnDisplay,
                                'panel_column_default_name' => $panelColumnDefault,
                                'status' => 'active',
                            ];

                            $request->merge($data);
                            // dd($request);
                            $newChallanDesign = new PanelColumnsController;
                            $response = $newChallanDesign->update($request, $this->invoiceDesignData[$i]['id']);
                            $result = $response->getData();
                            // dd($result);
                            // Set the status code and message received from the result
                            $this->statusCode = $result->status_code;

                            if ($result->status_code === 200) {
                                $this->successMessage = $result->message;

                                $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
                            } else {
                                $this->errorMessage = json_encode($result->errors);
                            }
                        }
                    }
                }
        // dd($request);
    }

    public $invoiceIds;
    public $showButtons = false;

        public function removeFile()
    {
        $this->uploadFile = null;
        $this->emit('refreshComponent');
    }

    public function bulkInvoiceUpload()
    {
        $this->reset(['errorFileUrl', 'errorMessage', 'successMessage', 'showButtons']);

        if (!$this->uploadFile) {
            $this->dispatchBrowserEvent('show-error-message', ['message' => 'No file was uploaded.']);
            return;
        }

        $allowedMimeTypes = ['text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($this->uploadFile->getMimeType(), $allowedMimeTypes)) {
            $this->dispatchBrowserEvent('show-error-message', ['message' => 'Invalid file type. Please upload a CSV file.']);
            return;
        }

        $request = new Request();
        $requestData = [
            'file' => $this->uploadFile,
        ];
        $request->merge($requestData);

        $ChallanController = new InvoiceController;

        try {
            $response = $ChallanController->bulkInvoiceImport($request);
            $result = $response->getData();

            $this->statusCode = $result->status_code;

            switch ($this->statusCode) {
                case 200:
                    $this->handleSuccessResponse($result);
                    break;
                case 400:
                    $this->handleValidationErrors($response);
                    break;
                case 422:
                    $this->handleValidationErrors($response);
                    break;
                case 500:
                    $this->handleServerError($result);
                    break;
                default:
                    $this->handleUnknownError($result);
                    break;
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function handleSuccessResponse($result)
    {
        $this->invoiceIds = $result->data->invoice_ids;
        $this->successMessage = $result->message;
        $this->showButtons = true;
        $this->dispatchBrowserEvent('show-success-message', ['message' => $this->successMessage]);
        $this->reset(['statusCode', 'errorMessage']);
    }

    private function handleValidationErrors($response)
    {
        $errors = json_decode($response->content(), true)['errors'];
        // $errorMessage = is_array($errors) ? implode(', ', $errors) : $errors;
        $errorMessage = "Validation errors occurred, please check the uploaded file for errors. ". (isset($errors['file'])? "File: ". $errors['file'][0] : '');
        $this->dispatchBrowserEvent('show-error-message', ['message' => $errorMessage]);
        if ($this->statusCode === 422) {
            $this->errorFileUrl = $this->createErrorFile($errors);
            $this->reset('uploadFile');
        }
    }

    private function handleServerError($result)
    {
        $this->showButtons = false;
        $this->errorMessage = isset($result->error)
            ? "Error occurred while creating challans: " . $result->error
            : "An unknown error occurred while creating challans.";
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function handleUnknownError($result)
    {
        $this->errorMessage = json_encode((array) $result->errors);
        $this->showButtons = false;
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function handleException(\Exception $e)
    {
        $this->errorMessage = "An unexpected error occurred: " . $e->getMessage();
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function createErrorFile($errors)
    {
        $content = "Error Report\n\n";
        foreach ($errors as $error) {
            $content .= $error . "\n";
        }
        $fileName = 'error_report_' . time() . '.txt';
        Storage::put('public/error_reports/' . $fileName, $content);
        return Storage::url('error_reports/' . $fileName);
    }


    public function modifySentInvoice(Request $request)
    {
        $params = $request->input('updates.0.payload.params');
        $method = $params[0];
        $id = $params[1];
        // dd($request->all());
        // dd($id);
        $PanelColumnsController = new InvoiceController;
        $showData = $PanelColumnsController->show($request, $id);

        $this->showData = json_decode(json_encode($showData), true);

        // dd($this->showData);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);

        $this->ColumnDisplayNames = $ColumnDisplayNames;
        $billTo = new BuyersController;
        $this->billTo = $billTo->index($request)->getData()->data;
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);

    }

    public function sendInvoice($id)
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->send($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // $this->innerFeatureRedirect('sent_invoice', '13');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $sendChoice;
    public function sendBulkInvoice($choice)
    {
        // dd($choice);
        $request = request();
        $this->sendChoice = $choice;
        // Perform the necessary actions based on the user's choice
        if ($choice === 'send') {
            foreach($this->invoiceIds as $id){
                // dd($challanId);
                $ChallanController = new InvoiceController;

                $response = $ChallanController->send($request, $id);
                $result = $response->getData();
            }
            // $this->innerFeatureRedirect('sent_invoice', '13');
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
            // Code to send the challans immediately
            // ...
        } else {
            // Code to send the challans later
            // ...
            // $this->innerFeatureRedirect('sent_invoice', '13');
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
        }
    }

    public $selectedInvoiceId;
    public $status_comment = '';
    public function updateTimelineModal($invoiceId)
    {
        $this->selectedInvoiceId = $invoiceId;
        $this->emit('openTimelineModal');
    }

    public function addCommentSentInvoice($id)
    {
        $request = request();
        $request->merge([
            'status_comment' => $this->status_comment,

        ]);

        $InvoiceController = new InvoiceController;
        $response = $InvoiceController->addComment($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_invoice', '13');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('seller')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function reSendInvoice($id)
    {

        $request = request();
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->resend($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);


        $this->mount();
    }

    public function selfAcceptInvoice($id)
    {
        $request = request();
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->selfAccept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);


        $this->mount();
    }

    public function deleteInvoice($id)
    {

        $request = request();
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->delete($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
            return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);


        $this->mount();
    }

    public $panelColumnDisplayNames;
    public function createInvoice(Request $request)
    {


        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 12,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });
        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = $panelColumnDisplayNames;


        $request->merge([
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 12;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

        // Add from stock modal data
        $request = request();
        $columnFilterDataset = [
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        ];
        $request->merge($columnFilterDataset);
        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        $this->ColumnDisplayNames = $ColumnDisplayNames;
        array_push($this->ColumnDisplayNames, 'item code', 'location', 'unit', 'qty', 'rate');


        $request = request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
        ]);
        $products = new ProductController;
        $response = $products->index($request);
        $result = $response->getData();
        $this->products = (array) $result->data;
        $this->articles = [];
        foreach ($this->products as $product) {
            array_push($this->articles, $product->details[0]->column_value);
        }
        $this->item_codes = array_unique(array_column($this->products, 'item_code'));
        $this->locations = array_unique(array_column($this->products, 'location'));

        // dd( $this->products);

        $this->create_invoice_request['challan_date'] = now()->format('Y-m-d');
        // Set the status code and message received from the result
        // $this->statusCode = $result->status_code;
        // $this->products = [];
        // dd($this->billTo);
        $units = new UnitsController;
        $unitsCollection = $units->index('sender')->original;
        $this->units = $unitsCollection->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unit' => $unit->unit,
                'short_name' => $unit->short_name,
                'is_default' => $unit->is_default,
            ];
        })->toArray();
        $billTo = new BuyersController;
        $this->billTo = $billTo->index($request)->getData()->data;
        $this->billTo = collect($this->billTo)->sortBy(function ($item) {
            return strtolower($item->buyer_name);
        })->values()->all();
        // dd($this->billTo);
        $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');
    }
    public function filterVariable($variable, $value)
    {
        $this->{$variable} = $value;
        // dd($this->{$variable});
        $request = request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
        ]);
        // $request = new Request;
        $products = new ProductController;
        $response = $products->index($request);
        $result = $response->getData();
        $this->products = (array) $result->data;
        $this->articles = [];
        foreach ($this->products as $product) {
            array_push($this->articles, $product->details[0]->column_value);
        }
        $this->item_codes = array_unique(array_column($this->products, 'item_code'));
        $this->locations = array_unique(array_column($this->products, 'location'));

        // dd( $this->products);
    }

    public $barcodeError;

    public function updatedBarcode($value)
    {
        // dd($value);
        if (empty($value)) {
            $this->barcodeError = ''; // Clear the error message when the input is empty
        } else {
            $this->barcodeError = ''; // Clear the error message before attempting to add from barcode
            $this->addFromBarcode();
        }
    }

    public function addFromBarcode()
    {
        $request = request();
        // dd($this->barcode);
        $request->merge([
            'barcode' => $this->barcode,
        ]);
        // dd($request);
        $products = new ProductController;
        $response = $products->fetchProductByBarcode($request);
        $result = $response->getData();
        // dd($result);
        if ($result->status_code === 200) {
            $this->barcodeError = null;
            $product = $result->data;
            $columns = [];
            foreach ($product->details as $detail) {
                $columns[] = [
                    'column_name' => $detail->column_name,
                    'column_value' => $detail->column_value,
                ];
            }
            $productDetails = [
                'p_id' => $product->id,
                'unit' => $product->unit,
                'rate' => $product->rate,
                'qty' => 1,
                'item_code' => $product->item_code,
                'total_amount' => $product->rate,
                'columns' => $columns,
            ];

            $existingProductKey = array_search($product->id, array_column($this->create_invoice_request['order_details'], 'p_id'));
             if ($existingProductKey !== false) {
            // If the product already exists, increase the quantity and update the total amount
            $this->create_invoice_request['order_details'][$existingProductKey]['qty']++;
            $this->create_invoice_request['order_details'][$existingProductKey]['total_amount'] += $product->rate;
        } else {
            // If the product doesn't exist, add it to the order_details array
            if (empty($this->create_invoice_request['order_details'][0]['p_id'])) {
                $this->create_invoice_request['order_details'][0] = $productDetails;
            } else {
                $this->create_invoice_request['order_details'][] = $productDetails;
            }
        }
            $this->reset('barcode');
            // $this->calculateTotalAmount();
            // $this->calculateTotalQuantity();
        } else {
            $this->barcodeError = $result->message;
        }
    }
    public function selectFromStock($product, $key)
    {

        foreach ($this->selectedProductP_ids as $productId => $isSelected) {
            if ($this->selectedProductP_ids[$productId] == false) {
                unset($this->fromStockRequest[$key]);
                unset($this->selectedProductP_ids[$productId]);
            } else {
                $columns = [];
                // foreach ($product['details'] as $detail) {
                //     $columns[] = [
                //         'column_name' => $detail['column_name'],
                //         'column_value' => $detail['column_value'],
                //     ];
                // }
                foreach ($product['details'] as $index => $detail) {
                    if ($index >= 3 && $index <= 6) {
                        // Skip index 3 to 6
                        continue;
                    }

                    $columns[] = [
                        'column_name' => $detail['column_name'],
                        'column_value' => $detail['column_value'],
                    ];
                }

                // Retrieve the product details based on the ID and add it to the array
                // Replace this with your actual logic to fetch product details
                $productDetails = [
                    'p_id' => $product['id'],
                    'unit' => $product['unit'],
                    'rate' => $product['rate'],
                    'qty' => $product['qty'],
                    'tax' => $product['tax'] ?? null,
                    'discount' => $product['discount'] ?? null,
                    'discount_total_amount' => $product['discount_total_amount'] ?? null,
                    'item_code' => $product['item_code'],
                    'total_amount' => $product['total_amount'],
                    'columns' => $columns,
                    // Add other details here...
                ];

                $this->fromStockRequest[$key] = $productDetails;
            }
        }

        // array_push($this->fromStockRequest,$product);
        // dd($this->fromStockRequest);

    }

    // public function addFromStock()
    // {
    //     // dd(collect($this->create_invoice_request['order_details']));
    //     if (collect($this->create_invoice_request['order_details'])->first()['rate'] == 0 && collect($this->create_invoice_request['order_details'])->first()['qty'] == 0) {
    //         $orderDetails = $this->create_invoice_request['order_details'];
    //         // dd($orderDetails);
    //         // Remove the first element
    //         array_shift($orderDetails);

    //         // Update the original array if needed
    //         $this->create_invoice_request['order_details'] = $orderDetails;
    //         // dd($this->create_invoice_request['order_details']);
    //         // unset(collect($this->create_invoice_request['order_details'])->first());
    //     }
    //     // Extract existing p_ids from array1
    //     $existing_p_ids = array_column($this->create_invoice_request['order_details'], 'p_id');
    //     // dd($this->fromStockRequest);
    //     foreach ($this->fromStockRequest as $element) {
    //         $p_id = $element['p_id'];

    //         if (!in_array($p_id, $existing_p_ids)) {
    //             // dd($element);
    //             $this->create_invoice_request['order_details'][] = $element;
    //         }
    //     }
    //     // $this->updatedCreateInvoiceRequest();
    //     $this->calculateTotalQuantity();
    // }


    public function addFromStock($productIds)
    {
        // dd($productIds);
        $this->selectedProductIds = $productIds;
        // Loop through the selected product IDs
        $totalQty = 0;
        $totalAmount = 0;

        foreach ($this->selectedProductIds as $selectedProductId) {
            // Find the product details in $this->products
            $selectedProductDetails = array_filter($this->products, function ($product) use ($selectedProductId) {
                return $product['id'] == $selectedProductId;
            });
            // dd($selectedProductDetails);
            // If product details were found
            if (!empty($selectedProductDetails)) {
                // Get the first element of the array (since array_filter returns an array)
                $selectedProductDetails = reset($selectedProductDetails);

                if ($selectedProductDetails['with_tax'] == false && $selectedProductDetails['tax'] !== null ) {
                    // Calculate the rate excluding tax using the provided formula
                    // $rateWithTax = $selectedProductDetails['rate'];
                    // $taxPercentage = $selectedProductDetails['tax'];
                    // $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));
                // dd('sdf');
                    // // Round the calculated rate to two decimal places
                    // $rateWithoutTax = round($rateWithoutTax, 2);

                    // // Assign the calculated rate to the rate field
                    // $selectedProductDetails['rate'] = $rateWithoutTax;
                    // dd($selectedProductDetails['rate']);
                    $taxPercentage = $selectedProductDetails['tax'];
                    $rateWithTax = $selectedProductDetails['rate'];

                    // Add the tax to the rate
                    $rateWithTax = $rateWithTax + ($rateWithTax * $taxPercentage / 100);

                    // Round the rate with tax to two decimal places
                    $rateWithTax = round($rateWithTax, 2);

                    // Calculate the total amount by multiplying the rate with tax by the quantity
                    $totalAmount = $rateWithTax * $selectedProductDetails['qty'];

                    // Assign the calculated total amount to the total_amount field
                    $selectedProductDetails['total_amount'] = $totalAmount;
                    $dataToMerge = [
                        'p_id' => $selectedProductDetails['id'],
                        'unit' => $selectedProductDetails['unit'],
                        'rate' => $selectedProductDetails['rate'],
                        'qty' => $selectedProductDetails['qty'],
                        'tax' => $selectedProductDetails['tax'],
                        'total_amount' => $selectedProductDetails['total_amount'],
                        'item_code' => $selectedProductDetails['item_code'],
                        'columns' => $selectedProductDetails['details'],
                    ];
                }elseif($selectedProductDetails['with_tax'] && $selectedProductDetails['tax'] !== null )
                {

                    $this->calculateTax = false;
                    $dataToMerge = [
                        'p_id' => $selectedProductDetails['id'],
                        'unit' => $selectedProductDetails['unit'],
                        'rate' => $selectedProductDetails['rate'],
                        'qty' => $selectedProductDetails['qty'],
                        'tax' => $selectedProductDetails['tax'],
                        'total_amount' => $selectedProductDetails['rate'] * $selectedProductDetails['qty'],
                        'item_code' => $selectedProductDetails['item_code'],
                        'columns' => $selectedProductDetails['details'],
                    ];
                }else
                {
                    $dataToMerge = [
                        'p_id' => $selectedProductDetails['id'],
                        'unit' => $selectedProductDetails['unit'],
                        'rate' => $selectedProductDetails['rate'],
                        'qty' => $selectedProductDetails['qty'],
                        'tax' => $selectedProductDetails['tax'],
                        'total_amount' => $selectedProductDetails['rate'] * $selectedProductDetails['qty'],
                        'item_code' => $selectedProductDetails['item_code'],
                        'columns' => $selectedProductDetails['details'],
                    ];
                }

                // Check if the product is already in $this->createChallanRequest['order_details']
                $productExists = array_filter($this->create_invoice_request['order_details'], function ($product) use ($dataToMerge) {
                    return isset($product['p_id']) && $product['p_id'] == $dataToMerge['p_id'];
                });

                // If the product is not already in $this->create_invoice_request['order_details']
                if (empty($productExists)) {
                    $replaced = false;
                    foreach ($this->create_invoice_request['order_details'] as $key => $value) {
                        if ($value['rate'] == null && $value['qty'] == null) {
                            $this->create_invoice_request['order_details'][$key] = $dataToMerge;
                            $replaced = true;
                            break;
                        }
                    }
                    if (!$replaced) {
                        $this->create_invoice_request['order_details'][] = $dataToMerge;
                    }
                     // Update total quantity and total amount
                     $totalQty += $dataToMerge['qty'];
                     $totalAmount += $dataToMerge['total_amount'];


                }
            }
        }

        // Assign updated totals to the create_invoice_request array
        $this->create_invoice_request['total_qty'] = $totalQty;
        $this->create_invoice_request['total'] = $totalAmount;
        // $this->selectedProductIds = [];

        // $this->updatedCreateInvoiceRequest();
        // $this->calculateTotalQuantity();
    }

    // Public getter method to access selectedUserDetailsData
    public function getSelectedUserDetails()
    {
        return $this->selectedUserDetailsData;
    }
    public $isNewAddressSelected;

    public function updateSeriesNumber($newSeriesNumber)
    {
        $this->selectedUser['invoiceNumber'] = $newSeriesNumber;
        // dd($this->selectedUser);
        $this->disabledButtons = true;
    }

    // public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $buyer, $selectedUserDetails)
    public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $buyer, $selectedUserDetails)
    {
        // dd($selectedUserDetails, $buyer, $email, $phone, $gst, $state, $address, $invoiceSeries);
        try {
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();

            if ($invoiceSeries == 'Not Assigned') {
                if ($series == null) {
                    throw new \Exception('Please add one default Series number');
                }
                $invoiceSeries = $series->series_number;
                $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
                // dd($seriesNum);
            } else {
                $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            }

            $this->inputsDisabled = false; // Adjust the condition as needed
            $this->selectedUser = [
                "invoiceSeries" => $invoiceSeries,
                "invoiceNumber" => $seriesNum,
                "address" => $address,
                "buyer_name" => $buyer,
                "email" => $email,
                "phone" => $phone,
                "gst" => $gst
            ];

            // Decode $selectedUserDetails once
            $decodedUserDetails = json_decode($selectedUserDetails);
            // dd($decodedUserDetails);
            $this->buyerName = $this->selectedUser['buyer_name'];
            $this->create_invoice_request['invoice_series'] = $invoiceSeries;
            $this->create_invoice_request['series_num'] = $seriesNum;
            $this->create_invoice_request['buyer'] = $buyer;
            $this->create_invoice_request['buyer_id'] = $decodedUserDetails->buyer_user_id;
            $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;
            $this->selectedUserDetails = $decodedUserDetails->user->details;
            $this->city = $decodedUserDetails->city;
            $this->state = $decodedUserDetails->state;
            $this->pincode = $decodedUserDetails->pincode;
            $this->inputsDisabled = false; // Adjust the condition as needed

            // Fetch billTo data
            $request = request();
            $billTo = new BuyersController;
                $this->billTo = $billTo->index($request)->getData()->data;
                $this->billTo = collect($this->billTo)->sortBy(function ($item) {
                    return strtolower($item->buyer_name);
                })->values()->all();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in selectUser method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while processing your request.';
            return;
        }
    }

    public function selectUserAddress($selectedUserDetail, $selectedUserDetails)
    {
        // $selectedUserDetails = json_decode($selectedUserDetails);
        $selectedUserDetail = json_decode($selectedUserDetail);
        // dd($selectedUserDetails);
        // $this->selectedUser = [
        //     // "challanSeries" => $challanSeries,
        //     // "seriesNumber" => $seriesNum,
        //     "address" => $selectedUserDetails->address,
        //     // "email" => $selectedUserDetails->email,
        //     "phone" => $selectedUserDetails->phone,
        //     "gst" => $selectedUserDetails->gst_number
        // ];

        $this->selectedUser['address'] = $selectedUserDetail->address;
        $this->selectedUser['phone'] = $selectedUserDetail->phone;
        $this->selectedUser['gst'] = $selectedUserDetail->gst_number;
        $this->create_invoice_request['buyer_detail_id'] = $selectedUserDetail->id;

        $this->selectedUserDetails = json_decode($selectedUserDetails);

        // dd($this->selectedUser);
        $request = request();

        $billTo = new BuyersController;
        $this->billTo = $billTo->index($request)->getData()->data;
        // dd($this->selectedUser);



    }



    // create invoice
    public function addRow()
    {
        $this->create_invoice_request['order_details'][] = [
            'unit' => '',
            'rate' => null,
            'qty' => null,
            'details' => null,
            'discount' => null,
            'total_amount' => null,
            'tax' => null,
            'cgst' => null, // CGST amount
            'sgst' => null, // SGST amount
            'total_with_tax' => null,
            'toalTax' => null,
            'totalSales' => null,
            'total_tax' => null,
            'total_without_tax' => null,
            'net_price' => null,
            'toalTaxRate' => null,
            'cgst_rate' => null, // Initialize CGST rate
            'sgst_rate' => null, // Initialize SGST rate

            'columns' => [
                [
                    'column_name' => '',
                    'column_value' => '',
                ]
            ],
        ];
    }

    public $barcode;
    public function removeRow($index)
    {
        // unset($this->rows[$index]);
        // $this->rows = array_values($this->rows);
        // Remove the specified row
        unset($this->create_invoice_request['order_details'][$index]);

        // Reindex the array to ensure sequential keys
        $this->create_invoice_request['order_details'] = array_values($this->create_invoice_request['order_details']);
    }

    public function calculateTotalQuantity()
    {
        $totalQuantity = 0;

        foreach ($this->create_invoice_request['order_details'] as $row) {
            if (isset($row['qty'])) {
                $totalQuantity += (int) $row['qty'];
            }
        }

        $this->create_invoice_request['total_qty'] = $totalQuantity;
    }


    // public function updateTotalAmount($index)
    // {
    //     if (isset($this->create_invoice_request['order_details'][$index]['rate']) && isset($this->create_invoice_request['order_details'][$index]['qty'])) {
    //         $rate = $this->create_invoice_request['order_details'][$index]['rate'];
    //         $qty = $this->create_invoice_request['order_details'][$index]['qty'];
    //         $discount = $this->create_invoice_request['order_details'][$index]['discount'];

    //         $totalAmountWithoutDiscount = $rate * $qty;

    //         $discountAmount = $totalAmountWithoutDiscount * ($discount / 100);
    //         $totalAmount = $totalAmountWithoutDiscount - $discountAmount;

    //         $this->create_invoice_request['order_details'][$index]['total_amount'] = $totalAmount;
    //         // Calculate the total amount for the entire order after updating this row.
    //         $this->calculateTotalAmount();
    //         $this->calculateTotalQuantity();
    //         $this->discountEntered($index);
    //         // dd($this->create_invoice_request['order_details'][$index]['total_amount']);
    //     }
    // }
    public $netPrice;

    // CORRECT CODE
    public function updateTotalAmount($index)
    {
        if (isset($this->create_invoice_request['order_details'][$index]['rate']) && isset($this->create_invoice_request['order_details'][$index]['qty']) ) {
            $rate = $this->create_invoice_request['order_details'][$index]['rate'];
            $qty = $this->create_invoice_request['order_details'][$index]['qty'];
            if(isset($this->create_invoice_request['order_details'][$index]['tax']))
            {

                $taxRate = $this->create_invoice_request['order_details'][$index]['tax'] ?? null;
                // dd($taxRate);
            }

            $totalAmountWithoutTax = $rate * $qty;

            // Calculate tax based on the $calculateTax property
            $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * (isset($taxRate) / 100)) : 0;

            // Calculate GST Amount
            $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax * (100 / (100 + isset($taxRate))));
            // dd($gstAmount);
            // Calculate Net Price
            $netPrice = $totalAmountWithoutTax - $gstAmount;

            $taxWithNetPrice = $totalAmountWithoutTax - $netPrice;
            // Calculate CGST and SGST based on the tax rate
            if(isset($taxRate)) {
                $cgstRate = $taxRate / 2;
                $sgstRate = $taxRate / 2;
            }
            $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($taxWithNetPrice / 2);
            // dd($cgstWithRate);
            $sgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($taxWithNetPrice / 2);
            $totalTaxRate = isset($taxRate);

            $existingTaxAmount = 0;

            foreach ($this->create_invoice_request['order_details'] as $key => $row)
            {
                if ($key != $index && isset($row['tax']) && $row['tax'] == isset($taxRate))
                {
                    $existingTaxAmount += $row['total_tax'];
                }
            }

            $taxAmount += $existingTaxAmount;
            if(isset($tax)){
            foreach ($this->create_invoice_request['order_details'] as $key => $row) {
                if ($key != $index && $row['tax'] == isset($taxRate)) {
                    $this->create_invoice_request['order_details'][$key]['total_tax'] = $taxAmount;
                    $this->create_invoice_request['order_details'][$key]['cgst_rate'] = isset($taxRate) / 2;
                    $this->create_invoice_request['order_details'][$key]['sgst_rate'] = isset($taxRate) / 2;
                    $this->create_invoice_request['order_details'][$key]['cgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
                    $this->create_invoice_request['order_details'][$key]['sgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
                }
            }
        }
            // $textRateText = $taxRate !== null ?  "{$taxRate}%" : "0%";
            // Display the total amount without tax in the desired format for your blade file
            if(isset($taxRate)){


            $totalSaleText = "Total Sale at". ($taxRate !== null ?  "{$taxRate}%" : "0%") ." : " . number_format($totalAmountWithoutTax, 2);
            $cgstText = "CGST at {$cgstRate}% : " . number_format($cgstWithRate, 2);
            $sgstText = "SGST at {$sgstRate}% : " . number_format($sgstWithRate, 2);

            // Store these formatted values in the data for use in your blade file
            $this->create_invoice_request['order_details'][$index]['total_sale_text'] = $totalSaleText;
            $this->create_invoice_request['order_details'][$index]['cgst_text'] = $cgstText;
            $this->create_invoice_request['order_details'][$index]['sgst_text'] = $sgstText;
        }
            // Store the GST Amount and Net Price
            $this->create_invoice_request['order_details'][$index]['gst_amount'] = $gstAmount;
            $this->create_invoice_request['order_details'][$index]['net_price'] = $netPrice;

            $this->create_invoice_request['order_details'][$index]['total_without_tax'] = $totalAmountWithoutTax;
            $this->create_invoice_request['order_details'][$index]['total_amount'] = $totalAmountWithoutTax + $taxAmount;
            $this->create_invoice_request['order_details'][$index]['cgst_rate'] = $cgstRate;
            $this->create_invoice_request['order_details'][$index]['sgst_rate'] = $sgstRate;
            $this->create_invoice_request['order_details'][$index]['cgst'] = $cgstWithRate;
            $this->create_invoice_request['order_details'][$index]['sgst'] = $sgstWithRate;
            $this->create_invoice_request['order_details'][$index]['total_tax'] = $taxAmount;

            // $this->calculateTotalAmount();
            // $this->calculateTotalQuantity();
            // $this->calculateTotalSales();
            // $this->discountEntered($index);
        }
    }

//     public function updateTotalAmount($index)
// {
//     if (isset($this->create_invoice_request['order_details'][$index]['rate']) && isset($this->create_invoice_request['order_details'][$index]['qty'])) {
//         $rate = $this->create_invoice_request['order_details'][$index]['rate'];
//         $qty = $this->create_invoice_request['order_details'][$index]['qty'];
//         $taxRate = $this->create_invoice_request['order_details'][$index]['tax'] ?? null;
//         // dd($this->selectedUser['state']);
//         $totalAmountWithoutTax = $rate * $qty;
//         // dd($totalAmountWithoutTax);
//         // Calculate tax based on the $calculateTax property
//         $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * ($taxRate / 100)) : 0;

//         // Calculate GST Amount
//         $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax * (100 / (100 + $taxRate)));

//         // Calculate Net Price
//         $netPrice = $totalAmountWithoutTax - $gstAmount;

//         $taxWithNetPrice = $totalAmountWithoutTax - $netPrice;

//         // Check if the state is Uttar Pradesh for IGST calculation
//         if (strtoupper($this->selectedUser['state']) == 'UTTAR PRADESH') {
//             // Tax is dynamic, retrieve it from the current order detail
//             $taxRate = $this->create_invoice_request['order_details'][$index]['tax'] ?? 0;

//             // Calculate IGST
//             $igstRate = $taxRate;
//             $igstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($igstRate / 100)) : ($taxWithNetPrice);

//             // Update IGST related values in the current row
//             $this->create_invoice_request['order_details'][$index]['igst_rate'] = $igstRate;
//             $this->create_invoice_request['order_details'][$index]['igst'] = $this->calculateTax ? $igstWithRate : ($netPrice);

//             // Set CGST and SGST related values to zero for IGST
//             $this->create_invoice_request['order_details'][$index]['cgst_rate'] = 0;
//             $this->create_invoice_request['order_details'][$index]['sgst_rate'] = 0;
//             $this->create_invoice_request['order_details'][$index]['cgst'] = 0;
//             $this->create_invoice_request['order_details'][$index]['sgst'] = 0;


//         } else {

//         // Calculate CGST and SGST based on the tax rate
//         $cgstRate = $taxRate / 2;
//         $sgstRate = $taxRate / 2;
//         $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($taxWithNetPrice / 2);
//         $sgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($taxWithNetPrice / 2);
//         $totalTaxRate = $taxRate;

//         $existingTaxAmount = 0;
//         foreach ($this->create_invoice_request['order_details'] as $key => $row) {
//             if ($key != $index && isset($row['tax']) && $row['tax'] == $taxRate) {
//                 $existingTaxAmount += $row['total_tax'];
//             }
//         }


//         $taxAmount += $existingTaxAmount;

//         // Calculate discount based on the entered discount percentage
//         $this->calculateDiscount($index);

//         foreach ($this->create_invoice_request['order_details'] as $key => $row) {
//             if ($key != $index && isset($row['tax']) && $row['tax'] == $taxRate) {
//                 $this->create_invoice_request['order_details'][$key]['total_tax'] = $taxAmount;
//                 $this->create_invoice_request['order_details'][$key]['cgst_rate'] = $taxRate / 2;
//                 $this->create_invoice_request['order_details'][$key]['sgst_rate'] = $taxRate / 2;
//                 $this->create_invoice_request['order_details'][$key]['cgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
//                 $this->create_invoice_request['order_details'][$key]['sgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
//             }
//         }

//         // Update the total amount with the applied discount
//         $discountAmount = $this->create_invoice_request['order_details'][$index]['discount_amount'];
//         $this->create_invoice_request['order_details'][$index]['total_amount'] = $totalAmountWithoutTax + $taxAmount - $discountAmount;

//         // Display the total amount without tax in the desired format for your blade file
//         $totalSaleText = "Total Sale at {$taxRate}% : " . number_format($totalAmountWithoutTax, 2);
//         $cgstText = "CGST at {$cgstRate}% : " . number_format($cgstWithRate, 2);
//         $sgstText = "SGST at {$sgstRate}% : " . number_format($sgstWithRate, 2);

//         // Store these formatted values in the data for use in your blade file
//         $this->create_invoice_request['order_details'][$index]['total_sale_text'] = $totalSaleText;
//         $this->create_invoice_request['order_details'][$index]['cgst_text'] = $cgstText;
//         $this->create_invoice_request['order_details'][$index]['sgst_text'] = $sgstText;

//         // Store the GST Amount and Net Price
//         $this->create_invoice_request['order_details'][$index]['gst_amount'] = $gstAmount;
//         $this->create_invoice_request['order_details'][$index]['net_price'] = $netPrice;

//         $this->create_invoice_request['order_details'][$index]['total_without_tax'] = $totalAmountWithoutTax;
//         $this->create_invoice_request['order_details'][$index]['cgst_rate'] = $cgstRate;
//         $this->create_invoice_request['order_details'][$index]['sgst_rate'] = $sgstRate;
//         $this->create_invoice_request['order_details'][$index]['cgst'] = $cgstWithRate;
//         $this->create_invoice_request['order_details'][$index]['sgst'] = $sgstWithRate;
//         $this->create_invoice_request['order_details'][$index]['total_tax'] = $taxAmount;
//     }
//         $this->calculateTotalAmount();
//         $this->calculateTotalQuantity();
//         $this->calculateTotalSales();
//         $this->discountEntered($index);
//     }
// }

public function calculateDiscount($index)
{
    $rate = $this->create_invoice_request['order_details'][$index]['rate'];
    $qty = $this->create_invoice_request['order_details'][$index]['qty'];
    $discountPercentage = $this->create_invoice_request['order_details'][$index]['discount']?? null;

    // Calculate discount amount based on the entered discount percentage
    $discountAmount = ($rate * $qty * $discountPercentage) / 100;

    // Update the discount amount in the data
    $this->create_invoice_request['order_details'][$index]['discount_amount'] = $discountAmount;
}

//     public function updateTotalAmount($index)
//     {
//         if (isset($this->create_invoice_request['order_details'][$index]['rate']) && isset($this->create_invoice_request['order_details'][$index]['qty'])) {
//             $rate = $this->create_invoice_request['order_details'][$index]['rate'];
//             $qty = $this->create_invoice_request['order_details'][$index]['qty'];
//             $taxRate = $this->create_invoice_request['order_details'][$index]['tax'];

//             $totalAmountWithoutTax = $rate * $qty;

//             // Calculate tax based on the $calculateTax property
//             $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * ($taxRate / 100)) : 0;

//             // Calculate GST Amount
//             $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax * (100 / (100 + $taxRate)));

//             // Calculate Net Price
//             $netPrice = $totalAmountWithoutTax - $gstAmount;

//             $taxWithNetPrice = $totalAmountWithoutTax - $netPrice;
//             // Calculate CGST and SGST based on the tax rate
//             $cgstRate = $taxRate / 2;
//             $sgstRate = $taxRate / 2;
//             $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($taxWithNetPrice / 2);
//             $sgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($taxWithNetPrice / 2);
//             $totalTaxRate = $taxRate;

//             $existingTaxAmount = 0;

//             foreach ($this->create_invoice_request['order_details'] as $key => $row) {
//                 if ($key != $index && $row['tax'] == $taxRate) {
//                     $existingTaxAmount += $row['total_tax'];
//                 }
//             }

//             $taxAmount += $existingTaxAmount;

//             // Calculate discount based on the entered discount percentage
//             $this->calculateDiscount($index);

//             foreach ($this->create_invoice_request['order_details'] as $key => $row) {
//                 if ($key != $index && $row['tax'] == $taxRate) {
//                     $this->create_invoice_request['order_details'][$key]['total_tax'] = $taxAmount;
//                     $this->create_invoice_request['order_details'][$key]['cgst_rate'] = $taxRate / 2;
//                     $this->create_invoice_request['order_details'][$key]['sgst_rate'] = $taxRate / 2;
//                     $this->create_invoice_request['order_details'][$key]['cgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
//                     $this->create_invoice_request['order_details'][$key]['sgst'] = $this->calculateTax ? ($taxAmount / 2) : ($netPrice / 2);
//                 }
//             }

//             // Display the total amount without tax in the desired format for your blade file
//             $totalSaleText = "Total Sale at {$taxRate}% : " . number_format($totalAmountWithoutTax, 2);
//             $cgstText = "CGST at {$cgstRate}% : " . number_format($cgstWithRate, 2);
//             $sgstText = "SGST at {$sgstRate}% : " . number_format($sgstWithRate, 2);

//             // Store these formatted values in the data for use in your blade file
//             $this->create_invoice_request['order_details'][$index]['total_sale_text'] = $totalSaleText;
//             $this->create_invoice_request['order_details'][$index]['cgst_text'] = $cgstText;
//             $this->create_invoice_request['order_details'][$index]['sgst_text'] = $sgstText;

//             // Store the GST Amount and Net Price
//             $this->create_invoice_request['order_details'][$index]['gst_amount'] = $gstAmount;
//             $this->create_invoice_request['order_details'][$index]['net_price'] = $netPrice;

//             $this->create_invoice_request['order_details'][$index]['total_without_tax'] = $totalAmountWithoutTax;
//             $this->create_invoice_request['order_details'][$index]['total_amount'] = $totalAmountWithoutTax + $taxAmount;
//             $this->create_invoice_request['order_details'][$index]['cgst_rate'] = $cgstRate;
//             $this->create_invoice_request['order_details'][$index]['sgst_rate'] = $sgstRate;
//             $this->create_invoice_request['order_details'][$index]['cgst'] = $cgstWithRate;
//             $this->create_invoice_request['order_details'][$index]['sgst'] = $sgstWithRate;
//             $this->create_invoice_request['order_details'][$index]['total_tax'] = $taxAmount;

//             $this->calculateTotalAmount();
//             $this->calculateTotalQuantity();
//             $this->calculateTotalSales();
//             $this->discountEntered($index);
//         }
//     }
//     public function calculateDiscount($index)
// {
//     $rate = $this->create_invoice_request['order_details'][$index]['rate'];
//     $qty = $this->create_invoice_request['order_details'][$index]['qty'];
//     $discountPercentage = $this->create_invoice_request['order_details'][$index]['discount'];

//     // Calculate discount amount based on the entered discount percentage
//     $discountAmount = ($rate * $qty * $discountPercentage) / 100;

//     // Update the discount amount in the data
//     $this->create_invoice_request['order_details'][$index]['discount_amount'] = $discountAmount;

//     // Update the total amount considering the discount
//     $this->create_invoice_request['order_details'][$index]['total_amount'] = ($rate * $qty) - $discountAmount;
// }
    // public function updateTotalAmount($index)
    // {
    //     if (isset($this->create_invoice_request['order_details'][$index]['rate']) && isset($this->create_invoice_request['order_details'][$index]['qty'])) {
    //         $rate = $this->create_invoice_request['order_details'][$index]['rate'];
    //         $qty = $this->create_invoice_request['order_details'][$index]['qty'];
    //         $taxRate = $this->create_invoice_request['order_details'][$index]['tax'];


    //         $totalAmountWithoutTax = $rate * $qty;


    //         // Calculate tax based on the $calculateTax property
    //         $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * ($taxRate / 100)) : 0;

    //         // Calculate CGST and SGST based on the tax rate
    //         $cgstRate = $taxRate / 2;
    //         $sgstRate = $taxRate / 2;
    //         $cgstWithRate = $this->calculateTax ? ($taxAmount / 2) : 0;
    //         $sgstWithRate = $this->calculateTax ? ($taxAmount / 2) : 0;


    //         $totalTaxRate = $taxRate;
    //         $existingTaxAmount = 0;


    //         foreach ($this->create_invoice_request['order_details'] as $key => $row) {
    //             if ($key != $index && $row['tax'] == $taxRate) {
    //                 $existingTaxAmount += $row['total_tax'];
    //             }
    //         }


    //         $taxAmount += $existingTaxAmount;


    //         foreach ($this->create_invoice_request['order_details'] as $key => $row) {
    //             if ($key != $index && $row['tax'] == $taxRate) {
    //                 $this->create_invoice_request['order_details'][$key]['total_tax'] = $taxAmount;
    //                 $this->create_invoice_request['order_details'][$key]['cgst_rate'] = $taxRate / 2;
    //                 $this->create_invoice_request['order_details'][$key]['sgst_rate'] = $taxRate / 2;
    //                 $this->create_invoice_request['order_details'][$key]['cgst'] = $this->calculateTax ? ($taxAmount / 2) : 0;
    //                 $this->create_invoice_request['order_details'][$key]['sgst'] = $this->calculateTax ? ($taxAmount / 2) : 0;
    //             }
    //         }


    //         $this->create_invoice_request['order_details'][$index]['total_without_tax'] = number_format($totalAmountWithoutTax, 2);
    //         $this->create_invoice_request['order_details'][$index]['total_tax'] = $taxAmount;
    //         $this->create_invoice_request['order_details'][$index]['cgst_rate'] = $cgstRate;
    //         $this->create_invoice_request['order_details'][$index]['sgst_rate'] = $sgstRate;
    //         $this->create_invoice_request['order_details'][$index]['cgst'] = $cgstWithRate;
    //         $this->create_invoice_request['order_details'][$index]['sgst'] = $sgstWithRate;


    //         $this->calculateTotalAmount();
    //         $this->calculateTotalQuantity();
    //         $this->calculateTotalSales();
    //         $this->recalculateTotal();
    //     }
    // }



    // correct code
    public function recalculateTotalAmounts()
    {
        foreach ($this->create_invoice_request['order_details'] as $key => &$row) {
            // Calculate total amount without tax
            $totalAmountWithoutTax = $row['rate'] * $row['qty'];
            // dd($totalAmountWithoutTax);
            // Calculate tax based on the $calculateTax property
           $taxAmount = 0;
            $gstAmount = $totalAmountWithoutTax;

            if (isset($row['tax'])) {
                $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * ($row['tax'] / 100)) : 0;
                $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax * (100 / (100 + ($row['tax']))));
            }

            // Calculate Net Price
            $netPrice = $totalAmountWithoutTax - $gstAmount;

            // Calculate CGST and SGST based on the tax rate
            if (isset($row['tax'])) {
                $cgstRate = $row['tax'] / 2;
                $sgstRate = $row['tax'] / 2;
            }
            if (isset($cgstRate)) {
                $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($netPrice / 2);
            }
            if (isset($sgstRate)) {
                $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($netPrice / 2);
            }
            // $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($netPrice / 2);
            // $sgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($netPrice / 2);

            // Update the item's fields
            $row['total_without_tax'] = $totalAmountWithoutTax;
            $row['gst_amount'] = $gstAmount;
            $row['net_price'] = $netPrice;
            $row['total_amount'] = $totalAmountWithoutTax + $taxAmount;
            $row['cgst_rate'] = (isset($cgstRate));
            $row['sgst_rate'] =  (isset($sgstRate));
            $row['cgst'] = isset($cgstWithRate);
            $row['sgst'] = isset($sgstWithRate);

            // Update the item in the order_details array
            $this->create_invoice_request['order_details'][$key] = $row;
        }
    }

    //     public function recalculateTotalAmounts()
    // {
    //     foreach ($this->create_invoice_request['order_details'] as $key => &$row) {
    //         $totalAmountWithoutTax = $row['rate'] * $row['qty'];
    //         $taxRate = $row['tax'];

    //         // Calculate tax based on the $calculateTax property
    //         $taxAmount = $this->calculateTax ? ($totalAmountWithoutTax * ($taxRate / 100)) : 0;

    //         // Calculate GST Amount
    //         $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax * (100 / (100 + $taxRate)));

    //         // Calculate Net Price
    //         $netPrice = $totalAmountWithoutTax - $gstAmount;

    //         $taxWithNetPrice = $totalAmountWithoutTax - $netPrice;

    //         // Calculate CGST and SGST based on the tax rate
    //         $cgstRate = $taxRate / 2;
    //         $sgstRate = $taxRate / 2;
    //         $cgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($cgstRate / 100)) : ($taxWithNetPrice / 2);
    //         $sgstWithRate = $this->calculateTax ? ($totalAmountWithoutTax * ($sgstRate / 100)) : ($taxWithNetPrice / 2);

    //         // Calculate discount based on the entered discount percentage
    //         $discountPercentage = $row['discount'];
    //         $discountAmount = $this->calculateDiscountAmount($totalAmountWithoutTax, $discountPercentage);

    //         // Update the item's fields
    //         $row['total_without_tax'] = $totalAmountWithoutTax;
    //         $row['total_amount'] = $totalAmountWithoutTax + $taxAmount - $discountAmount;
    //         $row['cgst_rate'] = $cgstRate;
    //         $row['sgst_rate'] = $sgstRate;
    //         $row['cgst'] = $cgstWithRate;
    //         $row['sgst'] = $sgstWithRate;

    //         // Update the item in the order_details array
    //         $this->create_invoice_request['order_details'][$key] = $row;
    //     }
    // }

    // public function calculateDiscountAmount($amount, $discountPercentage)
    // {
    //     return $amount * ($discountPercentage / 100);
    // }


    // public function recalculateTotal()
    // {
    //     $index = '';
    //     $this->updateTotalAmount($index);
    //     $this->recalculateTotalAmounts();
    //     $this->calculateTotalAmount();
    //     // $this->calculateDiscountAmount();
    // }

    public function calculateTotalDiscount()
    {
        $totalDiscount = 0;

        foreach ($this->create_invoice_request['order_details'] as $row) {
            if (isset($row['discount'])) {
                $totalDiscount += (float) $row['discount'];
            }
        }

        $this->create_invoice_request['total_discount'] = $totalDiscount;
    }

    public function discountEntered($index)
    {
        $this->discountEntered = true;
    }
    public function calculateTotalSales()
    {
        $totalSales = 0;


        foreach ($this->create_invoice_request['order_details'] as $row) {
            if (isset($row['total_amount']) && is_numeric($row['total_amount'])) {
                $totalSales += $row['total_amount'];
            }
        }


        $this->totalSales = $totalSales;
    }


    public function calculateTotalTax()
    {
        $totalTax = 0;

        foreach ($this->create_invoice_request['order_details'] as $row) {
            if (isset($row['tax_amount'])) {
                $totalTax += (float) $row['tax_amount'];
            }
        }

        $this->create_invoice_request['total_tax'] = $totalTax;
    }


    // public function calculateTotalAmount()
    // {
    //     $total = 0;

    //     foreach ($this->create_invoice_request['order_details'] as $row) {
    //         if (isset($row['rate']) && isset($row['qty'])) {
    //             $total += (float) $row['total_amount'];
    //         }
    //     }

    //     $this->create_invoice_request['total'] = $total;
    //     $this->create_invoice_request['total_words'] = $this->numberToIndianRupees((float) $total);
    // }



    // Add Buyer Manually
    // public function addBuyerManually(Request $request)
    // {
    //     $validator = Validator::make($this->addBuyerData, [
    //         'email' => 'nullable|email|unique:users,email',
    //         'phone' => 'nullable|string|unique:users,phone',
    //     ]);


    //     $validator->validate();
    //     $request->merge($this->addBuyerData);
    //     // dd($request);
    //     $BuyersController = new BuyersController;
    //     $response = $BuyersController->addManualBuyer($request);
    //     $result = $response->getData();

    //     $this->reset(['statusCode', 'message', 'errors', 'validationErrorsJson']);

    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status;
    //     // $this->message = $result->message;
    //     // $this->validationErrorsJson = json_encode($result->errors);
    //     if ($this->statusCode === 200) {
    //         $this->successMessage = $result->message;
    //         $this->reset(['addBuyerData', 'statusCode', 'message', 'errors', 'validationErrorsJson']);
    //         // $this->reset(['create_invoice_request', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //         $this->reset(['statusCode', 'message', 'validationErrorsJson']);
    //     }
    // }


    public $activeTab;

    public function setActiveTab($tab)
    {
        // dd($tab);
        $this->activeTab = $tab;
        // dd($this->activeTab);
    }
    public function validateAndAddBuyer()
    {
        $this->validate([
            'addBuyerData.buyer_name' => 'required',
            'addBuyerData.address' => 'required',
            'addBuyerData.pincode' => 'required|numeric|digits:6',
            'addBuyerData.city' => 'required',
            'addBuyerData.state' => 'required',
            'addBuyerData.email' => 'nullable|email|unique:users,email',
            'addBuyerData.phone' => 'nullable|string|size:10|unique:users,phone',

        ], [
            'addBuyerData.buyer_name.required' => 'The buyer name is required.',
            'addBuyerData.address.required' => 'The address is required.',
            'addBuyerData.pincode.required' => 'The pincode is required.',
            'addBuyerData.pincode.numeric' => 'The pincode must be a number.',
            'addBuyerData.pincode.digits' => 'The pincode must be 6 digits.',
            'addBuyerData.city.required' => 'The city is required.',
            'addBuyerData.state.required' => 'The state is required.',
            'addBuyerData.email.email' => 'The email must be a valid email address.',
            'addBuyerData.email.unique' => 'The email has already been taken.',
            'addBuyerData.phone.string' => 'The phone number must be a string.',
            'addBuyerData.phone.size' => 'The phone number must be 10 digits.',
            'addBuyerData.phone.unique' => 'The phone number has already been taken.',
        ]);

        if (!$this->getErrorBag()->isEmpty()) {
            return;
        }

        $this->addBuyerManually();
    }

    public function addBuyerManually()
    {
        $request = request();
        $request->merge($this->addBuyerData);
        // $this->addReceiver = new addReceiver;
        $BuyersController = new BuyersController;

        $response = $BuyersController->addManualBuyer($request);

        $result = $response->getData();

        $this->reset(['statusCode', 'message', 'errors']);

        // Set the status code and message received from the result
        $this->statusCode = $result->status;

        if ($this->statusCode === 200) {
            // $this->successMessage = $result->message;
            $this->success = $result->message;
            $this->addBuyerData = [];
            $this->reset([ 'statusCode', 'message', 'errors']);
            // $this->reset(['createChallanRequest', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->reset(['statusCode', 'message']);
        }
    }

    public function cityAndStateByPincode()
    {
        $pincode = $this->addBuyerData['pincode'];

        $receiverController = new BuyersController;
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        if (isset($result->city) && isset($result->state)) {
            $this->addBuyerData['city'] = $result->city;
            $this->addBuyerData['state'] = $result->state;
        }
    }

    public $messages = [
        'addBuyerData.email.unique' => 'The email address is already registered.',
        'addBuyerData.phone.unique' => 'The phone number is already registered.',
    ];

    public $rules = [
        'addBuyerData.email' => 'nullable|email|unique:users,email',
        'addBuyerData.phone' => 'nullable|numeric|unique:users,phone',
    ];

    public function updated($property)
    {
        if (
            ($property === 'addBuyerData.email' && !empty($this->addBuyerData['email'])) ||
            ($property === 'addBuyerData.phone' && !empty($this->addBuyerData['phone']))
        ) {
            $this->validateOnly($property, $this->rules);
        }
    }



    public function callAddBuyer(Request $request)
    {

        $request->replace([]);
        $request->merge($this->addBuyerData);

        $newBuyerController = new BuyersController;
        $response = $newBuyerController->addBuyer($request);
        $result = $response->getData();
        if ($result->status === 200) {
            $this->addBuyerData = [];
            $this->success = $result->message;
            $this->successMessage = $result->message;
            return view('components.panel.seller.all_buyer');
        } else {
            $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
        }

        $this->reset(['addBuyerData']);
    }

    public function addNewBuyerDetail()
    {
        // Make sure $this->selectBuyer['details'] is an array
        if (!is_array($this->selectBuyer['details'])) {
            $this->selectBuyer['details'] = [];
        }
        // Then you can use array_unshift to prepend an element
        array_unshift($this->selectBuyer['details'], [
            "id" => "",
            "buyer_id" => $this->selectBuyer['id'],
            "address" => "",
            "pincode" => "",
            "phone" => "",
            "gst_number" => "",
            "state" => "",
            "city" => "",
            "bank_name" => "",
            "branch_name" => "",
            "bank_account_no" => "",
            "ifsc_code" => "",
            "tan" => "",
            "status" => "status"
        ]);
    }
    public function productUpload()
    {
        $this->reset('errorFileUrl');
        if ($this->uploadFile->getClientOriginalExtension() !== 'csv' || $this->uploadFile->getMimeType() !== 'text/csv') {
            $this->dispatchBrowserEvent('show-error-message', [ 'Please upload a valid CSV file.']);
            return;
        }

        // Check if the file contains data
        $fileContent = file_get_contents($this->uploadFile->getRealPath());
        if (empty(trim($fileContent))) {
            $this->dispatchBrowserEvent('show-error-message', ['The file is empty. Please upload a file with data.']);
            return;
        }
        $request = new Request();
        $requestData = [
            'field_name' => 'value',
            'file' => $this->uploadFile,
        ];
        $request->merge($requestData);

        $productUpload = new BuyersController;
        $response = $productUpload->bulkUpload($request);
        $result = $response->getData();
        // dd($result);
        $this->statusCode = $result->status_code;
        $this->reset(['successMessage', 'errorMessage', 'errorFileUrl']);

        switch ($result->status_code) {
            case 200:
                // $this->successMessage = $result->message;
                $this->dispatchBrowserEvent('show-success-message', [$result->message]);
                $this->reset('uploadFile');
                break;
            case 400:
                    $errors = json_decode($response->content(), true)['errors'];
                    $this->dispatchBrowserEvent('show-error-message', [$errors]);
                    // $this->errorMessage = $errors;
                    break;
            case 422:
                $errors = json_decode($response->content(), true)['errors'];
                $errorFileUrl = $this->createErrorFile($errors);
                $this->dispatchBrowserEvent('show-error-message', ['Validation failed. Download the error report:']);
                // $this->errorMessage = 'Validation failed. Download the error report:';
                $this->errorFileUrl = $errorFileUrl;
                $this->reset('uploadFile');
                break;
            case 500:
                $this->dispatchBrowserEvent('show-error-message', ['Internal server error occurred.']);
                // $this->errorMessage = 'Internal server error occurred.';
                break;
            default:
                $this->dispatchBrowserEvent('show-error-message', ['An unknown error occurred.']);
                $this->errorMessage = 'An unknown error occurred.';
                break;
        }
    }




    public function selectBuyer($buyer)
    {
        $this->selectBuyer = [];
        $details = [];
        // dd($buyer);
        foreach ($buyer['details'] as $key => $detail) {
            // dd($detail);
            array_push($details, [
                "id" => $detail['id'],
                "buyer_id" => $detail['buyer_id'],
                "address" => $detail['address'],
                "pincode" => $detail['pincode'],
                "phone" => $detail['phone'],
                "gst_number" => $detail['gst_number'],
                "state" => $detail['state'],
                "city" => $detail['city'],
                "bank_name" => $detail['bank_name'],
                "branch_name" => $detail['branch_name'],
                "bank_account_no" => $detail['bank_account_no'],
                "ifsc_code" => $detail['ifsc_code'],
                "tan" => $detail['tan'],
            ]);
        }
        $this->selectBuyer = array(
            'id' => $buyer['id'],
            'added_by' => $buyer['user']['added_by'],
            'buyer_name' => $buyer['buyer_name'],
            'details' => $details
        );

        // dd($selectBuyer);
    }
    public function removeBuyerDetail($key)
    {
        unset($this->selectBuyer['details'][$key]);
    }

    public function updateBuyerDetail(Request $request)
    {

        $request->replace([]);
        $request->merge($this->selectBuyer);
        // dd($request);
        $newBuyerController = new BuyersController;
        $response = $newBuyerController->updateBuyer($request, $request->id);
        foreach ($request->details as $key => $detail) {
            $request->replace([]);
            $request->merge($detail);
            if ($request['id'] !== "") {
                $detailResponse = $newBuyerController->updateBuyerDetail($request, $request['id']);
                $result = $detailResponse->getData();
                if ($result->status === 200) {
                    // $this->successMessage = $result->message;
                    // return view('components.panel.sender.view_buyer');
                } else {
                    $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
                }
            } else {
                $detailResponse = $newBuyerController->storeBuyerDetail($request);
                $result = $detailResponse->getData();
                if ($result->status === 200) {
                    // $this->successMessage = $result->message;
                    // return view('components.panel.sender.view_buyer');
                } else {
                    $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
                }
            }
        }
        $result = $response->getData();
        if ($result->status === 200) {
            $this->successMessage = $result->message;
            // return view('components.panel.sender.view_buyer');
        } else {
            $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
        }

        $this->reset(['selectBuyer']);
    }


    public function invoiceSeries(Request $request)
    {
        // dd($this->sellerInvoiceSeriesData);
        $request->merge($this->sellerInvoiceSeriesData);
        $newInvoiceSeriesNoController = new PanelSeriesNumberController;
        $response = $newInvoiceSeriesNoController->store($request);
        // $this->reset(['sellerInvoiceSeriesData']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        // if ($result->status_code === 200 || $result->status_code === 201) {
        //     $this->successMessage = $result->message;
        //     $this->reset(['sellerInvoiceSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        //     $request->replace([]);
        //     $newInvoiceSeriesIndex = new PanelSeriesNumberController;
        //     $request->merge(['panel_id' => '3']);
        //     $data = $newInvoiceSeriesIndex->index($request);

        //     $this->seriesNoData = (array) $data->getData()->data;
        //     $newBuyerController = new BuyersController;
        //     $request->replace([]);
        //     $response = $newBuyerController->index($request);
        //     $buyerData = $response->getData();
        //     $this->buyerDatas = $buyerData->data;
        //     session()->flash('message', $this->successMessage);
        //     return redirect()->route('seller', ['template' => 'invoice_series_no']);
        // } else {
        //     $this->errorMessage = json_encode($result->errors);
        // }
        if (!$result->success) {
            $this->errorMessage = $result->message ?? 'An error occurred while processing your request.';
            $this->dispatchBrowserEvent('show-error', ['message' => $this->errorMessage]);
        } else {
            // Handle success case
            $this->dispatchBrowserEvent('show-success', ['message' => 'Challan series added successfully.']);
            return redirect()->route('seller', ['template' => 'invoice_series_no']);
        }
    }

    // public function deleteInvoiceSeries($id)
    // {
    //     $controller = new PanelSeriesNumberController;
    //     $controller->destroy($id);
    //     // $this->emit('triggerDelete', $id);
    // }

    public function deleteInvoiceSeries($id)
    {

        $controller = new PanelSeriesNumberController;
        $controller->destroy($id);
        // $request = new request;
        // $this->successMessage = $result->message;
        return redirect()->route('seller', ['template' => 'invoice_series_no'])->with('message', $this->successMessage ?? $this->errorMessage);

        // $this->emit('triggerDelete', $id);
    }

    public function deleteBuyer($id)
    {
        $buyer = new BuyersController;
        $buyer->delete($id);
        // $this->emit('triggerDelete', $id);
    }

    // public function selectInvoiceSeries($series_id,$invoice_number, $valid_till, $valid_from, $assigned_to_name)
    public function selectInvoiceSeries($seriesData)
    {
        $seriesData = json_decode($seriesData);
        $this->reset(['updateInvoiceSeriesData']);
        $this->updateInvoiceSeriesData = (array)$seriesData;
        $this->updateInvoiceSeriesData['assigned_to_b_id'] = '';
        // $this->updateInvoiceSeriesData['invoice_number'] = $seriesData->invoice_number;
        // $this->updateInvoiceSeriesData['valid_till'] = $seriesData->valid_till;
        // $this->updateInvoiceSeriesData['valid_from'] = $seriesData->valid_from;
        // $this->updateInvoiceSeriesData['assigned_to_name'] = $seriesData->assigned_to_name;
    }
    public function resetInvoiceSeries()
    {
        $this->reset(['updateInvoiceSeriesData']);
    }

    public function updatePanelSeries()
    {
        $request =  request();
        $request->merge($this->updateInvoiceSeriesData);
        // Create instances of necessary classes
        $PanelSeriesNumberController = new PanelSeriesNumberController;
        // dd($request);

        $response = $PanelSeriesNumberController->update($request, $this->updateInvoiceSeriesData['id']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            $this->dispatchBrowserEvent('close-edit-modal'); // Add this line
            $this->successMessage = $result->message;
            $this->reset(['updateInvoiceSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $request->replace([]);
            $newInvoiceSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '3']);
            $data = $newInvoiceSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newBuyerController = new BuyersController;
            $request->replace([]);
            $response = $newBuyerController->index($request);
            $buyerData = $response->getData();
            $this->buyerDatas = $buyerData->data;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    // public function selectBuyer($buyer_name, $buyer_special_id)
    // {
    //     $this->selectBuyer = [
    //         "buyer_name" => $buyer_name,
    //         "buyer_special_id" => $buyer_special_id,
    //     ];
    //     $this->addBuyerData['buyer_name'] = $buyer_name;
    //     $this->addBuyerData['buyer_special_id'] = $buyer_special_id;
    // }

    public function selectAllInvoice($invoiceData)
    {
        // dd($invoiceData);
        $invoiceData = json_decode($invoiceData);
        // dd($invoiceData);
        $this->reset(['updateAllInvoiceData']);
        $this->updateAllInvoiceData = (array)$invoiceData;

        // dd($this->updateAllInvoiceData);

    }

    // THIS WORKS FINE ONLY FOR UPDATE BYUER FOR
    // public function updateAllInvoice(Request $request)
    // {
    //     $request->merge($this->updateAllInvoiceData);

    //     // You need to get the buyerId from $this->updateAllInvoiceData or another source
    //     $buyerId = $this->updateAllInvoiceData['id'];

    //     // Call the updateBuyer method with the $buyerId parameter
    //     $buyersData = new BuyersController;
    //     $response = $buyersData->updateBuyer($request, $buyerId);

    //     dd($response);
    // }

    // public function resetAllInvoice()
    // {
    //     $this->reset(['updateAllInvoiceData']);
    // }

    public function updateAllInvoice(Request $request)
    {
        $request->merge($this->updateAllInvoiceData);

        $buyerId = $this->updateAllInvoiceData['id'];
        $buyerDetailId = $this->updateAllInvoiceData['details'][0]['id'];
        // dd($buyerDetailId);
        $buyersData = new BuyersController;
        $responseBuyer = $buyersData->updateBuyer($request, $buyerId);
        $responseDetail = $buyersData->updateBuyerDetail($request, $buyerDetailId);
        // dd($responseDetail);
        $buyerData = $responseBuyer->getContent();
        $buyerDetailData = $responseDetail->getContent();

        $buyerArray = json_decode($buyerData, true);
        $buyerDetailArray = json_decode($buyerDetailData, true);
        // dd($buyerDetailArray);

        if ($buyerArray['status'] === 200 && $buyerDetailArray['status'] === 200) {
            // dd('updates successful');
        } else {
            // dd('error');
        }
    }

    // TERMS AND CONDITIONS
    public $inputs = [''];
    public function addInput()
    {
        $this->inputs[] = '';
    }

    public function removeInput($index)
    {
        unset($this->inputs[$index]);
    }
    public function invoiceTermsAndConditions(Request $request)
    {
        $request->merge($this->termsAndConditionsData);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->termsIndexData = (array) $data->getData()->data;
        $request->merge($this->termsAndConditionsData);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData']);
    }


    public function deleteInvoiceTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);
        $this->mount();
    }
    public $selectedContent;

    public function selectInvoiceTerms($data)
    {
        $item = json_decode($data, true);
        $this->selectedContent = $item['content'];
    }
    public $purchase_order_series;
    public function purchaseOrder($page)
    {
        $request = request();
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 14,
            'panel_id' => 3,
        ];
        $request->merge($columnFilterDataset);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->ColumnDisplayNames = $ColumnDisplayNames;

        request()->replace([]);

         // Filter by purchase_order_series
         if ($request->has('purchase_order_series')) {
            // Split the search term into series and number
            $searchTerm = $request->purchase_order_series;
            $searchParts = explode('-', $searchTerm);

            if (count($searchParts) == 2) {
                $series = $searchParts[0];
                $num = $searchParts[1];

                // Perform the search
                $query->where('purchase_order_series', $series)
                    ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }


        // Filter by buyer_id
        if ($this->buyer_id != null) {
            // dump($this->buyer_id);
            $request->merge(['buyer_id' => $this->buyer_id]);
        }

        // Filter by Seller id
        if ($this->seller_id != null) {
            $request->merge(['seller_id' => $this->seller_id]);
        }

        // Filter by status
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        // Filter by state in BuyerDetails
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        // Filter by date range
        if ($this->fromDate != null && $this->toDate != null) {
           $request->merge([
               'from_date' => $this->fromDate,
               'to_date' => $this->toDate,
           ]);
        }

        $this->tableTdData = [];
        $request = new Request(['page' => $page, 'perPage' => $this->perPage]);
        $challanController = new PurchaseOrderController();
        $tableTdData = $challanController->getPurchaseOrders($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        // dd($this->tableTdData);

        // dd($this->tableTdData);

        $this->emit('PODataReceived', $tableTdData);
    }
    public function acceptPurchaseOrder($purchaseOrderId)
    {
        $request = request();
        $purchaInvoiceController = new PurchaseOrderController;

        $response = $purchaInvoiceController->accept($request, $purchaseOrderId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('seller', ['template' => 'purchase_order_seller'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function rejectPurchaseOrder($purchaseOrderId)
    {

        $request = request();
        $purchaInvoiceController = new PurchaseOrderController;

        $response = $purchaInvoiceController->reject($request, $purchaseOrderId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
            return redirect()->route('seller', ['template' => 'purchase_order_seller'])->with('message', $this->successMessage ?? $this->errorMessage);
    }
    // DETAILED VIEW OF PURCHASE ORDER
    public function detailedPurchaseOrder($page)
    {
        // dd('hello');
        $request = request();
        $this->ColumnDisplayNames = ['PO No.', 'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn', 'Details', 'Unit', 'Quantity', 'Unit Price', 'Tax', 'Total Amount'];

        request()->replace([]);

        if ($this->invoice_series != null) {
            $request->merge(['invoice_series' => $this->invoice_series]);
        }
        if ($this->buyer_id != null) {
            $request->merge(['buyer_id' => $this->buyer_id]);
        }
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        // dd($request);
        $purchaseOrderController = new PurchaseOrderController;
        $purchaseOrderBuyerData = $purchaseOrderController->getIndexDetailData($request);
        $this->purchaseOrderBuyerData = $purchaseOrderBuyerData->getData()->data;
        $this->invoiceFiltersData = json_encode($purchaseOrderBuyerData->getData());
    }

    public $tags = [];
    public $warehouse, $category, $context;



    public function render()
    {
        $this->context = 'invoice';
        session()->put('previous_url', url()->current());
        $request = request();

        $this->uploadFile = $this->uploadFile;
        switch ($this->persistedTemplate) {
            case 'all_buyer':
                $perPage = 10; // Number of items per page
                $offset = ($this->currentPage - 1) * $perPage;
                $allBuyer = new BuyersController;
                $response = $allBuyer->index(request()->merge([
                    'offset' => $offset,
                    'limit' => $perPage,
                ]));
                $buyerData = $response->getData();
                $this->buyerDatas = $buyerData->data;
                break;
        }

        $UserResource = new UserAuthController;
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id; // assuming the user is authenticated and has an id

        // Try to get the user details from the cache
        $response = $UserResource->user_details($request);
        $response = $response->getData();

        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage', 'successMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        $filters = [
            'article' => $this->Article,
            'item_code' => $this->item_code,
            'warehouse' => $this->warehouse,
            'location' => $this->location,
            'category' => $this->category,
            'from_date' => $this->from,
            'to_date' => $this->to,
        ];

        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }


        $query = Product::query()->with('details');

        // Filter by user_id
        $query->where('user_id', $userId);

        // Add a where clause to the query to filter out products where qty is not equal to 0
        $query->where('qty', '!=', 0);

        // Apply filters dynamically
        if (!empty($this->Article)) {
            $query->whereHas('details', function ($q) {
                $q->where('column_value', $this->Article);
            });
        }
        if (!empty($this->item_code)) {
            $query->where('item_code', $this->item_code);
        }
        if (!empty($this->location)) {
            $query->where('location', $this->location);
        }
        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }
        if (!empty($this->warehouse)) {
            $query->where('warehouse', $this->warehouse);
        }

        // Fetch filtered results
        $products = $query->get();

        // Fetch unique values based on the filtered results
        $this->articles = $products->pluck('details.0.column_value')->unique()->filter()->values()->toArray();
        $this->item_codes = $products->pluck('item_code')->unique()->filter()->values()->toArray();
        $this->locations = $products->pluck('location')->unique()->filter()->values()->toArray();
        $this->categories = $products->pluck('category')->unique()->filter()->values()->toArray();
        $this->warehouses = $products->pluck('warehouse')->unique()->filter()->values()->toArray();

        // Apply further filters to the paginated results
        if (!empty($this->item_code)) {
            $query->where('item_code', $this->item_code);
        }
        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }
        if (!empty($this->warehouse)) {
            $query->where('warehouse', $this->warehouse);
        }
        if (!empty($this->location)) {
            $query->where('location', $this->location);
        }

        // Fetch paginated results
        $products = $query->paginate(50);
        // $template = $this->persistedTemplate;
        // dd($template);
        // if ($template === 'po-to-invoice') {
        //     return view('livewire.seller.screens.screen', [
        //         'poToInvoiceComponent' => new PoToInvoice(),
        //     ]);
        // }

        return view('livewire.seller.screens.screen', [
            'createInvoiceData' => $this->createInvoiceData,
            'stock' => $products,

        ]);
    }




    function convertNumberToWords($number)
    {
        $words = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety'
        );

        if ($number < 21) {
            return $words[$number];
        } elseif ($number < 100) {
            $tens = $words[10 * floor($number / 10)];
            $units = $number % 10;
            return $tens . ($units ? ' ' . $words[$units] : '');
        } elseif ($number < 1000) {
            $hundreds = $words[floor($number / 100)] . ' Hundred';
            $remainder = $number % 100;
            return $hundreds . ($remainder ? ' and ' . $this->convertNumberToWords($remainder) : '');
        } elseif ($number < 100000) {
            $thousands = $this->convertNumberToWords(floor($number / 1000)) . ' Thousand';
            $remainder = $number % 1000;
            return $thousands . ($remainder ? ' ' . $this->convertNumberToWords($remainder) : '');
        } elseif ($number < 10000000) {
            $lakhs = $this->convertNumberToWords(floor($number / 100000)) . ' Lakh';
            $remainder = $number % 100000;
            return $lakhs . ($remainder ? ' ' . $this->convertNumberToWords($remainder) : '');
        } else {
            $crores = $this->convertNumberToWords(floor($number / 10000000)) . ' Crore';
            $remainder = $number % 10000000;
            return $crores . ($remainder ? ' ' . $this->convertNumberToWords($remainder) : '');
        }
    }

    function numberToIndianRupees($number)
    {
        $amount_in_words = $this->convertNumberToWords(floor($number));
        $decimal_part = intval(($number - floor($number)) * 100);

        if ($decimal_part > 0) {
            $decimal_in_words = $this->convertNumberToWords($decimal_part);
            return $amount_in_words . ' Rupees and ' . $decimal_in_words . ' Paisa';
        } else {
            return $amount_in_words . ' Rupees';
        }
    }
}
