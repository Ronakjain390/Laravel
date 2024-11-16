<?php

namespace App\Http\Livewire\Seller\Screen;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Estimates;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyer\BuyersController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use Livewire\Component;

class QuotationToInvoice extends Component
{
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
    public $estimateId;
    public $buyerName;
    public $estimate_series;
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

    public function mount($estimateId = null)
    {
        // Check if estimateId is null, if so, try to get it from the session
        if ($estimateId === null) {
            $estimateId = Session::get('quotation_to_invoice_id');
        }

        // If estimateId is still null, redirect to the seller dashboard or show an error
        if ($estimateId === null) {
            // You can choose to redirect or show an error message
            // return redirect()->route('seller')->with('error', 'No estimate selected for conversion');
            $this->addError('estimate', 'No estimate selected for conversion');
            return;
        }

        $this->estimateId = $estimateId;
        $this->loadEstimateToInvoiceData();
        $this->context = 'invoice';
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute

        $this->fetchTeamMembers();

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
        $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');

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

        // Clear the session variable after use
        // Session::forget('estimate_to_invoice_id');
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

    private function loadEstimateToInvoiceData()
    {
        // Implement the logic to load estimate data and populate the invoice fields
        // This method should fetch the estimate details using $this->estimateId
        // and set the relevant data in $this->create_invoice_request

        // Example (you'll need to adjust this based on your actual data structure):
        $data = Estimates::where('id', $this->estimateId)->with(['orderDetails', 'orderDetails.columns', 'buyerUser', 'buyerUser'])->first();;
        // $data = PurchaseOrder::where('id', $this->poId)->with(['orderDetails', 'orderDetails.columns', 'buyerUser', 'buyerUser'])->first();
        // dd($data);
        $buyerUser = $data->buyerUser;

        // Extract necessary data from $buyerUser
        $invoiceSeries = 'Not Assigned'; // or any default value
        $address = $buyerUser->address;
        $email = $buyerUser->email;
        $phone = $buyerUser->phone;
        $gst = $buyerUser->gst_number;
        $state = $buyerUser->state;
        $buyer = $buyerUser->name;
        $pincode = $buyerUser->pincode;
        $city = $buyerUser->city;
        $selectedUserDetails = json_encode([
            'buyer_user_id' => $buyerUser->id,
            'user' => [
                'details' => $buyerUser
            ]
        ]);
        // dd($selectedUserDetails);
        // $this->create_invoice_request['buyer_id'] = $data->seller_id;
        // Call selectUser method
        $this->selectUser($invoiceSeries, $address, $email, $phone, $pincode, $city, $gst, $state, $buyer, $selectedUserDetails);
        $this->challanModifyData = json_encode($data);
        $modifiedDataArray = json_decode(json_encode($data), true);
        $this->estimate_series = $data->estimate_series . '-' . $data->series_num;

        // Ensure each order detail has an 'id' key
        $modifiedDataArray['order_details'] = collect($modifiedDataArray['order_details'])->map(function ($detail) {
            if (!isset($detail['id'])) {
                $detail['id'] = $detail['purchase_order_id'] ?? null; // or use any other unique identifier
            }
            return $detail;
        })->toArray();

        $this->create_invoice_request = $modifiedDataArray;
    }

    public function selectUser($invoiceSeries, $address, $email, $pincode, $city, $phone, $gst, $state, $buyer, $selectedUserDetails)
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
                "gst" => $gst,
                "city" => $city,
                "state" => $state,
                "pincode" => $pincode,
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

        $this->buyerAddress = $selectedUserDetail->address;
        // dd($this->buyerAddress);
        // $this->create_invoice_request['receiver_detail_id'] = $selectedUserDetail->id;
        $this->create_invoice_request['user_detail_id'] = $selectedUserDetail->id;
        $this->selectedUserDetails = json_decode($selectedUserDetails);

        // dd($this->selectedUser);
        $request = request();

        $billTo = new BuyersController;
        $this->billTo = $billTo->index($request)->getData()->data;
        // dd($this->selectedUser);
    }

    public function saveRows($requestData)
    {

        $request = request();
        // dd($this->estimate_series);
         // Access the series number from the create_invoice_request array
        // $seriesNumber = $this->create_invoice_request['series_num'];
        // dd($this->create_invoice_request['series_num']);
        // Update the create_invoice_request with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->create_invoice_request['series_num'] = $this->selectedUser['invoiceNumber'] ?? null;
        $this->create_invoice_request['invoice_series'] = $this->selectedUser['invoiceSeries'] ?? null;
        $this->create_invoice_request['estimate_series'] = $this->estimate_series;
          // Ensure statuses are included
          if (!isset($this->create_invoice_request['statuses'])) {
            $this->create_invoice_request['statuses'] = [
                ['comment' => $this->status_comment ?? '']
            ];
        }

        $request->merge($this->create_invoice_request);
        // dd($request, $this->create_invoice_request['series_num'], $this->create_invoice_request['invoice_series'] ?? null);
        if($this->updateForm == false)
        {
            $request->merge($this->addBuyerData);
            // dd($request);
            if($request->phone || $request->email){
                // dd($request);

            $BuyersController = new BuyersController;
            $response = $BuyersController->addManualBuyer($request);
            $result = $response->getData();

            }

            $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1")
                ->where('panel_id', '3')
                ->first();
                $invoiceSeries = $series->series_number;
                $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                // ->max('series_num');
                ->get();
                    // dd($latestSeriesNum);
                    // if ($latestSeriesNum->isNotEmpty()) {
                        // Get the maximum 'series_num' from the collection
                        $maxSeriesNum = $latestSeriesNum->max('series_num');

                        // Now $maxSeriesNum contains the maximum 'series_num'
                        // You can use it as needed
                        // echo $maxSeriesNum;
                        // dd($maxSeriesNum);
                    // }
                // Increment the latestSeriesNum for the new challan
                $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;
                // dd($seriesNum);
                // $this->create_invoice_request['invoice_series'] = $invoiceSeries;
                // $this->create_invoice_request['buyer'] = $buyer;
                // $this->create_invoice_request['buyer_id'] = json_decode($selectedUserDetails)->buyer_user_id;
                // $this->create_invoice_request['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                // $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;


                $this->create_invoice_request['invoice_series'] = $invoiceSeries;
                $this->create_invoice_request['series_num'] = $invoiceNum;
                if (isset($result->buyer->buyer_name)) {
                    $this->create_invoice_request['buyer'] = $result->buyer->buyer_name;
                    $this->create_invoice_request['buyer_id'] = $result->buyer->buyer_user_id;
                } elseif (isset($request->buyer_name)) {
                    $this->create_invoice_request['buyer'] = $request->buyer_name;
                    $this->create_invoice_request['buyer_id'] = null;
                    $this->sendButtonDisabled = false;
                } else {
                    $this->create_invoice_request['buyer'] = 'Others';
                    $this->create_invoice_request['buyer_id'] = null;
                }

                // $this->create_invoice_request['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                // $this->create_invoice_request['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
                $this->create_invoice_request['feature_id'] = 13;

        }
        $this->create_invoice_request['calculate_tax'] = $this->calculateTax;
        foreach ($this->create_invoice_request['order_details'] as $index => $orderDetail) {
            $this->create_invoice_request['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }
        $request->merge($this->create_invoice_request);

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
        $invoiceController = new InvoiceController;
        $response = $invoiceController->store($request);
        $result = $response->getData();
        // Check the status code from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->save = $result->message;
            $this->inputsDisabled = true;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
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

    // Send invoice
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
            $this->reset(['statusCode', 'message', 'errorMessage']);
            // $this->innerFeatureRedirect('sent_invoice', '13');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function invoiceEdit()
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


    public function render()
    {
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
        return view('livewire.seller.screen.quotation-to-invoice', [
            'stocks' => $products,
        ]);
    }
}
