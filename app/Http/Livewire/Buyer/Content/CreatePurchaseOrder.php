<?php

namespace App\Http\Livewire\Buyer\Content;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\PanelSeriesNumber;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Seller\SellerController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use Livewire\Component;

class CreatePurchaseOrder extends Component
{
    public $sfpModal = false;
    public $mainUser;
    public $errorMessage;
    public $pdfData;
    public $panelColumnDisplayNames;
    public $panelUserColumnDisplayNames;
    public $columnDisplayNames;
    public $billTo;
    public $buyerName;
    public $save;
    public $showRate;
    public $status_comment = '';
    public $barcode;
    public $isOpen = false;
    public $open = false;
    public $statusCode;
    public $productCode;
    public $successMessage;
    public $message;
    public $selectedProducts = [];
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $sendButtonDisabled = true;
    public $updateForm = true;
    public $selectedUser;
    public $purchaseOrderId;
    public $selectedUserDetails = [];
    public $admin_ids = [];
    public $team_user_ids = [];
    public $calculateTax = true;
    public $hideWithoutTax = true;
    public $productId;
    public $data;
    public $quantity;
    public $totalAmount;
    public $showInputBoxes = true;
    public $isLoading = true;
    public $products, $articles = [], $locations = [], $item_codes, $Article, $location, $item_code, $warehouse, $category, $from, $to;
    public $authUserState;
    public $selectedProductIds = [];
    public $rows = [];
    public $context;
    public $action = 'save';
    public $invoiceId;
    use WithPagination;


    public $createChallanRequest = array(
        'purchase_order_series' => '',
        'feature_id' => '',
        'invoice_date' => '',
        'buyer_id' => '',
        'buyer_name' => '',
        'seller_name' => "",
        'comment' => '',
        'total_qty' => null,
        'total' => null,
        'round_off' => null,
        'total_words' => '',
        'order_date' => '',
        'order_details' => [
            [
                'unit' => '',
                'rate' => 0,
                'qty' => 0,
                'details' =>'',
                'total_amount' => 0,
                'tax_percentage' => 0,
                'discountPercentage' => 0,
                'discount_total_amount' => 0,
                'tax_amount' => 0,
                'tax' => 0,
                'total_with_tax' => 0,
                'toalTax' => 0,
                'total_tax' => 0,
                'toalTaxRate' => 0,
                'totalSales' => 0,
                'cgst_rate' => 0,
                'sgst_rate' => 0,
                'cgst' => 0,
                'sgst' => 0,
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

    public function mount()
    {
        $request = request();

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

       // dd($id);
       $PanelColumnsController = new PanelColumnsController;

       $request->merge([
           'feature_id' => 22,
           // Auth::guard(Auth::getDefaultDriver())->user()->id
           'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
       ]);
       $columnsResponse = $PanelColumnsController->index($request);
       $columnsData = json_decode($columnsResponse->content(), true);

       $filteredColumns = array_filter($columnsData['data'], function ($column) {
           return $column['feature_id'] == 22;
       });
       $panelColumnDisplayNames = array_map(function ($column) {
           return $column['panel_column_display_name'];
       }, $filteredColumns);
       $this->panelColumnDisplayNames = $panelColumnDisplayNames;


       $request->merge([
           'feature_id' => 22,
           'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
       ]);
       $columnsUserResponse = $PanelColumnsController->index($request);
       $columnsUserData = json_decode($columnsUserResponse->content(), true);

       $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
           return $column['feature_id'] == 22;
       });
       $panelUserColumnDisplayNames = array_map(function ($column) {
           return $column['panel_column_display_name'];
       }, $filteredUserColumns);

       $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

       $receiverScreen = new SellerController;
       $response = $receiverScreen->index($request);
       // $receiverScreen = new PurchaseOrderController;

       $responseData = $response->getData()->data; // Assuming $response is your JsonResponse
       // dd($responseData);
       $this->Sellers = $responseData;

       $this->createChallanRequest['order_date'] = now()->format('Y-m-d');
       $products = new ProductController;
       $response = $products->searchStock($request);
       $result = $response->getData();
       $this->products = (array) $result->data;
       $filteredProducts = array_filter($this->products, function ($product) {
           return ((object) $product)->qty > 0;
       });
       $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

       $this->panelColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
       // dd($this->panelColumnDisplayNames);
       $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
       $this->ColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
       array_push($this->ColumnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');


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
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();

        if ($response->success == "true") {
            $this->mainUser = json_encode($response->user);
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        $this->initializeRows();
        $this->context = 'purchase_order';
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute

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

    public function getUnitProperty()
    {
        $unitData = new UnitsController();
        $response = $unitData->index();
        return json_decode($response->getContent(), true);
    }


    public function checkPincodeLength($pincode)
    {
        if (strlen($pincode) === 6) {
            $this->cityAndStateByPincode($pincode);
        }
    }

    public function cityAndStateByPincode()
    {
        $pincode = $this->addReceiverData['pincode'];
        // dd($pincode);

        $receiverController = new ReceiversController();
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        // dd($result);
        if (isset($result->city) && isset($result->state)) {
            // Update the city and state fields
            $this->addReceiverData['city'] = $result->city;
            $this->addReceiverData['state'] = $result->state;
        }
    }

    // public function selectUser($purchase_order_series, $address, $email, $phone, $gst, $buyer, $selectedUserDetails)
    // {
    //     // dd($purchase_order_series, $address, $email, $phone, $gst, $buyer, $selectedUserDetails);
    //     if ($purchase_order_series == 'Not Assigned') {
    //         $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->where('default', "1")
    //             ->where('panel_id', "4")
    //             ->first();
    //             // dd($series);
    //         if ($series == null) {
    //             $this->errorMessage = json_encode([['Please add one default Series number']]);
    //         } else {
    //             $purchaseOrderSeries = $series->series_number;
    //             // dd($purchaseOrderSeries);
    //             $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchaseOrderSeries)
    //                 ->where('buyer_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                 ->max('series_num');
    //             // Increment the latestSeriesNum for the new challan
    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

    //             $this->selectedUser = [
    //                 "purchase_order_series" => $purchaseOrderSeries,
    //                 "seriesNumber" => $seriesNum,
    //                 "address" => $address,
    //                 "email" => $email,
    //                 "phone" => $phone,
    //                 'buyer_name' => $buyer,
    //                 "gst" => $gst
    //             ];
    //             // dd(json_decode($selectedUserDetails));
    //             $this->buyerName = $this->selectedUser['buyer_name'];
    //             $this->createChallanRequest['purchase_order_series'] = $purchaseOrderSeries;
    //             $this->createChallanRequest['buyer_name'] = $buyer;
    //             $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->seller_user_id;
    //             $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //             $this->createChallanRequest['feature_id'] = 23;
    //             $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //             $this->createChallanRequest['order_date'] = now()->format('Y-m-d');
    //             $this->inputsDisabled = false; // Adjust the condition as needed
    //             // dd($this->selectedUserDetails);

    //         }
    //     } else {

    //         // Get the latest series_num for the given challan_series and user_id
    //         $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchaseOrderSeries)
    //             ->where('buyer_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->max('series_num');
    //         // Increment the latestSeriesNum for the new challan
    //         $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;




    //         $this->selectedUser = [
    //             "purchase_order_series" => $purchaseOrderSeries,
    //             "seriesNumber" => $seriesNum,
    //             "address" => $address,
    //             "email" => $email,
    //             'buyer_name' => $buyer,
    //             "phone" => $phone,
    //             "gst" => $gst
    //         ];
    //         // dd($this->selectedUser);
    //         $this->buyerName = $this->selectedUser['buyer_name'];
    //         $this->createChallanRequest['purchase_order_series'] = $purchaseOrderSeries;
    //         $this->createChallanRequest['buyer_name'] = $buyer;
    //         $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->seller_user_id;
    //         $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //         $this->createChallanRequest['feature_id'] = 23;
    //         $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //         $this->inputsDisabled = false; // Adjust the condition as needed
    //     }
    // }

    public function selectUser($purchase_order_series, $address, $email, $phone, $gst, $buyer, $selectedUserDetails)
    {
        // dd($selectedUserDetails);
        try {
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '4')->select('series_number')->first();

            if ($purchase_order_series == 'Not Assigned') {
                if ($series == null) {
                    throw new \Exception('Please add one default Series number');
                }
                $purchase_order_series = $series->series_number;
                $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchase_order_series)
                    ->where('buyer_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            } else {
                $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchase_order_series)
                    ->where('buyer_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            }

            $this->inputsDisabled = false; // Adjust the condition as needed

            $this->selectedUser = [
                "purchase_order_series" => $purchaseOrderSeries,
                "seriesNumber" => $seriesNum,
                "address" => $address,
                "email" => $email,
                "phone" => $phone,
                'buyer_name' => $buyer,
                "gst" => $gst
            ];
            // Decode $selectedUserDetails once
            $decodedUserDetails = json_decode($selectedUserDetails);
            $this->createChallanRequest['purchase_order_series'] = $purchaseOrderSeries;
                $this->createChallanRequest['buyer_name'] = $buyer;
                $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->seller_user_id;
                $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                $this->createChallanRequest['feature_id'] = 23;
                $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
                $this->createChallanRequest['order_date'] = now()->format('Y-m-d');
                $this->inputsDisabled = false; // Adjust the condition as needed
                // dd($this->selectedUserDetails);

            // Fetch billTo data
            // $request = request();
            // $billTo = new ReceiversController;
            // $responseContent = $billTo->index($request)->content();
            // $decompressedContent = gzdecode($responseContent);
            // $decodedResponse = json_decode($decompressedContent);
            // // dd($decodedResponse);
            // if ($decodedResponse === null) {
            //     // Handle error: invalid JSON or empty response
            //     $this->billTo = [];
            // } else {
            //     $this->billTo = collect($decodedResponse->data)
            //         ->filter(function ($item) {
            //             return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
            //         })
            //         ->map(function ($item) {
            //             $receiverName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
            //             return (object) array_merge((array) $item, ['receiver_name' => $receiverName]);
            //         })
            //         ->sortBy(function ($item) {
            //             $receiverName = strtolower($item->receiver_name);
            //             return is_numeric($receiverName[0]) ? 'z' . $receiverName : $receiverName;
            //         })
            //         ->values()
            //         ->all();
            // }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in selectUser method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while processing your request.';
            return;
        }
    }
    public function selectUserAddress($selectedUserDetails)
    {
        $selectedUserDetails = json_decode($selectedUserDetails);

        // $this->selectedUser = [
        //     // "purchase_order_series" => $purchase_order_series,
        //     // "seriesNumber" => $seriesNum,
        //     "address" => $selectedUserDetails->address,
        //     // "email" => $selectedUserDetails->email,
        //     "phone" => $selectedUserDetails->phone,
        //     "gst" => $selectedUserDetails->gst_number
        // ];

        $this->selectedUser['address'] = $selectedUserDetails->address;
        $this->selectedUser['phone'] = $selectedUserDetails->phone;
        $this->selectedUser['gst'] = $selectedUserDetails->gst_number;
        $this->createChallanRequest['buyer_detail_id'] = $selectedUserDetails->id;
        $this->createChallanRequest['buyer_name'] = $selectedUserDetails->seller_name;

        // dd($this->selectedUser);
    }


    public function saveRows($requestData)
    {
        $request = request();
        // dd($requestData);
         // Update the createChallanRequest with the new data
         $this->createChallanRequest['order_details'] = $requestData['order_details'];
         $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
         $this->createChallanRequest['total'] = $requestData['total'];
         $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];

        $sellerName = Auth::guard(Auth::getDefaultDriver())->user()->name;
        $sellerId = Auth::guard(Auth::getDefaultDriver())->user()->id;

        // dd($this->createChallanRequest);
        $request->merge($this->createChallanRequest);
        // dd($request);
        // Filter out the empty rows
        $errors = false;
        // dd($request->all());
        // Check if there is an empty 'Article' in the order details
        foreach ($request->order_details as $index => $order_detail) {
            // Check if 'qty' is null
            if (is_null($order_detail['qty'])) {
                $this->addError('qty.' . $index, 'Required.');
                $errors = true;
            }
           // Check if 'columns' is set and validate 'Article' column
            if (isset($order_detail['columns'])) {
                foreach ($order_detail['columns'] as $column) {
                    if ($column['column_name'] == 'Article' && empty($column['column_value'])) {
                        // Use Livewire's error messaging system
                        $this->addError('article.' . $index, 'Required.');
                        $errors = true;
                    }
                }
            }
        }

        if ($errors) {
            return;
        }
        // Create instances of necessary classes
        $purchaseOrderDate = now()->format('Y-m-d');
        $request->merge([
            'seller_name' => $sellerName,
            'seller_id' => $sellerId,
            'order_date' => $purchaseOrderDate,
        ]);
        // dd($request);
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->purchaseOrderId = $result->purchase_order_id;

            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function purchaseOrderEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;
        $this->reset([ 'message', 'save']);
    }


    public function editRows($requestData)
    {
        $request = request();
        // Update the createChallanRequest with the new data
       $this->createChallanRequest['order_details'] = $requestData['order_details'];
       $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
       $this->createChallanRequest['total'] = $requestData['total'];
       $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];


        $request->merge($this->createChallanRequest);

        // Create instances of necessary classes
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->update($request, $this->purchaseOrderId);
        $result = $response->getData();
        // dd($result);

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->challanSave = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->purchaseOrderId = $result->purchase_order_id;

            $this->reset([ 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }

    public function sendPurchaseOrder($id)
    {

        $request = request();
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->send($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // $this->innerFeatureRedirect('purchase_order', '19');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // redirect()->route('buyer');
        return redirect()->route('buyer', ['template' => 'purchase_order'])->with('message', $this->successMessage ?? $this->errorMessage);
    }




    public function render()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $request = request();
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

        return view('livewire.buyer.content.create-purchase-order',[
            'stocks' => $products,
        ]);
    }
}
