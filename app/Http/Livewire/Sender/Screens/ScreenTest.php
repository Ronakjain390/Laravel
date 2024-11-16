<?php

namespace App\Http\Livewire\Sender\Screens;

use App\Models\Challan;
use Livewire\Component;
use App\Models\User;
use App\Models\Receiver;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\ChallanStatus;
use App\Models\UserDetails;
use App\Models\ChallanSfp;
use App\Models\CompanyLogo;
use App\Models\ReceiverDetails;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanOrderDetail;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Livewire\Sender\Screens\addReceiver;
use App\Http\Livewire\Sender\Screens\sentChallan;
use App\Http\Livewire\Sender\Screens\createChallan;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

class ScreenTest extends Component
{
    use WithFileUploads;
    use WithPagination;

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
    );
    public $mainUser;
    public $uploadFile;
    public $updateFile;
    public $persistedTemplate;
    public $persistedActiveFeature;
    public $features = [];
    public $template;
    public $activeFeature;
    protected $queryString = ['activeTab'];
    public $rate;
    public $paginate = 1; // Default pagination value
    public $totalItems = 0;
    public $file;
    public $data;
    public $quantity;
    public $totalAmount;
    public $rows = [];
    public $showRate;
    public $createChallan;
    public $disabledButtons = true;
    public $challanId;
    protected $paginationTheme = 'tailwind';
    public $action = 'save';
    // public $isSaveButtonDisabled = false;
    public $validationErrorsJson = [];
    public $createChallanInstance;
    protected $panelColumnDisplayNames;
    protected $panelUserColumnDisplayNames;
    protected $ColumnDisplayNames;
    public $sentChallan, $invoiceData;
    public $autofillData = [];
    public  $challanFiltersData, $hideSuccessMessage, $receivedPanelColumnDisplayNames, $assigned_to_name;
    protected $addReceiver, $storeChallanSeries;
    private $addReceiverCode;
    public $total_qty;
    public $total = 0;
    public $response, $receiverData, $seriesNoData, $newChallanDesign;
    public $newChallanSeriesNoController, $addChallanSeries, $receiverDatas, $existingRecord;
    public $receiver_name, $company_name, $receiverName, $receiverAddress, $receiverPhone, $challanModifyData, $email, $address, $pincode, $phone, $state, $sfp, $city, $tan, $errorMessage = null, $successMessage, $showManualReceiverTab, $receiver_special_id, $errors, $statusCode, $message, $fromDate, $toDate, $termsIndexData;

    //sent challan
    public $tableTdData, $currentPage = 1, $paginateLinks;
    public $calculateTax = true;
    public $showBlock = true;
    public $showInputBoxes = true;
    public $isLoading = true;
    // team
    public $teamMembers;
    // team

    // sfp
    public $team_user_id, $challan_id, $challan_sfp;
    // sfp
    public function toggleBlock()
    {
        $this->showBlock = !$this->showBlock;
    }






    // create challan screen
    public $products, $articles = [], $locations = [], $item_codes, $Article, $location, $item_code;
    public $isOpen = false;
    public $open = false;
    public $selectedProducts = [];
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    public $sendButtonDisabled = true;
    public $updateForm = true;
    public $selectedUser;
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


    public $termsAndConditionsData = array(
        'content' => '',
        'panel_id' => '1',
        'section_id' => '1',
    );
    protected $billTo;
    public $selectedUserDetails = [];
    public  $discount_total_amount;
    public $challan_series, $challan_date, $series_num, $status, $sender_id, $receiverId, $sender, $receiver_id, $receiver, $comment, $details,   $artical = [];
    public $challanDesignData = array(
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
    public $addReceiverData = array(
        'receiver_name' => '',
        'company_name' => '',
        'email' => '',
        'address' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'organisation_type' => '',
        'receiver_special_id' => '',
        'gst_number'=> '',
    );
    public $addChallanSeriesData = array(
        'series_number' => '',
        'valid_from' => '',
        'valid_till' => '',
        'receiver_user_id' => '',
        'panel_id' => '1',
        'section_id' => '1',
        'assigned_to_r_id' => '',
        'assigned_to_name' => '',
        'default' => '0',
        'status' => 'active',
    );






    public $fromStockRequest = [];
    public $selectedProductP_ids = [];


    // create challan screen
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
        'innerFeatureRoute' => 'handleFeatureRoute',
        'manualReceiverAdded' => 'handleManualReceiverAdded',
        'forceDelete' => 'deleteChallanSeries',
        'modifyChallan' => 'handleModifyChallan',
        'viewChallan' => 'handleViewChallan',
        'selfReturnChallanView' => 'handleSelfReturnChallanView',
        'detailedSentChallan' => 'handleDetailedSentChallan',
        'detailedReceivedChallan' => 'handleDetailedReceivedChallan',
        'deletedSentChallan' => 'handleDeletedSentChallan',
        'inputToggled' => 'handleInputToggled',
        'invoiceTermsAndConditions' => 'handleInvoiceTermsAndConditions',
        // 'showSfp' => 'loadSfp'
        'updateTotalQty' => 'updateTotalQty',
        'updateValues' => 'handleUpdateValues',
        'updateSignatureData' => 'setSignatureData',
        'seriesNumberUpdated' => 'updateSeriesNumber',
        'disabledInputs' => 'updateInputs',
    ];

    public function updateInputs($value)
    {
        $this->disabledButtons = false;
    }

    public function updateTotalQty($totalQty)
    {
        $this->total_qty = $totalQty;
    }

    public function updateChallanValues($totalQty, $total)
    {
        $this->createChallanRequest['total_qty'] = $totalQty;
        $this->createChallanRequest['total'] = $total;
    }

    public $from;
    public $to;

    // public function loadSfp($sfp)
    // {
    //     'showSfp' => 'loadSfp'
    //     dd($sfp);
    //     $this->sfp = json_decode($sfp, true);
    // }

    public $sfpModal = false;
    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;

        if($variable == 'challan_sfp'){
            $this->sfpModal = true;
            $this->challan_id = $value;
        }
        //  ($this->{$variable});
        // $this->routeHydrate();
        // dd($this->{$variable},$variable,$value);
        // dd($this->challan_id);
        // $request = request();
        // // $request = new Request(['page' => $page]);
        // if ($this->challan_series != null) {
        //     // dump($this->challan_series);
        //     $request->merge(['challan_series' => $this->challan_series]);
        // }

        // // // Filter by sender_id
        // if ($this->sender_id != null) {
        //     $request->merge(['sender_id' => $this->sender_id]);
        // }

        // // Filter by receiver_id
        // if ($this->receiver_id != null) {
        //     // dump($this->receiver_id);
        //     $request->merge(['receiver_id' => $this->receiver_id]);
        // }
        // // Filter by status
        // if ($this->status != null) {
        //     $request->merge(['status' => $this->status]);
        // }

        // // Filter by state in ReceiverDetails
        // if ($this->state != null) {
        //     $request->merge(['state' => $this->state]);
        // }
        //     // Filter by date range
        //     if ($this->from != null && $this->to != null) {
        //         $request->merge([
        //             'from' => $this->from,
        //             'to' => $this->to,
        //         ]);
        //         $this->hideDropdown();
        //     }

        //     if ($this->recvdfrom != null && $this->recvdto != null) {
        //         $request->merge([
        //             'recvdfrom' => $this->recvdfrom,
        //             'recvdto' => $this->recvdto,
        //         ]);
        //     }

        //   // Filter by article
        //   if ($this->article_sent != null) {
        //       $request->merge(['article_sent' => $this->article_sent]);
        //   }

        // //   Filter by Status
        // if ($this->status != null) {
        //     $request->merge(['status' => $this->status]);
        // }


        // // $request = new Request(['page' => $this->page]);
        // switch ($this->persistedTemplate) {
        // //     case 'sent_challan':

        // // $challanController = new ChallanController();
        // // $tableTdData = $challanController->index($request);
        // // $this->tableTdData = $tableTdData->getData()->data->data;
        // // // dd($this->tableTdData);
        // // $this->currentPage = $tableTdData->getData()->data->current_page;
        // // $this->paginateLinks = $tableTdData->getData()->data->links;
        // // // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        // // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // // // dd($this->challanFiltersData);
        // // // redirect()->route('sender')->with('message',$this->successMessage??$this->errorMessage);
        // // break;
        // case 'detailed_sent_challan':
        //     $challanController = new ChallanController();
        //     $tableTdData = $challanController->index($request);
        //     $this->tableTdData = $tableTdData->getData()->data->data;
        //     // dd($this->tableTdData);
        //     $this->currentPage = $tableTdData->getData()->data->current_page;
        //     $this->paginateLinks = $tableTdData->getData()->data->links;
        //     // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        //     $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        //     // dd($this->challanFiltersData);
        //     // redirect()->route('sender')->with('message',$this->successMessage??$this->errorMessage);
        //     break;
        // case 'received_challan':
        //      // // Filter by sender_id
        // if ($this->sender_id != null) {
        //     $request->merge(['sender_id' => $this->sender_id]);
        // }

        // // Filter by receiver_id
        // if ($this->receiver_id != null) {
        //     // dump($this->receiver_id);
        //     $request->merge(['receiver_id' => $this->receiver_id]);
        // }
        //     // $this->receivedChallan($this->currentPage);
        //     $challanController = new ReturnChallanController();
        // $tableTdData = $challanController->indexReceivedReturnChallan($request);
        // $this->tableTdData = $tableTdData->getData()->data->data;
        // $this->currentPage = $tableTdData->getData()->data->current_page;
        // $this->paginateLinks = $tableTdData->getData()->data->links;
        // // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        // // dd($this->challanFiltersData);
        // break;
        //     case 'check_balance':
        // //         $challanController = new ChallanController();
        // // $tableTdData = $challanController->indexCheckBalance($request);
        // // $this->tableTdData = $tableTdData->getData()->data;
        // // $this->challans = $tableTdData->getData()->challans->data;
        // // $this->total = $tableTdData->getData()->totalBalance;
        // // // dd($this->total);

        // // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // // dd($this->challanFiltersData);
        //         break;
        // }
    }
    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }

    public function hideDropdown()
    {
        $this->dispatchBrowserEvent('hide-dropdown');
    }
    // public function placeholder()
    // {
    //     return view('livewire.sender.screens.placeholders');
    // }

    public function loadData()
    {
        $this->isLoading = false;
        $this->emit('loadData');
    }

    // public function fetchProduct()
    // {
    //     $request = request();
    //     $request->merge([
    //         'article' => $this->Article ?? null,
    //         'location' => $this->location ?? null,
    //         'item_code' => $this->item_code ?? null,
    //     ]);

    //     $products = new ProductController;
    //     $response = $products->index($request);
    //     $result = $response->getData();
    //     $this->products = $result->data;
    //     $this->articles = [];
    //     foreach ($this->products as $product) {
    //         array_push($this->articles, $product->details[0]->column_value);
    //     }
    //     $this->item_codes = array_unique(array_column($this->products, 'item_code'));
    //     $this->locations = array_unique(array_column($this->products, 'location'));

    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //     } else {
    //         $this->errorMessage = json_encode((array) $result->errors);
    //     }
    // }

   public $discount;
    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        // $this->dispatchBrowserEvent('inputsDisabledChanged', ['value' => false]);
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

        $this->createChallanRequest['total'] = number_format(floatval(str_replace(',', '', $total)), 2, '.', '');
        $this->createChallanRequest['total_words'] = $this->numberToIndianRupees((float) $total);
    }

    public function clearArticleError($index)
    {
        $this->resetErrorBag('article.' . $index);
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

    // public function updatedCreateChallanRequest($value, $name)
    // {
    //     // Split the name into parts
    //     $parts = explode('.', $name);

    //     // Check if the updated field is a unit
    //     if (isset($parts[2]) && $parts[2] === 'unit') {
    //         // Normalize the case of the unit
    //         $this->createChallanRequest[$parts[1]][$parts[2]] = ucfirst(strtolower($value));
    //     }
    // }

    public function disableSaveButton()
    {
        $this->isSaveButtonDisabled = true;
    }
    public $challanSave;
    public function challanModify(Request $request)
    {
        $request->merge($this->createChallanRequest);
        // dd($request);
        // Create instances of necessary classes
        $ChallanController = new ChallanController;

        $response = $ChallanController->update($request, $this->challanId);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->challanId = $result->challan_id;
            $this->inputsDisabled = true;
            $this->reset(['errorMessage',  'validationErrorsJson' ]);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }

    public function saveChallanModify(Request $request)
    {
        $request->merge($this->createChallanRequest);
        // dd($request);

        // Create instances of necessary classes
        $ChallanController = new ChallanController;

        $response = $ChallanController->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->challanSave = $result->message;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            // $this->isSaveButtonDisabled = true;
            $this->challanId = $result->challan_id;

            $this->reset(['errorMessage',   'message',  'validationErrorsJson' ]);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->isSaveButtonDisabled = false;
        }
    }
    public function openModal()
    {
        $this->isOpen = true;

        $request = new  request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
        ]);

        $this->dispatchBrowserEvent('loadStockTable', ['request' => $request]);
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function addSelectedProducts()
    {
        // Handle adding selected products here
        // You can access selected product IDs using $this->selectedProducts

        // Reset the selection after adding products
        $this->selectedProducts = [];

        // Close the modal
        $this->isOpen = false;

        // Emit an event or perform any necessary actions
    }

    public $createChallanData = [
        'rate' => null,
        'quantity' => null,
        'totalAmount' => 0,
        'rows' => [],
        // Add other relevant properties here
    ];

    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 1;
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
    // jPMHXUbECXeAhDHAE4BRyU5zWCdvJdvZhDVag16H
    // jPMHXUbECXeAhDHAE4BRyU5zWCdvJdvZhDVag16H

    public function mount()
    {
        $sessionId = session()->getId();

        if (session()->has('panel')) {
            $this->panel = session('panel');
        }
        if (!request()->query('activeTab')) {
            $this->activeTab = 'tab1';
        }

        // $UserResource = new UserAuthController;
        // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // // Try to get the user details from the cache

        // $response = $UserResource->user_details($request);
        // $response = $response->getData();

        // if ($response->success == "true") {
        //     $this->UserDetails = $response->user->plans;
        //     $this->user = json_encode($response->user);
        //     $this->successMessage = $response->message;
        //     $this->reset(['errorMessage', 'successMessage']);
        // } else {
        //     $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        // }

        $template = request('template', 'index');

        if (view()->exists('components.panel.sender.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template;

            $request = request();
            $userAgent = $request->header('User-Agent');
            $this->fetchTeamMembers();
            $this->isMobile = isMobileUserAgent($userAgent);
            // dd($this->isMobile);

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

            switch ($this->persistedTemplate) {
                case 'create_challan':
                    $this->createChallan($request);
                    break;
                case 'update_challan':
                    $this->challanUpdate($this->persistedActiveFeature);
                    break;
                case 'sent_challan':
                    break;
                case 'bulk_create_challan':
                    break;
                case 'sent_sfp_challan':
                    $this->sentSfpChallan($request);
                    break;
                case 'check_balance':
                    break;
                case 'received_challan':
                    break;
                case 'received_sfp_challan':
                    $this->receivedSfpChallan($request);
                    break;
                case 'view_draft_challan':
                    $this->viewChallan($request);
                    break;
                case 'add_receiver':
                    break;
                case 'deleted_sent_challans':
                    break;
                case 'view_receiver':
                    $this->viewReceiver($request);
                    break;
                case 'challan_design':
                    $this->challanDesign($request);
                    break;
                case 'modify_challan':
                    $this->modifyChallan($request);
                    break;
                case 'self_return_challan':
                    $this->selfReturnChallanView($request);
                    break;
                case 'view_sfp_sender_challan':
                    $this->viewSfpSenderChallan($request);
                    break;
                case 'detailed_sent_challan':
                    $this->detailedSentChallan($this->currentPage);
                    break;
                case 'detailed_received_challan':
                    $this->detailedReceivedChallan($this->currentPage);
                    break;
                case 'deleted_sent_challan':
                    $this->deletedSentChallan($request);
                    break;
                case 'challan_series_no':
                    $newChallanSeriesIndex = new PanelSeriesNumberController;
                    $request->merge(['panel_id' => '1', 'section_id' => '1']);
                    $data = $newChallanSeriesIndex->index($request);

                    $this->seriesNoData = $data->getData()->data;
                    $newReceiversController = new ReceiversController;
                    $response = $newReceiversController->index($request);
                    $receiverData = gzdecode($response->content());
                    $receiverDatas = json_decode($receiverData);
                    $this->receiverDatas = $receiverDatas->data;
                    break;
                case 'invoice_terms_and_conditions':
                    $this->invoiceTermsAndConditions($request);
                    break;
                    case 'sfp_challans':
                        // $this->sfpChallans($request);
                        break;
                default:
                    $this->persistedTemplate = 'index';
                    $this->persistedActiveFeature = null;
                    break;
            }
        } else {
            $this->persistedTemplate = 'index';
            $this->persistedActiveFeature = null;
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
    // public function getPublicPropertiesDefinedBySubClass()
    // {
    //     $properties = parent::getPublicPropertiesDefinedBySubClass();

    //     // Filter out properties with null values
    //     return array_filter($properties, function ($value) {
    //         return !is_null($value);
    //     });
    // }

    public function routeHydrate()
    {

        $request = request();
        $query = (new TeamUserController)->index();
        $status = $query->getStatusCode();
        $queryData = $query->getData();

        switch ($this->persistedTemplate) {
            case 'create_challan':
                $this->createChallan($request);
                break;
            case 'update_challan':
                $this->challanUpdate($this->persistedActiveFeature);
                break;
            case 'sent_challan':
                break;
                case 'deleted_sent_challans':
                    break;
            case 'sent_sfp_challan':
                $this->sentSfpChallan($request);
                break;
            case 'check_balance':
                // $this->checkBalance($request);
                break;
            case 'received_challan':
                // dd('here');
                $this->receivedChallan($this->currentPage);
                break;
            case 'received_sfp_challan':
                $this->receivedSfpChallan($request);
                break;
            case 'view_draft_challan':
                $this->viewChallan($request);
                break;
            case 'modify_challan':
                $this->modifyChallan($request);
                break;
            case 'self_return_challan':
                $this->selfReturnChallanView($request);
                break;
            case 'add_receiver':
                // $this->addReceiver = new addReceiver;
                break;
            case 'detailed_sent_challan':
                $this->detailedSentChallan($request);
                break;
            case 'detailed_received_challan':
                $this->detailedReceivedChallan($request);
                break;
            case 'deleted_sent_challan':
                $this->deletedSentChallan($request);
                break;
            case 'view_receiver':
                // $this->addReceiver = new addReceiver;
                break;
            case 'challan_series_no':
                $newChallanSeriesIndex = new PanelSeriesNumberController;
                $request->merge(['panel_id' => '1']);
                $data = $newChallanSeriesIndex->index($request);

                $this->seriesNoData = $data->getData()->data;
                $newReceiversController = new ReceiversController;

                $request->replace([]);
                $response = $newReceiversController->index($request);
                $receiverData = $response->getData();
                $this->receiverDatas = $receiverData->data;
                // dump("2");
                // dump(json_encode($this->receiverDatas));
                break;

            default:
            case 'others':
                $request->merge(['panel_id' => '1', 'feature_id' => '1']);

                $newChallanDesign = new PanelColumnsController;
                $data = $newChallanDesign->index($request);
                $data = $data->getData()->data;

                if (count($data) == 2) {
                    $challanDesignData = [
                        [
                            'panel_id' => 1,
                            'section_id' => 1,
                            'feature_id' => 1,
                            'default' => 0,
                        ],
                        [
                            'panel_id' => 1,
                            'section_id' => 1,
                            'feature_id' => 1,
                            'default' => 0,
                        ],
                        [
                            'panel_id' => 1,
                            'section_id' => 1,
                            'feature_id' => 1,
                            'default' => 0,
                        ],
                        [
                            'panel_id' => 1,
                            'section_id' => 1,
                            'feature_id' => 1,
                            'default' => 0,
                        ]
                    ];
                    foreach ($challanDesignData as $key => $value) {
                        $request->replace($value);
                        $newChallanDesign->store($request);
                        # code...
                    }
                    $request->merge(['panel_id' => '1', 'feature_id' => '1']);
                    $data = $newChallanDesign->index($request);
                }
                $this->challanDesignData = $data->getData()->data;
                break;
        }
    }

    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    // public function handleFeatureRoute($template, $activeFeature)
    // {

    //     $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
    //     $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
    //     $this->savePersistedTemplate($template, $activeFeature);

    //     redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);
    // }

    public function handleFeatureRoute($template, $activeFeature)
    {
        // dd($template, $activeFeature);
        $viewPath = 'components.panel.sender.' . $template;
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        return redirect()->route('sender', ['template' => $this->persistedTemplate]);
    }


    // public function sentChallan($page)
    // {

    // }
    // public $page = 10;

    public $page = 1;
    public $perPage = 100;
    public $maxPerPage = 100;

    public function loadMore()
    {
        if (count($this->tableTdData) < $this->maxPerPage) {
            $this->perPage += 25;
            $this->sentChallan($this->page);
        }
    }

    public function nextPage()
    {
        $this->page++;
        $this->perPage = 100;
        $this->sentChallan($this->page);
    }
    public function sentSfpChallan(Request $request)
    {
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 2
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

        if ($this->challan_series != null) {
            // dump($this->challan_series);
            $request->merge(['challan_series' => $this->challan_series]);
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

        $challanController = new ChallanController();
        $tableTdData = $challanController->sfpIndex($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        // dd($this->tableTdData);
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        // $this->successMessage = $result->message;

        $this->emit('challanDataReceived', $tableTdData);
    }

    // DETAILED VIEW OF SENT CHALLAN
    public function detailedSentChallan($page)
    {
        $request = new Request;
        $columnFilterDataset = [
            'feature_id' => 2,
        ];
        $request->merge($columnFilterDataset);
        $this->ColumnDisplayNames = ['Challan No', 'Time', 'Date', 'Creator', 'Receiver', 'Article', 'Hsn', 'Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];

        request()->replace([]);

        // Filter by challan_series
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

        if ($this->receiver_id != null) {
            $request->merge(['receiver_id' => $this->receiver_id]);
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
        $challanController = new ChallanController();
        $tableTdData = $challanController->indexDetailed($request);
        $this->tableTdData = array_merge($this->tableTdData, $tableTdData->getData()->data->data);
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        // $challanController = new ChallanController();
        // $challanData = $challanController->index($request);
        // $this->challanData = $challanData->getData()->data->data;
        // // dd($this->challanData);
        // $this->challanFiltersData = json_encode($challanData->getData());

        // $challanController = new ChallanController();
        // $tableTdData = $challanController->index($request);
        // $this->tableTdData = $tableTdData->getData()->data->data;
        // $this->currentPage = $tableTdData->getData()->data->current_page;
        // $this->paginateLinks = $tableTdData->getData()->data->links;
        // // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
    }

    // DETAILED VIEW OF SENT CHALLAN
    public function detailedReceivedChallan($page)
    {
        $request = new Request;
        $columnFilterDataset = [
            'feature_id' => 2,
        ];
        $request->merge($columnFilterDataset);
        $this->ColumnDisplayNames = ['Challan No', 'Time', 'Date', 'Creator', 'Receiver', 'Article', 'Hsn', 'Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];

        request()->replace([]);

        // Filter by challan_series
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

        if ($this->receiver_id != null) {
            $request->merge(['receiver_id' => $this->receiver_id]);
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
        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->indexReceivedReturnChallan($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
    }

    // DELETED INVOICE
    public function deletedSentChallan(Request $request)
    {
        $this->ColumnDisplayNames = ['Challan No', 'Time', 'Date', 'Receiver','Creator', 'Article', 'Hsn', 'Unit', 'Quantity', 'Unit Price', 'Total Amount'];
        request()->replace([]);
        if ($this->challan_series != null) {
            // dump($this->challan_series);
            $request->merge(['challan_series' => $this->challan_series]);
        }
        if ($this->receiver_id != null) {
            $request->merge(['receiver_id' => $this->receiver_id]);
        }

        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }

        $challanController = new ChallanController();
        $challanData = $challanController->deletedChallan($request);
        $this->challanData = $challanData->getData()->data;
        $this->challanFiltersData = json_encode($challanData->getData());
        $this->emit('challanDataReceived', $challanData);
    }


    public function loadPageData($page)
    {
        $challanController = new ChallanController();
        $this->tableTdData = $challanController->index(request()->merge(['page' => $page]));
        // dd($this->tableTdData);
    }

    // public function loadPageData($page)
    // {
    //     $request = request();
    //     $this->page = $page;

    //     $request = request()->merge(['page' => $page]);

    //     $challanController = new ChallanController();

    //     $this->tableTdData = $challanController->index($request)->getData()->data->data;

    //     // dd($this->tableTdData, $this->page);
    //     $this->sentChallan($request);
    //     $this->sentChallan($this->currentPage);
    // }

    public function receivedChallan($page)
    {
        $request = new Request;
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 3
        ];
        $request->merge($columnFilterDataset);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->ColumnDisplayNames = $ColumnDisplayNames;
        // dd($this->ColumnDisplayNames);

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


        // Filter by receiver_id
        // if ($this->receiver_id != null) {
        // dump($this->receiver_id);
        $request->merge(['receiver_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        // }

        // // Filter by deleted
        // if ($deleted != null) {
        //     $request->merge(['deleted'=> $deleted]);
        // }

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
        // $challanController = new ReturnChallanController();
        // $tableTdData = $challanController->index($request);
        // $this->tableTdData = $tableTdData->getData()->data->data;
        // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // dd($this->tableTdData);



        $request = new Request(['page' => $page]);
        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->indexReceivedReturnChallan($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        $this->emit('challanDataReceived', $tableTdData);
    }

    public function receivedSfpChallan(Request $request)
    {
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 3
        ];
        $request->merge($columnFilterDataset);

        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->ColumnDisplayNames = $ColumnDisplayNames;
        // dd($this->ColumnDisplayNames);

        request()->replace([]);

        if ($this->challan_series != null) {
            // dump($this->challan_series);
            $request->merge(['challan_series' => $this->challan_series]);
        }

        // // Filter by sender_id
        // if ($sender_id != null) {
        //     $request->merge(['sender_id'=> $sender_id]);
        // }

        // Filter by receiver_id
        // if ($this->receiver_id != null) {
        // dump($this->receiver_id);
        $request->merge(['receiver_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        // }

        // // Filter by deleted
        // if ($deleted != null) {
        //     $request->merge(['deleted'=> $deleted]);
        // }

        // Filter by status
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        // Filter by state in ReceiverDetails
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }

        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->sfpIndex($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
        // dd($this->tableTdData);

        $this->emit('challanDataReceived', $tableTdData);
    }

    public function receivedChallanAccept(Request $request, $challanId)
    {

        // dd($request, $challanId);
        $request->merge(['status_comment' => $this->status_comment]);
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->accept($request, $challanId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'Return Challan accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
        redirect()->route('sender');
    }
    public function receivedChallanReject(Request $request, $challanId)
    {

        $request->merge(['status_comment' => $this->status_comment]);
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->reject($request, $challanId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'Return Challan rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
        redirect()->route('sender');
    }

    public function SfpAccept(Request $request, $sfpId)
    {
        $receiverScreen = new ChallanController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReject(Request $request, $sfpId)
    {
        $receiverScreen = new ChallanController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    public function SfpReAccept(Request $request, $sfpId)
    {
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->sfpAccept($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }
    public function SfpReReject(Request $request, $sfpId)
    {
        $receiverScreen = new ReturnChallanController;
        $columnsResponse = $receiverScreen->sfpReject($request, $sfpId);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('success', 'SFP rejected successfully.');
        } else {
            session()->flash('error', 'Failed to accept challan.');
        }
    }

    public $article_sent , $recvdfrom, $recvdto, $recvdfromDate, $recvdtoDate;
    // public function checkBalance(Request $request)
    // {
    //     // dd($this->article_sent);

    //     $this->ColumnDisplayNames = ['Receiver', 'Sent Date', 'Challan No', 'Article', 'Qty Sent','Sent Status', 'Received Challan No', 'Received Date', 'Article', 'Received Qty','Received Status', 'Balance', 'Margin Qty'];
    //     // 'Margin Qty', 'Balance'
    //     request()->replace([]);
    //     // dd($request);
    //     if ($request->has('challan_series')) {
    //         // Split the search term into series and number
    //         $searchTerm = $request->challan_series;
    //         $searchParts = explode('-', $searchTerm);

    //         if (count($searchParts) == 2) {
    //             $series = $searchParts[0];
    //             $num = $searchParts[1];

    //             // Perform the search
    //             $query->where('challan_series', $series)
    //                 ->where('series_num', $num);
    //         } else {
    //             // Invalid search term format, handle accordingly
    //             // For example, you could return an error message or ignore the filter
    //         }
    //     }

    //     // // Filter by sender_id
    //     if ($this->sender_id != null) {
    //         $request->merge(['sender_id' => $this->sender_id]);
    //     }

    //     // Filter by receiver_id
    //     if ($this->receiver_id != null) {
    //         $request->merge(['receiver_id' => $this->receiver_id]);
    //     }

    //     // Filter by status
    //     if ($this->status != null) {
    //         $request->merge(['status' => $this->status]);
    //     }

    //     // Filter by state in ReceiverDetails
    //     if ($this->state != null) {
    //         $request->merge(['state' => $this->state]);
    //     }

    //     // Filter by date range
    //     if ($this->fromDate != null && $this->toDate != null) {
    //        $request->merge([
    //            'from_date' => $this->fromDate,
    //            'to_date' => $this->toDate,
    //        ]);
    //     }
    //     // Filter by date range
    //     if ($this->recvdfromDate != null && $this->recvdtoDate != null) {
    //       $request->merge([
    //           'recvdfrom_date' => $this->recvdfromDate,
    //           'recvdto_date' => $this->recvdtoDate,
    //       ]);
    //     }

    //     // Filter by article
    //     if ($this->article_sent != null) {
    //         $request->merge(['article_sent' => $this->article_sent]);
    //     }

    //     // Filter by article
    //     if ($this->recvdfrom != null) {
    //         $request->merge(['recvdfrom' => $this->recvdfrom]);
    //     }


    //     // dd($request);
    //     $this->tableTdData = [];
    //     // $request = new Request(['page' => $page, 'perPage' => $this->perPage]);

    //     $challanController = new ChallanController();
    //     $tableTdData = $challanController->indexCheckBalance($request);
    //     $this->tableTdData = $tableTdData->getData()->data;
    //     $this->challans = $tableTdData->getData()->challans->data;
    //     $this->total = $tableTdData->getData()->totalBalance;
    //     // dd($this->total);

    //     $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
    //     // dd($this->challanFiltersData['data']);
    //     $this->emit('challanDataReceived', $tableTdData);



    //     // $this->successMessage = $result->message;
    //     $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
    //     $this->emit('challanDataReceived', $tableTdData);
    // }

    public function acceptMargin(Request $request, $challanId)
    {
        // dd($challanId);
        $receiverScreen = new ChallanController;
        $columnsResponse = $receiverScreen->acceptMargin($request, $challanId);
        // dd($columnsResponse);
        if ($columnsResponse->getStatusCode() === 200) {
            session()->flash('message', 'Margin accepted successfully.');
        } else {
            session()->flash('error', 'Failed to accept margin.');
        }
        redirect()->route('sender', ['template' => 'check_balance'])->with('message', 'Margin accepted successfully.');
            // Call checkBalance method here
            // $this->checkBalance($request);
    }

    public function challanUpdate($id)
    {
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed

        $request = request();
        $ChallanController = new ChallanController;
        // dd($id);
        $response = $ChallanController->show($request, $id);
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
            $responseContent = $billTo->index($request)->content();
            $decompressedContent = gzdecode($responseContent);
            $decodedResponse = json_decode($decompressedContent);

            if ($decodedResponse === null) {
                // Handle error: invalid JSON or empty response
                $this->billTo = [];
            } else {
                $this->billTo = collect($decodedResponse->data)
                    ->filter(function ($item) {
                        return !empty($item->receiver_name) || !empty($item->details[0]->phone) || !empty($item->details[0]->email);
                    })
                    ->map(function ($item) {
                        // Filter out empty and null values from the user details
                        $item->user = array_filter((array) $item->user, function ($value) {
                            return !is_null($value) && $value !== '';
                        });

                        // Filter out empty and null values from the details array
                        $item->user->details = array_map(function ($detail) {
                            return array_filter((array) $detail, function ($value) {
                                return !is_null($value) && $value !== '';
                            });
                        }, $item->user->details);

                        // Set receiver_name if empty
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
        // redirect()->route('sender')->with('message',$this->successMessage??$this->errorMessage);


    }


    public function challanEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsResponseDisabled = true; // Adjust the condition as needed
        $this->reset([ 'message', 'challanSave']);
    }

    public $status_comment = '';
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

    public $selectedChallanId;

    public function updateTimelineModal($challanId)
    {
        $this->selectedChallanId = $challanId;
        $this->emit('openTimelineModal');
    }

    public function addComment($id)
    {
        $request = request();
        $request->merge([
            'status_comment' => $this->status_comment,

        ]);

        $ChallanController = new ChallanController;
        $response = $ChallanController->addComment($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_challan', '2');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }
    public function addCommentReceivedChallan($id)
    {
        $request = request();
        $request->merge([
            'status_comment' => $this->status_comment,

        ]);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->addComment($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('received_challan', '3');
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }
    public $admin_ids = [];
    public $team_user_ids = [];
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

    public function sfpReturnChallan()
    {

        $request = request();
        $request->merge([
            'id' => $this->team_user_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        // dd($request);
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->returnChallanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('received_challan', '3');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        return redirect()->route('sender', ['template' => 'received_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function reSendChallan($id)
    {

        $request = request();
        $ChallanController = new ChallanController;

        $response = $ChallanController->resend($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $template = 'sent_challan';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);    }

    public function selfAcceptChallan($id)
    {

        $request = request();
        $ChallanController = new ChallanController;

        $response = $ChallanController->selfAccept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $template = 'sent_challan';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);    }
    public $saveButtonDisabled = false;

    public function saveSelfReturnChallan(){
        // dd('save');
        $this->inputsResponseDisabled = false;
    }
    public function selfReturnChallan($id)
    {

        $request = request();

        // Loop through each item in the 'order_details' array
        // foreach ($this->createChallanRequest['order_details'] as $key => $orderDetail) {
        //     // Skip the row if "remaining_qty" is 0
        //     if (isset($orderDetail['remaining_qty']) && $orderDetail['remaining_qty'] == 0) {
        //         continue;
        //     }

        //     // Overwrite the value of "qty" with the value of "remaining_qty"
        //     if (isset($orderDetail['remaining_qty'])) {
        //         $this->createChallanRequest['order_details'][$key]['qty'] = $orderDetail['remaining_qty'];
        //     }

        //     // Remove the "remaining_qty" key
        //     unset($this->createChallanRequest['order_details'][$key]['remaining_qty']);
        // }

        $request->merge($this->createChallanRequest);
        // dd($request);
        $ChallanController = new ChallanController;


        $response = $ChallanController->selfReturn($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->saveButtonDisabled = true; // Adjust the condition as needed
            $this->innerFeatureRedirect('sent_challan', '3');
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

        // Use the `redirect` method with a Livewire route
        $template = 'sent_challan';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
     }


    public function deleteChallan($id)
    {

        $request = request();
        $ChallanController = new ChallanController;

        $response = $ChallanController->delete($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $template = 'sent_challan';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);    }

    public $pdfData;
    public function createChallan(Request $request)
    {
        $PanelColumnsController = new PanelColumnsController;
        $billTo = new ReceiversController;
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

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData, $userId, 'shdj');
        $this->pdfData = $pdfData;
        // dd($this->pdfData);

        $this->panelColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        // dd($this->panelColumnDisplayNames);
        $this->panelUserColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        $this->ColumnDisplayNames = $this->getColumnDisplayNames($PanelColumnsController, $request, $userId);
        array_push($this->ColumnDisplayNames, 'item code', 'category', 'location','warehouse', 'unit', 'qty', 'rate', 'tax');

        $responseContent = $billTo->index($request)->content();
        $decompressedContent = gzdecode($responseContent);
        $decodedResponse = json_decode($decompressedContent);

        if ($decodedResponse === null) {
            // Handle error: invalid JSON or empty response
            $this->billTo = [];
        } else {
            $this->billTo = collect($decodedResponse->data)
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

        $response = $products->searchStock($request);
        $result = $response->getData();
        $this->products = (array) $result->data;
        $filteredProducts = array_filter($this->products, function ($product) {
            return ((object) $product)->qty > 0;
        });

        $this->createChallanRequest['challan_date'] = now()->format('Y-m-d');
        $showColumns = User::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->first()->pluck('show_rate');

        $this->showRate = $showColumns;
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

    public function getUnitProperty()
    {
        $unitData = new UnitsController();
        $response = $unitData->index();
        return json_decode($response->getContent(), true);
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

    public $addCustomUnit = array(
        'unit' => '',
        'status' => 'active',
    );

    public function addUnit()
    {
        // dd('ds,nj');
        $request = request();
        request()->replace([]);
        // $request->merge($columnFilterDataset);
        $request->merge($this->addCustomUnit);
        // dd($request);
        $unitData = new UnitsController();
        $response = $unitData->store($request);
        // dd($response);
        if ($response->getStatusCode() == 201) {
            $this->successMessage = 'Unit Added Successfully';
            $this->reset(['addCustomUnit']);
            $this->dispatchBrowserEvent('close-modal');
        } else {
            $this->errorMessage = 'Unit Not Added, Please Try Again';
        }
        // $this->showInput = $showInput;
    }

        public function updateSelectedUnit($unit)
    {
        // dd($index);
        $index = 0;
        $this->createChallanRequest['order_details'][$index]['unit'] = $unit;
        // dd($this->selectedUnit);

    }

    public $challanIds;
    public $showButtons = false;
    public $fileF;
    public function bulkChallantUpload()
    {
        // $request = request();
        // Create a new Request instance
        $request = new Request();
        // Merge the file data with the existing request data
        $requestData = [
            'field_name' => 'value',
            'file' => $this->uploadFile,
        ];
        $request->merge($requestData);
        // Create instances of necessary classes
        // dd($request);
        $ChallanController = new ChallanController;


        $response = $ChallanController->bulkChallanImport($request);
        $result = $response->getData();

            // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        $challanIds = $result->data->challan_ids;

        // Assign the challan_ids to the $challanIds property
        $this->challanIds = $challanIds;

        if ($result->status_code === 200) {
            // dump($result->message);
            $this->successMessage = $result->message;
            $this->showButtons = true;
            session()->flash('success', $this->successMessage);
            // dd($this->successMessage);
            // $this->innerFeatureRedirect('sent_challan', '3');
            // return redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode((array) $result->errors);
        }

    }
    public $sendChoice;
    public function sendChallans($choice)
    {
        // dd($choice);
        $request = request();
        $this->sendChoice = $choice;
        // Perform the necessary actions based on the user's choice
        if ($choice === 'send') {
            foreach($this->challanIds as $id){
                // dd($challanId);
                $ChallanController = new ChallanController;

                $response = $ChallanController->send($request, $id);
                $result = $response->getData();
            }
            $template = 'sent_challan';

            // Redirect to the 'sender' route with the template as a query parameter
            return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);            // Code to send the challans immediately
            // ...
        } else {
            // Code to send the challans later
            // ...
            $this->innerFeatureRedirect('sent_challan', '3');
            $template = 'sent_challan';

            // Redirect to the 'sender' route with the template as a query parameter
            return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);        }
    }


    public function updateSeriesNumber($newSeriesNumber)
    {
        $this->selectedUser['seriesNumber'] = $newSeriesNumber;
        $this->disabledButtons = true;
        // Additional logic to handle the updated series number
    }


    // public function handleUserSelection()
    // {
    //     // dd('jfskd');
    //     $user = json_decode($this->selectedUser);

    //     if ($user) {
    //         $this->selectUser(
    //             $user->series_number->series_number ?? 'Not Assigned',
    //             $user->details[0]->address ?? null,
    //             $user->user->email ?? null,
    //             $user->details[0]->phone ?? null,
    //             $user->details[0]->gst_number ?? null,
    //             $user->receiver_name ?? 'Select Receiver',
    //             $this->selectedUser
    //         );
    //     }
    // }

    public function selectUser($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails)
    {
        // dd($selectedUserDetails);
        try {
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '1')->select('series_number')->first();

            if ($challanSeries == 'Not Assigned') {
                if ($series == null) {
                    throw new \Exception('Please add one default Series number');
                }
                $challanSeries = $series->series_number;
                $latestSeriesNum = Challan::where('challan_series', $challanSeries)
                    ->where('sender_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            } else {
                $latestSeriesNum = Challan::where('challan_series', $challanSeries)
                    ->where('sender_id', $userId)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
            }

            $this->inputsDisabled = false; // Adjust the condition as needed
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

            // Decode $selectedUserDetails once
            $decodedUserDetails = json_decode($selectedUserDetails);
            $this->receiverName = $this->selectedUser['receiver_name'];
            $this->createChallanRequest['challan_series'] = $challanSeries;
            $this->createChallanRequest['series_num'] = $seriesNum;
            $this->createChallanRequest['receiver'] = $receiver;
            $this->createChallanRequest['receiver_id'] = $decodedUserDetails->receiver_user_id;
            $this->createChallanRequest['feature_id'] = $this->persistedActiveFeature;
            $this->selectedUserDetails = $decodedUserDetails->user->details;
            $this->inputsDisabled = false; // Adjust the condition as needed

            // Fetch billTo data
            $request = request();
            $billTo = new ReceiversController;
            $responseContent = $billTo->index($request)->content();
            $decompressedContent = gzdecode($responseContent);
            $decodedResponse = json_decode($decompressedContent);
            // dd($decodedResponse);
            if ($decodedResponse === null) {
                // Handle error: invalid JSON or empty response
                $this->billTo = [];
            } else {
                $this->billTo = collect($decodedResponse->data)
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
        $selectedUserDetail = json_decode($selectedUserDetail);
        // dd($selectedUserDetail);
        // $this->selectedUser['address'] = $selectedUserDetail->address;
        $this->receiverAddress = $selectedUserDetail->address;
        $this->receiverLocation = $selectedUserDetail->location_name;
        $this->receiverPhone = $selectedUserDetail->phone;
        // $this->selectedUser['phone'] = $selectedUserDetail->phone;
        $this->selectedUser['gst'] = $selectedUserDetail->gst_number;
        // $this->createChallanRequest['receiver_detail_id'] = $selectedUserDetail->id;
        $this->createChallanRequest['user_detail_id'] = $selectedUserDetail->id;
        $this->selectedUserDetails = json_decode($selectedUserDetails);
        // dd($selectedUserDetail);
        $request = request();

        $billTo = new ReceiversController;
        $responseContent = $billTo->index($request)->content();
        $decompressedContent = gzdecode($responseContent);
        $decodedResponse = json_decode($decompressedContent);

        if ($decodedResponse === null) {
            // Handle error: invalid JSON or empty response
            $this->billTo = [];
        } else {
            $this->billTo = collect($decodedResponse->data)
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
    // public $i = 1;
    // public function addRow($i)
    // {
    //     $i = $i + 1;
    //     $this->i = $i;

    //     $this->createChallanRequest['order_details'][] = [
    //         'p_id' => '',
    //         'unit' => '',
    //         'rate' => null,
    //         'qty' => null,
    //         'total_amount' => null,
    //         'item_code' => 0,
    //         'columns' => [
    //             [
    //                 'column_name' => '',
    //                 'column_value' => '',
    //             ]
    //         ],
    //     ];

    //     // dd($this->createChallanRequest);
    // }


    // public function selectFromStock($product)
    // {
    //     // $product = json_decode($product);
    //     $columns = [];

    //     foreach ($product['details'] as $key => $detail) {
    //         $columns[] = [
    //             'column_name' => $detail['column_name'],
    //             'column_value' => $detail['column_value'],
    //         ];
    //     }
    //     $this->fromStockRequest[] = [
    //         'p_id' => $product['id'],
    //         'unit' => $product['unit'],
    //         'rate' => $product['rate'],
    //         'qty' => $product['qty'],
    //         'total_amount' => $product['total_amount'],
    //         'columns' => $columns,
    //     ];
    //     // dd($this->fromStockRequest,$this->createChallanRequest);

    // }

    public function selectFromStock($product, $key)
    {
        $selectedProductIds = array_filter($this->selectedProductP_ids, function ($isSelected) {
            return $isSelected;
        });
        // dd($selectedProductIds);
        // Add the product details to the $this->fromStockRequest array
        if (in_array($product['id'], array_keys($selectedProductIds))) {

            $filteredColumnNames = array_filter($this->panelColumnDisplayNames);

            $columns = array_map(function ($detail) use ($filteredColumnNames) {
                if (in_array($detail['column_name'], $filteredColumnNames)) {
                    return [
                        'column_name' => $detail['column_name'],
                        'column_value' => $detail['column_value'],
                    ];
                }
            }, $product['details']);

            $columns = array_values(array_filter($columns)); // re-index and remove null values
            // dd($columns);


            $productDetails = [
                'p_id' => $product['id'],
                'unit' => $product['unit'],
                'rate' => $product['rate'],
                'qty' => $product['qty'],
                'item_code' => $product['item_code'],
                'total_amount' => $product['total_amount'],
                'columns' => $columns,
            ];

            $this->fromStockRequest[$product['id']] = $productDetails;
        } else {
            // Remove the product details from the $this->fromStockRequest array
            unset($this->fromStockRequest[$product['id']]);
        }
    }

    public function selectProduct($product, $key)
    {
        $product = json_decode($product, true);

        if (in_array($product['id'], array_keys($this->selectedProductP_ids))) {
            $columns = array_map(function ($detail) {
                return [
                    'column_name' => $detail['column_name'],
                    'column_value' => $detail['column_value'],
                ];
            }, $product['details']);

            $productDetails = [
                'p_id' => $product['id'],
                'unit' => $product['unit'],
                'rate' => $product['rate'],
                'qty' => $product['qty'],
                'item_code' => $product['item_code'],
                'total_amount' => $product['total_amount'],
                'columns' => $columns,
            ];

            $this->fromStockRequest[$product['id']] = $productDetails;
            // $this->addFromStock();
        } else {
            // Remove the product details from the $this->fromStockRequest array
            unset($this->fromStockRequest[$product['id']]);
        }
    }

    // Method for select product from searching from stock
    public function selectArticle($product , $key)
    {
        // dd($product, $key);
        $product = json_decode($product, true);
        $product = (object) $product;
        foreach ($product->details as $detail) {
            $detail = (object) $detail;
            $columns[] = [
                'column_name' => $detail->column_name,
                'column_value' => $detail->column_value,
            ];
        }
        $productDetails = [
            'p_id' => $product->id,
            'unit' => $product->unit,
            'rate' => $product->rate,
            'qty' => $product->qty,
            'item_code' => $product->item_code,
            'total_amount' => $product->rate,
            'columns' => $columns,
        ];

        if (empty($this->createChallanRequest['order_details'][$key]['p_id'])) {
            $this->createChallanRequest['order_details'][$key] = $productDetails;
        } else {
            $this->createChallanRequest['order_details'][$key] = $productDetails;
        }
        // $this->calculateTotalAmount();
        // $this->calculateTotalQuantity();
    }

    public $productId;

    public $selectedProductIds = [];

    // public function addFromStock($productIds)
    // {
    //     $this->selectedProductIds = $productIds;
    //     dd($this->selectedProductIds);

    //     // Do something with the selected product IDs
    // }


    public $hideWithoutTax = true;

    // public function addFromStock($productIds)
    // {
    //     $this->selectedProductIds = $productIds;
    //     $this->hideWithoutTax = false;
    //     // Initialize total quantity and total amount
    //     $totalQty = 0;
    //     $totalAmount = 0;
    //     foreach ($this->selectedProductIds as $selectedProductId) {
    //         // Extract the actual product ID
    //         $actualProductId = explode('-', $selectedProductId)[0];

    //         $selectedProductDetails = array_filter($this->products, function ($product) use ($actualProductId) {
    //             return $product['id'] == $actualProductId;
    //         });
    //         // dd($selectedProductDetails);
    //         if (!empty($selectedProductDetails)) {
    //             $selectedProductDetails = reset($selectedProductDetails);

    //             if ($selectedProductDetails['with_tax']  && $selectedProductDetails['tax'] !== null && $this->pdfData->challan_templete == 4) {
    //                 // Calculate the rate excluding tax using the provided formula
    //                 $rateWithTax = $selectedProductDetails['rate'];
    //                 $taxPercentage = $selectedProductDetails['tax'];
    //                 $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));

    //                 $rateWithoutTax = round($rateWithoutTax, 2);

    //                 // Assign the calculated rate to the rate field
    //                 // $selectedProductDetails['rate'] = $rateWithoutTax;
    //                 // dd($selectedProductDetails['rate']);
    //                 $taxPercentage = $selectedProductDetails['tax'];
    //                 // $rateWithTax = $selectedProductDetails['rate'];

    //                 // Add the tax to the rate
    //                 $rateWithTax = $rateWithTax + ($rateWithTax * $taxPercentage / 100);

    //                 // Round the rate with tax to two decimal places
    //                 $rateWithTax = round($rateWithTax, 2);

    //                 // Calculate the total amount by multiplying the rate with tax by the quantity
    //                 $totalAmount = $rateWithTax * $selectedProductDetails['qty'];

    //                 // Assign the calculated total amount to the total_amount field
    //                 $selectedProductDetails['total_amount'] = $totalAmount;
    //                 $dataToMerge = [
    //                     'p_id' => $selectedProductDetails['id'],
    //                     'unit' => $selectedProductDetails['unit'],
    //                     'rate' => $selectedProductDetails['rate'],
    //                     'qty' => $selectedProductDetails['qty'],
    //                     'tax' => $selectedProductDetails['tax'],
    //                     'total_amount' => $selectedProductDetails['total_amount'],
    //                     'item_code' => $selectedProductDetails['item_code'],
    //                     'columns' => $selectedProductDetails['details'],
    //                 ];
    //             }elseif($selectedProductDetails['with_tax'] == false && $selectedProductDetails['tax'] !== null && $this->pdfData->challan_templete == 4)
    //             {
    //                 $rateWithTax = $selectedProductDetails['rate'];
    //                 $taxPercentage = $selectedProductDetails['tax'];
    //                 $rateWithoutTax = $rateWithTax * (100 / (100 + $taxPercentage));

    //                 $rateWithoutTax = round($rateWithoutTax, 2);

    //                 // Assign the calculated rate to the rate field
    //                 $selectedProductDetails['rate'] = $rateWithoutTax;
    //                 // dd($selectedProductDetails['rate']);
    //                 $taxPercentage = $selectedProductDetails['tax'];
    //                 // $rateWithTax = $selectedProductDetails['rate'];
    //                 // $this->calculateTax = false;
    //                 $selectedProductDetails['rate'] = $rateWithoutTax;

    //                 // Add the tax to the rate
    //                 $rateWithTax = $rateWithoutTax + ($rateWithoutTax * $taxPercentage / 100);
    //                   // Calculate the total amount by multiplying the rate with tax by the quantity
    //                   $totalAmount = $rateWithTax * $selectedProductDetails['qty'];

    //                   // Assign the calculated total amount to the total_amount field
    //                   $selectedProductDetails['total_amount'] = $totalAmount;
    //                 $dataToMerge = [
    //                     'p_id' => $selectedProductDetails['id'],
    //                     'unit' => $selectedProductDetails['unit'],
    //                     'rate' => $selectedProductDetails['rate'],
    //                     'qty' => $selectedProductDetails['qty'],
    //                     'tax' => $selectedProductDetails['tax'],
    //                     'total_amount' => $selectedProductDetails['total_amount'],
    //                     'item_code' => $selectedProductDetails['item_code'],
    //                     'columns' => $selectedProductDetails['details'],
    //                 ];
    //             }else
    //             {
    //                 $dataToMerge = [
    //                     'p_id' => $selectedProductDetails['id'],
    //                     'unit' => $selectedProductDetails['unit'],
    //                     'rate' => $selectedProductDetails['rate'],
    //                     'qty' => $selectedProductDetails['qty'],
    //                     'tax' => $selectedProductDetails['tax'],
    //                     'total_amount' => $selectedProductDetails['rate'] * $selectedProductDetails['qty'],
    //                     'item_code' => $selectedProductDetails['item_code'],
    //                     'columns' => $selectedProductDetails['details'],
    //                 ];
    //             }




    //             $productExists = array_filter($this->createChallanRequest['order_details'], function ($product) use ($dataToMerge) {
    //                 return isset($product['p_id']) && $product['p_id'] == $dataToMerge['p_id'];
    //             });

    //             if (empty($productExists)) {
    //                 $replaced = false;
    //                 foreach ($this->createChallanRequest['order_details'] as $key => $value) {
    //                     if ($value['rate'] == null && $value['qty'] == null) {
    //                         $this->createChallanRequest['order_details'][$key] = $dataToMerge;
    //                         $replaced = true;
    //                         break;
    //                     }
    //                 }
    //                 if (!$replaced) {
    //                     $this->createChallanRequest['order_details'][] = $dataToMerge;
    //                 }
    //                 // Update total quantity and total amount
    //                 $totalQty += $dataToMerge['qty'];
    //                 $totalAmount += $dataToMerge['total_amount'];
    //             }
    //         }
    //     }
    //     // Assign updated totals to the createChallanRequest array
    //     $this->createChallanRequest['total_qty'] = $totalQty;
    //     $this->createChallanRequest['total'] = $totalAmount;
    //     // $this->selectedProductIds = [];
    // }

    public $element;
    public $barcode;
    public $errorQty =[];

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

    public function removeRow($index)
    {
        // Remove the specified row
        unset($this->createChallanRequest['order_details'][$index]);

        // Reindex the array to ensure sequential keys
        $this->createChallanRequest['order_details'] = array_values($this->createChallanRequest['order_details']);
    }

     public $newRecord;

    public function removeItem($index, $newRecord = null)
    {
        unset($this->createChallanRequest['order_details'][$index]);

        $this->createChallanRequest['order_details'] = array_values($this->createChallanRequest['order_details']);

        // Only add the new record if it's not null
        if ($newRecord !== null) {
            $this->createChallanRequest['order_details'][] = $newRecord;
        }
        // $this->calculateTotalAmount();
        // $this->calculateTotalQuantity();

        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $this->createChallanRequest['order_details']);

        // // Encode the modified data array
        $this->challanModifyData = json_encode($this->createChallanRequest);

        // dd($this->createChallanRequest['order_details']);
        // Emit an event or perform an action to send the updated data to the server
        $this->emit('updatedCreateChallanRequest', $this->createChallanRequest);
    }





    public function deleteOrderDetail($index)
    {
        $orderDetail = $this->createChallanRequest['order_details'][$index];

        if (isset($orderDetail['id'])) {
            // Assuming you have a ChallanOrderDetail model
            $challanOrderDetail = ChallanOrderDetail::find($orderDetail['id']);
            if ($challanOrderDetail) {
                $challanOrderDetail->delete();
            }
        }

        unset($this->createChallanRequest['order_details'][$index]);
        $this->createChallanRequest['order_details'] = array_values($this->createChallanRequest['order_details']);
        // $this->calculateTotalAmount();
        // $this->calculateTotalQuantity();

        $this->refreshChallanRequest();

        // $this->emit('triggerDelete', $index);
    }

    private function refreshChallanRequest()
    {
        $request = request();
        $id = session('persistedActiveFeature');
        $challanController = new ChallanController();
        $challanModifyData = $challanController->show($request, $id);
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);
        $this->challanModifyData = json_encode($modifiedDataArray);
    }



    public function updateTotalAmount($index)
    {
        $orderDetails = &$this->createChallanRequest['order_details'][$index];

        $qty = isset($orderDetails['qty']) ? $orderDetails['qty'] : (isset($orderDetails['remaining_qty']) ? $orderDetails['remaining_qty'] : null);

        if (isset($qty)) { // Check if quantity is set
            if (isset($orderDetails['rate'])) { // Check if rate is set
                $orderDetails['total_amount'] = (float) $orderDetails['rate'] * (float) $qty;
            }

            $itemCode = $orderDetails['item_code'];

            $this->errorQty[$index] = $this->checkStockQuantity($qty, $itemCode);

            $this->calculateTotalAmountModify();
        }

        $this->calculateTotalQuantityModify(); // Always calculate total quantity
    }
    public function calculateTotalQuantityModify()
    {
        $totalQuantity = array_reduce($this->createChallanRequest['order_details'], function ($carry, $row) {
            $qty = isset($row['qty']) ? (float) $row['qty'] : 0;
            return $carry + $qty;
        }, 0);

        $this->createChallanRequest['total_qty'] = $totalQuantity;

        // return $totalQuantity; // Return the total quantity
    }

     public function calculateTotalAmountModify()
    {
        $total = array_reduce($this->createChallanRequest['order_details'], function ($carry, $row) {
            $qty = isset($row['qty']) ? $row['qty'] : (isset($row['remaining_qty']) ? $row['remaining_qty'] : null);
            return $carry + (isset($row['rate'], $qty) ? (float) $row['rate'] * (float) $qty : 0);
        }, 0);

        $this->createChallanRequest['total'] = $total;
        $this->createChallanRequest['total_words'] = $this->numberToIndianRupees($total);
    }



    private function checkStockQuantity($qty, $itemCode)
    {
        foreach ($this->fromStockRequest as $element) {
            if ($qty > $element['qty']) {
                return 'Remaining Qty in stock is less';
            }
        }

        if ($this->persistedTemplate == 'modify_challan') {
            $stock = Product::where('item_code', $itemCode)->first();

            if ($stock) {
                return $qty > $stock->qty ? 'Remaining Qty in stock is less' : null;
            } else {
                return 'No product found with this item_code';
            }
        }

        return null;
    }







    // Add Receiver Manually
    public function callAddReceiverManually(Request $request)
    {
        // dd($request);

        $request->merge($this->addReceiverData);
        $this->addReceiver = new addReceiver;
        $ReceiversController = new ReceiversController;

        $response = $ReceiversController->addManualReceiver($request);

        $result = $response->getData();

        $this->reset(['statusCode', 'message', 'errors', 'validationErrorsJson']);

        // Set the status code and message received from the result
        $this->statusCode = $result->status;

        if ($this->statusCode === 200) {
            // $this->successMessage = $result->message;
            $this->success = $result->message;
            $this->addReceiverData = [];
            $this->reset([ 'statusCode', 'message', 'errors', 'validationErrorsJson']);
            // $this->reset(['createChallanRequest', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            $this->reset(['statusCode', 'message', 'validationErrorsJson']);
        }
    }
    public $activeTab;
    // public $activeTab = 'receiver-manually';

    public function setActiveTab($tab)
    {
        // dd($tab);
        $this->activeTab = $tab;
        // dd($this->activeTab);
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


    public $rules = [
        'addReceiverData.email' => 'nullable|email|unique:users,email',
        'addReceiverData.phone' => 'nullable|string|unique:users,phone',
        // 'addReceiverData.receiver_special_id' => 'nullable|unique:receivers,receiver_special_id',

        // 'addReceiverData.gst_number' =>  'alpha_num,regex:/^[a-zA-Z0-9]+$/,gst_number',
    ];

    public $messages = [
        'addReceiverData.email.unique' => 'The email address is already registered.',
        'addReceiverData.phone.unique' => 'The phone number is already registered.',
    ];

    public function updated($propertyName)
    {
        // Only validate email and phone number fields
        // if ($property === 'addReceiverData.email' || $property === 'addReceiverData.phone' || $property === 'addReceiverData.receiver_special_id' ) {
        //     $this->validateOnly($property, $this->rules);
        //     // || $property === 'addReceiverData.gst_number'
        // }
        $this->validateOnly($propertyName, $this->rules);
    }



    // public function challanDesign()
    // {
    //     $request = new Request;

    //     $request->merge([
    //         'default' => '0',
    //         'panel_id' => '1',
    //         'section_id' => '1',
    //         'feature_id' => '1',
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //     ]);
    //     $newChallanDesign = new PanelColumnsController;
    //     $response = $newChallanDesign->index($request);
    //     $this->challanDesignData = $response->getData()->data;
    //     // dd($this->challanDesignData);
    //     $this->additionalInputs = 3;

    // }

    public function challanDesign()
{
    $request = new Request;

    $requestData = [
        'panel_id' => '1',
        'section_id' => '1',
        'feature_id' => '1',
        'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    ];

    $newChallanDesign = new PanelColumnsController;

    // Fetch data with default value 0
    $requestData['default'] = '1';
    $request->merge($requestData);
    $response = $newChallanDesign->index($request);
    $this->challanDesignData = $response->getData()->data;

    // Fetch data with default value 1
    $requestData['default'] = '0';
    $request->merge($requestData);
    $response = $newChallanDesign->index($request);
    $this->challanDesignData = array_merge($this->challanDesignData, $response->getData()->data);

    $this->additionalInputs = count($this->challanDesignData);
    // dd($this->challanDesignData);
}


    public function createChallanDesign(){
        $request = new Request;
        for ($i = 0; $i <= $this->additionalInputs; $i++) {
                    $inputKey = "$i"; // Assuming the input names are column3, column4, etc.
                    // dd($inputKey);
                    // dd($this->challanDesignData[$i]['panel_column_default_name']);
                    if (isset($this->challanDesignData[$i]['panel_column_display_name'])) {
                        $panelColumnDisplay = $this->challanDesignData[$i]['panel_column_display_name'];
                        $panelColumnDefault = "column_$i";

                        // Define the data array for the new record

                        if (isset($this->challanDesignData[$i]['id'])) {
                            $data = [
                                'id' => $this->challanDesignData[$i]['id'],
                                'panel_id' => '1',
                                'section_id' => '1',
                                'feature_id' => '1',
                                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                                'panel_column_display_name' => $panelColumnDisplay,
                                'panel_column_default_name' => $panelColumnDefault,
                                'status' => 'active',
                            ];

                            $request->merge($data);
                            // dump($request);
                            $newChallanDesign = new PanelColumnsController;
                            $response = $newChallanDesign->update($request, $this->challanDesignData[$i]['id']);
                            $result = $response->getData();
                            // dd($result);
                            // Set the status code and message received from the result
                            $this->statusCode = $result->status_code;

                            if ($result->status_code === 200) {
                                $this->successMessage = $result->message;

                                session()->flash('success', $this->successMessage);

                                $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
                            } else {
                                $this->errorMessage = json_encode($result->errors);

                                session()->flash('error', $this->errorMessage);
                            }
                        }
                    }
                }
        // dd($request);
    }

    public $additionalInputs = 3, $editMode, $editModeIndex;

    public function challanSeries(Request $request)
    {
        // if($request->ass)
        $request->merge($this->addChallanSeriesData);
        // dd($request);
        if($request->assigned_to_r_id == 'default'){
            $request->merge(['assigned_to_r_id' => '', 'default' => '1']);
        }
        $newChallanSeriesNoController = new PanelSeriesNumberController;
        $response = $newChallanSeriesNoController->store($request);
        // $this->reset(['addChallanSeriesData']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        $this->reset(['addChallanSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            return redirect()->route('sender', ['template' => 'challan_series_no']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '1']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiversController;

            $request->replace([]);
            session()->flash('success', $this->successMessage);
        }
    }

    public function deleteChallanSeries($id)
    {

        $controller = new PanelSeriesNumberController;
        $controller->destroy($id);
        // $request = new request;
        // $this->successMessage = $result->message;
        return redirect()->route('sender', ['template' => 'challan_series_no'])->with('message', $this->successMessage ?? $this->errorMessage);

        // $this->emit('triggerDelete', $id);
    }

    public function deleteReceiver($id)
    {
        $receiver = new ReceiversController;
        $receiver->delete($id);
        return redirect()->route('sender', ['template' => 'view_receiver']);
    }

    // public function selectChallanSeries($series_id,$series_number, $valid_till, $valid_from, $assigned_to_name)
    public function selectChallanSeries($seriesData)
    {
        $seriesData = json_decode($seriesData);
        $this->reset(['updateChallanSeriesData']);
        $this->updateChallanSeriesData = (array)$seriesData;
        $this->updateChallanSeriesData['assigned_to_r_id'] = '';

    }
    public $manuallyAdded;
    public function resetChallanSeries()
    {
        $this->reset(['updateChallanSeriesData']);
    }
    public $success;
    public function callAddReceiver(Request $request)
    {
        // $this->validate();
        $request->replace([]);
        $request->merge($this->addReceiverData);

        $newReceiversController = new ReceiversController;
        $response = $newReceiversController->addReceiver($request);
        $result = $response->getData();
        // dd($result);
        if ($result->status === 200) {
            $this->success = $result->message;
            $this->reset(['errorMessage', 'addReceiverData']);
            return view('components.panel.sender.view_receiver');

        } else {
            // $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
            $this->manuallyAdded = json_encode((array) $result->errors);
            // $this->manuallyAdded = json_encode((array) $result->errors->receiver_special_id);
        }

        $this->reset(['addReceiverData']);
    }

    public $errorFileUrl;
    // Bulk Add Receiver
    public function productUpload()
    {
        // dd($this->uploadFile->getClientOriginalExtension());
        // Check if the uploaded file is a CSV
        // Validate the file
        if ($this->uploadFile->getClientOriginalExtension() !== 'csv' || $this->uploadFile->getMimeType() !== 'text/csv') {
        $this->dispatchBrowserEvent('show-error-message', ['message' => 'Please upload a valid CSV file.']);
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

        $productUpload = new ReceiversController;
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
// public function callAddReceiver(Request $request)
// {


//     // Continue with the original logic
//     // $request =  $this->validate();
//     $request->replace([]);
//     $request->merge($this->addReceiverData);
//     $newReceiversController = new ReceiversController;
//     $response = $newReceiversController->addReceiver($request);
//     $result = $response->getData();

//     if ($result->status === 200) {
//         $this->successMessage = $result->message;
//         return view('components.panel.sender.view_receiver');
//     } else {
//         $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
//     }

//     $this->reset(['addReceiverData']);
// }


    public $otherAddresses = [];
    // Controller or Livewire Component


    public function saveAddress($index)
    {
        // Assuming $this->selectReceiver contains all necessary data
        $user = User::find($this->selectReceiver['id']);
        if ($user) {
            // Check if user details exist, if not, create a new one
            $userDetails = $user->userDetails()->firstOrCreate([]);

            // Update user details
            $userDetails->update([
                'name' => $this->selectReceiver['name'],
                'email' => $this->selectReceiver['email'],
                'phone' => $this->selectReceiver['phone'],
                'gst_number' => $this->selectReceiver['gst_number'],
                'state' => $this->selectReceiver['state'],
                'city' => $this->selectReceiver['city'],
                'tan' => $this->selectReceiver['tan'], // Assuming you want to update this even if it's null
                'address' => $this->selectReceiver['address'],
                'pincode' => $this->selectReceiver['pincode'],
            ]);

            // Optionally, flash a message to the session to indicate success
            session()->flash('message', 'Default address updated successfully.');
        } else {
            // Handle the case where the user is not found
            session()->flash('error', 'User not found.');
        }
        // Save logic for other addresses
    }
    // public function selectReceiver($receiver)
    // {
    //     $this->selectReceiver = [];
    //     $details = [];
    //     dd($receiver);
    //     foreach ($receiver['details'] as $key => $detail) {
    //         // dd($detail);
    //         array_push($details, [
    //             "id" => $detail['id'],
    //             "receiver_id" => $detail['receiver_id'],
    //             "address" => $detail['address'],
    //             "pincode" => $detail['pincode'],
    //             "phone" => $detail['phone'],
    //             "gst_number" => $detail['gst_number'],
    //             "state" => $detail['state'],
    //             "city" => $detail['city'],
    //             "bank_name" => $detail['bank_name'],
    //             "branch_name" => $detail['branch_name'],
    //             "bank_account_no" => $detail['bank_account_no'],
    //             "ifsc_code" => $detail['ifsc_code'],
    //             "tan" => $detail['tan'],
    //             "location_name" => $detail['location_name'],
    //         ]);
    //     }
    //     $this->selectReceiver = array(
    //         'id' => $receiver['id'],
    //         'added_by' => $receiver['user']['added_by'],
    //         'receiver_name' => $receiver['receiver_name'],
    //         'details' => $details
    //     );

    // }


    public function selectReceiver($receiver)
    {
        // dd($receiver['user']['details']);
        $this->selectReceiver = [];

        $user = $receiver['user'];
        $this->selectReceiver = [
            'id' => $user['id'],
            'added_by' => $user['added_by'],
            'receiver_name' => $receiver['receiver_name'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'gst_number' => $user['gst_number'],
            'state' => $user['state'],
            'city' => $user['city'],
            'tan' => $user['tan'],
            'address' => $user['address'],
            'pincode' => $user['pincode'],
            'location_name' => 'Default',
        ];

        if (isset($receiver['user']['details']) && is_array($receiver['user']['details'])) {
            $this->otherAddresses = $receiver['user']['details'];
            // dd($this->otherAddresses);
        }
    }
    public function addNewReceiverDetail()
    {
        $this->otherAddresses[] = [
            'location_name' => '',
            'address' => '',
            'phone' => '',
            'gst_number' => '',
            'pincode' => '',
            'state' => '',
            'city' => '',
            'bank_name' => '',
            'branch_name' => '',
            'bank_account_no' => '',
            'ifsc_code' => '',
            'tan' => '',
        ];
        // dd($this->otherAddresses);
    }

    public function saveDefaultAddress()
    {
        // Assuming $this->selectReceiver contains all necessary data
        $user = User::find($this->selectReceiver['id']);
        if ($user) {
            // Update user details
            $user->update([
                'name' => $this->selectReceiver['name'],
                'email' => $this->selectReceiver['email'],
                'phone' => $this->selectReceiver['phone'],
                'gst_number' => $this->selectReceiver['gst_number'],
                'state' => $this->selectReceiver['state'],
                'city' => $this->selectReceiver['city'],
                'tan' => $this->selectReceiver['tan'], // Assuming you want to update this even if it's null
                'address' => $this->selectReceiver['address'],
                'pincode' => $this->selectReceiver['pincode'],
                // Add any other fields you want to update
            ]);
            // Optionally, flash a message to the session to indicate success
            session()->flash('message', 'Default address updated successfully.');
        } else {
            // Handle the case where the user is not found
            session()->flash('error', 'User not found.');
        }
    }

    public function removeReceiverDetail($key)
    {
        unset($this->selectReceiver['details'][$key]);
        // Reindex the array to ensure sequential keys
        $this->selectReceiver['details'] = array_values($this->selectReceiver['details']);
        // Livewire will automatically update the frontend
    }

    public function updateReceiverDetail(Request $request)
    {

        // $request->replace([]);
        $request->merge($this->selectReceiver);

        $newReceiversController = new ReceiversController;
        $response = $newReceiversController->updateReceiver($request, $request->id);
        foreach ($request->details as $key => $detail) {
            $request->replace([]);
            $request->merge($detail);
            if ($request['id'] !== "") {
                $detailResponse = $newReceiversController->updateReceiverDetail($request, $request['id']);
                $result = $detailResponse->getData();
                if ($result->status === 200) {
                    // $this->successMessage = $result->message;
                    // return view('components.panel.sender.view_receiver');
                } else {
                    $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
                }
            } else {
                $detailResponse = $newReceiversController->storeReceiverDetail($request);
                $result = $detailResponse->getData();
                if ($result->status === 200) {
                    // $this->successMessage = $result->message;
                    // return view('components.panel.sender.view_receiver');
                } else {
                    $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
                }
            }
        }
        $result = $response->getData();
        if ($result->status === 200) {
            $this->successMessage = $result->message;
            // return view('components.panel.sender.view_receiver');
        } else {
            $this->errorMessage = json_encode(isset($result->errors) ? $result->errors : null);
        }

        $this->reset(['selectReceiver']);
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => 'view_receiver'])->with('message', $this->successMessage ?? $this->errorMessage);
        // redirect()->route('sender')->with('message',$this->successMessage??$this->errorMessage);
    }
    // TERMS AND CONDITIONS
    public function invoiceTermsAndConditions(Request $request)
    {
        // $request->merge($this->termsAndConditionsData);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->termsIndexData = (array) $data->getData()->data;
        // dd($this->termsIndexData);

    }
    public function addTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
        $this->mount();
    }


    public function deleteInvoiceTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);
        // $this->mount();
        // dd('delete');
    }

    public $selectedContent, $itemId;

    public function selectInvoiceTerms($data)
    {
        $item = json_decode($data, true);
        // dd($item);
        $this->selectedContent = $item['content'];
        $this->itemId = $item['id'];
        // dd($this->id);
    }
    public $updateChallanSeriesData = [];
    public function updatePanelSeries()
    {
        // dd($this->itemId);
        $request =  request();
        $request->merge($this->updateChallanSeriesData);
        if($request->assigned_to_r_id == 'default'){
            $request->merge(['assigned_to_r_id' => '', 'default' => '1']);
        }
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
            session()->flash('success', $this->successMessage);
            $this->reset(['updateChallanSeriesData', 'statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '1']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiversController;

            $request->replace([]);
            $response = $newReceiversController->index($request);
            return redirect()->route('sender', ['template' => 'challan_series_no'])->with('message', $this->successMessage ?? $this->errorMessage);
            // $this->receiverDatas = $receiverData->data;
            // dump("4");
            // dump(json_encode($this->receiverDatas));
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }
    //     public function modifyChallan(Request $request)
    //     {
    //         $id = session('persistedActiveFeature');
    //         $challanController = new ChallanController();
    //         $challanModifyData = $challanController->show($request, $id);
    //         // $request->merge($this->createChallanRequest);
    //         // dd($challanModifyData);
    //         $this->challanModifyData = json_encode($challanModifyData->getData()->data);
    //         $this->inputsDisabled = false;
    //          // dd($challanModifyData->getData()->data);
    //         $PanelColumnsController = new PanelColumnsController;

    //         $columnsResponse = $PanelColumnsController->index($request);
    //         $columnsData = json_decode($columnsResponse->content(), true);
    //         // dd($columnsData);
    //         $filteredColumns = array_filter($columnsData['data'], function ($column) {
    //             return $column['feature_id'] == 1;
    //         });
    //         $panelColumnDisplayNames = array_map(function ($column) {
    //             return $column['panel_column_display_name'];
    //         }, $filteredColumns);

    //         $this->panelColumnDisplayNames = $panelColumnDisplayNames;


    //     }


    public function modifyChallan(Request $request)
    {
        $id = session('persistedActiveFeature');
        $this->reset(['errorMessage', 'successMessage','challanSave','statusCode', 'message' ]);
        $challanController = new ChallanController();
        $challanModifyData = $challanController->show($request, $id);

        // Convert the stdClass to an array
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);
        // dd($modifiedDataArray);
        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);
        // dd($this->createChallanRequest);
        $this->challanModifyData = json_encode($modifiedDataArray);
        $this->inputsDisabled = false;
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


    }

    // public function selfReturnChallanView($id, $receiverId)
    // {
    //     $request = new request();
    //     $this->innerFeatureRedirect('self_return_challan', null);

    //     $PanelColumnsController = new PanelColumnsController;

    //     $request->merge([
    //         'feature_id' => 1,
    //         // Auth::guard(Auth::getDefaultDriver())->user()->id
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //     ]);
    //     $columnsResponse = $PanelColumnsController->index($request);
    //     $columnsData = json_decode($columnsResponse->content(), true);

    //     $filteredColumns = array_filter($columnsData['data'], function ($column) {
    //         return $column['feature_id'] == 1;
    //     });
    //     $panelColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $filteredColumns);
    //     $this->panelColumnDisplayNames = $panelColumnDisplayNames;

    //     // dd($this->panelColumnDisplayNames);
    //     $request->merge([
    //         'feature_id' => 1,
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //     ]);
    //     $columnsUserResponse = $PanelColumnsController->index($request);
    //     $columnsUserData = json_decode($columnsUserResponse->content(), true);

    //     $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
    //         return $column['feature_id'] == 1;
    //     });
    //     $panelUserColumnDisplayNames = array_map(function ($column) {
    //         return $column['panel_column_display_name'];
    //     }, $filteredUserColumns);



    //     $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;
    //     $receivedArticles = new ReturnChallanController;
    //     $request->merge(['id' => $id]);
    //     $receivedArticles = $receivedArticles->showReturnChallan($request, $receiverId);
    //     $modifiedDataArray = json_decode(json_encode($receivedArticles->getData()->article), true);

    //     $this->createChallanRequest = array_merge($this->createChallanRequest,$modifiedDataArray[0]);

    //     $this->challanModifyData = json_encode($modifiedDataArray);
    //     // return redirect()->route('sender', ['createChallanRequest' => $this->createChallanRequest]);
    //     session()->flash('createChallanRequest', $this->createChallanRequest);

    //     $this->inputsDisabled = false;



    // }
    public function selfReturnChallanView(Request $request)
    {

        $id = session('persistedActiveFeature');
        $challanController = new ChallanController();
        $challanModifyData = $challanController->show($request, $id);

        // Convert the stdClass to an array

        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);

        // Filter out rows where remaining_qty is zero and update qty with remaining_qty
        $filteredOrderDetails = [];
        foreach ($modifiedDataArray['order_details'] as $orderDetail) {
            if (isset($orderDetail['remaining_qty']) && $orderDetail['remaining_qty'] > 0) {
                // Update qty with remaining_qty
                $orderDetail['qty'] = $orderDetail['remaining_qty'];
                $filteredOrderDetails[] = $orderDetail; // Include this row in the filtered data
            }
        }

        // Update order_details with the filtered and modified data
        $modifiedDataArray['order_details'] = array_values($filteredOrderDetails);

        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);
        // dd($this->createChallanRequest);
        // Encode the modified data array
        $this->challanModifyData = json_encode($this->createChallanRequest);

        $this->inputsDisabled = false;


        // dd($challanModifyData->getData()->data);
        $PanelColumnsController = new PanelColumnsController;

        // $PanelColumnsController = new PanelColumnsController;

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
    }


    public function viewChallan(Request $request)
    {
        $id = session('persistedActiveFeature');
        $challanController = new ChallanController();
        $challanModifyData = $challanController->show($request, $id);

        // Convert the stdClass to an array
        $modifiedDataArray = json_decode(json_encode($challanModifyData->getData()->data), true);

        // Merge the existing createChallanRequest with the modified data
        $this->createChallanRequest = array_merge($this->createChallanRequest, $modifiedDataArray);

        $this->challanModifyData = json_encode($modifiedDataArray);
        $this->inputsDisabled = false;
        // dd($challanModifyData->getData()->data);
        $PanelColumnsController = new PanelColumnsController;

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
        $this->reset(['errorMessage', 'successMessage','challanSave','statusCode', 'message',  'validationErrorsJson' ]);
    }

    public function viewReceiver()
    {
        $request = request();
        $viewReceiver = new ReceiversController;
                // $response = $viewReceiver->index($request);
                // $receiverData = $response->getData();
                // $this->receiverDatas = $receiverData->data;

                $responseContent = $viewReceiver->index($request)->content();
                $decompressedContent = gzdecode($responseContent);
                $decodedResponse = json_decode($decompressedContent);

                if ($decodedResponse === null) {
                    $this->receiverDatas = [];
                } else {
                    $this->receiverDatas = collect($decodedResponse->data)->sortBy(function ($item) {
                        return strtolower($item->receiver_name);
                    })->values()->all();
                }

                // dd($receiverData->data);
                // $this->receiverDatas = $receiverData->data->paginate($this->pagination);
                // dump("5");
                // dump(json_encode($this->receiverDatas));

    }

    public function viewSfpSenderChallan(Request $request)
    {
        request()->replace([]);

        $columnFilterDataset = [
            'feature_id' => 2
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

        if ($this->challan_series != null) {
            // dump($this->challan_series);
            $request->merge(['challan_series' => $this->challan_series]);
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
        if ($this->sfp != null) {
            $request->merge(['sfp' => $this->sfp]);
        }
        // Filter by date range
        if ($this->fromDate != null && $this->toDate != null) {
            $request->merge([
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
            ]);
        }

        $challanController = new ChallanController();
        $tableTdData = $challanController->indexSfp($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        // dd($this->tableTdData);
        $this->challanFiltersData = json_encode($tableTdData->getData()->filters);

        // $this->successMessage = $result->message;

        $this->emit('challanDataReceived', $tableTdData);
    }

    // public function render()
    // {
    //     $request = request();
    //     switch ($this->persistedTemplate) {
    //         case 'view_receiver':
    //             $viewReceiver = new ReceiversController;
    //             // $response = $viewReceiver->index($request);
    //             // $receiverData = $response->getData();
    //             // $this->receiverDatas = $receiverData->data;

    //             $responseContent = $viewReceiver->index($request)->content();
    //             $decompressedContent = gzdecode($responseContent);
    //             $decodedResponse = json_decode($decompressedContent);

    //             if ($decodedResponse === null) {
    //                 $this->receiverDatas = [];
    //             } else {
    //                 $this->receiverDatas = collect($decodedResponse->data)->sortBy(function ($item) {
    //                     return strtolower($item->receiver_name);
    //                 })->values()->all();
    //             }

    //             // dd($receiverData->data);
    //             // $this->receiverDatas = $receiverData->data->paginate($this->pagination);
    //             // dump("5");
    //             // dump(json_encode($this->receiverDatas));
    //             break;
    //             case 'create_challan':
    //                 $products = new ProductController;
    //     $request->merge([
    //         'article' => $this->Article ?? null,
    //         'location' => $this->location ?? null,
    //         'item_code' => $this->item_code ?? null,
    //     ]);

    //     $response = $products->index($request);
    //     $result = $response->getData();
    //     $this->products = (array) $result->data;
    //     break;
    //     }


    //     $UserResource = new UserAuthController;
    //     $response = $UserResource->user_details($request);
    //     $response = $response->getData();
    //     if ($response->success == "true") {
    //         $this->UserDetails = $response->user->plans;
    //         $this->user = json_encode($response->user);
    //         $this->successMessage = $response->message;
    //         $this->reset(['errorMessage', 'successMessage']);
    //     } else {
    //         $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    //     }

    //     return view('livewire.sender.screens.screen', [
    //         'createChallanData' => $this->createChallanData,
    //         'page' => $this->page,

    //     ]);
    // }
    public $signature, $attributes, $columnId;



    public function saveSignature()
    {
        $signature = $this->signature;
        $columnId = $this->columnId;
        // dd( $columnId);

        // Create a new Request instance and set the 'signed' attribute to the signature data
        $request = request();
        $request->setMethod('POST');
        $request->merge(['signed' => $signature,
        'column_id' => $columnId
                    ]);

        // Create a new instance of the ChallanController
        $challanController = new ChallanController;

        // Call the uploadSignature method and pass the Request instance
        $response = $challanController->uploadSignature($request);
        // Get the original data from the response
        $data = $response->getData(true);

        if ($data['status_code'] == 200) {
            $this->successMessage = $data['message'];
            $this->reset(['signature']);
        } else {
            $this->errorMessage = $data['message'];
        }
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $warehouse, $category;

    // public $teamUsers;
    public function render()
    {
        // dd('view');
        // $request = request();
        session()->put('previous_url', url()->current());
        // $UserResource = new UserAuthController;
        // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // // Try to get the user details from the cache

        // $response = $UserResource->user_details($request);
        // $response = $response->getData();

        // if ($response->success == "true") {
        //     $this->UserDetails = $response->user->plans;
        //     $this->user = json_encode($response->user);
        //     $this->successMessage = $response->message;
        //     $this->reset(['errorMessage', 'successMessage']);
        // } else {
        //     $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        // }

        // $filters = [
        //     'article' => $this->Article,
        //     'item_code' => $this->item_code,
        //     'warehouse' => $this->warehouse,
        //     'location' => $this->location,
        //     'category' => $this->category,
        //     'from_date' => $this->from,
        //     'to_date' => $this->to,
        // ];

        // // Apply filters from the request to the query
        // foreach ($filters as $key => $value) {
        //     if ($value !== null) {
        //         $request->merge([$key => $value]);
        //     }
        // }


        // $query = Product::query()->with('details');

        // // Filter by user_id
        // $query->where('user_id', $userId);

        // // Add a where clause to the query to filter out products where qty is not equal to 0
        // $query->where('qty', '!=', 0);

        // // Apply filters dynamically
        // if (!empty($this->Article)) {
        //     $query->whereHas('details', function ($q) {
        //         $q->where('column_value', $this->Article);
        //     });
        // }
        // if (!empty($this->item_code)) {
        //     $query->where('item_code', $this->item_code);
        // }
        // if (!empty($this->location)) {
        //     $query->where('location', $this->location);
        // }
        // if (!empty($this->category)) {
        //     $query->where('category', $this->category);
        // }
        // if (!empty($this->warehouse)) {
        //     $query->where('warehouse', $this->warehouse);
        // }

        // // Fetch filtered results
        // $products = $query->get();

        // // Fetch unique values based on the filtered results
        // $this->articles = $products->pluck('details.0.column_value')->unique()->filter()->values()->toArray();
        // $this->item_codes = $products->pluck('item_code')->unique()->filter()->values()->toArray();
        // $this->locations = $products->pluck('location')->unique()->filter()->values()->toArray();
        // $this->categories = $products->pluck('category')->unique()->filter()->values()->toArray();
        // $this->warehouses = $products->pluck('warehouse')->unique()->filter()->values()->toArray();

        // // Apply further filters to the paginated results
        // if (!empty($this->item_code)) {
        //     $query->where('item_code', $this->item_code);
        // }
        // if (!empty($this->category)) {
        //     $query->where('category', $this->category);
        // }
        // if (!empty($this->warehouse)) {
        //     $query->where('warehouse', $this->warehouse);
        // }
        // if (!empty($this->location)) {
        //     $query->where('location', $this->location);
        // }

        // // Fetch paginated results
        // $products = $query->paginate(50);

        return view('livewire.sender.screens.screen', [
            // 'stock' => $products,
        ]);
    }

    // function convertNumberToWords($number)
    // {
    //     $words = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    //     $tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    //     $suffixes = ["", "Thousand", "Lakh", "Crore"];

    //     $num = (float)$number;

    //     $numInWords = '';

    //     if ($num == 0) {
    //         $numInWords = "Zero Rupees Only";
    //     } else {
    //         $groups = array_reverse(explode(',', number_format($num, 2, ',', ',')));

    //         for ($i = 0; $i < count($groups); $i++) {
    //             $group = (float)$groups[$i];

    //             if ($group != 0) {
    //                 $hundreds = floor($group / 100);
    //                 $remainder = $group % 100;
    //                 $tensDigit = floor($remainder / 10);
    //                 $onesDigit = $remainder % 10;

    //                 if ($hundreds > 0) {
    //                     $numInWords .= $words[$hundreds] . " Hundred ";
    //                 }

    //                 if ($tensDigit == 1) {
    //                     $numInWords .= $words[$remainder] . " ";
    //                 } else {
    //                     if ($tensDigit > 1) {
    //                         $numInWords .= $tens[$tensDigit] . " ";
    //                     }

    //                     if ($onesDigit > 0) {
    //                         $numInWords .= $words[$onesDigit] . " ";
    //                     }
    //                 }

    //                 $numInWords .= $suffixes[$i] . " ";
    //             }
    //         }

    //         $numInWords .= "Rupees Only";
    //     }

    //     return ucwords(strtolower(trim($numInWords)));
    // }

    public $rates = [];
    public $quantities = [];


    public function handleUpdateValues($index, $rate, $quantity, $total)
    {
        $this->rates[$index] = $rate;
        $this->quantities[$index] = $quantity;
        $this->total[$index] = $total;
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
