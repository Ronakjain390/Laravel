<?php

namespace App\Http\Livewire\Sender\Screens;
use App\Models\User;
use App\Models\Challan;
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
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

use Livewire\Component;





class CreateChallan extends Component
{
    public $mainUser;
    public $errorMessage;
    public $pdfData;
    public $panelColumnDisplayNames;
    public $panelUserColumnDisplayNames;
    public $columnDisplayNames;
    private $billTo = [];
    public $billToData = [];
    public $showRate;
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
    public $challanId;
    public $showInputBoxes = true;
    public $isLoading = true;
    // team
    public $team_user_id, $challan_id, $challan_sfp;
    public $teamMembers;
    public $action = 'save';
    // public $createChallanRequest = array(
    //     'challan_series' => '',
    //     'series_num' => '',
    //     'challan_date' => '',
    //     'feature_id' => '',
    //     'receiver_id' => '',
    //     'receiver' => '',
    //     'comment' => '',
    //     'total_qty' => null,
    //     'total' => '',
    //     'calculate_tax' => null,
    //     'total_words' => '',
    //     'additional_phone_number' => '',
    //     'discount_total_amount' => '',
    //     'order_details' => [
    //         [
    //             'p_id' => '',
    //             'unit' => null,
    //             'rate' => null,
    //             'qty' => null,
    //             'round_off' => null,
    //             'discount' => null,
    //             'total_amount' => null,
    //             'tax_percentage' => null,
    //             'discount_total_amount' => null,
    //             'tax_amount' => null,
    //             'tax' => null,
    //             'item_code' => null,
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
    public $createChallanRequest = [];
    public $selectedProductIds = [];

    public $company_name, $receiverName,$additionalNumberPermission, $receiverAddress, $receiverPhone, $challanModifyData, $email, $address, $pincode, $phone, $state, $sfp, $city, $tan, $successMessage,  $receiver_special_id, $errors, $statusCode, $message, $fromDate, $toDate, $termsIndexData;

    public $selectReceiver =  array(
        'id' => "",
        'added_by' => "",
        'receiver_name' => "",
        'details' => [
            [
                "id" => "",
                "receiver_id" => "",
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
                "location_name" => "",
                "organisation_type" => "",
            ]
        ]
    );

    protected $listeners = ['addFromStock'];

    public function getBillToDataProperty()
    {
        return collect($this->billTo)
            ->filter(function ($item) {
                return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
            })
            ->map(function ($item) {
                $receiverName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                return (object) array_merge((array) $item, ['receiver_name' => $receiverName]);
            })
            ->sortBy(function ($item) {
                $receiverName = strtolower($item->receiver_name);
                return is_numeric($receiverName[0]) ? 'z' . $receiverName : $receiverName;
            })
            ->values()
            ->all();
    }

    // public function getColumnDisplayNamesDataProperty()
    // {
    //     return $this->columnDisplayNames;
    // }

    public function mount()
    {
        $request = request();
        $this->fetchTeamMembers();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Fetch User Details
        $this->mainUser = $this->fetchUserDetails($request);

        // Fetch necessary data
        $this->pdfData = CompanyLogo::where('user_id', $userId)->first();
        $this->billToData = $this->fetchBillToData($request);
        $this->products = $this->fetchAvailableStock($request);
        $this->units = $this->fetchUnits();

        $PanelColumnsController = new PanelColumnsController;
        $this->columnDisplayNames = $this->getColumnDisplayNames(new PanelColumnsController, $request, $userId);
        $this->panelColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        // dd($this->panelColumnDisplayNames);
        $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        $this->columnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        array_push($this->columnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');

        // Initialize Challan Request
        $this->initializeChallanRequest();
        $billToData = new ReceiversController;
        $responseContent = $billToData->index($request)->content();
        $decompressedContent = gzdecode($responseContent);
        $decodedResponse = json_decode($decompressedContent);

        if ($decodedResponse === null) {
            // Handle error: invalid JSON or empty response
            $this->billToData = [];
        } else {
            $this->billToData = collect($decodedResponse->data)
                ->filter(function ($item) {
                    return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                })
                ->map(function ($item) {
                    $receiverName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                    return (object) array_merge((array) $item, ['receiver_name' => $receiverName]);
                })
                ->sortBy(function ($item) {
                    $receiverName = strtolower($item->receiver_name);
                    return is_numeric($receiverName[0]) ? 'z' . $receiverName : $receiverName;
                })
                ->values()
                ->all();
        }
    }

    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        // $this->dispatchBrowserEvent('inputsDisabledChanged', ['value' => false]);
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

    // Encapsulate fetching user details in a separate method
    protected function fetchUserDetails($request)
    {
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        if ($response->success == "true") {
            return json_encode($response->user);
        }
        $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    }

    // Fetching available stock for products
    protected function fetchAvailableStock($request)
    {
        $products = new ProductController;
        $response = $products->searchStock($request);
        $result = $response->getData();
        return (array) $result->data;
    }

    // Fetch billTo data
    // protected function fetchBillToData($request)
    // {
    //     $billTo = new ReceiversController;
    //     $responseContent = $billTo->index($request)->content();
    //     $decompressedContent = gzdecode($responseContent);
    //     $decodedResponse = json_decode($decompressedContent);
    //     return $decodedResponse->data ?? [];
    // }

    // Fetch units dynamically
    protected function fetchUnits()
    {
        $units = new UnitsController;
        $unitsCollection = $units->index('sender')->original;
        return $unitsCollection->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unit' => $unit->unit,
                'short_name' => $unit->short_name,
                'is_default' => $unit->is_default,
            ];
        })->toArray();
    }


    // Separate method to initialize challan request
    protected function initializeChallanRequest()
    {
        $this->createChallanRequest = [
            'challan_series' => '',
            'series_num' => '',
            'challan_date' => now()->format('Y-m-d'),
            'feature_id' => '',
            'receiver_id' => '',
            'receiver' => '',
            'comment' => '',
            'total_qty' => null,
            'total' => '',
            'calculate_tax' => null,
            'total_words' => '',
            'additional_phone_number' => '',
            'discount_total_amount' => '',
            'order_details' => [
                [
                    'p_id' => '',
                    'unit' => null,
                    'rate' => null,
                    'qty' => null,
                    'round_off' => null,
                    'discount' => null,
                    'total_amount' => null,
                    'tax_percentage' => null,
                    'discount_total_amount' => null,
                    'tax_amount' => null,
                    'tax' => null,
                    'item_code' => null,
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
        ];
    }


    // public function hydrate()
    // {
    //     $request = request();
    //     $UserResource = new UserAuthController;
    //         $response = $UserResource->user_details($request);
    //         $response = $response->getData();

    //         if ($response->success == "true") {
    //             $this->mainUser = json_encode($response->user);
    //             // $this->UserDetails = $response->user->plans;
    //             $this->user = json_encode($response->user);
    //             $this->reset(['errorMessage']);
    //         } else {
    //             $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    //         }
    // }

    public function updateVariable($variable, $value)
    {
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


    // public function addRow()
    // {
    //     // Add a new empty row to the order_details array
    //     $this->createChallanRequest['order_details'][] = [
    //         'p_id' => '',
    //         'unit' => '',
    //         'rate' => null,
    //         'qty' => null,
    //         'total_amount' => null,
    //         'item_code' => null,
    //         'discount_total_amount' => null,
    //         'tax' => null,
    //         'tax_percentage' => null,
    //         'discount' => null,
    //         'columns' => [
    //             [
    //                 'column_name' => '',
    //                 'column_value' => '',
    //             ]
    //         ],
    //     ];
    // }

    public function addRow()
    {
        // Add a new empty row to the order_details array with minimal initial data
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
            'columns' => [],
        ];
    }

    // Lazy load columns when needed
    public function loadColumns($index)
    {
        $this->createChallanRequest['order_details'][$index]['columns'] = [
            [
                'column_name' => '',
                'column_value' => '',
            ]
        ];
    }

    public function removeRow($index)
    {
        // Remove the specified row
        unset($this->createChallanRequest['order_details'][$index]);

        // Reindex the array to ensure sequential keys
        $this->createChallanRequest['order_details'] = array_values($this->createChallanRequest['order_details']);
    }
    public function updatedBarcode()
    {
        $this->addFromBarcode();
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

            $existingProductKey = array_search($product->id, array_column($this->createChallanRequest['order_details'], 'p_id'));
             if ($existingProductKey !== false) {
            // If the product already exists, increase the quantity and update the total amount
            $this->createChallanRequest['order_details'][$existingProductKey]['qty']++;
            $this->createChallanRequest['order_details'][$existingProductKey]['total_amount'] += $product->rate;
        } else {
            // If the product doesn't exist, add it to the order_details array
            if (empty($this->createChallanRequest['order_details'][0]['p_id'])) {
                $this->createChallanRequest['order_details'][0] = $productDetails;
            } else {
                $this->createChallanRequest['order_details'][] = $productDetails;
            }
        }
            $this->reset('barcode');
            // $this->calculateTotalAmount();
            // $this->calculateTotalQuantity();
        } else {
            $this->errorMessage = $result->message;
        }
    }
    public function addFromStock($productIds)
    {
        // dd($productIds);
        $this->selectedProductIds = $productIds;
        $this->hideWithoutTax = false;
        // Initialize total quantity and total amount
        $totalQty = 0;
        $totalAmount = 0;
        foreach ($this->selectedProductIds as $selectedProductId) {
            // Extract the actual product ID
            $actualProductId = explode('-', $selectedProductId)[0];

            $selectedProductDetails = array_filter($this->products, function ($product) use ($actualProductId) {
                return $product['id'] == $actualProductId;
            });
            // dd($selectedProductDetails);
            if (!empty($selectedProductDetails)) {
                $selectedProductDetails = reset($selectedProductDetails);

                if ($selectedProductDetails['with_tax']  && $selectedProductDetails['tax'] !== null && $this->pdfData->challan_templete == 4) {
                    // Calculate the rate excluding tax using the provided formula
                    $rateWithTax = $selectedProductDetails['rate'];
                    $taxPercentage = $selectedProductDetails['tax'];
                    $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));

                    $rateWithoutTax = round($rateWithoutTax, 2);

                    // Assign the calculated rate to the rate field
                    // $selectedProductDetails['rate'] = $rateWithoutTax;
                    // dd($selectedProductDetails['rate']);
                    $taxPercentage = $selectedProductDetails['tax'];
                    // $rateWithTax = $selectedProductDetails['rate'];

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
                }elseif($selectedProductDetails['with_tax'] == false && $selectedProductDetails['tax'] !== null && $this->pdfData->challan_templete == 4)
                {
                    $rateWithTax = $selectedProductDetails['rate'];
                    $taxPercentage = $selectedProductDetails['tax'];
                    $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));

                    $rateWithoutTax = round($rateWithoutTax, 2);

                    // Assign the calculated rate to the rate field
                    $selectedProductDetails['rate'] = $rateWithoutTax;
                    // dd($selectedProductDetails['rate']);
                    $taxPercentage = $selectedProductDetails['tax'];
                    // $rateWithTax = $selectedProductDetails['rate'];
                    // $this->calculateTax = false;
                    $selectedProductDetails['rate'] = $rateWithoutTax;

                    // Add the tax to the rate
                    $rateWithTax = $rateWithoutTax + ($rateWithoutTax * $taxPercentage / 100);
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
        // dd($this->createChallanRequest['order_details']);
        // Assign updated totals to the createChallanRequest array
        $this->createChallanRequest['total_qty'] = $totalQty;
        $this->createChallanRequest['total'] = $totalAmount;
        // $this->selectedProductIds = [];
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

    public function getProductsQueryProperty()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Product::with('details')
            ->where('user_id', $userId)
            ->where('qty', '!=', 0)
            ->when(!empty($this->article), function ($query) {
                $query->whereHas('details', function ($q) {
                    $q->where('column_name', 'Article')->whereIn('column_value', $this->article);
                });
            })
            ->when(!empty($this->item_code), function ($query) {
                $query->whereIn('item_code', $this->item_code);
            })
            ->when(!empty($this->location), function ($query) {
                $query->whereIn('location', $this->location);
            })
            ->when(!empty($this->category), function ($query) {
                $query->whereIn('category', $this->category);
            })
            ->when(!empty($this->warehouse), function ($query) {
                $query->whereIn('warehouse', $this->warehouse);
            });

        // Apply dynamic filters
        foreach ($this->filters as $column => $value) {
            if (!empty($value)) {
                $query->whereHas('details', function ($q) use ($column, $value) {
                    $q->where('column_name', $column)->whereIn('column_value', (array) $value);
                });
            }
        }

        return $query;
    }

    public function resetFilters()
    {
        $this->article = [];
        $this->item_code = [];
        $this->location = [];
        $this->category = [];
        $this->warehouse = [];
    }

    public function getProductsProperty()
    {
        // dd($this->productsQuery->get());
        $products = $this->productsQuery->paginate($this->paginate);

        $this->stockInPage = $products->pluck('id')->toArray();

        return $products;
    }
    public function updatedPaginate()
    {
        $this->stockInPage = $this->products->pluck('id')->toArray();
    }

    public function disableSaveButton()
    {
        $this->isSaveButtonDisabled = true;
    }

    // public function selectUser($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails)
    // {
    //     // dd($selectedUserDetails);
    //     try {
    //         DB::beginTransaction();

    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //         $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '1')->select('series_number')->first();

    //         if ($challanSeries == 'Not Assigned') {
    //             if ($series == null) {
    //                 throw new \Exception('Please add one default Series number');
    //             }
    //             $challanSeries = $series->series_number;
    //             $latestSeriesNum = Challan::where('challan_series', $challanSeries)
    //                 ->where('sender_id', $userId)
    //                 ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    //         } else {
    //             $latestSeriesNum = Challan::where('challan_series', $challanSeries)
    //                 ->where('sender_id', $userId)
    //                 ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    //         }

    //         $this->inputsDisabled = false; // Adjust the condition as needed
    //         $this->selectedUser = [
    //             "challanSeries" => $challanSeries,
    //             "seriesNumber" => $seriesNum,
    //             "address" => $address,
    //             "receiver_name" => $receiver,
    //             "email" => $email,
    //             "phone" => $phone,
    //             "gst" => $gst,
    //             "city" => $city,
    //             "state" => $state,
    //             "pincode" => $pincode,
    //         ];

    //         // Decode $selectedUserDetails once
    //         $decodedUserDetails = json_decode($selectedUserDetails);
    //         $this->receiverName = $this->selectedUser['receiver_name'];
    //         $this->createChallanRequest['challan_series'] = $challanSeries;
    //         $this->createChallanRequest['series_num'] = $seriesNum;
    //         $this->createChallanRequest['receiver'] = $receiver;
    //         $this->createChallanRequest['receiver_id'] = $decodedUserDetails->receiver_user_id;
    //         $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
    //         $this->selectedUserDetails = $decodedUserDetails->user->details;
    //         $this->inputsDisabled = false; // Adjust the condition as needed

    //         // Fetch billTo data
    //         $request = request();
    //         $billTo = new ReceiversController;
    //         $responseContent = $billTo->index($request)->content();
    //         $decompressedContent = gzdecode($responseContent);
    //         $decodedResponse = json_decode($decompressedContent);
    //         // dd($decodedResponse);
    //         if ($decodedResponse === null) {
    //             // Handle error: invalid JSON or empty response
    //             $this->billTo = [];
    //         } else {
    //             $this->billTo = collect($decodedResponse->data)
    //                 ->filter(function ($item) {
    //                     return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
    //                 })
    //                 ->map(function ($item) {
    //                     $receiverName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
    //                     return (object) array_merge((array) $item, ['receiver_name' => $receiverName]);
    //                 })
    //                 ->sortBy(function ($item) {
    //                     $receiverName = strtolower($item->receiver_name);
    //                     return is_numeric($receiverName[0]) ? 'z' . $receiverName : $receiverName;
    //                 })
    //                 ->values()
    //                 ->all();
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Error in selectUser method: ' . $e->getMessage());
    //         $this->errorMessage = 'An error occurred while processing your request.';
    //         return;
    //     }
    // }
    public function selectUser($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails)
    {
        try {
            DB::beginTransaction();

            // Determine the user ID
            $userId = $this->getUserId();

            // Get default series if challan series is not assigned
            $challanSeries = $this->getChallanSeries($challanSeries, $userId);

            // Get the latest series number
            $seriesNum = $this->getNextSeriesNumber($challanSeries, $userId);

            // Set selected user details
            $this->setSelectedUserDetails($challanSeries, $seriesNum, $address, $receiver, $email, $phone, $gst, $city, $state, $pincode);

            // Set challan request details
            $this->setCreateChallanRequest($challanSeries, $seriesNum, $receiver, $selectedUserDetails);

            // Fetch and set billTo data
            $this->billToData = $this->fetchBillToData();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in selectUser method: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while processing your request.';
        }
    }

    private function fetchBillToData()
    {
        try {
            $request = request();
            $billTo = new ReceiversController;
            $responseContent = $billTo->index($request)->content();
            $decompressedContent = gzdecode($responseContent);
            $decodedResponse = json_decode($decompressedContent);

            if ($decodedResponse === null) {
                return [];
            }

            return collect($decodedResponse->data)
                ->filter(function ($item) {
                    return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                })
                ->map(function ($item) {
                    $receiverName = !empty($item->receiver_name) ? $item->receiver_name : (!empty($item->details[0]->phone) ? $item->details[0]->phone : $item->details[0]->email);
                    return (object) array_merge((array) $item, ['receiver_name' => $receiverName]);
                })
                ->sortBy(function ($item) {
                    $receiverName = strtolower($item->receiver_name);
                    return is_numeric($receiverName[0]) ? 'z' . $receiverName : $receiverName;
                })
                ->values()
                ->all();
        } catch (\Exception $e) {
            \Log::error('Error fetching billTo data: ' . $e->getMessage());
            return [];
        }
    }

    public function selectUserAddress($selectedUserDetail, $selectedUserDetails)
    {
        $selectedUserDetail = json_decode($selectedUserDetail);

        // Set user address details
        $this->setSelectedUserAddressDetails($selectedUserDetail);

        // Fetch and set billTo data
        $this->billTo = $this->fetchBillToData();
    }

    // Helper functions

    private function getUserId()
    {
        return Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    }

    private function getChallanSeries($challanSeries, $userId)
    {
        if ($challanSeries == 'Not Assigned') {
            $series = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', '1')
                ->where('panel_id', '1')
                ->select('series_number')
                ->first();

            if ($series == null) {
                throw new \Exception('Please add one default Series number');
            }
            return $series->series_number;
        }
        return $challanSeries;
    }

    private function getNextSeriesNumber($challanSeries, $userId)
    {
        $latestSeriesNum = Challan::where('challan_series', $challanSeries)
            ->where('sender_id', $userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        return $latestSeriesNum ? $latestSeriesNum + 1 : 1;
    }

    private function setSelectedUserDetails($challanSeries, $seriesNum, $address, $receiver, $email, $phone, $gst, $city, $state, $pincode)
    {
        $this->inputsDisabled = false;
        $this->selectedUser = [
            "challanSeries" => $challanSeries,
            "seriesNumber" => $seriesNum,
            "address" => $address,
            "receiver_name" => $receiver,
            "email" => $email,
            "phone" => $phone,
            "gst" => $gst,
            "city" => $city,
            "state" => $state,
            "pincode" => $pincode,
        ];
        $this->receiverName = $this->selectedUser['receiver_name'];
    }

    private function setCreateChallanRequest($challanSeries, $seriesNum, $receiver, $selectedUserDetails)
    {
        $decodedUserDetails = json_decode($selectedUserDetails);

        $this->receiverName = $this->selectedUser['receiver_name'];
        $this->createChallanRequest = [
            'challan_series' => $challanSeries,
            'series_num' => $seriesNum,
            'receiver' => $receiver,
            'receiver_id' => $decodedUserDetails->receiver_user_id,
            'feature_id' => $this->persistedActiveFeature,
        ];
        $this->selectedUserDetails = $decodedUserDetails->user->details;
    }

    private function setSelectedUserAddressDetails($selectedUserDetail)
    {
        $this->receiverAddress = $selectedUserDetail->address;
        $this->receiverLocation = $selectedUserDetail->location_name;
        $this->receiverPhone = $selectedUserDetail->phone;
        $this->selectedUser['gst'] = $selectedUserDetail->gst_number;
        $this->createChallanRequest['user_detail_id'] = $selectedUserDetail->id;
    }



    // Challan Save
    public function challanCreate(Request $request)
    {
        // dd($request->all());

        if($this->updateForm == false)
        {
            $request->merge($this->addReceiverData);
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
                // dd($result);
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
                $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;

        }
        $this->createChallanRequest['calculate_tax'] = $this->calculateTax;
        foreach ($this->createChallanRequest['order_details'] as $index => $orderDetail) {
            $this->createChallanRequest['order_details'][$index]['discount'] = $this->discount_total_amount;
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
        $ChallanController = new ChallanController;

        $response = $ChallanController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // dd($result);
        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->inputsDisabled = false;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            if($this->createChallanRequest['receiver_id'] == null){
                $this->successMessage = $result->message;
                return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
            }
            $this->challanId = $result->challan_id;

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }
    public function challanEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed
        $this->reset([ 'message', 'challanSave']);
    }

    public function sendChallan($id)
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ChallanController = new ChallanController;
        $response = $ChallanController->send($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            // Assuming $this->persistedTemplate holds the template name
         } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
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

        $ChallanController = new ChallanController;

        $response = $ChallanController->challanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_challan', '2');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        $request = request();
        // $request = new Request(['page' => $page]);
        $challanController = new ChallanController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }


    // public function render()
    // {
    //     return view('livewire.sender.screens.create-challan',
    //     [
    //         'availableStock' => $this->products,
    //         'billToData' => $this->billTo,
    //         'columnDisplayNamesData' => $this->columnDisplayNames,
    //         'panelColumnDisplayNamesData' => $this->panelColumnDisplayNames,
    //         'panelUserColumnDisplayNamesData' => $this->panelUserColumnDisplayNames,
    //     ]);
    // }
    // Optimized render method to minimize data passed to the view
    public function render()
    {
        return view('livewire.sender.screens.create-challan', [
            'availableStock' => $this->products,
            // 'columnDisplayNamesData' => $this->columnDisplayNames,
            // 'panelColumnDisplayNamesData' => $this->panelColumnDisplayNames,
            // 'panelUserColumnDisplayNamesData' => $this->panelUserColumnDisplayNames,
        ]);
    }
}
