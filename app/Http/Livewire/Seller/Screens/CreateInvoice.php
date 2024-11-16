<?php

namespace App\Http\Livewire\Seller\Screens;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Buyer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PanelSeriesNumber;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

use Livewire\Component;

class CreateInvoice extends Component
{

    public $sfpModal = false;
    public $mainUser;
    public $errorMessage;
    public $pdfData;
    public $panelColumnDisplayNames;
    public $panelUserColumnDisplayNames;
    public $columnDisplayNames;
    public $billTo;
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
    public $userSelected = false;
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $sendButtonDisabled = true;
    public $updateForm = true;
    public $selectedUser;
    public $selectedUserDetails = [];
    public $admin_ids = [];
    public $team_user_ids = [];
    public $calculateTax = true;
    public $hideWithoutTax = true;
    public $productId;
    public $data;
    public $quantity;
    public $selectAll = false;
    public $totalAmount;
    public $showInputBoxes = true;
    public $isLoading = true;
    public $products, $articles = [], $locations = [], $item_codes, $city, $Article, $location, $item_code, $warehouse, $category, $from, $to;
    public $authUserState;
    public $selectedProductIds = [];
    public $rows = [];
    public $context;
    public $showBarcode;
    public $action = 'save';
    public $invoiceId;
    use WithPagination;

    public $buyerName, $buyerAddress;

    protected $listeners = [
        'seriesNumberUpdated' => 'updateSeriesNumber',
    ];
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

    public $addBuyerData = array(
        'buyer_name' => '',
        'company_name' => '',
        'email' => '',
        'address' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'organisation_type' => '',
        'buyer_special_id' => '',
        'gst_number'=> '',
    );

    // public function updateSeriesNumber($newSeriesNumber)
    // {
    //     $request = request();
    //     $request->merge(['series_num' => $newSeriesNumber]);

    //     $this->create_invoice_request['series_num'] = $newSeriesNumber;
    //     $this->series_num = $newSeriesNumber;
    //     $this->disabledButtons = true;
    // }

    public function updateSeriesNumber($newSeriesNumber)
    {
        // dd($newSeriesNumber);
        $this->selectedUser['invoiceNumber'] = $newSeriesNumber;
        // \Log::info('Updated invoiceNumber:', ['invoiceNumber' => $this->selectedUser['invoiceNumber']]);
        $this->disabledButtons = true;
    }



    // public function mount()
    // {
    //     $request = request();
    //     $this->loadStocks = true;
    //     $query = new TeamUserController;
    //     $query = $query->index();
    //     $status = $query->getStatusCode();
    //     $query = $query->getData();
    //     if ($status === 200) {
    //         $this->teamMembers = $query->data;
    //     } else {
    //         $this->errorMessage = json_encode($query->errors);
    //         $this->reset(['status', 'successMessage']);
    //     }

    //         $userAgent = $request->header('User-Agent');

    //         // Check if the User-Agent indicates a mobile device
    //         $this->isMobile = isMobileUserAgent($userAgent);
    //         $UserResource = new UserAuthController;
    //         $response = $UserResource->user_details($request);
    //         $response = $response->getData();
    //         if ($response->success == "true") {
    //             $this->mainUser = json_encode($response->user);
    //             // $this->successMessage = $response->message;
    //             $this->reset(['errorMessage']);
    //         } else {
    //             $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    //         }

    //     $PanelColumnsController = new PanelColumnsController;

    //     $request->merge([
    //         'feature_id' => 12,
    //         // Auth::guard(Auth::getDefaultDriver())->user()->id
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

    //     // Add from stock modal data
    //     $request = request();
    //     $columnFilterDataset = [
    //         'feature_id' => 12,
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

    //     ];
    //     $request->merge($columnFilterDataset);
    //     $PanelColumnsController = new PanelColumnsController;
    //     $columnsResponse = $PanelColumnsController->index($request);
    //     $columnsData = json_decode($columnsResponse->content(), true);
    //     $ColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $columnsData['data']);

    //     $this->ColumnDisplayNames = $ColumnDisplayNames;
    //     array_push($this->ColumnDisplayNames, 'item code','category', 'location','warehouse', 'unit', 'qty', 'rate');


    //     $request = request();
    //     $request->merge([
    //         'article' => $this->Article ?? null,
    //         'location' => $this->location ?? null,
    //         'item_code' => $this->item_code ?? null,
    //     ]);
    //     $products = new ProductController;
    //     $response = $products->index($request);
    //     $result = $response->getData();
    //     $this->products = (array) $result->data;
    //     $this->articles = [];
    //     foreach ($this->products as $product) {
    //         array_push($this->articles, $product->details[0]->column_value);
    //     }
    //     $this->item_codes = array_unique(array_column($this->products, 'item_code'));
    //     $this->locations = array_unique(array_column($this->products, 'location'));

    //     // dd( $this->products);

    //     $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');
    //     // Set the status code and message received from the result
    //     // $this->statusCode = $result->status_code;
    //     // $this->products = [];
    //     // dd($this->billTo);
    //     $units = new UnitsController;
    //     $unitsCollection = $units->index('seller')->original;
    //     $this->units = $unitsCollection->map(function ($unit) {
    //         return [
    //             'id' => $unit->id,
    //             'unit' => $unit->unit,
    //             'short_name' => $unit->short_name,
    //             'is_default' => $unit->is_default,
    //         ];
    //     })->toArray();
    //     $billTo = new BuyersController;
    //     $this->billTo = $billTo->index($request)->getData()->data;
    //     // $this->billTo = collect($this->billTo)->sortBy(function ($item) {
    //     //     return strtolower($item->buyer_name);
    //     // })->values()->all();
    //     // dd($this->billTo);

    //     if ($this->billTo === null) {
    //         // Handle error: invalid JSON or empty response
    //         $this->billTo = [];
    //     } else {
    //         $this->billTo = collect($this->billTo)
    //             ->filter(function ($item) {
    //                 return !empty($item->buyer_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
    //             })
    //             ->map(function ($item) {
    //                 $buyerName = !empty($item->buyer_name) ? $item->buyer_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
    //                 return (object) array_merge((array) $item, ['buyer_name' => $buyerName]);
    //             })
    //             ->sortBy(function ($item) {
    //                 $buyerName = strtolower($item->buyer_name);
    //                 return is_numeric($buyerName[0]) ? 'z' . $buyerName : $buyerName;
    //             })
    //             ->values()
    //             ->all();
    //     }
    //         $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');
    //         $this->initializeRows();
    //         $this->context = 'invoice';
    //         $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute

    //         // Fetch panel settings
    //         $panelSettings = \App\Models\PanelSettings::where('user_id', Auth::user()->id)->first();
    //         if ($panelSettings) {
    //             $settings = json_decode($panelSettings)->settings;
    //             // dd($settings);
    //             if (isset($settings) && isset($settings->seller)) {
    //                 $senderSettings = $settings->seller;
    //                 $this->showBarcode = $senderSettings->barcode ?? false;
    //             }
    //         }
    // }

    public function mount()
    {
        $this->loadStocks = true;

        // Load team members
        $this->loadTeamMembers();

        // Check if the user is on a mobile device
        $this->isMobile = $this->checkMobileUserAgent(request()->header('User -Agent'));

        // Load main user details
        $this->loadMainUserDetails();

        // Load panel column display names
        $this->loadPanelColumnDisplayNames();

        // Load stock modal data
        $this->loadStockModalData();

        // Load products
        $this->loadProducts();

        // Load units
        $this->loadUnits();

        // Load buyers
        $this->loadBuyers();

        // Set invoice date
        $this->create_invoice_request['invoice_date'] = now()->format('Y-m-d');

        // Initialize additional properties
        $this->initializeRows();
        $this->context = 'invoice';
        $this->authUserState = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->state;

        // Fetch panel settings
        $this->loadPanelSettings();
    }

    private function loadTeamMembers()
    {
        $query = (new TeamUserController)->index();
        $status = $query->getStatusCode();
        $queryData = $query->getData();

        if ($status === 200) {
            $this->teamMembers = $queryData->data;
        } else {
            $this->errorMessage = json_encode($queryData->errors);
            $this->reset(['status', 'successMessage']);
        }
    }

    private function checkMobileUserAgent($userAgent)
    {
        return isMobileUserAgent($userAgent);
    }

    private function loadMainUserDetails()
    {
        $response = (new UserAuthController)->user_details(request());
        $responseData = $response->getData();

        if ($responseData->success == "true") {
            $this->mainUser  = json_encode($responseData->user);
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($responseData->errors ?? [[$responseData->message]]);
        }
    }

    private function loadPanelColumnDisplayNames()
    {
        $userId = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $this->panelColumnDisplayNames = $this->getPanelColumnDisplayNames($userId);
        $this->panelUserColumnDisplayNames = $this->getPanelUserColumnDisplayNames($userId);
    }

    private function getPanelColumnDisplayNames($userId)
    {
        $request = request()->merge(['feature_id' => 12, 'user_id' => $userId]);
        $response = (new PanelColumnsController)->index($request);
        $columnsData = json_decode($response->content(), true);

        return array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, array_filter($columnsData['data'], fn($column) => $column['feature_id'] == 12));
    }

    private function getPanelUserColumnDisplayNames($userId)
    {
        $request = request()->merge(['feature_id' => 12, 'user_id' => $userId]);
        $response = (new PanelColumnsController)->index($request);
        $columnsUserData = json_decode($response->content(), true);

        return array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, array_filter($columnsUserData['data'], fn($column) => $column['feature_id'] == 12));
}

private function loadStockModalData()
{
    $userId = Auth::getDefaultDriver() == 'team-user'
        ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
        : Auth::guard(Auth::getDefaultDriver())->user()->id;

    $request = request()->merge(['feature_id' => 12, 'user_id' => $userId]);
    $response = (new PanelColumnsController)->index($request);
    $columnsData = json_decode($response->content(), true);
    $this->ColumnDisplayNames = array_map(function ($column) {
        return $column['panel_column_display_name'];
    }, $columnsData['data']);

    array_push($this->ColumnDisplayNames, 'item code', 'category', 'location', 'warehouse', 'unit', 'qty', 'rate');
}

private function loadProducts()
{
    $request = request()->merge([
        'article' => $this->Article ?? null,
        'location' => $this->location ?? null,
        'item_code' => $this->item_code ?? null,
    ]);

    $response = (new ProductController)->index($request);
    $result = $response->getData();
    $this->products = (array) $result->data;
    $this->articles = array_column($this->products, 'details.0.column_value');
    $this->item_codes = array_unique(array_column($this->products, 'item_code'));
    $this->locations = array_unique(array_column($this->products, 'location'));
}

private function loadUnits()
{
    $unitsCollection = (new UnitsController)->index('seller')->original;
    $this->units = $unitsCollection->map(function ($unit) {
        return [
            'id' => $unit->id,
            'unit' => $unit->unit,
            'short_name' => $unit->short_name,
            'is_default' => $unit->is_default,
        ];
    })->toArray();
}

private function loadBuyers()
{
    $response = (new BuyersController)->index(request())->getData()->data;

    if ($response === null) {
        $this->billTo = [];
    } else {
        $this->billTo = collect($response)
            ->filter(fn($item) => !empty($item->buyer_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email))
            ->map(function ($item) {
                $buyerName = !empty($item->buyer_name) ? $item->buyer_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                return (object) array_merge((array) $item, ['buyer_name' => $buyerName]);
            })
            ->sortBy(fn($item) => strtolower($item->buyer_name[0]) === '0' ? 'z' . strtolower($item->buyer_name) : strtolower($item->buyer_name))
            ->values()
            ->all();
    }
}

private function loadPanelSettings()
{
    $panelSettings = \App\Models\PanelSettings::where('user_id', Auth::user()->id)->first();
    if ($panelSettings) {
        $settings = json_decode($panelSettings)->settings;
        if (isset($settings) && isset($settings->seller)) {
            $senderSettings = $settings->seller;
            $this->showBarcode = $senderSettings->barcode ?? false;
        }
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

    public function getUnitProperty()
    {
        $unitData = new UnitsController();
        $response = $unitData->index();
        return json_decode($response->getContent(), true);
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

    public function checkPincodeLength($pincode)
    {
        if (strlen($pincode) === 6) {
            $this->cityAndStateByPincode($pincode);
        }
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
                'buyer_name' => $this->existingUser->name,
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



    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();
                $invoiceSeries = $series->series_number;
                $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            $this->inputsDisabled = false; // Adjust the condition as needed
            $this->selectedUser = [
                "invoiceSeries" => $invoiceSeries,
                "invoiceNumber" => $seriesNum,
            ];
        // $this->dispatchBrowserEvent('inputsDisabledChanged', ['value' => false]);
    }

    // public function selectUser($invoiceSeries, $address, $email, $phone, $gst, $buyer, $selectedUserDetails)
    // public function selectUser($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails)
    // {
    //     // Start logging
    //     Log::info('selectUser method started');
    //     $this->userSelected = true;

    //     try {
    //         DB::beginTransaction();

    //         $startTime = microtime(true);

    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //         $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();

    //         if ($invoiceSeries == 'Not Assigned') {
    //             if ($series == null) {
    //                 throw new \Exception('Please add one default Series number');
    //             }
    //             $invoiceSeries = $series->series_number;
    //             $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
    //                 ->where('seller_id', $userId)
    //                 ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    //         } else {
    //             $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
    //                 ->where('seller_id', $userId)
    //                 ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    //         }

    //         $this->inputsDisabled = false; // Adjust the condition as needed
    //         $this->selectedUser = [
    //             "invoiceSeries" => $invoiceSeries,
    //             "invoiceNumber" => $seriesNum,
    //             "address" => $address,
    //             "buyer_name" => $buyer,
    //             "email" => $email,
    //             "phone" => $phone,
    //             "gst" => $gst,
    //             "city" => $city,
    //             "state" => $state,
    //             "pincode" => $pincode,
    //         ];
    //         // dd($this->selectedUser);
    //         // Decode $selectedUserDetails once
    //         $decodedUserDetails = json_decode($selectedUserDetails);
    //         $this->buyerName = $this->selectedUser['buyer_name'];
    //         $this->create_invoice_request['invoice_series'] = $invoiceSeries;
    //         $this->create_invoice_request['series_num'] = $seriesNum;
    //         $this->create_invoice_request['buyer'] = $buyer;
    //         $this->create_invoice_request['buyer_id'] = $decodedUserDetails->buyer_user_id;
    //         $this->create_invoice_request['feature_id'] = 13;
    //         $this->selectedUserDetails = $decodedUserDetails->user->details;
    //         $this->city = $decodedUserDetails->city;
    //         $this->state = $decodedUserDetails->state;
    //         $this->pincode = $decodedUserDetails->pincode;
    //         $this->inputsDisabled = false; // Adjust the condition as needed

    //         // Fetch billTo data
    //         $request = request();
    //         $billTo = new BuyersController;
    //         $this->billTo = $billTo->index($request)->getData()->data;
    //         if ($this->billTo === null) {
    //             // Handle error: invalid JSON or empty response
    //             $this->billTo = [];
    //         } else {
    //             $this->billTo = collect($this->billTo)
    //                 ->filter(function ($item) {
    //                     return !empty($item->buyer_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
    //                 })
    //                 ->map(function ($item) {
    //                     $buyerName = !empty($item->buyer_name) ? $item->buyer_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
    //                     return (object) array_merge((array) $item, ['buyer_name' => $buyerName]);
    //                 })
    //                 ->sortBy(function ($item) {
    //                     $buyerName = strtolower($item->buyer_name);
    //                     return is_numeric($buyerName[0]) ? 'z' . $buyerName : $buyerName;
    //                 })
    //                 ->values()
    //                 ->all();
    //         }

    //         DB::commit();

    //         $endTime = microtime(true);
    //         $executionTime = $endTime - $startTime;
    //         Log::info('selectUser method completed', ['execution_time' => $executionTime]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error in selectUser method: ' . $e->getMessage());
    //         $this->errorMessage = 'An error occurred while processing your request.';
    //         return;
    //     }
    // }

    public $showSeriesExpirationModal = false;

    public $tempUserData = [];

    public function selectUser($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails)
    {
        try {
            $this->userSelected = true;
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', "1")
                ->where('panel_id', '3')
                ->first();

            if (!$series) {
                throw new \Exception('No default series number found. Please add a default series number.');
            }

            $currentDate = now();
            $validTill = Carbon::parse($series->valid_till);

            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $invoiceSeries;

                // Store the user data temporarily
                $this->tempUserData = [
                    'invoiceSeries' => $invoiceSeries,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'buyer' => $buyer,
                    'selectedUserDetails' => $selectedUserDetails,
                ];

                DB::rollBack();
                return;
            }

            $this->inputsDisabled = false;
            // If the series is not expired, proceed with the normal flow
            $this->invoiceNumber($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails, $userId, $series);

            // Fetch billTo data
            $this->fetchBillToData();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in selectUser method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while processing your request: ' . $e->getMessage();
            return;
        }
    }
  public function invoiceNumber($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails, $userId, $series)
    {
        if ($invoiceSeries == 'Not Assigned') {
            if ($series == null) {
                throw new \Exception('Please add one default Series number');
            }
            $invoiceSeries = $series->series_number;
        } else {
            // Check if the selected series is valid and not expired
            $selectedSeries = PanelSeriesNumber::where('user_id', $userId)
                ->where('series_number', $invoiceSeries)
                ->where('panel_id', '3')
                ->first();

            if (!$selectedSeries) {
                throw new \Exception('Invalid series number selected.');
            }

            $validTill = Carbon::parse($selectedSeries->valid_till);
            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $invoiceSeries;
                // Store the user data temporarily
                $this->tempUserData = [
                    'invoiceSeries' => $invoiceSeries,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'buyer' => $buyer,
                    'selectedUserDetails' => $selectedUserDetails,
                ];
                // dd($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails, $userId, $series);
                return;
            }
        }

        $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

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

        $decodedUserDetails = json_decode($selectedUserDetails);
        $this->buyerName = $this->selectedUser['buyer_name'];
        $this->create_invoice_request['invoice_series'] = $invoiceSeries;
        $this->create_invoice_request['series_num'] = $seriesNum;
        $this->create_invoice_request['buyer'] = $buyer;
        $this->create_invoice_request['buyer_id'] = $decodedUserDetails->buyer_user_id;
        $this->create_invoice_request['feature_id'] = 1;
        $this->selectedUserDetails = $decodedUserDetails->user->details;
    }

    public function useDefaultSeries()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $defaultSeries = PanelSeriesNumber::where('user_id', $userId)
            ->where('default', "1")
            ->where('panel_id', '3')
            ->first();

        if (!$defaultSeries) {
            $this->errorMessage = 'No default series number found. Please add a default series number.';
            return;
        }

        $validTill = Carbon::parse($defaultSeries->valid_till);
        if ($validTill->isPast()) {
            $this->errorMessage = 'The default series number has also expired. Please update your series numbers.';
            return;
        }

        // Use the default series number
        $this->selectedUser['invoiceSeries'] = $defaultSeries->series_number;
        $this->create_invoice_request['invoice_series'] = $defaultSeries->series_number;

        // Recalculate the series number

        $latestSeriesNum = Invoice::where('invoice_series', $defaultSeries->series_number)
            ->where('seller_id', $userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $this->selectedUser['invoiceNumber'] = $seriesNum;
        $this->create_invoice_request['series_num'] = $seriesNum;

        $this->showSeriesExpirationModal = false;
        $this->errorMessage = null;
    }

    private function fetchBillToData()
    {
        try {
            $request = request();
            $billTo = new BuyersController;
            $this->billTo = $billTo->index($request)->getData()->data;
            if ($this->billTo === null) {
                // Handle error: invalid JSON or empty response
                $this->billTo = [];
            } else {
                $this->billTo = collect($this->billTo)
                    ->filter(function ($item) {
                        return !empty($item->buyer_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                    })
                    ->map(function ($item) {
                        $buyerName = !empty($item->buyer_name) ? $item->buyer_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                        return (object) array_merge((array) $item, ['buyer_name' => $buyerName]);
                    })
                    ->sortBy(function ($item) {
                        $buyerName = strtolower($item->buyer_name);
                        return is_numeric($buyerName[0]) ? 'z' . $buyerName : $buyerName;
                    })
                    ->values()
                    ->all();
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching billTo data: ' . $e->getMessage());
            $this->billTo = [];
        }
    }

    public function useDefaultSeriesNumber()
    {
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            // Fetch the default series
            $defaultSeries = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', "1")
                ->where('panel_id', '3')
                ->first();

            if (!$defaultSeries) {
                $this->errorMessage = 'No default series number found. Please add a default series number.';
                return;
            }

            // Check if the default series is valid
            $validTill = Carbon::parse($defaultSeries->valid_till);
            if ($validTill->isPast()) {
                $this->errorMessage = 'The default series number has expired. Please update your series numbers.';
                return;
            }

            // Use the stored temporary data
            if (!empty($this->tempUserData)) {
                $this->invoiceNumber(
                    $defaultSeries->series_number,
                    $this->tempUserData['address'],
                    $this->tempUserData['city'],
                    $this->tempUserData['state'],
                    $this->tempUserData['pincode'],
                    $this->tempUserData['email'],
                    $this->tempUserData['phone'],
                    $this->tempUserData['gst'],
                    $this->tempUserData['buyer'],
                    $this->tempUserData['selectedUserDetails'],
                    $userId,
                    $defaultSeries
                );

                // Clear the temporary data
                $this->tempUserData = [];
            } else {
                $this->errorMessage = 'User data not found. Please try selecting the user again.';
                return;
            }

            // Fetch BillTo data
            $this->fetchBillToData();

            // Reset error message and close modal
            $this->showSeriesExpirationModal = false;
            $this->errorMessage = null;

        } catch (\Exception $e) {
            \Log::error('Error in useDefaultSeriesNumber method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while using the default series number: ' . $e->getMessage();
        }
    }


    protected function getListeners()
    {
        return array_merge(parent::getListeners(), [
            'openUpdateSeriesModal' => 'openUpdateSeriesModal',
        ]);
    }

    public function openUpdateSeriesModal()
    {
        return redirect()->route('seller', ['template' => 'invoice_series_no']);
        $this->showSeriesExpirationModal = false;
        $this->showUpdateSeriesModal = true;
    }

    public function closeUpdateSeriesModal()
    {
        $this->showSeriesExpirationModal = false;
        $this->showUpdateSeriesModal = true;
    }

    public $buyerPhone;

    public function filterVariable($variable, $value)
    {
        $this->{$variable} = $value;

        $request = request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
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
    }

    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;
    }

    public function selectUserAddress($selectedUserDetail, $selectedUserDetails)
    {
        // $selectedUserDetails = json_decode($selectedUserDetails);
        $selectedUserDetail = json_decode($selectedUserDetail);
        $this->buyerAddress = $selectedUserDetail->address;
        $this->receiverLocation = $selectedUserDetail->location_name;
        $this->receiverPhone = $selectedUserDetail->phone;
        // dd($selectedUserDetail);
        // $this->selectedUser['address'] = $selectedUserDetail->address;
        // $this->selectedUser['phone'] = $selectedUserDetail->phone;
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

        if ($this->billTo === null) {
            // Handle error: invalid JSON or empty response
            $this->billTo = [];
        } else {
            $this->billTo = collect($this->billTo)
                ->filter(function ($item) {
                    return !empty($item->buyer_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                })
                ->map(function ($item) {
                    $buyerName = !empty($item->buyer_name) ? $item->buyer_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                    return (object) array_merge((array) $item, ['buyer_name' => $buyerName]);
                })
                ->sortBy(function ($item) {
                    $buyerName = strtolower($item->buyer_name);
                    return is_numeric($buyerName[0]) ? 'z' . $buyerName : $buyerName;
                })
                ->values()
                ->all();
        }
        // dd($this->billTo);
    }

    public function saveRows($requestData)
    {

        $request = request();
        // dd($requestData);
        // Update the create_invoice_request with the new data
        $this->create_invoice_request['order_details'] = $requestData['order_details'];
        $this->create_invoice_request['total_qty'] = $requestData['total_qty'];
        $this->create_invoice_request['total'] = $requestData['total'];
        $this->create_invoice_request['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->create_invoice_request['series_num'] = $this->selectedUser['invoiceNumber'] ?? null;
        $request->merge($this->create_invoice_request);
        // dd($request);
        // if($this->updateForm == false)
        // {
        //     $request->merge($this->addBuyerData);
        //     $result = null;


        //     if($request->phone || $request->email){
        //         // dd($request);

        //     $BuyersController = new BuyersController;
        //     $response = $BuyersController->addManualBuyer($request);
        //     $result = $response->getData();

        //     }

        //     $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        //         ->where('default', "1")
        //         ->where('panel_id', '3')
        //         ->first();
        //         $invoiceSeries = $series->series_number;
        //         $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
        //         ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        //         // ->max('series_num');
        //         ->get();
        //             // dd($latestSeriesNum);
        //             if ($latestSeriesNum->isNotEmpty()) {
        //                 // Get the maximum 'series_num' from the collection
        //                 $maxSeriesNum = $latestSeriesNum->max('series_num');

        //                 // Now $maxSeriesNum contains the maximum 'series_num'
        //                 // You can use it as needed
        //                 // echo $maxSeriesNum;
        //                 // dd($maxSeriesNum);
        //             }
        //         // Increment the latestSeriesNum for the new challan
        //         $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;
        //         // dd($seriesNum);
        //         // $this->create_invoice_request['invoice_series'] = $invoiceSeries;
        //         // $this->create_invoice_request['buyer'] = $buyer;
        //         // $this->create_invoice_request['buyer_id'] = json_decode($selectedUserDetails)->buyer_user_id;
        //         // $this->create_invoice_request['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
        //         // $this->create_invoice_request['feature_id'] = $this->persistedActiveFeature;


        //         $this->create_invoice_request['invoice_series'] = $invoiceSeries;
        //         // $this->create_invoice_request['series_num'] = $invoiceNum;
        //         if (isset($result->buyer->buyer_name)) {
        //             $this->create_invoice_request['buyer'] = $result->buyer->buyer_name;
        //             $this->create_invoice_request['buyer_id'] = $result->buyer->buyer_user_id;
        //         } elseif (isset($request->buyer_name)) {
        //             $this->create_invoice_request['buyer'] = $request->buyer_name;
        //             $this->create_invoice_request['buyer_id'] = null;
        //             $this->sendButtonDisabled = false;
        //         } else {
        //             $this->create_invoice_request['buyer'] = 'Others';
        //             $this->create_invoice_request['buyer_id'] = null;
        //         }

        //         // $this->create_invoice_request['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
        //         // $this->create_invoice_request['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
        //         $this->create_invoice_request['feature_id'] = 13;

        // }

        if ($this->updateForm == false) {
            $request->merge($this->addBuyerData);
            $result = null;

            if ($this->existingUser) {
                // Use the existing user's data
                $this->create_invoice_request['buyer_id'] = $this->existingUser->id;
                $this->create_invoice_request['buyer'] = $request->buyer_name ?? $this->existingUser->name;
            } else {
                // Check if email or phone is provided
                if ($request->email || $request->phone) {
                    // Check if a user with the provided email or phone exists
                    $user = User::where(function ($query) use ($request) {
                        if ($request->email) {
                            $query->where('email', $request->email);
                        }
                        if ($request->phone) {
                            $query->orWhere('phone', $request->phone);
                        }
                    })->first();

                    if ($user) {
                        // Use the existing user's data
                        $this->create_invoice_request['buyer_id'] = $user->id;
                        $this->create_invoice_request['buyer'] = $request->buyer_name;
                    } else {
                        // Handle new user scenario
                        $ReceiversController = new BuyersController;
                        $response = $ReceiversController->addManualBuyer($request);
                        $result = $response->getData();
                        $this->create_invoice_request['buyer_id'] = $result->buyer->id;
                        $this->create_invoice_request['buyer'] = $result->buyer->buyer_name;
                    }
                } else {
                    // No email or phone provided, set buyer_id to null and use the provided buyer_name
                    $this->create_invoice_request['buyer_id'] = null;
                    $this->create_invoice_request['buyer'] = $request->buyer_name ?: 'Others';
                }
            }

            // Continue with the rest of the logic
            $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1")
                ->where('panel_id', '3')
                ->first();
            $invoiceSeries = $series->series_number;
            $latestSeriesNum = Invoice::where('invoice_series', $invoiceSeries)
                ->where('seller_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->get();

            if ($latestSeriesNum->isNotEmpty()) {
                // Get the maximum 'series_num' from the collection
                $maxSeriesNum = $latestSeriesNum->max('series_num');
            }

            // Increment the latestSeriesNum for the new challan
            $invoiceNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;

            $this->create_invoice_request['invoice_series'] = $invoiceSeries;

            // Set buyer details
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

            // Continue with the rest of the logic
            // $this->create_invoice_request['receiver_detail_id'] = json_decode($selectedUser Details)->details[0]->id;
            // $this->create_invoice_request['user_detail_id'] = json_decode($selectedUser Details)->details[0]->id;
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

    // Barcode
    // This method is called when the product code is entered
    public function updateBarcode()
    {
          if (!empty($this->barcode)) {
              $product = Product::where('item_code', $this->barcode)
                  ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                  ->with('details')
                  ->first();
              // dd($product);
              if ($product) {
                  $this->barcode = null;
                  // Emit the product data along with details to the Alpine.js component
                  $this->emit('productFound', [
                      'item_code' => $product->item_code,
                      'qty' => 1, // Emit quantity as 1 for incrementing
                      'rate' => $product->rate,
                      'total' => $product->qty * $product->rate,
                      'details' => $product->details->map(function ($detail) {
                          return [
                              'column_name' => $detail->column_name,
                              'column_value' => $detail->column_value
                          ];
                      })->toArray()
                  ]);
              } else {
                  // Emit an empty product data if not found
                  $this->emit('productNotFound');
              }
          }
    }

    public function loadStocks()
        {
            $this->loadStocks = true;
        }

      public $loadStocks = false;

    //   public function render()
    //   {
    //       DB::enableQueryLog();

    //       $startTime = microtime(true);

    //       Log::info('Render method started');
    //       if ($this->barcode) {
    //           $this->updateBarcode();
    //       }

    //       $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //       $request = request();
    //       $filters = [
    //           'article' => $this->Article,
    //           'item_code' => $this->item_code,
    //           'warehouse' => $this->warehouse,
    //           'location' => $this->location,
    //           'category' => $this->category,
    //           'from_date' => $this->from,
    //           'to_date' => $this->to,
    //       ];

    //       // Apply filters from the request to the query
    //       foreach ($filters as $key => $value) {
    //           if ($value !== null) {
    //               $request->merge([$key => $value]);
    //           }
    //       }

    //       $query = Product::query()->with('details');

    //       // Filter by user_id
    //       $query->where('user_id', $userId);

    //       // Add a where clause to the query to filter out products where qty is not equal to 0
    //       $query->where('qty', '!=', 0);

    //       // Apply filters dynamically
    //       if (!empty($this->Article)) {
    //           $query->whereHas('details', function ($q) {
    //               $q->where('column_value', $this->Article);
    //           });
    //       }
    //       if (!empty($this->item_code)) {
    //           $query->where('item_code', $this->item_code);
    //       }
    //       if (!empty($this->location)) {
    //           $query->where('location', $this->location);
    //       }
    //       if (!empty($this->category)) {
    //           $query->where('category', $this->category);
    //       }
    //       if (!empty($this->warehouse)) {
    //           $query->where('warehouse', $this->warehouse);
    //       }

    //       // Fetch filtered results
    //       $products = $query->get();

    //       // Fetch unique values based on the filtered results
    //       $this->articles = $products->pluck('details.0.column_value')->unique()->filter()->values()->toArray();
    //       $this->item_codes = $products->pluck('item_code')->unique()->filter()->values()->toArray();
    //       $this->locations = $products->pluck('location')->unique()->filter()->values()->toArray();
    //       $this->categories = $products->pluck('category')->unique()->filter()->values()->toArray();
    //       $this->warehouses = $products->pluck('warehouse')->unique()->filter()->values()->toArray();

    //       // Apply further filters to the paginated results
    //       if (!empty($this->item_code)) {
    //           $query->where('item_code', $this->item_code);
    //       }
    //       if (!empty($this->category)) {
    //           $query->where('category', $this->category);
    //       }
    //       if (!empty($this->warehouse)) {
    //           $query->where('warehouse', $this->warehouse);
    //       }
    //       if (!empty($this->location)) {
    //           $query->where('location', $this->location);
    //       }

    //       // Fetch paginated results
    //       $products = $query->paginate(10);

    //       $endTime = microtime(true);
    //       $executionTime = $endTime - $startTime;

    //       Log::info('Render method completed', ['execution_time' => $executionTime]);

    //       // Log the queries executed
    //       Log::info('Queries executed', DB::getQueryLog());

    //       return view('livewire.seller.screens.create-invoice', [
    //           'stocks' => $products,
    //       ]);
    //   }

        public function render()
        {
            DB::enableQueryLog();

            $startTime = microtime(true);

            Log::info('Render method started');
            if ($this->barcode) {
                $this->updateBarcode();
            }

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

            $products = collect(); // Initialize an empty collection

            if ($this->loadStocks) {
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
                $products = $query->paginate(10);
            }

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            Log::info('Render method completed', ['execution_time' => $executionTime]);

            // Log the queries executed
            Log::info('Queries executed', DB::getQueryLog());

            return view('livewire.seller.screens.create-invoice', [
                'stocks' => $products,
            ]);
        }
}
