<?php

namespace App\Http\Livewire\Receiver\Content;

use App\Models\User;
use App\Models\Challan;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\ReturnChallan;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class Content extends Component
{
    public $createChallanRequest = array(
        'challan_series' => '',
        'series_num' => '',
        'challan_date' => '',
        'feature_id' => '',
        'receiver_id' => '',
        'receiver' => '',
        'comment' => '',
        'total_qty' => null,
        'total' => '',
        'round_off' => null,
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
    );
    public $persistedTemplate;
    public $sendButtonDisabled = true;
    public $persistedActiveFeature, $template;
    public $features = [];
    public $activeFeature;
    public $discount_total_amount;
    public $rate;
    public $data;
    public $quantity;
    public $totalAmount;
    public $rows = [];
    public $createChallan;
    public $validationErrorsJson = [];
    public $selectedUserDetails = [];
    public $createChallanInstance;
    public $sentChallan;
    public $challanSave;
    public $tableTdData, $currentPage = 1, $paginateLinks;
    public $action = 'save';
    public $autofillData = [];
    public $challanId, $invoiceData;
    public $panelColumnDisplayNames, $challanFiltersData, $receivedPanelColumnDisplayNames, $ColumnDisplayNames, $assigned_to_name, $mainUser;
    protected $addReceiver, $storeChallanSeries, $responseData;
    private $addReceiverCode;
    public $response, $receiverData, $seriesNoData, $newChallanDesign;
    public $newChallanSeriesNoController, $addChallanSeries, $receiverDatas, $senderList, $qty, $senderNames;
    public $receiver_name, $company_name, $fromDate, $toDate,$from, $to, $isMobile, $email, $address, $pincode, $phone, $state, $city, $tan, $errorMessage, $successMessage, $showManualReceiverTab, $receiver_special_id, $errors, $statusCode, $message, $remaining_qty;
    public $page = 1;
    public $perPage = 100;
    public $maxPerPage = 100;
    //sent challan
    public $challanData;
    public $total_qty;
    public $total = 0;
    // create challan screen
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;

    public $selectedUser;
    public $receivedArticles;
    public $selectReceiver;
    public $Senders = [];
    public $billTo;
    public $selectedSenderId;
    public $selectedColumnName;
    public $isLoading = true;
    public $teamMembers;

    // sfp
    public $team_user_id, $challan_id, $challan_sfp;
    // sfp
    public $updateChallanSeriesData = [];
    public $challan_series, $challan_date, $series_num, $status, $sender_id, $sender, $receiver_id, $receiver, $comment,  $unit = [], $artical = [];

    public $sortField = null;
    public $sortDirection = null;

    public $addChallanSeriesData = array(
        'series_number' => '',
        'valid_from' => '',
        'valid_till' => '',
        'receiver_user_id' => '',
        'panel_id' => '2',
        'section_id' => '1',
        // 'assigned_to_r_id' => '',
        'assigned_to_s_id' => '',
        'assigned_to_name' => '',
        'default' => '0',
        'status' => 'active',
    );
    public $challanDesignData = array(
        'panel_id' => '2',
        'section_id' => '1',
        'feature_id' =>  '11',
        'default' => '0',
        'status' => '',
        'panel_column_default_name' => '',
        'user_id' => '',

    );

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // Reset pagination to the first page when sorting
        // $this->resetPage();
    }
    public function mount()
    {
        // $this->createChallanInstance = new createChallan;
        // Retrieve the persisted value from the session, if available
        $sessionId = session()->getId();
        $template = request('template', 'index');
        if (view()->exists('components.panel.receiver.' . $template)) {
            // $this->persistedTemplate = view()->exists('components.panel.receiver.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            // $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            // dd('sedg');
            $request = request();
            $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);
            $id = '';
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
                case 'create_return_challan':
                    $this->createChallan($request, $id);

                    break;
                case 'sent_return_challan':
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
                    // $this->sentChallan($request);
                    $this->sentChallan($this->currentPage);
                    break;
                case 'sfp_return_challans':
                    // $this->sfpChallans($request);
                    break;
                case 'received_return_challan':
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
                    $this->receivedChallan($this->currentPage);
                    // $this->seriesNoData = (array) $data->getData()->data;
                    break;
                case 'detailed_received_return_challan';
                    $this->detailedReceivedReturnChallan($this->currentPage);
                    break;
                case 'modify_return_challan':
                    $this->modifyChallan($request);
                    break;
                case 'detailed_sent_return_challan';
                    $this->detailedSentReturnChallan($this->currentPage);
                    break;
                case 'return_challan_series_no':
                    $filterDataset = [
                        'panel_id' => 2,
                        'section_id' => 1,
                    ];
                    $request->merge($filterDataset);
                    $newChallanSeriesIndex = new PanelSeriesNumberController;
                    $request->merge(['panel_id' => '2']);
                    $data = $newChallanSeriesIndex->index($request);
                    // dd($data);
                    $this->seriesNoData = (array) $data->getData()->data;

                    // dd($this->receiverDatas);
                    $receiverScreen = new ReturnChallanController;
                    $response = $receiverScreen->getSender($request);


                    $responseData = $response->getData(); // Assuming $response is your JsonResponse

                    $this->receiverDatas = $responseData->sender_list;


                    // dd($this->receiverDatas);
                    break;
                    default:
                    $this->persistedTemplate = 'index';
                    $this->persistedActiveFeature = null;
                    break;

            }
        }
        else {
            $this->persistedTemplate = 'index';
            $this->persistedActiveFeature = null;
        }
        // $this->emit('updateDynamicView', 'components.panel.sender.' . $this->persistedTemplate);
    }
    // public function innerFeatureRedirect($template, $activeFeature)
    // {
    //     $this->handleFeatureRoute($template, $activeFeature);
    //     // $this->emit('innerFeatureRoute',$template,$activeFeature);
    //     $this->template = '';
    //     $this->activeFeature = '';
    // }
    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 2;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems); // Get the first item
            $this->panel = $item->panel;
            // dd($this->panel);
            // Store $this->panel in session data
            Session::put('panel', $this->panel);

        }

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }

    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    // public function handleFeatureRoute($template, $activeFeature)
    // {

    //     $this->persistedTemplate = view()->exists('components.panel.receiver.' . $template) ? $template : 'index';
    //     $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . $template) ? $activeFeature : null;
    //     //    dd( $this->persistedTemplate,
    //     //    $this->persistedActiveFeature);
    //     $this->savePersistedTemplate($template, $activeFeature);
    //             return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);


    //     // $this->mount();
    // }
    public function handleFeatureRoute($template, $activeFeature)
    {
        $viewPath = 'components.panel.receiver.' . $template;
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);
        return redirect()->route('receiver', ['template' => $this->persistedTemplate]);
    }


    // create challan screen
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'manualReceiverAdded' => 'handleManualReceiverAdded',
        'forceDelete' => 'deleteChallanSeries',
        'modifyChallan' => 'handleModifyChallan',
        'detailedSentReturnChallan' => 'handleDetailedSentReturnChallan',
        'detailedReceivedReturnChallan' => 'handleDetailedReceivedReturnChallan',
        'updateTotalQty' => 'updateTotalQty',
        'seriesNumberUpdated' => 'updateSeriesNumber',
    ];


    public function updateSeriesNumber($newSeriesNumber)
    {
        if (is_string($this->selectedUser)) {
            $this->selectedUser = json_decode($this->selectedUser, true);
        }

        if (!is_array($this->selectedUser)) {
            $this->selectedUser = [];
        }

        $this->selectedUser['seriesNumber'] = $newSeriesNumber;
        $this->disabledButtons = true;
    }

    public function updateTotalQty($totalQty)
    {
        // dd($totalQty);
        $this->total_qty = $totalQty;
    }


    public function updateChallanValues($totalQty, $total)
    {
        $this->createChallanRequest['total_qty'] = $totalQty;
        $this->createChallanRequest['total'] = $total;
    }

    public $sfpModal = false;
    public $searchModalAction;

    public function updateVariable($variable, $value)
    {
        // dd($this->{$variable},$variable,$value);
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
    public function updatedCreateInvoiceRequest()
    {
        $orderDetails = collect($this->createChallanRequest['order_details']);

        $totalQuantity = $orderDetails->sum(function ($detail) {
            return (float)$detail['qty'];
        });


        $totalWithoutTax = array_reduce($this->createChallanRequest['order_details'], function ($carry, $item) {
            if (isset($item['total_amount']) && isset($item['total_tax'])) {
                return $carry + $item['total_amount'] - $item['total_tax'];
            }
            return $carry;
        }, 0);

        $this->createChallanRequest['order_details'] = array_map(function ($item) {
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
        }, $this->createChallanRequest['order_details']);


        $this->createChallanRequest['total_qty'] = $totalQuantity;
        $this->createChallanRequest['total_without_tax'] = $totalWithoutTax;
        // $this->createChallanRequest['total_discount'] = $totalDiscount;
        $this->updateTotal();
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

        foreach ($this->createChallanRequest['order_details'] as $row) {
            if (isset($row['tax_amount'])) {
                $totalTax += (float) $row['tax_amount'];
            }
        }

        $this->createChallanRequest['total_tax'] = $totalTax;
    }
    public function updateTotal()
    {
        $orderDetails = collect($this->createChallanRequest['order_details']);

        $totalWithoutTax = $orderDetails->sum(function ($item) {
            return $item['total_amount'] - $item['total_tax'];
        });

        $totalTax = $orderDetails->sum('total_tax');

        $totalDiscount = $this->createChallanRequest['total_discount'] ?? 0;

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
        if ($this->discount_total_amount) {
            $totalWithoutTaxDiscount = $totalWithoutTax - (float)($totalWithoutTax * $this->discount_total_amount / 100);
            $total = $totalWithoutTaxDiscount + $totalTax;
            $this->discountWithoutTax = $totalWithoutTaxDiscount;
        }

        // $this->createChallanRequest['total'] = number_format(floatval(str_replace(',', '', $total)), 2, '.', '');
        // $this->createChallanRequest['total_words'] = $this->numberToIndianRupees((float) $total);
    }

    public function loadData()
    {
        $this->isLoading = false;
    }

    public function challanCreate(Request $request)
    {
        // Filter out null or empty order details
        // $this->createChallanRequest['order_details'] = array_filter($this->createChallanRequest['order_details'], function ($orderDetail) {
        //     return isset($orderDetail['id']) && $orderDetail['id'] !== null;
        // });
        if (is_array($this->selectedUser ) && isset($this->selectedUser ['seriesNumber'])) {
            // dd($this->selectedUser ['seriesNumber']);
            $this->createChallanRequest['series_num'] = $this->selectedUser['seriesNumber'] ?? null;
        }
        // Merge the filtered request data

        $request->merge($this->createChallanRequest );
        // dd($request->all());
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

        // If there are errors, return early
        if ($errors) {
            return;
        }
        // Create instances of necessary classes
        $ChallanController = new ReturnChallanController;

        // Call the store method and get the response
        $response = $ChallanController->store($request);

        // Get the result from the response
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->challanId = $result->challan_id;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }


    public function modifyChallan(Request $request)
    {
        $id = session('persistedActiveFeature');
        $challanController = new ReturnChallanController();
        $challanModifyData = $challanController->show($request, $id);

        // Convert the stdClass to an array
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);

        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);

        $this->challanModifyData = json_encode($modifiedDataArray);
        $this->inputsDisabled = false;
        request()->replace([]);
        $request->merge($this->createChallanRequest);
        // dd($request->sender_id);
        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 1,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
            'user_id' => $request->sender_id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = $panelColumnDisplayNames;
        $request->merge([
            'feature_id' => 1,
            'user_id' => $request->sender_id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

        // dd($this->panelColumnDisplayNames);
    }

    public function saveChallanModify(Request $request)
    {
        $request->merge($this->createChallanRequest);
        // dd($request);

        // Create instances of necessary classes
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->challanId = $result->challan_id;

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function challanModify(Request $request)
    {
        $request->merge($this->createChallanRequest);

        // Create instances of necessary classes
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->update($request, $this->challanId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // dd($result);
            $this->challanId = $result->challan_id;

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public $createChallanData = [
        'rate' => null,
        'quantity' => null,
        'totalAmount' => 0,
        'rows' => [],
        // Add other relevant properties here
    ];
    public function challanEdit()
    {
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed


    }

    public function sendChallan($id)
    {

        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);
        // dd($request);
        $ChallanController = new ReturnChallanController;
        $response = $ChallanController->send($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->innerFeatureRedirect('sent_return_challan', '8');
            session()->flash('success', 'Challan send successfully.');

            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }
    public $selectedReturnChallanId;
    public function updateTimelineModal($returnChallanId)
    {
        $this->selectedReturnChallanId = $returnChallanId;
        $this->emit('openTimelineModal');
    }

    // public function addCommentReceivedReturnChallan($id)
    // {
    //     // dd($id);
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
    //         $this->innerFeatureRedirect('received_return_challan', '9');
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //     redirect()->route('receiver')->with('message', $this->successMessage ?? $this->errorMessage);
    // }

    public function addCommentSentReturnChallan($id)
    {
        // dd($id);
        $request = request();
        $request->merge([
            'status_comment' => $this->status_comment,

        ]);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->addComment($request, $id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_return_challan', '8');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('receiver')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function reSendChallan($id)
    {

        $request = request();
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->resend($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('success', 'Challan resend successfully.');

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
                return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);


    }
    public $status_comment = '';
    // public function acceptChallan($id)
    // {

    //     $request = request();
    //     $ChallanController = new ChallanController;

    //     $response = $ChallanController->accept($request, $id);
    //     $result = $response->getData();

    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         session()->flash('success', 'Challan accepted successfully.');

    //         $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //             return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    // }
    // public function rejectChallan($id)
    // {

    //     $request = request();
    //     $ChallanController = new ChallanController;

    //     $response = $ChallanController->reject($request, $id);
    //     $result = $response->getData();

    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         session()->flash('success', 'Challan rejected successfully.');

    //         $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //             return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    // }

    public function deleteChallan($id)
    {

        $request = request();
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->delete($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('success', 'Challan deleted successfully.');

            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
                return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    }

    public function createChallan(Request $request, $id)
    {
        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 1,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = $panelColumnDisplayNames;


        $request->merge([
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;


        $receiverScreen = new ChallanController;
        $response = $receiverScreen->getSenderDataForSeries($request);


        $responseData = $response->getData(); // Assuming $response is your JsonResponse

        $this->Senders = $responseData->sender_list;
        // dd($this->Senders);
        // Call the getSender method with the required parameters

        // dd($response);
        // $this->responseData = json_decode($response->content());
        // dd($this->responseData);

    }
    public $challanSeries, $senderName;

    public function selectUser($sender, $phone, $email, $address, $city, $state, $gstNumber, $senderId)
    {
        // dd($sender, $phone, $email, $address, $gstNumber, $senderId);
        try {
        $request = request();
        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 1,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
            'user_id' => $senderId,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = $panelColumnDisplayNames;


        $request->merge([
            'feature_id' => 1,
            'user_id' => $senderId,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

        $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('default', "1")
            ->where('panel_id', '2')
            ->first();
            // dd($series);
        $sender = new ChallanController();
        $selectedUser = $sender->senderDetails($senderId)->getData()->data;
        // dd($selectedUser);
        $challanSeries = $series->series_number;
        $this->challanSeries = $series->series_number;

        $latestSeriesNum = ReturnChallan::where('challan_series', $challanSeries)
            ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            // ->max('series_num');
            ->get();
        // $latestSeriesNum = ReturnChallan::where('challan_series', $challanSeries)
        //             ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)

        //             ->get();
            // dd($latestSeriesNum);
            // if ($latestSeriesNum->isNotEmpty()) {
                // Get the maximum 'series_num' from the collection
                $maxSeriesNum = $latestSeriesNum->max('series_num');

            // }
        // Increment the latestSeriesNum for the new challan
        $seriesNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;
        // $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $selectedUser->series_num = $seriesNum;
        $this->selectedUser = json_encode($selectedUser);
        // dd($this->selectedUser);
        $request = request();

        $receivedArticles = new ReturnChallanController;
        $request->merge(['sender_id' => $selectedUser->id]);
        $receivedArticles = $receivedArticles->getSenderData($request, $senderId);
        $receivedArticles = $receivedArticles->getData()->article;
        // dd($receivedArticles);
        $this->senderName = $selectedUser->name;
         // Decode $selectedUserDetails once
        //  $decodedUserDetails = json_decode($selectedUserDetails);
        $this->receivedArticles = json_encode($receivedArticles);
        $this->createChallanRequest['series_num'] = $seriesNum;
        $this->createChallanRequest['challan_series'] = $challanSeries;
        $this->createChallanRequest['challan_date'] = date("Y-m-d");
        // dd($this->createChallanRequest['challan_date']);
        $this->createChallanRequest['receiver'] = $selectedUser->name;
        $this->createChallanRequest['receiver_id'] = $senderId;
        $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
        //  $this->selectedUserDetails = $decodedUserDetails->user->details;
        $this->inputsDisabled = false;

        $receiverScreen = new ChallanController;
        $response = $receiverScreen->getSenderDataForSeries($request);


        $responseData = $response->getData(); // Assuming $response is your JsonResponse

        $this->Senders = $responseData->sender_list;
    } catch (\Exception $e) {
        // Log the exception for debugging
        \Log::error('Error in selectUser: ' . $e->getMessage());

        // Set an error message to be displayed
        $this->errorMessage = json_encode([['An error occurred while selecting the user. Please try again.']]);

        // Optionally, you could rethrow the exception if you want it to be handled by the global exception handler
        // throw $e;
        }
    }

    // public function selectUser($challanSeries, $address, $email, $phone, $gst, $sender)
    // {
    //     if ($challanSeries == 'Not Assigned') {
    //         $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->where('default', "1")
    //             ->first();
    //         if ($series == null) {
    //             $this->errorMessage = json_encode([['Please add one default Series number']]);
    //         } else {
    //             $challanSeries = $series->series_number;
    //             $latestSeriesNum = Challan::where('challan_series', $challanSeries)
    //                 ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //                 ->max('series_num');
    //             // Increment the latestSeriesNum for the new challan
    //             $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

    //             $this->selectedUser = [
    //                 "challanSeries" => $challanSeries,
    //                 "seriesNumber" => $seriesNum,
    //                 "address" => $address,
    //                 "receiver_name" => $receiver,
    //                 "email" => $email,
    //                 "phone" => $phone,
    //                 "gst" => $gst
    //             ];
    //             $this->receiverName = $this->selectedUser['receiver_name'];
    //             $this->receiverAddress = $this->selectedUser['address'];
    //             // dd(json_decode($selectedUserDetails));
    //             $this->createChallanRequest['challan_series'] = $challanSeries;
    //             $this->createChallanRequest['receiver'] = $receiver;
    //             $this->createChallanRequest['receiver_id'] = json_decode($selectedUserDetails)->receiver_user_id;
    //             $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //             $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
    //             $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //             $this->inputsDisabled = false; // Adjust the condition as needed
    //             // dd($this->selectedUserDetails);

    //         }
    //     } else {

    //         // Get the latest series_num for the given challan_series and user_id
    //         $latestSeriesNum = Challan::where('challan_series', $challanSeries)
    //             ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->max('series_num');
    //         // Increment the latestSeriesNum for the new challan
    //         $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;




    //         $this->selectedUser = [
    //             "challanSeries" => $challanSeries,
    //             "seriesNumber" => $seriesNum,
    //             "address" => $address,
    //             "receiver_name" => $receiver,
    //             "email" => $email,
    //             "phone" => $phone,
    //             "gst" => $gst
    //         ];
    //         // dd($this->selectedUser);
    //         $this->receiverName = $this->selectedUser['receiver_name'];
    //         $this->receiverAddress = $this->selectedUser['address'];
    //         $this->createChallanRequest['challan_series'] = $challanSeries;
    //         $this->createChallanRequest['receiver'] = $receiver;
    //         $this->createChallanRequest['receiver_id'] = json_decode($selectedUserDetails)->receiver_user_id;
    //         $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
    //         $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
    //         $this->selectedUserDetails = json_decode($selectedUserDetails)->details;
    //         $this->inputsDisabled = false; // Adjust the condition as needed
    //     }
    // }

    public function getSenderData($request, $id)
    {
        $receiverScreen = new ReturnChallanController;
        $senderDataList = $receiverScreen->getSenderData($request, $id);
        $content = $senderDataList->getContent();

        $data = json_decode($content, true);
        // dd($data);
        $this->senderList = $data['sender_list'];
        $this->inputsDisabled = false;
    }

    // public function updatedSelectedColumnName()
    // {
    //     foreach ($this->senderList as $item) {
    //         if ($item['column_value'] === $this->selectedColumnName) {
    //             $this->rate = $item['rate'];
    //             $this->unit = $item['unit'];
    //             $this->qty = $item['remaining_qty'];
    //             $this->details = $item['details'];
    //             $this->total_amount = $item['total_amount'];
    //             $this->comment = $item['comment'];
    //             break;
    //         }
    //     }
    // }

    // create challan
      public function addRow()
    {
        // Add a new empty row to the order_details array
        $this->createChallanRequest['order_details'][] = [
            'p_id' => '',
            'unit' => '',
            'rate' => null,
            'qty' => null,
            'total_amount' => null,
            'item_code' => null,
            'remaining_qty' => null,
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
        // Check if there's more than one row before removing
        if (count($this->createChallanRequest['order_details']) > 1) {
            // Remove the row from the $this->createChallanRequest['order_details'] array
            array_splice($this->createChallanRequest['order_details'], $index, 1);

            // Remove the row from the $this->rows array if it exists
            if (isset($this->rows[$index])) {
                array_splice($this->rows, $index, 1);
            }

            // Update the component state
            $this->createChallanRequest['order_details'] = array_values($this->createChallanRequest['order_details']);
            $this->rows = array_values($this->rows);
        } else {
            // If it's the last row, just clear its contents instead of removing
            $this->createChallanRequest['order_details'][0] = [
                'p_id' => '',
                'unit' => '',
                'rate' => null,
                'qty' => null,
                'total_amount' => null,
                'item_code' => null,
                'remaining_qty' => null,
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
    }

    // public function calculateTotalQuantity()
    // {
    //     $totalQuantity = 0;

    //     foreach ($this->createChallanRequest['order_details'] as $row) {
    //         if (isset($row['remaining_qty'])) {
    //             $totalQuantity += (float) $row['remaining_qty'];
    //         }
    //     }

    //     $this->createChallanRequest['total_qty'] = $totalQuantity;
    // }



    public function selectArticle($detail, $key)
    {
        // dd($detail);
        $this->lastQty  = $detail['remaining_qty'];
        $this->createChallanRequest['order_details'][$key] = $detail;
        $this->discount_total_amount = $detail['discount'];

    }
    public $errorMessages;

    public function updateTotalAmount($index)
    {
        // dd($this->lastQty);
        // dump($this->createChallanRequest['order_details'][$index]['rate'],$this->createChallanRequest['order_details'][$index]['remaining_qty']);
        if (isset($this->createChallanRequest['order_details'][$index]['rate']) && isset($this->createChallanRequest['order_details'][$index]['remaining_qty'])) {
            $rate = $this->createChallanRequest['order_details'][$index]['rate'];
            $qty = $this->createChallanRequest['order_details'][$index]['remaining_qty'];
              // Add a condition to check if 'remaining_qty' is less than 'qty'

            $this->createChallanRequest['order_details'][$index]['total_amount'] = (float) $rate * (float) $qty;

            $this->calculateTotalAmount();
            $this->calculateTotalQuantity();
        }

    }




    // public function calculateTotalAmount()
    // {
    //     $total = 0;

    //     foreach ($this->createChallanRequest['order_details'] as $row) {
    //         if (isset($row['rate']) && isset($row['remaining_qty'])) {
    //             $total += (float) $row['rate'] * (float) $row['remaining_qty'];
    //         }
    //     }

    //     $this->createChallanRequest['total'] = $total;
    //     $this->createChallanRequest['total_words'] = $this->numberToIndianRupees((float) $total);
    // }
    // Sent Challan
    public function sentChallan($page)
    {
        $request = new Request;
        request()->replace([]);
        $columnFilterDataset = [
            'feature_id' => '8',
            'panel_id' => '2',
        ];
        $request->merge($columnFilterDataset);
        // dd($request);
        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        // dd($ColumnDisplayNames);
        $this->ColumnDisplayNames = $ColumnDisplayNames;
        request()->replace([]);

        if ($request->has('challan_series')) {
            // Split the search term into series and number
            $searchTerm = $request->challan_series;
            $searchParts = explode('-', $searchTerm);

            if (count($searchParts) == 2) {
                $series = $searchParts[0];
                $num = $searchParts[1];

                // Perform the search
                $query->where('challan_series', $series)
                    ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }

        // // Filter by sender_id
        if ($this->sender_id != null) {
            $request->merge(['sender_id' => $this->sender_id]);
        }

        // Filter by receiver_id
        if ($this->receiver_id != null) {
            // dump($this->receiver_id);
            $request->merge(['receiver_id' => $this->receiver_id]);
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
        if ($this->fromDate != null && $this->toDate != null) {
            $request->merge([
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
            ]);
        }
        $this->tableTdData = [];
        $request = new Request(['page' => $page, 'perPage' => $this->perPage]);

        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        // dd($this->tableTdData);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        $this->emit('challanDataReceived', $tableTdData);
    }

    // DETAILED VIEW OF SENT INVOICE
    public function detailedSentReturnChallan($page)
    {
        $request = new Request;
        // $this->ColumnDisplayNames = ['challan No', 'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn', 'Unit', 'Quantity', 'Unit Price', 'Total Amount', 'Details'];
        $this->ColumnDisplayNames = ['Challan No',   'Time', 'Date', 'Receiver','Creator', 'Article', 'Hsn','Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];
        request()->replace([]);

        if ($request->has('challan_series')) {
            // Split the search term into series and number
            $searchTerm = $request->challan_series;
            $searchParts = explode('-', $searchTerm);

            if (count($searchParts) == 2) {
                $series = $searchParts[0];
                $num = $searchParts[1];

                // Perform the search
                $query->where('challan_series', $series)
                    ->where('series_num', $num);
            }
        }
        if ($this->sender_id != null) {
            $request->merge(['sender_id' => $this->sender_id]);
        }

        // Filter by receiver_id
        if ($this->receiver_id != null) {
            // dump($this->receiver_id);
            $request->merge(['receiver_id' => $this->receiver_id]);
        }

        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }

        if ($this->fromDate != null && $this->toDate != null) {
            $request->merge([
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
            ]);
        }

        $this->tableTdData = [];
        $request = new Request(['page' => $page, 'perPage' => $this->perPage]);

        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // dd($this->tableTdData);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->challanData);
        $this->emit('challanDataReceived', $tableTdData);

    }

      // DETAILED VIEW OF SENT INVOICE
      public function detailedReceivedReturnChallan($page)
      {
          $request = new Request;
          // $this->ColumnDisplayNames = ['challan No', 'Buyer', 'TIme', 'Date', 'Creator', 'Article', 'Hsn', 'Unit', 'Quantity', 'Unit Price', 'Total Amount', 'Details'];
          $this->ColumnDisplayNames = ['Challan No',   'Time', 'Date', 'Receiver','Creator', 'Article', 'Hsn','Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];
          request()->replace([]);

          if ($request->has('challan_series')) {
              // Split the search term into series and number
              $searchTerm = $request->challan_series;
              $searchParts = explode('-', $searchTerm);

              if (count($searchParts) == 2) {
                  $series = $searchParts[0];
                  $num = $searchParts[1];

                  // Perform the search
                  $query->where('challan_series', $series)
                      ->where('series_num', $num);
              }
          }
          if ($this->sender_id != null) {
              $request->merge(['sender_id' => $this->sender_id]);
          }

          // Filter by receiver_id
          if ($this->receiver_id != null) {
              // dump($this->receiver_id);
              $request->merge(['receiver_id' => $this->receiver_id]);
          }

          if ($this->status != null) {
              $request->merge(['status' => $this->status]);
          }

          if ($this->state != null) {
              $request->merge(['state' => $this->state]);
          }

          if ($this->fromDate != null && $this->toDate != null) {
              $request->merge([
                  'from_date' => $this->fromDate,
                  'to_date' => $this->toDate,
              ]);
          }

          $this->tableTdData = [];
          $request = new Request(['page' => $page, 'perPage' => $this->perPage]);

         $challanController = new ChallanController();
        $tableTdData = $challanController->indexDetailed($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        // dd($this->tableTdData);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
          // dd($this->challanData);
          $this->emit('challanDataReceived', $tableTdData);

      }


    public function sfpReturnChallan()
    {

        $request = request();
        $request->merge([
            'id' => $this->team_user_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        $ChallanController = new ReturnChallanController;
        // dd($request);
        $response = $ChallanController->returnChallanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_return_challan', '9');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('receiver')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $admin_ids = [];
    public $team_user_ids = [];

    public function sfpChallan()
    {
        $request = request();
        $request->merge([
            'id' => $this->team_user_ids,
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
            $this->innerFeatureRedirect('received_return_challan', '8');
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // $request = $request();

        redirect()->route('receiver')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function SfpAccept(Request $request, $sfpId)
    {
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReject(Request $request, $sfpId)
    {
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    public function SfpReAccept(Request $request, $sfpId)
    {
        $receiverScreen = new ChallanController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReReject(Request $request, $sfpId)
    {
        $receiverScreen = new ChallanController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }


    public function challanSeries(Request $request)
    {
        // dd($request);
        $request->merge($this->addChallanSeriesData);
        // dd($request->assigned_to_r_id);
        if($request->assigned_to_s_id == 'default'){
            $request->merge(['assigned_to_s_id' => '', 'default' => '1', ]);
        }
        // dd($request);

        $newChallanSeriesNoController = new PanelSeriesNumberController;
        $response = $newChallanSeriesNoController->store($request);
        // $this->reset(['addChallanSeriesData']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            $this->reset(['addChallanSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '1']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiversController;

            $request->replace([]);
            $response = $newReceiversController->index($request);
            $receiverData = $response->getData();
            $this->receiverDatas = $receiverData->data;
            // dump("3");
            // dump(json_encode($this->receiverDatas));

        }
    }
    public function deleteChallanSeries($id)
    {
        $controller = new PanelSeriesNumberController;
        $controller->destroy($id);
        $this->emit('triggerDelete', $id);
    }

    public function deleteReceiver($id)
    {
        $receiver = new ReceiversController;
        $receiver->delete($id);
        // $this->emit('triggerDelete', $id);
    }

    // public function selectChallanSeries($series_id,$series_number, $valid_till, $valid_from, $assigned_to_name)
    public function selectChallanSeries($seriesData)
    {
        $seriesData = json_decode($seriesData);
        $this->reset(['updateChallanSeriesData']);
        $this->updateChallanSeriesData = (array)$seriesData;
        $this->updateChallanSeriesData['assigned_to_s_id'] = '';
        $this->updateChallanSeriesData['assigned_to_id'] = '';
        // $this->updateChallanSeriesData['series_number'] = $seriesData->series_number;
        // $this->updateChallanSeriesData['valid_till'] = $seriesData->valid_till;
        // $this->updateChallanSeriesData['valid_from'] = $seriesData->valid_from;
        // $this->updateChallanSeriesData['assigned_to_name'] = $seriesData->assigned_to_name;
    }
    public function resetChallanSeries()
    {
        $this->reset(['updateChallanSeriesData']);
    }

    public function updatePanelSeries()
    {
        $request =  request();
        $request->merge($this->updateChallanSeriesData);
        // Create instances of necessary classes
        $PanelSeriesNumberController = new PanelSeriesNumberController;
        // dd($request);

        $response = $PanelSeriesNumberController->update($request, $this->updateChallanSeriesData['id']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['updateChallanSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = (array) $data->getData()->data;
            $newReceiversController = new ReceiversController;

            $response = $newReceiversController->index($request);
            $receiverData = $response->getData();
            $this->receiverDatas = $receiverData->data;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }
    public function receivedChallan($page)
    {
        $request = request();
        $id = '';
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => '9',
            'panel_id' => '2',
        ];
        $request->merge($columnFilterDataset);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        // Filter only columns like "column_1," "column_2," etc.
        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return preg_match('/^column_\d+$/', $column['panel_column_display_name']);
        });

        // Sort the filtered columns by their numeric part in ascending order
        usort($filteredColumns, function ($a, $b) {
            preg_match('/\d+/', $a['panel_column_display_name'], $matchesA);
            preg_match('/\d+/', $b['panel_column_display_name'], $matchesB);

            $numA = $matchesA ? (int)$matchesA[0] : 0;
            $numB = $matchesB ? (int)$matchesB[0] : 0;

            return $numA - $numB;
        });

        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        // dd($ColumnDisplayNames);
        $this->ColumnDisplayNames = $ColumnDisplayNames;
        $request = request()->replace([]);
        $request = new Request(['page' => $page]);
        $request->merge(['receiver_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);

        $challanController = new ChallanController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        // dd($this->tableTdData);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
    }

    public function receivedChallanAccept(Request $request, $challanId)
    {
        $receiverScreen = new ChallanController;
        $request->merge(['status_comment' => $this->status_comment]);
        $columnsResponse = $receiverScreen->accept($request, $challanId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'Challan accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
                return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    }

    public function receivedChallanReject(Request $request, $challanId)
    {
        $receiverScreen = new ChallanController;
        $request->merge(['status_comment' => $this->status_comment]);
        // $columnsResponse = $receiverScreen->accept($request, $challanId);
        $columnsResponse = $receiverScreen->reject($request, $challanId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'Challan rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
                return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

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
    public $selectedArticles = [];

    public function addSelectedArticle($article)
    {
        // dd($article);
        $this->selectedArticles[] = $article;
    }
    public function render()
    {
    $request = request();
    $UserResource = new UserAuthController;
    $userId = $request->user()->id; // assuming the user is authenticated and has an id

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

        return view('livewire.receiver.content.content', [
            'challanId' => $this->challanId,

        ]);
    }
}
