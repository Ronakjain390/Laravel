<?php

namespace App\Http\Livewire\Receiver\Content;
use App\Models\Challan;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\PanelSeriesNumber;
use App\Models\ReturnChallan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use Livewire\Component;

class CreateReturnChallan extends Component
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
    public $challanSeries, $senderName;
    public $selectedProducts = [];
    public $rows = [];
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
    public $showInputBoxes = true;
    public $isLoading = true;
    public $context;
    public $Senders = [];
    public $authUserState;
    public $action = 'save';
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

    public function mount()
    {
        $request = request();
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
        $this->context = 'return_challan';
       $this->authUserState = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->state; // Assuming the state is stored in the 'state' attribute
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
        $this->initializeRows();
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




    public function selectUser($sender, $phone, $email, $address, $gstNumber, $senderId)
    {
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

        $latestSeriesNum = Challan::where('challan_series', $challanSeries)
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
        // dd($receivedArticles->order_details);
        $this->senderName = $selectedUser->name;
         // Decode $selectedUserDetails once
        //  $decodedUserDetails = json_decode($selectedUserDetails);
        $this->receivedArticles = json_encode($receivedArticles);
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

    public function render()
    {
        return view('livewire.receiver.content.create-return-challan');
    }
}
