<?php

namespace App\Http\Livewire\Buyer\Content;

use App\Models\Invoice;
use Livewire\Component;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\PurchaseOrder;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Seller\SellerController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class Content extends Component
{
    use WithPagination;

    public $persistedTemplate, $persistedActiveFeature, $features = [], $activeFeature, $message, $errors, $validationErrorsJson, $successMessage, $errorMessage, $challanFiltersData, $state, $selectedUserDetails, $totalAmount, $invoiceData, $template, $inputsDisabled = true, $rate, $data, $quantity, $index, $totalSales = 0, $discountEntered = false, $calculateTax = true;
    public $sellerDatas, $statusCode, $invoiceId,$products, $from, $to, $isMobile, $buyerName,$prevTax, $ColumnDisplayNames, $invoiceFiltersData, $panelColumnDisplayNames, $billTo, $responseData, $sellerList, $purchaseOrderId, $tax,$totalAmountWithoutTax, $isTaxIncluded = false;
    public $action = 'save';
    public  $articles = [], $locations = [], $item_codes, $warehouse, $category, $Article, $location, $item_code;
    public $sendButtonDisabled = true;
    public $inputsResponseDisabled = true;
    public $currentPage = 1;
    public $isLoading = true;
    public $manuallyAdded;
    public $success;
    public $mainUser;
    public $showInputBoxes = true;
    public $selectedUser;
    public $addSellerData = array(
        'seller_name' => '',
        'company_name' => '',
        'email' => '',
        'address' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'seller_special_id' => '',
    );
    public $selectSeller =  array(
        'id' => "",
        'added_by' => "",
        'seller_name' => "",
        'details' => [
            [
                "id" => "",
                "seller_id" => "",
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
    // public $createChallanRequest = array(
    //     'purchase_order_series' => '',
    //     'invoice_date' => '',
    //     'feature_id' => '',
    //     'buyer_id' => '',
    //     'buyer' => '',
    //     'comment' => '',
    //     'total_qty' => 0,
    //     'total' => 0,
    //     'order_details' => [
    //         [
    //             'unit' => '',
    //             'rate' => 0,
    //             'qty' => 0,
    //             'discount' => 0,
    //             'total_amount' => 0,
    //             'tax_percentage' => 0, // Tax percentage for the row
    //             'tax_amount' => 0, // Tax amount for the row
    //             'tax' => 0, // Tax input field
    //             'total_with_tax' => 0, // Total amount with tax
    //             'toalTax' => 0,
    //             'total_tax' => 0,
    //             'toalTaxRate' => 0,
    //             'totalSales' => 0,
    //             'cgst_rate' => 0, //  CGST rate
    //             'sgst_rate' => 0, //  SGST rate
    //             'cgst' => 0, // CGST amount
    //             'sgst' => 0, // SGST amount
    //             'columns' => [
    //                 [
    //                     'column_name' => '',
    //                     'column_value' => '',
    //                 ]
    //             ],
    //         ],
    //     ],
    //     'statuses' => [
    //         [
    //             'comment' => ''
    //         ]
    //     ]
    // );

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

    public $teamMembers;

    public function mount()
    {
        $sessionId = session()->getId();
        $template = request('template', 'index');
        if (view()->exists('components.panel.buyer.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template;
            $request = request();
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

            $id = '';
            switch ($this->persistedTemplate) {
                case 'add_seller':
                    break;
                case 'all_invoice':
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
                    // dd($this->invoiceData);
                    $this->allInvoiceData($this->currentPage);
                    break;
                case 'purchase_order':

                    $this->purchaseOrder($this->currentPage);
                    break;
                case 'new_purchase_order':
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
                    $this->newPurchaseOrder($request, $id);
                    break;
                case 'detailed_all_buyers';
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
                    $this->detailedAllBuyer($request);
                    break;
                case 'po_design':
                    $this->poDesign($request);
                    break;
                case 'detailed_purchase_order_buyer':
                    // dd('hallo');
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
                    $this->detailedPurchaseOrder($this->currentPage);
                    break;
                case 'modify_purchase_order':
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
                    $this->modifyPurchaseOrder($request);
                break;
                default:
                case 'others':
                    break;
            }
        } else {
            $this->persistedTemplate = 'index';
            $this->persistedActiveFeature = null;
            // dd($this->persistedActiveFeature);
        }
    }

    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }

    public function handleFeatureRoute($template, $activeFeature)
    {
        $viewPath = 'components.panel.buyer.' . $template;
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        return redirect()->route('buyer', ['template' => $this->persistedTemplate]);
    }


    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'manualReceiverAdded' => 'handleManualReceiverAdded',
        'detailedAllBuyer' => 'handledetailedAllBuyer',
        'newPurchaseOrder' => 'newPurchaseOrder',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'forceDelete' => 'deleteChallanSeries',
        'poDesign' => 'handlePoDesign',
        'detailedPurchaseOrder' => 'handleDetailedPurchaseOrder',
        'modifyPurchaseOrder' => 'handleModifyPurchaseOrder',
        'addFromStock' => 'addFromStock',
        'seriesNumberUpdated' => 'updateSeriesNumber',
    ];

    public function updateSeriesNumber($newSeriesNumber)
    {
        // dd($newSeriesNumber);
        $this->selectedUser['seriesNumber'] = $newSeriesNumber;
        // \Log::info('Updated invoiceNumber:', ['invoiceNumber' => $this->selectedUser['invoiceNumber']]);
        $this->disabledButtons = true;
    }

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

                $productExists = array_filter($this->createChallanRequest['order_details'], function ($product) use ($dataToMerge) {
                    return isset($product['p_id']) && $product['p_id'] == $dataToMerge['p_id'];
                });

                if (empty($productExists)) {
                    $replaced = false;
                    foreach ($this->createChallanRequest['order_details'] as $key => $value) {
                        if ($value['rate'] == null && $value['qty'] == null) {
                            $this->createChallanRequest['order_details'][$key] = $dataToMerge;
                            $replaced = true;
                            break;
                        }
                    }
                    if (!$replaced) {
                        $this->createChallanRequest['order_details'][] = $dataToMerge;
                    }
                    // Update total quantity and total amount
                    $totalQty += $dataToMerge['qty'];
                    $totalAmount += $dataToMerge['total_amount'];
                }
            }
        }
        // Assign updated totals to the createChallanRequest array
        $this->createChallanRequest['total_qty'] = $totalQty;
        $this->createChallanRequest['total'] = $totalAmount;
        // Assign updated totals to the createChallanRequest array
        // $this->createChallanRequest['total_qty'] = $totalQty;
        // $this->createChallanRequest['total'] = $totalAmount;
        // $this->selectedProductIds = [];
    }

    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 4;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems);
            $this->panel = $item->panel;
            Session::put('panel', $this->panel);

        }

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }

    public function hideDropdown()
    {
        $this->dispatchBrowserEvent('hide-dropdown');
    }
    public function loadData()
    {
        $this->isLoading = false;
    }
    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
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
        case 'all_invoice':
            $challanController = new InvoiceController();
            $request->merge([
                'buyer_id' => Auth::getDefaultDriver() == 'team-user'
                    ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
                    : Auth::guard(Auth::getDefaultDriver())->user()->id
            ]);
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        break;
        case 'purchase_order':

                // Filter by buyer_id
            if ($this->purchase_order_series != null) {
                // dump($this->purchase_order_series);
                $request->merge(['purchase_order_series' => $this->purchase_order_series]);
            }

            // Filter by Seller id
            if ($this->seller_id != null) {
                $request->merge(['seller_id' => $this->seller_id]);
            }
                // Filter by date range
            if ($this->from != null && $this->to != null) {
                $request->merge([
                    'from' => $this->from,
                    'to' => $this->to,
                ]);
                $this->hideDropdown();
            }

            $challanController = new PurchaseOrderController();
            $tableTdData = $challanController->index($request);
            $this->tableTdData =  $tableTdData->getData()->data->data;
            $this->currentPage = $tableTdData->getData()->data->current_page;
            $this->paginateLinks = $tableTdData->getData()->data->links;
            $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
            break;
            case 'detailed_purchase_order_buyer':

                // Filter by buyer_id
            if ($this->purchase_order_series != null) {
                // dump($this->purchase_order_series);
                $request->merge(['purchase_order_series' => $this->purchase_order_series]);
            }

            // Filter by Seller id
            if ($this->seller_id != null) {
                $request->merge(['seller_id' => $this->seller_id]);
            }
                // Filter by date range
            if ($this->from != null && $this->to != null) {
                $request->merge([
                    'from' => $this->from,
                    'to' => $this->to,
                ]);
                $this->hideDropdown();
            }

            $challanController = new PurchaseOrderController();
            $tableTdData = $challanController->index($request);
            $this->tableTdData =  $tableTdData->getData()->data->data;
            $this->currentPage = $tableTdData->getData()->data->current_page;
            $this->paginateLinks = $tableTdData->getData()->data->links;
            $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
            break;

    }

    }

    public $activeTab = 'receiver-manually';

    public function setActiveTab($tab)
    {
        // dd($tab);
        $this->activeTab = $tab;
        // dd($this->activeTab);
    }


    public function addRow()
    {
        $this->createChallanRequest['order_details'][] = [
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
    public function removeRow($index)
    {
        unset($this->createChallanRequest['order_details'][$index]);
        // $this->rows = array_values($this->rows);
    }

    public function validateAndAddBuyer()
    {
        $this->validate([
            'addSellerData.seller_name' => 'required',
            'addSellerData.address' => 'required',
            'addSellerData.pincode' => 'required|numeric|digits:6',
            'addSellerData.city' => 'required',
            'addSellerData.state' => 'required',
            'addSellerData.email' => 'nullable|email|unique:users,email',
            'addSellerData.phone' => 'nullable|string|size:10|unique:users,phone',

        ], [
            'addSellerData.seller_name.required' => 'The buyer name is required.',
            'addSellerData.address.required' => 'The address is required.',
            'addSellerData.pincode.required' => 'The pincode is required.',
            'addSellerData.pincode.numeric' => 'The pincode must be a number.',
            'addSellerData.pincode.digits' => 'The pincode must be 6 digits.',
            'addSellerData.city.required' => 'The city is required.',
            'addSellerData.state.required' => 'The state is required.',
            'addSellerData.email.email' => 'The email must be a valid email address.',
            'addSellerData.email.unique' => 'The email has already been taken.',
            'addSellerData.phone.string' => 'The phone number must be a string.',
            'addSellerData.phone.size' => 'The phone number must be 10 digits.',
            'addSellerData.phone.unique' => 'The phone number has already been taken.',
        ]);

        if (!$this->getErrorBag()->isEmpty()) {
            return;
        }

        $this->addSellerManually();
    }

    // Add Buyer Manually
    public function addSellerManually(Request $request)
    {
            $request->merge($this->addSellerData);
            $SellerController = new SellerController;
            $response = $SellerController->addManualSeller($request);
            $result = $response->getData();
            // dd($result);
            $this->reset(['statusCode', 'message', 'errors', 'validationErrorsJson']);

            $this->statusCode = $result->status;
            // $this->message = $result->message;
            // $this->validationErrorsJson = json_encode($result->errors);
            if ($this->statusCode === 200) {
                $this->success = $result->message;
                $this->reset(['addSellerData', 'statusCode', 'message', 'errors', 'validationErrorsJson']);
                // $this->reset(['createChallanRequest', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            } else {
                $this->errorMessage = json_encode($result->errors);
                $this->reset(['statusCode', 'message', 'validationErrorsJson']);

        }
    }

    public $messages = [
        'addSellerData.email.unique' => 'The email address is already registered.',
        'addSellerData.phone.unique' => 'The phone number is already registered.',
    ];

    // public $rules = [
    //     'addSellerData.email' => 'nullable|email|unique:users,email',
    //     'addSellerData.phone' => 'nullable|numeric|unique:users,phone',
    // ];

    // public function updated($property)
    // {
    //     if (
    //         ($property === 'addSellerData.email' && !empty($this->addSellerData['email'])) ||
    //         ($property === 'addSellerData.phone' && !empty($this->addSellerData['phone']))
    //     ) {
    //         $this->validateOnly($property, $this->rules);
    //     }
    // }


    public function callAddSeller(Request $request)
    {

        // $request->replace([]);
        $request->merge($this->addSellerData);
        // dd($request);
        $newSellerController = new SellerController;
        $response = $newSellerController->addSeller($request);
        $result = $response->getData();
        // dd($result);
        if ($result->status === 200) {
            $this->success = $result->message;
            $this->reset(['addSellerData']);
            return view('components.panel.seller.all_buyer');
        } else {
            $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
        }

        $this->reset(['addSellerData']);
    }

    public function addNewSellerDetail()
    {
        // Make sure $this->selectSeller['details'] is an array
        if (!is_array($this->selectSeller['details'])) {
            $this->selectSeller['details'] = [];
        }
        // Then you can use array_unshift to prepend an element
        array_unshift($this->selectSeller['details'], [
            "id" => "",
            "seller_id" => $this->selectSeller['id'],
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
        ]);
    }
    public function checkPincodeLength($pincode)
    {
        if (strlen($pincode) === 6) {
            $this->cityAndStateByPincode($pincode);
        }
    }

    public function cityAndStateByPincode()
    {
        $pincode = $this->addSellerData['pincode'];
        // dd($pincode);

        $receiverController = new ReceiversController();
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        // dd($result);
        if (isset($result->city) && isset($result->state)) {
            // Update the city and state fields
            $this->addSellerData['city'] = $result->city;
            $this->addSellerData['state'] = $result->state;
        }
    }
    public function selectSeller($seller)
    {
        $this->selectSeller = [];
        $details = [];
        // dd($seller);
        foreach ($seller['details'] as $key => $detail) {
            // dd($detail);
            array_push($details, [
                "id" => $detail['id'],
                "seller_id" => $detail['seller_id'],
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
        $this->selectSeller = array(
            'id' => $seller['id'],
            'added_by' => $seller['user']['added_by'],
            'seller_name' => $seller['seller_name'],
            'details' => $details
        );

        // dd($selectSeller);
    }
    public function removeSellerDetail($key)
    {
        unset($this->selectSeller['details'][$key]);
    }

    public function updateSellerDetail(Request $request)
    {

        $request->replace([]);
        $request->merge($this->selectSeller);
        // dd($request);
        $newSellerController = new SellerController;
        $response = $newSellerController->updateSeller($request, $request->id);
        foreach ($request->details as $key => $detail) {
            $request->replace([]);
            $request->merge($detail);
            if ($request['id'] !== "") {
                $detailResponse = $newSellerController->updateSellerDetail($request, $request['id']);
                $result = $detailResponse->getData();
                if ($result->status === 200) {
                    // $this->successMessage = $result->message;
                    // return view('components.panel.sender.view_buyer');
                } else {
                    $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
                }
            } else {
                $detailResponse = $newSellerController->storeSellerDetail($request);
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

        $this->reset(['selectSeller']);
    }
    public $purchase_order_series;
    public $buyer_id;
    public $status;
    public $fromDate;
    public $toDate;
    public $perPage = 10;
    public $seller_id;
    public $purchase_order_id;
    public $comment;
    public $invoice_series;

    // ALL SELLER
    // All Invoice Data

    public function allInvoiceData($page)
    {
        $request = request();
        $columnFilterDataset = [
            'feature_id' => '18',
            'panel_id' => '4',
        ];
        $request->merge($columnFilterDataset);
        $this->ColumnDisplayNames = ['Invoice No', 'Time', 'Date', 'Creator', 'Buyer',  'Amount', 'State', 'Status', 'SFP', 'Comment'];



        $request = request()->replace([]);
        // $request->replace([]);
         // dd($request);


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

        // // Filter by Seller id
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
        $request = new Request(['page' => $page, 'perPage' => $this->perPage, ]);
        $request->merge([
            'buyer_id' => Auth::getDefaultDriver() == 'team-user'
                ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
                : Auth::guard(Auth::getDefaultDriver())->user()->id
        ]);
        $challanController = new InvoiceController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

    }


    public function sfpInvoice()
    {

        $request = request();
        $request->merge([
            'id' => $this->team_user_id,
            'purchase_order_id' => $this->purchase_order_id,
            'comment' => $this->comment,
        ]);
        $ChallanController = new InvoiceController;

        $response = $ChallanController->challanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('all_invoice', '18');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function sfpPurchaseOrder()
    {
        $request = request();
        $request->merge([
            'id' => $this->team_user_id,
            'purchase_order_id' => $this->purchase_order_id,
            'comment' => $this->comment,
        ]);
        $ChallanController = new PurchaseOrderController;

        $response = $ChallanController->returnChallanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('purchase_order', '19');
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function SfpAccept(Request $request, $sfpId)
    {
        $receiverScreen = new PurchaseOrderController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReject(Request $request, $sfpId)
    {
        $receiverScreen = new PurchaseOrderController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    public function SfpReAccept(Request $request, $sfpId)
    {
        $receiverScreen = new InvoiceController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReReject(Request $request, $sfpId)
    {
        $receiverScreen = new InvoiceController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    public function detailedAllBuyer(Request $request)
    {
        $this->ColumnDisplayNames = ['invoice No', 'PO No', 'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn', 'Unit', 'Quantity', 'Unit Price', 'Tax', 'Total Amount', 'Details'];

        request()->replace([]);

        // if ($this->purchase_order_series != null) {
        //     $request->merge(['purchase_order_series' => $this->purchase_order_series]);
        // }
        // if ($this->buyer_id != null) {
        //     $request->merge(['buyer_id' => $this->buyer_id]);
        // }
        // if ($this->status != null) {
        //     $request->merge(['status' => $this->status]);
        // }

        // if ($this->state != null) {
        //     $request->merge(['state' => $this->state]);
        // }
        // dd($request);
        // $challanController = new PurchaseOrderController();
        // $invoiceData = $challanController->getSellerData($request);
        $receiverController = new PurchaseOrderController;
        $response = $receiverController->getSellerData($request);

        $responseData = json_decode($response->getContent(), true);
        $this->invoiceData = $responseData['seller_list'];
        // $this->invoiceData = $invoiceData;
        // // $this->invoiceFiltersData = json_encode($invoiceData->getData());
        // dd($this->invoiceData);
        // $this->emit('invoiceDataReceived', $invoiceData);
    }

    public function acceptPurchaseOrder($id)
    {

        $request = request();
        $purchaInvoiceController = new InvoiceController;

        $response = $purchaInvoiceController->accept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        // dd($this->statusCode);

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'all_invoice']);

        // $this->mount();
    }
    public function rejectPurchaseOrder($id)
    {

        $request = request();
        $purchaInvoiceController = new InvoiceController;

        $response = $purchaInvoiceController->reject($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'all_invoice']);

        // $this->mount();
    }

    public function selfAcceptInvoice($id)
    {
        $request = request();
        $purchaPurchaseOrderController = new PurchaseOrderController;

        $response = $purchaPurchaseOrderController->selfAccept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'all_invoice']);

    }

    public function deleteInvoice($id)
    {

        $sellerDelete = new SellerController;
        $response = $sellerDelete->delete($id);
        $result = $response->getData();
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'all_seller']);

        // $this->mount();
    }


    public function purchaseOrder($page)
    {
        $request = new Request;
        $this->ColumnDisplayNames = ['PO No', 'Seller', 'Time', 'Date', 'Creator', 'Amount', 'State','Status', 'Comment'];
        $columnFilterDataset = [
            'feature_id' => 19
        ];
        $request->merge($columnFilterDataset);
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
        $tableTdData = $challanController->index($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        // dd($this->tableTdData);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        $this->emit('PODataReceived', $tableTdData);
    }

    public $save;
    public $rows = [];
    public $context;
    public $authUserState;

    public function newPurchaseOrder(Request $request, $id)
    {
        // dd($id);
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

       $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

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

        $this->panelColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        // dd($this->panelColumnDisplayNames);
        $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        $this->ColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        array_push($this->ColumnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');

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
        $this->initializeRows();
        $this->context = 'purchase_order';
        // dd('dsf');
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute

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
            'feature_id' => 1,
            'user_id' => $userId,
        ]);

        $response = $controller->index($request);
        $data = json_decode($response->content(), true);

        return collect($data['data'])->where('feature_id', 1)->pluck('panel_column_display_name')->all();
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

        if (is_array($this->selectedUser ) && isset($this->selectedUser ['seriesNumber'])) {
            $this->createChallanRequest['series_num'] = $this->selectedUser['seriesNumber'] ?? null;
        }

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
        // dd($request);
        $response = $PurchaseOrderController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->successMessage = $result->message;
            $this->inputsDisabled = true;
            $this->inputsResponseDisabled = false;// Adjust the condition as needed
            // dd($result);
            $this->purchaseOrderId = $result->purchase_order_id;

            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
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
            $this->inputsDisabled = true;

            $this->reset([ 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }

    public function getSelectedUserDetails()
    {
        return $this->selectedUserDetailsData;
    }

    public function modifyPurchaseOrder(Request $request)
    {
        $id = session('persistedActiveFeature');
        $this->reset(['errorMessage', 'successMessage','statusCode', 'message' ]);
        // dd($id);
        $SellerController = new PurchaseOrderController();
        $challanModifyData = $SellerController->show($request, $id);
        // dd($challanModifyData);
        // Convert the stdClass to an array
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);

        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);

        $this->challanModifyData = json_encode($modifiedDataArray);
        $this->inputsDisabled = false;

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
    }

    public function savePurchaseOrder(Request $request)
    {
        $request->merge($this->createChallanRequest);
        // dd($request);

        // Create instances of necessary classes
        $ChallanController = new PurchaseOrderController;

        $response = $ChallanController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->purchaseOrderId = $result->purchase_order_id;

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }


    public function selectUser($purchase_order_series, $address, $city, $state, $email, $phone, $gst, $buyer, $selectedUserDetails)
    {
        // dd($purchase_order_series, $address, $email, $phone, $gst, $buyer, $selectedUserDetails);
        if ($purchase_order_series == 'Not Assigned') {
            $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1")
                ->where('panel_id', "4")
                ->first();
                // dd($series);
            if ($series == null) {
                $this->errorMessage = json_encode([['Please add one default Series number']]);
            } else {
                $purchaseOrderSeries = $series->series_number;
                // dd($purchaseOrderSeries);
                $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchaseOrderSeries)
                    ->where('buyer_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                    ->max('series_num');
                // Increment the latestSeriesNum for the new challan
                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

                $this->selectedUser = [
                    "purchase_order_series" => $purchaseOrderSeries,
                    "seriesNumber" => $seriesNum,
                    "address" => $address,
                    "email" => $email,
                    "phone" => $phone,
                    'buyer_name' => $buyer,
                    "gst" => $gst
                ];
                // dd(json_decode($selectedUserDetails));
                $this->buyerName = $this->selectedUser['buyer_name'];
                $this->createChallanRequest['purchase_order_series'] = $purchaseOrderSeries;
                $this->createChallanRequest['buyer_name'] = $buyer;
                $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->seller_user_id;
                $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
                $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
                $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
                $this->createChallanRequest['order_date'] = now()->format('Y-m-d');
                $this->inputsDisabled = false; // Adjust the condition as needed
                // dd($this->selectedUserDetails);

            }
        } else {

            // Get the latest series_num for the given challan_series and user_id
            $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $purchaseOrderSeries)
                ->where('buyer_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->max('series_num');
            // Increment the latestSeriesNum for the new challan
            $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;




            $this->selectedUser = [
                "purchase_order_series" => $purchaseOrderSeries,
                "seriesNumber" => $seriesNum,
                "address" => $address,
                "email" => $email,
                'buyer_name' => $buyer,
                "phone" => $phone,
                "gst" => $gst
            ];
            // dd($this->selectedUser);
            $this->buyerName = $this->selectedUser['buyer_name'];
            $this->createChallanRequest['purchase_order_series'] = $purchaseOrderSeries;
            $this->createChallanRequest['buyer_name'] = $buyer;
            $this->createChallanRequest['buyer_id'] = json_decode($selectedUserDetails)->seller_user_id;
            $this->createChallanRequest['buyer_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
            $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
            $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
            $this->inputsDisabled = false; // Adjust the condition as needed
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
    public function clearArticleError($index)
{
    $this->resetErrorBag('article.' . $index);
}
    public function purchaseOrderCreate(Request $request)
    {

        $request = request();
        $sellerName = Auth::guard(Auth::getDefaultDriver())->user()->name;
        $sellerId = Auth::guard(Auth::getDefaultDriver())->user()->id;
        $this->createChallanRequest['order_details'] = $requestData['order_details'];
        $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
        $this->createChallanRequest['total'] = $requestData['total'];
        $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];





        // dd($this->createChallanRequest);
        $request->merge($this->createChallanRequest);

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
        dd($request);
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

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }
    public $challanSave;
    public function purchaseOrdermodify(Request $request)
    {
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

            $this->reset([ 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }
    // public function calculateTotalQuantity()
    // {
    //     $totalQuantity = 0;

    //     foreach ($this->createChallanRequest['order_details'] as $row) {
    //         if (isset($row['qty'])) {
    //             $totalQuantity += (int) $row['qty'];
    //         }
    //     }

    //     $this->createChallanRequest['total_qty'] = $totalQuantity;
    // }
    public function calculateTotalQuantity()
    {
        $totalQuantity = 0;

        foreach ($this->createChallanRequest['order_details'] as $row) {
            if (isset($row['qty'])) {
                $totalQuantity += (int) $row['qty'];
            }
        }

        $this->createChallanRequest['total_qty'] = $totalQuantity;
    }


    public function updateTotalAmount($index)
    {
        if (isset($this->createChallanRequest['order_details'][$index]['rate']) && isset($this->createChallanRequest['order_details'][$index]['qty'])) {
            $rate = $this->createChallanRequest['order_details'][$index]['rate'];
            $qty = $this->createChallanRequest['order_details'][$index]['qty'];
            $taxRate = $this->createChallanRequest['order_details'][$index]['tax'];

            $totalAmountWithoutTax = $rate * $qty;

            // Calculate tax based on the $calculateTax property
            $taxAmount = ($totalAmountWithoutTax * $taxRate) / 100;

            // Calculate GST Amount
            $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax / (1 + ($taxRate / 100)));

            // Calculate Net Price
            $netPrice = $totalAmountWithoutTax ;

            // Calculate CGST and SGST based on the tax rate
            $cgstRate = $taxRate / 2;
            $sgstRate = $taxRate / 2;

            // Calculate CGST and SGST amounts as half of the total tax amount
            $cgstWithRate = $this->calculateTax ? ($taxAmount / 2) : 0;
            $sgstWithRate = $this->calculateTax ? ($taxAmount / 2) : 0;
            // dd($sgstWithRate);
            $totalTaxRate = $taxRate;
            $existingTaxAmount = 0;

            foreach ($this->createChallanRequest['order_details'] as $key => $row) {
                if ($key != $index && $row['tax'] == $taxRate) {
                    $existingTaxAmount += $row['total_tax'];
                }
            }

            $taxAmount += $existingTaxAmount;

            foreach ($this->createChallanRequest['order_details'] as $key => $row) {
                if ($key != $index && $row['tax'] == $taxRate) {
                    $this->createChallanRequest['order_details'][$key]['total_tax'] = $taxAmount;
                    $this->createChallanRequest['order_details'][$key]['cgst_rate'] = $taxRate / 2;
                    $this->createChallanRequest['order_details'][$key]['sgst_rate'] = $taxRate / 2;
                    $this->createChallanRequest['order_details'][$key]['cgst'] = $this->calculateTax ? : 0;
                    $this->createChallanRequest['order_details'][$key]['sgst'] = $this->calculateTax ? : 0;
                    $this->createChallanRequest['order_details'][$key]['net_price'] = $netPrice;
                }
            }

            // Update the total amount based on $calculateTax
            $this->calculateTotalAmount();
            $this->calculateTotalQuantity();
            $this->calculateTotalSales();
            $this->recalculateTotal();
            // $this->recalculateTotalAmounts();
        }
    }



public function recalculateTotalAmounts()
{
    foreach ($this->createChallanRequest['order_details'] as $key => &$row) {
        $totalAmountWithoutTax = $row['rate'] * $row['qty'];
        $taxRate = $row['tax'];
        // dd($taxRate);
        // Calculate tax based on the $calculateTax property
        $taxAmount = ($totalAmountWithoutTax * ($taxRate / 100)) ;
        // dd($taxAmount);
        // Calculate GST Amount
        $gstAmount = $totalAmountWithoutTax - ($totalAmountWithoutTax / (1 + ($taxRate / 100)));

        // Calculate Net Price
        $netPrice = $totalAmountWithoutTax;
        // dd($netPrice);
        // Calculate CGST and SGST based on the tax rate
        $cgstRate = $taxRate / 2;
        $sgstRate = $taxRate / 2;

        // Calculate CGST and SGST amounts as half of the total tax amount
        $cgstWithRate = ($taxAmount / 2) ;
        $sgstWithRate = ($taxAmount / 2) ;

        // Update the item's fields
        $row['total_without_tax'] = $totalAmountWithoutTax;
        $row['total_amount'] = $totalAmountWithoutTax + $taxAmount;
        $row['total_tax'] = $taxAmount;
        $row['cgst_rate'] = $cgstRate;
        $row['sgst_rate'] = $sgstRate;
        $row['cgst'] = $cgstWithRate;
        $row['sgst'] = $sgstWithRate;
        $row['net_price'] = $netPrice;

        // Update the item in the order_details array
        $this->createChallanRequest['order_details'][$key] = $row;
    }
}


    public function recalculateTotal()
    {
        $index = '';
        $this->updateTotalAmount($index);
        $this->recalculateTotalAmounts();
        $this->calculateTotalAmount();
    }

    public function calculateTotalDiscount()
    {
        $totalDiscount = 0;

        foreach ($this->createChallanRequest['order_details'] as $row) {
            if (isset($row['discount'])) {
                $totalDiscount += (float) $row['discount'];
            }
        }

        $this->createChallanRequest['total_discount'] = $totalDiscount;
    }

    public function discountEntered($index)
    {
        $this->discountEntered = true;
    }
    // public function calculateTotalSales()
    // {
    //     $totalSales = 0;


    // foreach ($this->createChallanRequest['order_details'] as $row) {
    //     if (isset($row['total_amount']) && is_numeric($row['total_amount'])) {
    //         $totalSales += $row['total_amount'];
    //     }
    // }


    //     $this->totalSales = $totalSales;
    // }


    // public function calculateTotalTax()
    // {
    //     $totalTax = 0;

    //     foreach ($this->createChallanRequest['order_details'] as $row) {
    //         if (isset($row['tax_amount'])) {
    //             $totalTax += (float) $row['tax_amount'];
    //         }
    //     }

    //     $this->createChallanRequest['total_tax'] = $totalTax;
    // }


    // public function calculateTotalAmount()
    // {
    //     $total = 0;

    //     foreach ($this->createChallanRequest['order_details'] as $row) {
    //         if (isset($row['rate']) && isset($row['qty'])) {
    //             $total += (float) $row['total_amount'];
    //         }
    //     }

    //     $this->createChallanRequest['total'] = $total;
    //     $this->createChallanRequest['total_words'] = $this->numberToIndianRupees((float) $total);
    // }


    public function challanUpdate($id)
    {
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed

        $request = request();
        $PurchaseOrderController = new PurchaseOrderController;
        // dd($id);
        $response = $PurchaseOrderController->show($request, $id);
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
                return $column['feature_id'] == 1;
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
                'feature_id' => 1
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

            // dd($this->createChallanRequest);
            $this->createChallanRequest = json_encode($result->data);
            // dd($result->data);
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // $this->innerFeatureRedirect('update_challan', '1');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // redirect()->route('sender');


    }

    public function purchaseOrderEdit()
    {
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;// Adjust the condition as needed
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
    public function updateTimelineModal($returnChallanId)
    {
        $this->selectedReturnChallanId = $returnChallanId;
        $this->emit('openTimelineModal');
    }
    public $status_comment = '';
    public function addCommentReceivedInvoice($id)
    {
        $request = request();
        $request->merge([
            'status_comment' => $this->status_comment,
            'buyer' => 'buyer',
        ]);
        // dd($request);
        $InvoiceController = new InvoiceController;
        $response = $InvoiceController->addComment($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('all_invoice', '18');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // redirect()->route('buyer')->with('message', $this->successMessage ?? $this->errorMessage);
    }
    // public function addCommentReceivedReturnChallan($id)
    // {
    //     $request = request();
    //     $request->merge([
    //         'status_comment' => $this->status_comment,

    //     ]);
    //     $ReturnChallanController = new ChallanController;
    //     $response = $ReturnChallanController->addComment($request, $id);
    //     $result = $response->getData();
    //     // dd($result);
    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //         $this->innerFeatureRedirect('all_invoice', '9');
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //     redirect()->route('buyer')->with('message', $this->successMessage ?? $this->errorMessage);
    // }
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

    public function reSendPurchaseOrders($id)
    {

        $request = request();
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->resend($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer');
    }

    public function selfAcceptPurchaseOrders($id)
    {

        $request = request();
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->selfAccept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'purchase_order']);
    }

    public function deletePurchaseOrders($id)
    {

        $request = request();
        $PurchaseOrderController = new PurchaseOrderController;

        $response = $PurchaseOrderController->delete($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('buyer', ['template' => 'purchase_order']);
    }
    // DETAILED VIEW OF PURCHASE ORDER
    public function detailedPurchaseOrder($page)
    {
    // dd('hello');
        $this->ColumnDisplayNames = ['PO No', 'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn', 'Details', 'Unit', 'Quantity', 'Unit Price', 'Tax', 'Total Amount'];

        request()->replace([]);
        // if ($request->has('purchase_order_series')) {
        //     // Split the search term into series and number
        //     $searchTerm = $request->purchase_order_series;
        //     $searchParts = explode('-', $searchTerm);

        //     if (count($searchParts) == 2) {
        //         $series = $searchParts[0];
        //         $num = $searchParts[1];

        //         // Perform the search
        //         $query->where('purchase_order_series', $series)
        //             ->where('series_num', $num);
        //     } else {
        //         // Invalid search term format, handle accordingly
        //         // For example, you could return an error message or ignore the filter
        //     }
        // }
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
    $tableTdData = $challanController->index($request);
    $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
    $this->currentPage = $tableTdData->getData()->data->current_page;
    $this->paginateLinks = $tableTdData->getData()->data->links;
    $this->challanFiltersData = json_encode($tableTdData->getData()->filters);



        // dd($this->invoiceFiltersData);
        // $this->emit('invoiceDataReceived', $purchaseOrderBuyerData);
    }

    public $additionalInputs = 3;
    public $poDesignData = array(
        [
            'panel_id' => '4',
            'section_id' => '2',
            'feature_id' =>  '22',
            'default' => '0',
            'status' => '',
            'panel_column_default_name' => '',
            'panel_column_display_name' => '',
            'user_id' => '',
        ]

    );
    public function poDesign()
    {
        $request = new Request;

        $request->merge([
            'default' => '0',
            'panel_id' => '4',
            'section_id' => '2',
            'feature_id' =>  '22',
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $newChallanDesign = new PanelColumnsController;
        $response = $newChallanDesign->index($request);
        $this->poDesignData = $response->getData()->data;
        $this->additionalInputs = 3;
        // $this->additionalInputs = count($this->poDesignData);
        // }
        // dd($this->poDesignData);
        // You can add any additional processing or redirection logic here

        // After processing, you might want to reset the input fields
        // $this->reset('poDesignData');
    }

    public function createPurchaseOrderDesign(){
        $request = new Request;
        for ($i = 0; $i <= $this->additionalInputs; $i++) {
            $inputKey = "$i"; // Assuming the input names are column3, column4, etc.
            // dd($this->poDesignData[$i]['panel_column_default_name']);
            if (isset($this->poDesignData[$i]['panel_column_display_name'])) {
                $panelColumnDisplay = $this->poDesignData[$i]['panel_column_display_name'];
                $panelColumnDefault = "column_$i";

                // Define the data array for the new record

                if (isset($this->poDesignData[$i]['id'])) {
                    $data = [
                        'id' => $this->poDesignData[$i]['id'],
                        'panel_id' => '4',
                        'section_id' => '2',
                        'feature_id' =>  '22',
                        'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                        'panel_column_display_name' => $panelColumnDisplay,
                        'panel_column_default_name' => $panelColumnDefault,
                        'status' => 'active',
                    ];

                    $request->merge($data);
                    // dd($request);
                    $newChallanDesign = new PanelColumnsController;
                    $response = $newChallanDesign->update($request, $this->poDesignData[$i]['id']);
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
    }

    public function render()
    {
        $request = request();
        $UserResource = new UserAuthController;
        $userId = $request->user()->id; // assuming the user is authenticated and has an id

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

        // dd($this->showTemplate);
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
        $products = $query->paginate(15);

        switch ($this->persistedTemplate) {
            case 'all_seller':
                $allSeller = new SellerController;
                $response = $allSeller->index(request());
                $sellerData = $response->getData();
                $this->sellerDatas = $sellerData->data;
                break;
        }
        return view('livewire.buyer.content.content', [
            'stocks' => $products,
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
