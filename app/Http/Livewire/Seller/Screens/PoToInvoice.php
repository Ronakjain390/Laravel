<?php

namespace App\Http\Livewire\Seller\Screens;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyer\BuyersController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;

use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;


use Livewire\Component;

class PoToInvoice extends Component
{
    // public $id;
    use WithPagination;
    public $selectedUserDetails = [], $panelUserColumnDisplayNames, $ColumnDisplayNames, $challanModifyData,$invoiceId,$errors;
    public $products, $articles = [], $locations = [], $message, $statusCode, $item_codes, $Article, $location, $item_code, $fromDate, $toDate,$warehouse, $category, $from, $to;
    private $selectedUserDetailsData;
    public $calculateTax = true;
    public $action = 'save';
    public $mainUser;
    public $billTo;
    public $showRate;
    public $status_comment = '';
    public $updateForm = true;
    public $inputsDisabled = true;
    public $isOpen = false;
    public $open = false;
    public $productCode;
    public $save;
    public $inputsResponseDisabled = true;
    public $disabledButtons = true;
    public $productId;
    public $errorMessage;
    public $rows = [];
    public $sendButtonDisabled = true;
    public $context;
    public $selectedProductIds = [];
    public $selectedProducts = [];
    public $selectedUser;
    public $admin_ids = [];
    public $team_user_ids = [];
    public $hideWithoutTax = true;
    public $data;
    public $quantity;
    public $totalAmount;
    public $showInputBoxes = true;
    public $authUserState;
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

    public function addRow()
    {
            // dd('sdf');
        // Add a new empty row to the order_details array
        $this->create_invoice_request['order_details'][] = [
            'p_id' => '',
            'unit' => '',
            'rate' => null,
            'qty' => null,
            'total_amount' => null,
            'item_code' => null,
            'discount_total_amount' => null,
            'tax' => null,
            'tax_percentage' => null,
            'discount' => null,
            'columns' => [
                [
                    'column_name' => '',
                    'column_value' => '',
                ]
            ],
        ];
    }

    // public function addRow()
    // {
    //     $this->create_invoice_request['order_details'][] = [
    //         'columns' => [],
    //         'unit' => '',
    //         'rate' => '',
    //         'qty' => '',
    //         'tax' => '',
    //         'total_amount' => ''
    //     ];
    // }
    public $newRowIndex = 1;
    public function removeRow($index)
    {
        // Remove the specified row
        unset($this->createChallanRequest['order_details'][$index]);

        // Reindex the array to ensure sequential keys
        $this->create_invoice_request['order_details'] = array_values($this->create_invoice_request['order_details']);
    }

     public function updatedCreateInvoiceRequest($value)
     {
         // This method will be called whenever the create_invoice_request property is updated
         // You can add any additional logic here if needed
         $this->create_invoice_request = $value;
     }



     protected $listeners = [
        'addFromStockPoInvoice' => 'handleAddFromStockPoInvoice',
        'poToInvoiceLoaded' => 'loadPoToInvoiceData',
    ];

    public function invoiceEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;
        $this->reset([ 'message', 'save']);
    }

    public function saveRows($requestData)
    {
        $request = request();

        // Update the create_invoice_request with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->create_invoice_request['invoice_series'] = $this->selectedUser['invoiceSeries'];
        $this->create_invoice_request['series_num'] = $this->selectedUser['invoiceNumber'];
        $this->create_invoice_request['buyer'] = $this->selectedUser['buyer_name'];
        $this->create_invoice_request['seller_name'] = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->name;
        $this->create_invoice_request['seller_id'] = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Ensure statuses are included
        if (!isset($this->create_invoice_request['statuses'])) {
            $this->create_invoice_request['statuses'] = [
                ['comment' => $this->status_comment ?? '']
            ];
        }

        // Merge the create_invoice_request into the request
        $request->merge($this->create_invoice_request);

        // Set the buyer_id
        $this->create_invoice_request['buyer_id'] = $request->seller_user['id'];

        // Merge again to include any updates
        $request->merge($this->create_invoice_request);

        // Set the invoice date
        $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');

        // Final merge to ensure all data is included
        $request->merge($this->create_invoice_request);

        // Debugging
        // dd($request->all());
        // dd($request);
        $errors = false;
            foreach ($request->order_details as $index => $order_detail) {
                // Check if 'qty' is null
                if (is_null($order_detail['qty'])) {
                    $this->addError('qty.' . $index, 'Required.');
                    $errors = true;
                }
                // Check if 'article' is null
                if (isset($order_detail['columns'])) {
                    foreach ($order_detail['columns'] as $column) {
                        if ($column['column_name'] == 'Article' && empty($column['column_value'])) {
                            $this->addError('article.' . $index, 'Required.');
                            $errors = true;
                        }
                    }
                }
            }

            if ($errors) {
                return;
            }
            // dd($request->all());
        $invoiceController = new InvoiceController;
        $response = $invoiceController->store($request);
        $result = $response->getData();
        // Check the status code from the result
        // dd($result);
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->save = $result->message;
            $this->inputsDisabled = true;
            $this->inputsResponseDisabled = false;// Adjust the condition as needed
            // dd($result);
            if($this->create_invoice_request['buyer_id'] == null){
                $this->successMessage = $result->message;
                // $this->innerFeatureRedirect('sent_invoice', '13');
                return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
            }
            $this->invoiceId = $result->invoice_id;
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function editRows($requestData)
    {
        $request = request();
         // Update the createChallanRequest with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];
        // Ensure statuses are included
        if (!isset($this->create_invoice_request['statuses'])) {
            $this->create_invoice_request['statuses'] = [
                ['comment' => $this->status_comment ?? '']
            ];
        }
        $request->merge($this->create_invoice_request);

        $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
        foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
            $this->create_invoice_request['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }
        $this->create_invoice_request['invoice_series'] = $this->selectedUser['invoiceSeries'];
        $this->create_invoice_request['series_num'] = $this->selectedUser['invoiceNumber'];

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
            $this->save = $result->message;
            $this->inputsDisabled = true;

            $this->reset([ 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function sendInvoice($id)
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);
        $InvoiceController = new InvoiceController;

        $response = $InvoiceController->send($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage']);
            // $this->innerFeatureRedirect('sent_invoice', '13');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    }


     public function handleAddFromStockPoInvoice($productIds)
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
    public $poId;
    public function mount($poId)
    {
        // dd('dsfsdf');
        $this->poId = $poId;
        // dd($poId);
        $this->loadPoToInvoiceData();
        $this->context = 'invoice';
        $this->fetchTeamMembers();
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $request = request();
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
        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        $this->ColumnDisplayNames = $ColumnDisplayNames;
        array_push($this->ColumnDisplayNames, 'Warehouse', 'Category', 'Location', 'item code', 'unit', 'qty', 'rate');
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
    public $purchase_order_series;
    public function loadPoToInvoiceData()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $data = PurchaseOrder::where('id', $this->poId)->with(['orderDetails', 'orderDetails.columns', 'sellerUser', 'buyerUser'])->first();
        // dd($data);
        $sellerUser = $data->sellerUser;

        // Extract necessary data from $sellerUser
        $invoiceSeries = 'Not Assigned'; // or any default value
        $address = $sellerUser->address;
        $email = $sellerUser->email;
        $phone = $sellerUser->phone;
        $gst = $sellerUser->gst_number;
        $state = $sellerUser->state;
        $buyer = $sellerUser->name;
        $selectedUserDetails = json_encode([
            'buyer_user_id' => $sellerUser->id,
            'user' => [
                'details' => $sellerUser
            ]
        ]);
        // $this->create_invoice_request['buyer_id'] = $data->seller_id;
        // Call selectUser method
        $this->selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $buyer, $selectedUserDetails);
        $this->challanModifyData = json_encode($data);
        $modifiedDataArray = json_decode(json_encode($data), true);
        $this->purchase_order_series = $data->purchase_order_series . '-' . $data->series_num;

        // Ensure each order detail has an 'id' key
        $modifiedDataArray['order_details'] = collect($modifiedDataArray['order_details'])->map(function ($detail) {
            if (!isset($detail['id'])) {
                $detail['id'] = $detail['purchase_order_id'] ?? null; // or use any other unique identifier
            }
            return $detail;
        })->toArray();

        $this->create_invoice_request = $modifiedDataArray;
    }

    public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $state, $buyer, $selectedUserDetails)
    {
        // dd($selectedUserDetails);
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

    public $sortField = null;
    public $sortDirection = null;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // Reset pagination to the first page when sorting
        $this->resetPage();
    }

    public function render()
    {
        // dd('fsg');
        $id = session('po_id');
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

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $this->allItemIds = $query->pluck('id')->toArray();

        // Apply sorting
        if ($this->sortField) {
            // Sort by total_qty as an integer
            if ($this->sortField === 'total_qty') {
                $query->orderByRaw('CAST(total_qty AS UNSIGNED) ' . $this->sortDirection);
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }


        // Fetch paginated results
        $products = $query->paginate(20);
        return view('livewire.seller.screens.po-to-invoice', [
            'stocks' => $products,
        ]);
    }
}
