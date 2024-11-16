<?php

namespace App\Http\Livewire\Seller\Screen;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\Estimates;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ChallanSfp;
use App\Models\CompanyLogo;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanOrderDetail;
use Livewire\WithFileUploads;
use App\Models\UserDetails;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Http\Livewire\Sender\Screens\createChallan;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Estimate\EstimateController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;
use Livewire\Component;

class CreateQuotation extends Component
{

    public $mainUser;
    public $errorMessage;
    public $pdfData;
    public $save;
    public $panelColumnDisplayNames;
    public $panelUserColumnDisplayNames;
    public $columnDisplayNames;
    public $billTo;
    public $billToData = [];
    public $showRate;
    public $status_comment = '';
    public $barcode;
    // public $articles = [], $locations = [], $item_codes, $Article, $location, $item_code;
    public $isOpen = false;
    public $open = false;
    public $selectedProducts = [];
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $sendButtonDisabled = true;
    public $updateForm = true;
    public $selectedUser;
    public $selectedUserDetails = [];
    public $userSelected = false;
    public $admin_ids = [];
    public $team_user_ids = [];
    public $calculateTax = true;
    public $hideWithoutTax = true;
    public $productId;
    public $data;
    public $quantity;
    public $totalAmount;
    public $challanSave;
    public $disabledButtons = true;
    public $invoiceId = null;
    public $showInputBoxes = true;
    public $isLoading = true;
    // team
    public $team_user_id, $challan_id, $challan_sfp;
    public $teamMembers;
    public $action = 'save';
    public $errorQty =[];
    public $barcodeError;
    public $authUserState;
    public $selectedProductIds = [];
    public $products, $articles = [], $locations = [], $item_codes, $Article, $location, $item_code, $warehouse, $category, $from, $to;
    public $company_name, $receiverName,$additionalNumberPermission, $receiverAddress, $receiverPhone, $challanModifyData, $email, $address, $pincode, $phone, $state, $sfp, $city, $tan, $successMessage,  $receiver_special_id, $errors, $statusCode, $message, $fromDate, $toDate, $termsIndexData;
    use WithPagination;
    public $buyerName, $buyerAddress, $showBarcode;

    protected $listeners = [
        'seriesNumberUpdated' => 'updateSeriesNumber',
    ];

    public function updateSeriesNumber($newSeriesNumber)
    {
        // dd($newSeriesNumber);
        $this->selectedUser['seriesNumber'] = $newSeriesNumber;
        // \Log::info('Updated invoiceNumber:', ['invoiceNumber' => $this->selectedUser['invoiceNumber']]);
        $this->disabledButtons = true;
    }

    public $createChallanRequest = [
        'estimate_series' => '',
        'series_num' => '',
        'estimate_date' => '',
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
                'comment' => '',
            ]
        ]
    ];


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

    public $rows = [];
    public $context;


    public function mount()
    {
        // dd('sdf');
        $request = request();
        $PanelColumnsController = new PanelColumnsController;
        $billTo = new BuyersController;
       // Fetch all products once and store them in a public property
       $this->context = 'estimate';
       $this->fetchTeamMembers();
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute
        $units = new UnitsController;
        $unitsCollection = $units->index('sender')->original;
        $this->units = $unitsCollection->filter(function ($unit) {
            return $unit->is_default == 1;
        })->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unit' => $unit->unit,
                'short_name' => $unit->short_name,
                'is_default' => $unit->is_default,
            ];
        })->toArray();

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData, $userId, 'shdj');
        $this->pdfData = $pdfData;


        $this->panelColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        // dd($this->panelColumnDisplayNames);
        $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        $this->ColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        array_push($this->ColumnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');


        $this->billTo = $billTo->index($request)->getData()->data;
        $this->billTo = collect($this->billTo)->sortBy(function ($item) {
            return strtolower($item->buyer_name);
        })->values()->all();

        // $response = $products->searchStock($request);
        // $result = $response->getData();
        // $this->products = (array) $result->data;
        // $filteredProducts = array_filter($this->products, function ($product) {
        //     return ((object) $product)->qty > 0;
        // });

        $this->createChallanRequest['estimate_date'] = now()->format('Y-m-d');
        $showColumns = User::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->first()->pluck('show_rate');

        $this->showRate = $showColumns;
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
        // Fetch panel settings
        $panelSettings = \App\Models\PanelSettings::where('user_id', Auth::user()->id)->first();
        if ($panelSettings) {
            $settings = json_decode($panelSettings)->settings;
            // dd($settings);
            if (isset($settings) && isset($settings->estimate)) {
                $senderSettings = $settings->estimate;
                $this->showBarcode = $senderSettings->barcode ?? false;
            }
        }
    }

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

    public function invoiceEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;
        $this->reset([ 'message', 'save']);
    }


    public function getUnitProperty()
    {
        $unitData = new UnitsController();
        $response = $unitData->index();
        return json_decode($response->getContent(), true);
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

    private function getColumnDisplayNames($controller, $request, $userId)
    {
        $request->merge([
            'feature_id' => 12,
            'user_id' => $userId,
        ]);

        $response = $controller->index($request);
        $data = json_decode($response->content(), true);

        return collect($data['data'])->where('feature_id', 12)->pluck('panel_column_display_name')->all();
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
        $this->createChallanRequest['estimate_date'] = now()->format('Y-m-d');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();
                $invoiceSeries = $series->series_number;
                $latestSeriesNum = Estimates::where('estimate_series', $series)
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


    // public function selectUser($invoiceSeries, $address, $email, $pincode, $city, $phone, $gst, $state, $buyer, $selectedUserDetails)
    // {
    //     $this->userSelected = true;

    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //         $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '3')->select('series_number')->first();

    //         if ($invoiceSeries == 'Not Assigned') {
    //             if ($series == null) {
    //                 throw new \Exception('Please add one default Series number');
    //             }
    //             $invoiceSeries = $series->series_number;
    //             // dd($invoiceSeries);
    //             $latestSeriesNum = Estimates::where('estimate_series', $invoiceSeries)
    //                 ->where('seller_id', $userId)
    //                 ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    //         } else {
    //             $latestSeriesNum = Estimates::where('estimate_series', $invoiceSeries)
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

    //         // Decode $selectedUserDetails once
    //         $decodedUserDetails = json_decode($selectedUserDetails);
    //         $this->buyerName = $this->selectedUser['buyer_name'];
    //         $this->createChallanRequest['estimate_series'] = $invoiceSeries;
    //         $this->createChallanRequest['series_num'] = $seriesNum;
    //         $this->createChallanRequest['buyer'] = $buyer;
    //         $this->createChallanRequest['buyer_id'] = $decodedUserDetails->buyer_user_id;
    //         $this->createChallanRequest['feature_id'] = 13;
    //         $this->selectedUserDetails = $decodedUserDetails->user->details;
    //         $this->city = $decodedUserDetails->city;
    //         $this->state = $decodedUserDetails->state;
    //         $this->pincode = $decodedUserDetails->pincode;
    //         $this->inputsDisabled = false; // Adjust the condition as needed

    //         // Fetch billTo data
    //         $request = request();
    //         $billTo = new BuyersController;
    //         $this->billTo = $billTo->index($request)->getData()->data;
    //         $this->billTo = collect($this->billTo)->sortBy(function ($item) {
    //             return strtolower($item->buyer_name);
    //         })->values()->all();

    //         DB::commit();

    //         $endTime = microtime(true);
    //         $executionTime = $endTime - $startTime;

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $this->errorMessage = 'An error occurred while processing your request.';
    //         return;
    //     }
    // }
    public function selectUser($invoiceSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $buyer, $selectedUserDetails)
    {
        $this->userSelected = true;

        try {
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            // New series generation logic
            $currentFinancialYearStart = now()->startOfYear()->month(4)->day(1);
            $nextFinancialYearEnd = now()->startOfYear()->addYear(1)->month(3)->day(31);
            if (now() < $currentFinancialYearStart) {
                $currentFinancialYearStart = $currentFinancialYearStart->subYear();
                $nextFinancialYearEnd = $nextFinancialYearEnd->subYear();
            }
            $financialYear = ($currentFinancialYearStart->year % 100) . '-' . ($nextFinancialYearEnd->year % 100);
            $companyName = Auth::user()->company_name ?? Auth::user()->name;
            $name = strtoupper(str_replace(' ', '', substr($companyName, 0, 4)));
            $series = 'EST-' . $name . '-' . $financialYear;

            if ($invoiceSeries == 'Not Assigned') {
                $invoiceSeries = $series;
                $latestSeriesNum = Estimates::where('estimate_series', $series)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            } else {
                $latestSeriesNum = Estimates::where('estimate_series', $series)
                    ->where('seller_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            }

            $this->inputsDisabled = false;
            $this->selectedUser = [
                "invoiceSeries" => $series,
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
            $this->buyerName = $this->selectedUser['buyer_name'];
            $this->createChallanRequest['estimate_series'] = $series;
            $this->createChallanRequest['series_num'] = $seriesNum;
            $this->createChallanRequest['buyer'] = $buyer;
            $this->createChallanRequest['buyer_id'] = $decodedUserDetails->buyer_user_id;
            $this->createChallanRequest['feature_id'] = 13;
            $this->selectedUserDetails = $decodedUserDetails->user->details;
            $this->city = $decodedUserDetails->city;
            $this->state = $decodedUserDetails->state;
            $this->pincode = $decodedUserDetails->pincode;
            $this->inputsDisabled = false;

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
            $this->errorMessage = 'An error occurred while processing your request.';
            return;
        }
    }

    public function selectUserAddress($selectedUserDetail, $selectedUserDetails)
    {
        // $selectedUserDetails = json_decode($selectedUserDetails);
        $selectedUserDetail = json_decode($selectedUserDetail);
        $this->selectedUser['address'] = $selectedUserDetail->address;
        $this->selectedUser['phone'] = $selectedUserDetail->phone;
        $this->selectedUser['gst'] = $selectedUserDetail->gst_number;
        $this->createChallanRequest['buyer_detail_id'] = $selectedUserDetail->id;

        $this->selectedUserDetails = json_decode($selectedUserDetails);

        $this->buyerAddress = $selectedUserDetail->address;
        // dd($this->buyerAddress);
        // $this->createChallanRequest['receiver_detail_id'] = $selectedUserDetail->id;
        $this->createChallanRequest['user_detail_id'] = $selectedUserDetail->id;
        $this->selectedUserDetails = json_decode($selectedUserDetails);

        // dd($this->selectedUser);
        $request = request();

        $billTo = new BuyersController;
        $this->billTo = $billTo->index($request)->getData()->data;
        // dd($this->selectedUser);
    }

    public function sendInvoice($id)
    {
        // dd( $id);
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $EstimateController = new EstimateController;
        $response = $EstimateController->send($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage']);
            // Assuming $this->persistedTemplate holds the template name
         } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('seller', ['template' => 'sent_quotation'])->with('message', $this->successMessage ?? $this->errorMessage);
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
        // dd($request);
        // Create instances of necessary classes
        $EstimateController = new EstimateController;

        $response = $EstimateController->update($request, $this->invoiceId);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->invoiceId = $result->estimate_id;
            $this->inputsDisabled = true;
            $this->reset(['errorMessage' ]);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }

    public function saveChallanModify(Request $request)
    {
        $request->merge($this->createChallanRequest);
        // dd($request);

        // Create instances of necessary classes
        $EstimateController = new EstimateController;

        $response = $EstimateController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // $this->isSaveButtonDisabled = true;
            $this->invoiceId = $result->estimate_id;

            $this->reset(['errorMessage',   'message' ]);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }

    public $sfpModal = false;
    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;

        if($variable == 'challan_sfp'){
            $this->sfpModal = true;
            $this->challan_id = $value;
        }
    }
    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }

    public function sfpChallan()
    {
        $request = request();
        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);

        $EstimateController = new EstimateController;

        $response = $EstimateController->challanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
            $this->innerFeatureRedirect('sent_quotation', '2');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        $request = request();
        // $request = new Request(['page' => $page]);
        $challanController = new EstimateController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        return redirect()->route('seller', ['template' => 'sent_quotation'])->with('message', $this->successMessage ?? $this->errorMessage);
    }


    public $draft = true;
    public $dataChanged = false;
    public $saved = false;
    public $challanSaved = false;

    public function saveRows($requestData)
    {

        $request = request();
        // dd($requestData);
         // Access the series number from the createChallanRequest array
        // $seriesNumber = $this->createChallanRequest['series_num'];
        // dd($this->createChallanRequest['series_num']);
        // Update the createChallanRequest with the new data
        $this->createChallanRequest['order_details'] = $requestData['order_details'];
        $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
        $this->createChallanRequest['total'] = $requestData['total'];
        $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->createChallanRequest['series_num'] = $this->selectedUser['invoiceNumber'] ?? null;
        if (!isset($this->createChallanRequest['statuses'])) {
            $this->createChallanRequest['statuses'] = [
                ['comment' => $this->status_comment ?? '']
            ];
        }
        $request->merge($this->createChallanRequest);
        // dd($request);
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
                $latestSeriesNum = Invoice::where('estimate_series', $invoiceSeries)
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
                // $this->createChallanRequest['estimate_series'] = $invoiceSeries;
                // $this->createChallanRequest['buyer'] = $buyer;
                // $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->buyer_user_id;
                // $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                // $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;


                $this->createChallanRequest['estimate_series'] = $invoiceSeries;
                $this->createChallanRequest['series_num'] = $invoiceNum;
                if (isset($result->buyer->buyer_name)) {
                    $this->createChallanRequest['buyer'] = $result->buyer->buyer_name;
                    $this->createChallanRequest['buyer_id'] = $result->buyer->buyer_user_id;
                } elseif (isset($request->buyer_name)) {
                    $this->createChallanRequest['buyer'] = $request->buyer_name;
                    $this->createChallanRequest['buyer_id'] = null;
                    $this->sendButtonDisabled = false;
                } else {
                    $this->createChallanRequest['buyer'] = 'Others';
                    $this->createChallanRequest['buyer_id'] = null;
                }

                // $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                // $this->createChallanRequest['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
                $this->createChallanRequest['feature_id'] = 13;

        }
        $this->createChallanRequest['calculate_tax'] = $this->calculateTax;
        foreach ($this->createChallanRequest['order_details'] as $index => $orderDetail) {
            $this->createChallanRequest['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }
        $this->createChallanRequest['estimate_date'] = now()->format('Y-m-d');
        $request->merge($this->createChallanRequest);

        // dd($request, $this->status_comment);
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
        $invoiceController = new EstimateController;
        $response = $invoiceController->store($request);
        $result = $response->getData();
        // dd($result);
        // Check the status code from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            $this->save = $result->message;
            $this->inputsDisabled = true;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            if($this->createChallanRequest['buyer_id'] == null){
                $this->successMessage = $result->message;
                // $this->innerFeatureRedirect('sent_invoice', '13');
                return redirect()->route('seller', ['template' => 'sent_quotation'])->with('message', $this->successMessage ?? $this->errorMessage);
            }
            $this->invoiceId = $result->estimate_id;
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }


    public function draftRows($requestData)
    {
        // dd($requestData);
        $request = request();
        // Update the createChallanRequest with the new data
        $this->createChallanRequest['order_details'] = $requestData['order_details'];
        $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
        $this->createChallanRequest['total'] = $requestData['total'];
        $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];
        // $this->createChallanRequest['series_num'] =  null;


        if($this->updateForm == false)
        {
            $request->merge($this->addBuyerData);
            // dd($request);
            $receiverId = null;
            $receiverPhone = null;
            $result = null;

            if($request->phone || $request->email){
                $usersQuery = User::query();

                if (!empty($request->phone)) {
                    $usersQuery->where('phone', $request->phone);
                }

                if (!empty($request->email)) {
                    $usersQuery->orWhere('email', $request->email);
                }

                $user = $usersQuery->first();
                // dd($user);
                if($user){
                // If user exists, get the special ID and call addReceiver
                $specialId = $user->special_id; // Assuming 'special_id' is the column name
                // dd($specialId);
                $request->merge(['receiver_special_id' => $specialId]);
                //    dd($request);
                $ReceiversController = new ReceiversController;

                $response = $ReceiversController->addReceiver($request);

                $result = $response->getData();
                } else {
                    //  dd($request);

                $ReceiversController = new ReceiversController;

                $response = $ReceiversController->addManualReceiver($request);

                $result = $response->getData();
                $this->createChallanRequest['receiver_id'] = $result->receiver->id;
                }
            }


            $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1")
                ->where('panel_id', '1')
                ->first();
                $challanSeries = $series->series_number;
            $latestSeriesNum = Challan::where('challan_series', $challanSeries)
                    ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)

                    ->get();
                        // Get the maximum 'series_num' from the collection
                        $maxSeriesNum = $latestSeriesNum->max('series_num');
                    // }
                // Increment the latestSeriesNum for the new challan
                $seriesNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;

                $this->createChallanRequest['challan_series'] = $challanSeries;
                $this->createChallanRequest['series_num'] = $seriesNum;
                if (isset($result->receiver->receiver_name)) {
                    $this->createChallanRequest['receiver'] = $result->receiver->receiver_name;
                    $this->createChallanRequest['receiver_id'] = $result->receiver->receiver_user_id;
                } elseif (isset($request->receiver_name)) {
                    $this->createChallanRequest['receiver'] = $request->receiver_name;
                    $this->createChallanRequest['receiver_id'] = null;
                    $this->sendButtonDisabled = false;
                } else {
                    $this->createChallanRequest['receiver'] = 'Others';
                    $this->createChallanRequest['receiver_id'] = null;

                }

                // $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                // $this->createChallanRequest['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
                $this->createChallanRequest['feature_id'] = 1;

        }
        $this->createChallanRequest['calculate_tax'] = $this->calculateTax;
        foreach ($this->createChallanRequest['order_details'] as $index => $orderDetail) {
            $this->createChallanRequest['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }
        // dd($this->createChallanRequest);
        $request->merge($this->createChallanRequest);
        // dd($request);
        $errors = false;

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
                        $this->addError('article.' . $index, 'Required');
                        $errors = true;
                    }
                }
            }
        }
        // dd($errors);
        // If there are errors, return early
        if ($errors) {
            return;
        }

        // dd($request->all());
        // Create instances of necessary classes
        $EstimateController = new EstimateController;

        $response = $EstimateController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // dd($result);
        if ($result->status_code === 200) {
            $this->invoiceId = $result->estimate_id;
            $this->emit('itemIdUpdated', $this->invoiceId);
            // $this->challanSave = $result->message;
            $this->inputsDisabled = false;
            $this->inputsResponseDisabled = true; // Adjust the condition as needed
            $this->dispatchBrowserEvent('show-success-message', ['Challan saved as draft']);
            // if($this->createChallanRequest['receiver_id'] == null){
            //     $this->successMessage = $result->message;
            //     return redirect()->route('seller', ['template' => 'sent_quotation'])->with('message', $this->successMessage ?? $this->errorMessage);
            // }


            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }

    public $productCode;



    // This method is called when the product code is entered
    // public function updateBarcode()
    // {
    //     if (!empty($this->barcode)) {
    //         $product = Product::where('item_code', $this->barcode)
    //             ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->with('details')
    //             ->first();
    //         // dd($product);
    //         if ($product) {
    //             $this->barcode = null;
    //             // Emit the product data along with details to the Alpine.js component
    //             $this->emit('productFound', [
    //                 'item_code' => $product->item_code,
    //                 'qty' => 1, // Emit quantity as 1 for incrementing
    //                 'rate' => $product->rate,
    //                 'total' => $product->qty * $product->rate,
    //                 'details' => $product->details->map(function ($detail) {
    //                     return [
    //                         'column_name' => $detail->column_name,
    //                         'column_value' => $detail->column_value
    //                     ];
    //                 })->toArray()
    //             ]);
    //         } else {
    //             // Emit an empty product data if not found
    //             $this->emit('productNotFound');
    //         }
    //     }
    // }


    public function render()
    {
        if (!empty($this->barcode)) {
            $product = Product::where('item_code', $this->barcode)
                ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->with('details')
                ->first();

            if ($product) {
                $this->barcode = null;
                // Emit the product data along with details to the Alpine.js component
                $this->emit('productFound', [
                    'item_code' => $product->item_code,
                    'qty' => 1, // Explicitly set quantity to 1
                    'rate' => $product->rate,
                    'total' => $product->rate, // Calculate total based on qty of 1
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
            // dd($products);
        return view('livewire.seller.screen.create-quotation',   [
            'stocks' => $products,
        ]);
    }
}
