<?php

namespace App\Http\Livewire\Grn\Screen;

use App\Models\User;
use App\Models\Buyer;
use Livewire\Component;
use App\Models\CompanyLogo;
use Illuminate\Support\Arr;
use App\Mail\ExportReadyMail;
use App\Models\Product;
use App\Models\GoodsReceipt;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\ReceiverGoodsReceiptsDetails;
// use App\Http\Livewire\Sender\Screens\sentInvoice;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\ReceiverGoodsReceipt\ReceiverGoodsReceiptsController;
use App\Http\Controllers\V1\GoodsReceipt\GoodsReceiptsController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;

use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

class CreateGoodsReceipt extends Component
{
    use WithPagination;
    public $create_invoice_request = array(
        'goods_series' => '',
        'series_num' => '',
        'goods_receipts_date' => '',
        'feature_id' => '',
        'receiver_goods_receipts_id' => '',
        'buyer' => '',
        'comment' => '',
        'total_qty' => null,
        'calculate_tax' => null,
        'total' => null,
        'round_off' => null,
        'total_words' => '',
        'order_details' => [
            [
                'p_id' => '',
                'unit' => '',
                'rate' => null,
                'qty' => null,
                // 'details' => '',
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
    public $mainUser;
    public $total_qty;
    public $status_comment;
    public $products, $articles = [], $locations = [], $item_codes, $warehouse, $category, $Article, $location, $item_code;
    public $ColumnDisplayNames, $invoiceFiltersData, $receivedColumnDisplayNames, $sentColumnDisplayNames, $assigned_to_name, $discountValue, $totalAmountWithoutTax,$panelUserColumnDisplayNames;
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $selectedUser;
    public $selectedUserDetails = [];
    public $totalSales = 0;
    public $discountEntered = false;
    public $discountPercentage = 0;
    public $calculateTax = true;
    public $teamMembers;
    public $discount_total_amount;
    public $withoutTax;
    public $total = 0; // Total amount
    public $discountWithoutTax;
    public $updateForm = true;
    public $persistedTemplate;
    public $rows = [];
    public $context;
    public $action = 'save';
    public $sendButtonDisabled = true;
    public $persistedActiveFeature;
    public $invoiceIds, $invoiceId;
    public $invoiceSave;
    public $showButtons = false;
    public $showBarcode;
    public $receiver_name, $company_name, $from, $to, $isMobile,$tax, $totalAmount, $buyerName, $buyerAddress, $email, $address, $pincode, $phone, $state, $city, $tan, $errorMessage, $successMessage, $showManualBuyerTab, $buyer_special_id, $errors, $statusCode, $message, $PanelColumnData, $termsIndexData;

    public function addRow()
    {
        // Add a new empty row to the order_details array
        $this->create_invoice_request['order_details'][] = [
            'p_id' => '',
            'unit' => '',
            'rate' => null,
            'qty' => null,
            'total_amount' => null,
            'item_code' => null,
            'columns' => [
                [
                    'column_name' => '',
                    'column_value' => '',
                ]
            ],
        ];
    }
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
    public function removeRow($index)
    {
        unset($this->create_invoice_request['order_details'][$index]);
    }

    protected $listeners = ['addFromStock' => 'addFromStock'];
    // public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails)
    // {
    //     try {
    //         // Check if invoice series is assigned
    //         if ($invoiceSeries == 'Not Assigned') {
    //             $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                 ->where('default', "1")
    //                 ->where('panel_id', '5')
    //                 ->first();

    //             if ($series == null) {
    //                 $this->errorMessage = json_encode([['Please add one default Series number']]);
    //                 return false;
    //             } else {
    //                 $invoiceSeries = $series->series_number;
    //                 $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
    //                     ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                     ->get();
    //                 $maxSeriesNum = $latestSeriesNum->max('series_num');
    //                 $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;

    //                 $this->selectedUser = [
    //                     "invoiceSeries" => $invoiceSeries,
    //                     "invoiceNumber" => $invoiceNum,
    //                     "address" => $address,
    //                     "email" => $email,
    //                     "receiver_name" => $receiver_name,
    //                     "phone" => $phone,
    //                     "gst" => $gst,
    //                     "state" => $state,
    //                     'pincode' => $pincode,
    //                     'city' => $city
    //                 ];

    //                 $this->buyerName = $this->selectedUser['receiver_name'];
    //                 $this->buyerAddress = $this->selectedUser['address'];
    //                 $this->create_invoice_request = [
    //                     'goods_series' => $invoiceSeries,
    //                     'series_num' => $invoiceNum,
    //                     'receiver_name' => $receiver_name,
    //                     'receiver_goods_receipts_id' => json_decode($selectedUserDetails)->id,
    //                     'buyer_detail_id' => json_decode($selectedUserDetails)->details[0]->id,
    //                     'feature_id' => $this->persistedActiveFeature
    //                 ];
    //                 $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //                 $this->inputsDisabled = false;

    //                 $request = request();
    //                 $billTo = new ReceiverGoodsReceiptsController;
    //                 $this->billTo = $billTo->index($request)->getData()->data;
    //                 $this->billTo = collect($this->billTo)->sortBy(function ($item) {
    //                     return strtolower($item->receiver_name);
    //                 })->values()->all();
    //             }
    //         } else {
    //             $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
    //                 ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                 ->max('series_num');
    //             $invoiceNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

    //             $this->selectedUser = [
    //                 "invoiceSeries" => $invoiceSeries,
    //                 "invoiceNumber" => $invoiceNum,
    //                 "address" => $address,
    //                 "receiver_name" => $receiver_name,
    //                 "email" => $email,
    //                 "phone" => $phone,
    //                 "gst" => $gst,
    //                 'pincode' => $pincode,
    //                 'city' => $city
    //             ];

    //             $this->buyerName = $this->selectedUser['receiver_name'];
    //             $this->receiverAddress = $this->selectedUser['address'];
    //             $this->receiverPhone = $this->selectedUser['phone'];
    //             $this->create_invoice_request = [
    //                 'goods_series' => $invoiceSeries,
    //                 'buyer' => $receiver_name,
    //                 'receiver_goods_receipts_id' => json_decode($selectedUserDetails)->buyer_user_id,
    //                 'buyer_detail_id' => json_decode($selectedUserDetails)->details[0]->id,
    //                 'feature_id' => $this->persistedActiveFeature
    //             ];
    //             $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //             $this->inputsDisabled = false;

    //             $request = request();
    //             $billTo = new ReceiverGoodsReceiptsController;
    //             $this->billTo = $billTo->index($request)->getData()->data;
    //             $this->billTo = collect($this->billTo)->sortBy(function ($item) {
    //                 return strtolower($item->receiver_name);
    //             })->values()->all();
    //         }
    //     } catch (\Exception $e) {
    //         // Log the exception for debugging
    //         \Log::error('Error in selectUser: ' . $e->getMessage());

    //         // Set an error message to be displayed
    //         $this->errorMessage = json_encode([['An error occurred while selecting the user. Please try again.']]);

    //         // Optionally, you could rethrow the exception if you want it to be handled by the global exception handler
    //         // throw $e;
    //     }
    // }

    public function addFromStock($productIds)
    {
        $this->selectedProductIds = $productIds;

        // Initialize total quantity and total amount
        $totalQty = 0;
        $totalAmount = 0;
        foreach ($this->selectedProductIds as $selectedProductId) {
            // Extract the actual product ID
            $actualProductId = explode('-', $selectedProductId)[0];

            $selectedProductDetails = array_filter($this->products, function ($product) use ($actualProductId) {
                return $product['id'] == $actualProductId;
            });

            if (!empty($selectedProductDetails)) {
                $selectedProductDetails = reset($selectedProductDetails);

                $dataToMerge = [
                    'p_id' => $selectedProductDetails['id'],
                    'unit' => $selectedProductDetails['unit'],
                    'rate' => $selectedProductDetails['rate'],
                    'qty' => $selectedProductDetails['qty'],
                    'total_amount' => $selectedProductDetails['rate'] * $selectedProductDetails['qty'],
                    'item_code' => $selectedProductDetails['item_code'],
                    'columns' => $selectedProductDetails['details'],
                ];

                $productExists = array_filter($this->create_invoice_request['order_details'], function ($product) use ($dataToMerge) {
                    return isset($product['p_id']) && $product['p_id'] == $dataToMerge['p_id'];
                });

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
        // Assign updated totals to the createChallanRequest array
        // $this->createChallanRequest['total_qty'] = $totalQty;
        // $this->createChallanRequest['total'] = $totalAmount;
        // $this->selectedProductIds = [];
    }

    public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $pincode, $city, $receiver_name, $selectedUserDetails)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user'
                ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
                : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $decodedUserDetails = json_decode($selectedUserDetails);

            if ($invoiceSeries == 'Not Assigned') {
                $series = PanelSeriesNumber::where('panel_series_numbers.user_id', $userId)
                    ->where('default', "1")
                    ->where('panel_id', '5')
                    ->first();

                if ($series == null) {
                    throw new \Exception('Please add one default Series number');
                }

                $invoiceSeries = $series->series_number;
            }

            $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
                ->where('sender_id', $userId)
                ->max('series_num');

            $invoiceNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

            $this->selectedUser = [
                "invoiceSeries" => $invoiceSeries,
                "invoiceNumber" => $invoiceNum,
                "address" => $address ?: $decodedUserDetails->details[0]->address ?? '',
                "email" => $email ?: $decodedUserDetails->user->email ?? '',
                "receiver_name" => $receiver_name,
                "phone" => $phone ?: $decodedUserDetails->user->phone ?? '',
                "gst" => $gst ?: $decodedUserDetails->user->gst_number ?? '',
                "state" => $state ?: $decodedUserDetails->user->state ?? '',
                'pincode' => $pincode ?: $decodedUserDetails->user->pincode ?? '',
                'city' => $city ?: $decodedUserDetails->user->city ?? ''
            ];

            $this->buyerName = $this->selectedUser['receiver_name'];
            $this->receiverAddress = $this->selectedUser['address'];
            $this->receiverPhone = $this->selectedUser['phone'];
            $this->create_invoice_request['goods_series'] = $invoiceSeries;
            $this->create_invoice_request['series_num'] = $invoiceNum;
            $this->create_invoice_request['receiver_name'] = $receiver_name;
            $this->create_invoice_request['receiver_goods_receipts_id'] = $decodedUserDetails->id;
            $this->create_invoice_request['buyer_detail_id'] = $decodedUserDetails->details[0]->id ?? null;
            $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;
            $this->selectedUserDetails = $decodedUserDetails->details;

            $this->inputsDisabled = false;

            $request = request();
            $billTo = new ReceiverGoodsReceiptsController;
            $this->billTo = $billTo->index($request)->getData()->data;
            $this->billTo = collect($this->billTo)->sortBy(function ($item) {
                return strtolower($item->receiver_name);
            })->values()->all();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
        }
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
    public function updatedCreateInvoiceRequest()
{
    $orderDetails = collect($this->create_invoice_request['order_details']);

    $totalQuantity = $orderDetails->sum(function ($detail) {
        return (float)$detail['qty'];
    });


    $totalWithoutTax = array_reduce($this->create_invoice_request['order_details'], function ($carry, $item) {
        if (isset($item['total_amount']) && isset($item['total_tax'])) {
            return $carry + $item['total_amount'] - $item['total_tax'];
        }
        return $carry;
    }, 0);

    $this->create_invoice_request['order_details'] = array_map(function ($item) {
        $item['total_amount'] = $this->calculateTotalAmount(
            $item['rate'],
            $item['qty'],
            $item['tax'] ?? 0, // Use null coalescing operator to provide a default value
            $item['discount'] ?? 0,
            $item['discount_total_amount'] ?? 0
        );
        $item['total_tax'] = isset($item['tax']) // Check if 'tax' key exists
            ? $this->calculateTotalTax($item['total_amount'], $item['tax']) // If 'tax' key exists, calculate total tax
            : 0; // If 'tax' key doesn't exist, set total tax to 0
        return $item;
    }, $this->create_invoice_request['order_details']);


    // $this->create_invoice_request['total_qty'] = $totalQuantity;
    // $this->create_invoice_request['total_without_tax'] = $totalWithoutTax;
    // $this->create_invoice_request['total_discount'] = $totalDiscount;
    $this->updateTotal();
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

private function calculateTotalAmount($rate, $qty, $tax, $discount)
    {
        $subtotal = (float)($rate) * (float)($qty);
        $subtotalAfterDiscount = $subtotal - ($subtotal * $discount / 100); // Apply discount before tax

        if ($this->calculateTax) {

            $totalAmount = $subtotalAfterDiscount * (1 + ((float)$tax / 100));

        } else {

            $totalAmount = (float)($subtotalAfterDiscount);

        }


        // dd($this->discount_total_amount);
        // Format totalAmount with 2 decimal places
        $totalAmount = number_format($totalAmount, 2, '.', '');
        return $totalAmount;
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
    public function updateTotal()
    {
        $orderDetails = collect($this->create_invoice_request['order_details']);

        $totalWithoutTax = $orderDetails->sum(function ($item) {
            return $item['total_amount'] - $item['total_tax'];
        });

        $totalTax = $orderDetails->sum('total_tax');

        $totalDiscount = $this->create_invoice_request['total_discount'] ?? 0;

        $discountPercentage = $totalDiscount / 100;
        $discountAmount = $totalWithoutTax * $discountPercentage;

        $total = $totalWithoutTax + $totalTax - $discountAmount;
        $this->totalwithoutDiscount = $totalWithoutTax;
        // dd($this->discount_total_amount);
        // if ($this->discount_total_amount) {
        //     $total = $total - (float)($total * $this->discount_total_amount / 100);
        //     $this->discountWithoutTax = $total - (float)($total * $this->discount_total_amount / 100);
        // }
        // Apply discount on total amount without tax
        // if ($this->discount_total_amount) {
        //     $totalWithoutTaxDiscount = $totalWithoutTax - (float)($totalWithoutTax * $this->discount_total_amount / 100);
        //     $total = $totalWithoutTaxDiscount + $totalTax;
        //     $this->discountWithoutTax = $totalWithoutTaxDiscount;
        // }

        // $this->create_invoice_request['total'] = number_format(floatval(str_replace(',', '', $total)), 2, '.', '');
        // $this->create_invoice_request['total_words'] = $this->numberToIndianRupees((float) $total);
    }

    public function invoiceModify(Request $request)
    {
        $request->merge($this->create_invoice_request);
        // dd($request);
        // Create instances of necessary classes
        $InvoiceController = new GoodsReceiptsController;

        $response = $InvoiceController->update($request, $this->invoiceId);
        $result = $response->getData();
        // dd($request);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->invoiceId = $result->goods_receipt_id;

            $this->reset([ 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function selectUserAddress($selectedUserDetail, $selectedUserDetails)
    {
        try {
            $selectedUserDetail = json_decode($selectedUserDetail);

            if (!$selectedUserDetail) {
                throw new \Exception("Invalid user detail provided.");
            }

            $this->selectedUser['address'] = $selectedUserDetail->address ?? null;
            $this->selectedUser['phone'] = $selectedUserDetail->phone ?? null;
            $this->selectedUser['gst'] = $selectedUserDetail->gst_number ?? null;
            $this->create_invoice_request['buyer_detail_id'] = $selectedUserDetail->id ?? null;

            $this->selectedUserDetails = json_decode($selectedUserDetails);

            if (!$this->selectedUserDetails) {
                throw new \Exception("Invalid user details provided.");
            }

            $request = request();

            $billTo = new ReceiverGoodsReceiptsController;
            $this->billTo = $billTo->index($request)->getData()->data;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in selectUserAddress: ' . $e->getMessage());

            // Handle the error, e.g., by setting an error message in the class state, returning false, etc.
            session()->flash('error', 'Failed to select user address: ' . $e->getMessage());
            // Depending on your method's expected return type, adjust the error handling response
            return false;
        }
    }

    public $pdfData;

    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();
            $invoiceSeries = $series->series_number;
            $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
                ->where('sender_id', $userId)
                ->max('series_num');

            $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->selectedUser = [
            "invoiceSeries" => $invoiceSeries,
            "invoiceNumber" => $seriesNum,
        ];
     }


     public function checkPincodeLength($pincode)
    {
        if (strlen($pincode) === 6) {
            $this->cityAndStateByPincode($pincode);
        }
    }

    public function clearArticleError($index)
    {
        $this->resetErrorBag('article.' . $index);
    }

    public function cityAndStateByPincode()
    {
        $pincode = $this->addBuyerData['pincode'];
        // dd($pincode);

        $receiverController = new ReceiversController();
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        // dd($result);
        if (isset($result->city) && isset($result->state)) {
            // Update the city and state fields
            $this->addBuyerData['city'] = $result->city;
            $this->addBuyerData['state'] = $result->state;
        }
    }

    public $existingUser = null;
    // Validate Phone Number
    public function updatedAddBuyerDataPhone($value)
    {
        $this->resetErrorBag('addBuyerData.phone');
        $this->existingUser = null;

        if (strlen($value) === 10) {
            $user = User::where('phone', $value)->first();
            if ($user) {
                $this->existingUser = $user;
                $this->addError('addBuyerData.phone', 'This phone number already exists in our records.');
            } else {
                $this->validateOnly('addBuyerData.phone', [
                    'addBuyerData.phone' => ['required', 'digits:10'],
                ]);
            }
        } elseif (!empty($value)) {
            $this->addError('addBuyerData.phone', 'Phone number must be 10 digits.');
        }
    }


    public function useExistingUserDetails()
    {
        if ($this->existingUser) {
            $this->addBuyerData = [
                'receiver_name' => $this->existingUser->name,
                'company_name' => $this->existingUser->company_name,
                'email' => $this->existingUser->email,
                'address' => $this->existingUser->address,
                'pincode' => $this->existingUser->pincode,
                'state' => $this->existingUser->state,
                'city' => $this->existingUser->city,
                'phone' => $this->existingUser->phone,
                'organisation_type' => $this->existingUser->organisation_type,
                'buyer_special_id' => $this->existingUser->buyer_special_id,
                'gst_number' => $this->existingUser->gst_number,
            ];
            $this->existingUser = null;
        }
    }

    public function saveRows($requestData)
    {
        // dd($requestData);
        $request = request();
        // Update the createChallanRequest with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];

        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            if ($this->updateForm == false) {
                $request->merge($this->addBuyerData);
                $receiverId = null;
                $receiverPhone = null;
                $result = null;

                if ($request->phone || $request->email) {
                    // Step 1 & 2: Check for existing receiver by phone or email
                    $query = ReceiverGoodsReceiptsDetails::with('receiver')
                        ->where('receiver_id', $userId);

                    if (!empty($request->phone)) {
                        $query->where('phone', $request->phone);
                    }

                    if (!empty($request->email)) {
                        if (empty($request->phone)) {
                            $query->where('email', $request->email);
                        } else {
                            $query->orWhere('email', $request->email);
                        }
                    }

                    $existingReceiver = $query->first();

                    if ($existingReceiver) {
                        // Existing receiver found, use its details
                        $receiverId = $existingReceiver->receiver_id;
                        $receiverPhone = $existingReceiver->phone ?? '';
                    } else {
                        // No existing receiver, proceed to add a new one
                        $BuyersController = new ReceiverGoodsReceiptsController;
                        $response = $BuyersController->addManualReceiver($request);
                        $result = $response->getData();
                        $receiverId = $result->receiver->id;
                        $receiverPhone = $result->receiver_detail->phone ?? '';
                    }
                }


                // Get the series number
                $series = PanelSeriesNumber::where('panel_series_numbers.user_id', $userId)
                    ->where('default', "1")
                    ->where('panel_id', '5')
                    ->first();

                if (!$series) {
                    throw new \Exception('Please add one default Series number');
                }

                $invoiceSeries = $series->series_number;
                $latestSeriesNum = GoodsReceipt::where('goods_series', $invoiceSeries)
                    ->where('sender_id', $userId)
                    ->get();

                $maxSeriesNum = $latestSeriesNum->max('series_num');
                $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;

                $this->create_invoice_request['goods_series'] = $invoiceSeries;
                $this->create_invoice_request['series_num'] = $invoiceNum;

                if (isset($result->buyer->buyer_name)) {
                    $this->create_invoice_request['receiver_goods_receipts'] = $result->buyer->buyer_name;
                    $this->create_invoice_request['receiver_goods_receipts_id'] = $result->buyer->buyer_user_id;
                } elseif (isset($request->buyer_name)) {
                    $this->create_invoice_request['receiver_goods_receipts'] = $receiverPhone;
                    $this->create_invoice_request['receiver_goods_receipts_id'] = $receiverId;
                    $this->sendButtonDisabled = true;
                } else {
                    $this->create_invoice_request['receiver_goods_receipts'] = 'Others';
                    $this->create_invoice_request['receiver_goods_receipts_id'] = null;
                }

                $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;
            }

            $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
            foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
                $this->create_invoice_request['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
            }
            $request->merge($this->create_invoice_request);
            $errors = false;
            foreach ($request->order_details as $index => $order_detail) {
                // Check if 'qty' is null
                if (is_null($order_detail['qty'])) {
                    $this->addError('qty.' . $index, 'Required');
                    $errors = true;
                }
                // Check if 'article' is null
                if (isset($order_detail['columns'])) {
                    foreach ($order_detail['columns'] as $column) {
                        if ($column['column_name'] == 'Article' && empty($column['column_value'])) {
                            $this->addError('article.' . $index, 'Required');
                            $errors = true;
                        }
                    }
                }
            }

            if ($errors) {
                return;
            }

            $invoiceController = new GoodsReceiptsController;
            $response = $invoiceController->store($request);
            $result = $response->getData();
            // dd($result);
            $this->statusCode = $result->status_code;

            if ($result->status_code === 200) {
                $this->invoiceSave = $result->message;
                $this->inputsDisabled = true;
                $this->inputsResponseDisabled = false;

                if (empty($request->phone) && $this->create_invoice_request['receiver_goods_receipts_id'] == null) {
                    $this->successMessage = $result->message;
                    return redirect()->route('grn', ['template' => 'sent-goods-receipt'])->with('message', $this->successMessage ?? $this->errorMessage);
                }
                $this->invoiceId = $result->goods_receipt_id;
                $this->reset(['statusCode', 'message', 'errorMessage']);
            } else {
                $this->errorMessage = json_encode($result->errors);
            }
        } catch (\Exception $e) {
            Log::error('Error in invoiceCreate: ' . $e->getMessage());
            $this->errorMessage = $e->getMessage();
            session()->flash('error', $e->getMessage());
        }
    }

    public function editRows($requestData)
    {
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
        $InvoiceController = new GoodsReceiptsController;

        $response = $InvoiceController->update($request, $this->invoiceId);
        $result = $response->getData();
        // dd($request);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->invoiceId = $result->goods_receipt_id;
            $this->save = $result->message;
            $this->inputsDisabled = true;

            $this->reset([ 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function invoiceEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;
        $this->reset([ 'message', 'invoiceSave']);
    }


    public function sendInvoice($id)
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);
        $GoodsReceiptsController = new GoodsReceiptsController;

        $response = $GoodsReceiptsController->send($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage']);
            // $this->innerFeatureRedirect('sent_invoice', '13');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('grn', ['template' => 'sent-goods-receipt'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function mount()
    {
        $request = request();
        // Check if the User-Agent indicates a mobile device
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
        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 122,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 122;
        });

        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = array_merge($panelColumnDisplayNames, [
            'Article',
            'Hsn', 'Details'
        ]);


        $request->merge([
            'feature_id' => 122,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 122;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;
        $this->panelUserColumnDisplayNames = array_merge($panelUserColumnDisplayNames, [
            'Article',
            'Hsn', 'Details'
        ]);
        // Add from stock modal data
        $request = request();
        $columnFilterDataset = [
            'feature_id' => 122,
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

        // Add static columns to the ColumnDisplayNames array
        $staticColumns = ['Article', 'Hsn', 'Details', 'item code', 'category', 'location', 'Warehouse', 'unit', 'qty', 'rate'];
        $this->ColumnDisplayNames = array_merge($this->ColumnDisplayNames, $staticColumns);


        $showTemplate = CompanyLogo::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->first();
        $this->showTemplate = $showTemplate->receipt_note_template;
        $this->initializeRows();
        $this->fetchTeamMembers();
        $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute
        $this->create_invoice_request['challan_date'] = now()->format('Y-m-d');
        // Set the status code and message received from the result
        // $this->statusCode = $result->status_code;
        // $this->products = [];
        // dd($this->billTo);
        $unitData = new UnitsController();
        $response = $unitData->index('receipt_note');
        // $this->unit = $response->getdata()0;
        $this->unit = json_decode($response->getContent(), true);
        $billTo = new ReceiverGoodsReceiptsController;
        // dd($billTo);
        $this->billTo = $billTo->index($request)->getData()->data;

        $this->billTo = collect($this->billTo)->map(function ($item) {
            // Check if receiver_name is null and details array is not empty
            if (is_null($item->receiver_name) && !empty($item->details)) {
                // Use phone from the first item in details as receiver_name if available
                $item->receiver_name = $item->details[0]->phone ?? 'Unknown';
            }
            return $item;
        })->sort(function ($a, $b) {
            // Check if receiver_name is numeric or alphabetic and sort accordingly
            $isANumeric = is_numeric($a->receiver_name);
            $isBNumeric = is_numeric($b->receiver_name);

            if ($isANumeric && !$isBNumeric) {
                return 1; // Move numeric values to the bottom
            } elseif (!$isANumeric && $isBNumeric) {
                return -1; // Keep alphabetic values at the top
            } else {
                // If both are of the same type, sort them naturally
                return strnatcasecmp($a->receiver_name, $b->receiver_name);
            }
        })->values()->all();
        // dd($this->billTo);
        $this->create_invoice_request['goods_receipts_date'] = now()->format('Y-m-d');

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData, $userId, 'shdj');
        $this->pdfData = $pdfData;

        // Fetch panel settings
        $panelSettings = \App\Models\PanelSettings::where('user_id', Auth::user()->id)->first();
        if ($panelSettings) {
            $settings = json_decode($panelSettings)->settings;
            // dd($settings);
            if (isset($settings) && isset($settings->receipt_note)) {
                $senderSettings = $settings->receipt_note;
                $this->showBarcode = $senderSettings->barcode ?? false;
            }
        }
    }

    private function fetchTeamMembers()
    {
        $query = app(TeamUserController::class)->index();
            $status = $query->getStatusCode();
            $queryData = $query->getData();

            if ($status === 200) {
                $this->teamMembers = $queryData->data;
            } else {
                $this->errorMessage = $queryData->errors;
                $this->reset(['status', 'successMessage']);
            }
    }
    private function initializeRows()
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
            'quantity' => null,
            'rate' => null,
            'tax' => null,
            'total' => null,
            'calculateTax' => true
        ];

        // Merge dynamic and static fields to initialize rows
        $this->rows = [
            array_merge($dynamicFields, $staticFields)
        ];
    }

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
    }
    public function render()
    {
        // dd($this->showTemplate);
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
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
        $products = $query->paginate(20);
        if($this->showTemplate == 'default') {
            return view('livewire.grn.screen.create-goods-receipt', [
                'stocks' => $products,
            ]);
        } elseif($this->showTemplate == 'form') {
            return view('livewire.grn.screen.create-receipt-note-form', [
                'stocks' => $products,
            ]);
        }
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
