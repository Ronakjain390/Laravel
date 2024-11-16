<?php

namespace App\Http\Livewire\Sender\Screens;

use App\Models\User;
use Carbon\Carbon;
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

class TestChallanComponent extends Component
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
    public $selectAll = false;
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
    public $challanId = null;
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
    protected $updatesQueryString = ['challanId'];

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
        'challan_series' => '',
        'series_num' => '',
        'challan_date' => '',
        'feature_id' => '',
        'receiver_id' => null,
        'receiver' => null,
        'comment' => '',
        'total_qty' => null,
        'total' => '',
        'calculate_tax' => null,
        'total_words' => '',
        'additional_phone_number' => '',
        'discount_total_amount' => '',
        'order_details' => [],
        'statuses' => [
            [
                'comment' => ''
            ]
        ]
    ];
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

    public $rows = [];
    public $showBarcode;
    public $context;


    public function mount()
    {
        $request = request();
        $PanelColumnsController = new PanelColumnsController;
        $billTo = new ReceiversController;
       // Fetch all products once and store them in a public property
       $this->context = 'challan';
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



        $this->createChallanRequest['challan_date'] = now()->format('Y-m-d');
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
            if (isset($settings) && isset($settings->sender)) {
                $senderSettings = $settings->sender;
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
    public function challanEdit()
    {
        // dd('sdf');
        $this->action = 'edit';
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->inputsResponseDisabled = true;
        $this->reset([ 'message', 'challanSave']);
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

    public function updateField() {
        $this->inputsDisabled = false;
        $this->updateForm = false;
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $series = PanelSeriesNumber::where('user_id', $userId)->where('default', "1")->where('panel_id', '1')->select('series_number')->first();
        // dd($series);
        $challanSeries = $series->series_number;
        $latestSeriesNum = Challan::where('challan_series', $challanSeries)
        ->where('sender_id', $userId)
        ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $this->inputsDisabled = false; // Adjust the condition as needed
        $this->selectedUser = [
            "challanSeries" => $challanSeries,
            "seriesNumber" => $seriesNum,
        ];
        // $this->dispatchBrowserEvent('inputsDisabledChanged', ['value' => false]);
    }


    // $series = PanelSeriesNumber::where('user_id', $userId)
    // ->where('default', "1")
    // ->where('panel_id', '1')
    // ->first();

    // if (!$series) {
    //     throw new \Exception('Please add one default Series number');
    // }

    // $currentDate = now();
    // $validTill = Carbon::parse($series->valid_till);

    // if ($validTill->isPast()) {
    //     // Series has expired
    //     $this->addError('challanSeries', 'The current challan series has expired.');
    //     $this->emit('showAlert', 'The current challan series has expired. Please assign a new series number.');

    //     // You can add logic here to automatically assign a new series or prompt the user to do so
    //     // For now, we'll use the expired series, but you should implement a proper solution
    //     $challanSeries = $series->series_number;
    // } else {
    //     $challanSeries = $challanSeries == 'Not Assigned' ? $series->series_number : $challanSeries;
    // }

    // $latestSeriesNum = Challan::where('challan_series', $challanSeries)
    //     ->where('sender_id', $userId)
    //     ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    // $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;


    public $showSeriesExpirationModal = false;

    public $tempUserData = [];


    public function selectUser($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails)
    {
        try {
            $this->userSelected = true;
            DB::beginTransaction();

            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $series = PanelSeriesNumber::where('user_id', $userId)
                ->where('default', "1")
                ->where('panel_id', '1')
                ->first();

            if (!$series) {
                throw new \Exception('No default series number found. Please add a default series number.');
            }

            $currentDate = now();
            $validTill = Carbon::parse($series->valid_till);

            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $challanSeries;

                // Store the user data temporarily
                $this->tempUserData = [
                    'challanSeries' => $challanSeries,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'receiver' => $receiver,
                    'selectedUserDetails' => $selectedUserDetails,
                ];

                DB::rollBack();
                return;
            }

            // If the series is not expired, proceed with the normal flow
            $this->seriesNumber($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails, $userId, $series);

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


    public function seriesNumber($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails, $userId, $series)
    {
        if ($challanSeries == 'Not Assigned') {
            if ($series == null) {
                throw new \Exception('Please add one default Series number');
            }
            $challanSeries = $series->series_number;
        } else {
            // Check if the selected series is valid and not expired
            $selectedSeries = PanelSeriesNumber::where('user_id', $userId)
                ->where('series_number', $challanSeries)
                ->where('panel_id', '1')
                ->first();

            if (!$selectedSeries) {
                throw new \Exception('Invalid series number selected.');
            }

            $validTill = Carbon::parse($selectedSeries->valid_till);
            if ($validTill->isPast()) {
                $this->errorMessage = 'Your series number has expired. Please choose an action:';
                $this->showSeriesExpirationModal = true;
                $this->expiredSeriesNumber = $challanSeries;
                // Store the user data temporarily
                $this->tempUserData = [
                    'challanSeries' => $challanSeries,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'email' => $email,
                    'phone' => $phone,
                    'gst' => $gst,
                    'receiver' => $receiver,
                    'selectedUserDetails' => $selectedUserDetails,
                ];
                // dd($challanSeries, $address, $city, $state, $pincode, $email, $phone, $gst, $receiver, $selectedUserDetails, $userId, $series);
                return;
            }
        }

        $latestSeriesNum = Challan::where('challan_series', $challanSeries)
            ->where('sender_id', $userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

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

        $decodedUserDetails = json_decode($selectedUserDetails);
        $this->receiverName = $this->selectedUser['receiver_name'];
        $this->createChallanRequest['challan_series'] = $challanSeries;
        $this->createChallanRequest['series_num'] = $seriesNum;
        $this->createChallanRequest['receiver'] = $receiver;
        $this->createChallanRequest['receiver_id'] = $decodedUserDetails->receiver_user_id;
        $this->createChallanRequest['feature_id'] = 1;
        $this->selectedUserDetails = $decodedUserDetails->user->details;
    }

    public function useDefaultSeries()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $defaultSeries = PanelSeriesNumber::where('user_id', $userId)
            ->where('default', "1")
            ->where('panel_id', '1')
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
        $this->selectedUser['challanSeries'] = $defaultSeries->series_number;
        $this->createChallanRequest['challan_series'] = $defaultSeries->series_number;

        // Recalculate the series number
        $latestSeriesNum = Challan::where('challan_series', $defaultSeries->series_number)
            ->where('sender_id', $userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;
        $this->selectedUser['seriesNumber'] = $seriesNum;
        $this->createChallanRequest['series_num'] = $seriesNum;

        $this->showSeriesExpirationModal = false;
        $this->errorMessage = null;
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
                ->where('panel_id', '1')
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
                $this->seriesNumber(
                    $defaultSeries->series_number,
                    $this->tempUserData['address'],
                    $this->tempUserData['city'],
                    $this->tempUserData['state'],
                    $this->tempUserData['pincode'],
                    $this->tempUserData['email'],
                    $this->tempUserData['phone'],
                    $this->tempUserData['gst'],
                    $this->tempUserData['receiver'],
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
        return redirect()->route('sender', ['template' => 'challan_series_no']);
        $this->showSeriesExpirationModal = false;
        $this->showUpdateSeriesModal = true;
    }

    public function closeUpdateSeriesModal()
    {
        $this->showSeriesExpirationModal = false;
        $this->showUpdateSeriesModal = true;
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

    public function sendChallan($id)
    {
        // dd( $id);
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
            $this->reset(['statusCode', 'message', 'errorMessage']);
            // Assuming $this->persistedTemplate holds the template name
         } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
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

        $ChallanController = new ChallanController;

        $response = $ChallanController->challanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
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


    public $draft = true;
    public $dataChanged = false;
    public $saved = false;
    public $challanSaved = false;

    public function saveRows($requestData)
    {
        // dd($this->createChallanRequest['receiver_id']);
        $request = request();
        // Update the createChallanRequest with the new data
        $this->createChallanRequest['order_details'] = $requestData['order_details'];
        $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
        $this->createChallanRequest['total'] = $requestData['total'];
        $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->createChallanRequest['series_num'] = $this->selectedUser['seriesNumber'] ?? null;
        // dd($request->all());

        // dd($this->createChallanRequest);

        // if($this->updateForm == false)
        // {
        //     $request->merge($this->addReceiverData);
        //     // dd($request);
        //     $receiverId = null;
        //     $receiverPhone = null;
        //     $result = null;

        //     if($request->phone || $request->email){
        //         $usersQuery = User::query();

        //         if (!empty($request->phone)) {
        //             $usersQuery->where('phone', $request->phone);
        //         }

        //         if (!empty($request->email)) {
        //             $usersQuery->orWhere('email', $request->email);
        //         }

        //         $user = $usersQuery->first();
        //         // dd($user);
        //         if($user){
        //         // If user exists, get the special ID and call addReceiver
        //         $specialId = $user->special_id; // Assuming 'special_id' is the column name
        //         // dd($specialId);
        //         $request->merge(['receiver_special_id' => $specialId]);
        //         //    dd($request);
        //         $ReceiversController = new ReceiversController;

        //         $response = $ReceiversController->addReceiver($request);

        //         $result = $response->getData();
        //         } else {
        //             //  dd($request);

        //         $ReceiversController = new ReceiversController;

        //         $response = $ReceiversController->addManualReceiver($request);

        //         $result = $response->getData();
        //         // dd($result);
        //         }
        //     }


        //     $series = PanelSeriesNumber::where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
        //         ->where('default', "1")
        //         ->where('panel_id', '1')
        //         ->first();
        //         $challanSeries = $series->series_number;
        //     $latestSeriesNum = Challan::where('challan_series', $challanSeries)
        //             ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)

        //             ->get();
        //                 // Get the maximum 'series_num' from the collection
        //                 $maxSeriesNum = $latestSeriesNum->max('series_num');
        //             // }
        //         // Increment the latestSeriesNum for the new challan
        //         $seriesNum = $maxSeriesNum ? $maxSeriesNum + 1 : 1;

        //         $this->createChallanRequest['challan_series'] = $challanSeries;
        //         $this->createChallanRequest['series_num'] = $seriesNum;
        //         if (isset($result->receiver->receiver_name)) {
        //             $this->createChallanRequest['receiver'] = $result->receiver->receiver_name;
        //             $this->createChallanRequest['receiver_id'] = $result->receiver->receiver_user_id;
        //         } elseif (isset($request->receiver_name)) {
        //             $this->createChallanRequest['receiver'] = $request->receiver_name;
        //             $this->createChallanRequest['receiver_id'] = null;
        //             $this->sendButtonDisabled = false;
        //         } else {
        //             $this->createChallanRequest['receiver'] = 'Others';
        //             $this->createChallanRequest['receiver_id'] = null;

        //         }

        //         // $this->createChallanRequest['receiver_detail_id'] = json_decode($selectedUserDetails)->details[0]->id;
        //         // $this->createChallanRequest['user_detail_id'] = json_decode($selectedUserDetails)->user->details->id;
        //         $this->createChallanRequest['feature_id'] = 1;

        // }

        $this->createChallanRequest['calculate_tax'] = $this->calculateTax;
        foreach ($this->createChallanRequest['order_details'] as $index => $orderDetail) {
            $this->createChallanRequest['order_details'][$index]['discount'] = $requestData['discount_total_amount'];
        }
        // dd($this->createChallanRequest);
        $request->merge($this->createChallanRequest);


        // if ($this->updateForm == false) {
        //     $userId = Auth::getDefaultDriver() == 'team-user'
        //         ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
        //         : Auth::guard(Auth::getDefaultDriver())->user()->id;

        //     $series = PanelSeriesNumber::where('user_id', $userId)
        //         ->where('default', "1")
        //         ->where('panel_id', '1')
        //         ->select('series_number')
        //         ->first();

        //     if ($series) {
        //         $challanSeries = $series->series_number;
        //         $latestSeriesNum = Challan::where('challan_series', $challanSeries)
        //             ->where('sender_id', $userId)
        //             ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        //         $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

        //         // Update both the request and createChallanRequest
        //         $request->merge([
        //             'challan_series' => $challanSeries,
        //             'series_num' => $seriesNum
        //         ]);
        //         $this->createChallanRequest['challan_series'] = $challanSeries;
        //         $this->createChallanRequest['series_num'] = $seriesNum;
        //     } else {
        //         // Handle the case where no default series is found
        //         $this->addError('series', 'No default Series number found for this user. Please add one.');
        //         return;
        //     }
        // }

        // Remove the dd() call to allow the code to continue execution
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
        // dd($errors);
        // If there are errors, return early
        if ($errors) {
            return;
        }

        // dd($request->all());
        // Create instances of necessary classes
        $ChallanController = new ChallanController;
        $request->merge(['challanId' => $this->challanId]);
        $response = $ChallanController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // dd($result);
        if ($result->status_code === 200) {
            $this->challanId = $result->challan_id;
            // $this->challanSave = $result->message;
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            $this->saved = true;
            $this->challanSaved = true;
            $this->inputsResponseDisabled = false; // Adjust the condition as needed
            $this->inputsDisabled = true;
            if($this->createChallanRequest['receiver_id'] == null){
                // $this->successMessage = $result->message;
                return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
            }
            $this->challanId = $result->challan_id;

            $this->reset(['statusCode', 'message', 'errorMessage']);
        } elseif ($result->status_code === 422 && $result->message === "Feature usage limit is over or expired.") {
            $this->errorMessage = $result->message;
            $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
        } else {
            $this->errorMessage = is_string($result->errors) ? $result->errors : json_encode($result->errors);
            $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
        }

    }
    public $featureUsageLimitExceeded = false;


    public function draftRows($requestData)
    {
        // dd($requestData);
        $request = request();
        // Update the createChallanRequest with the new data
        $this->createChallanRequest['order_details'] = $requestData['order_details'];
        $this->createChallanRequest['total_qty'] = $requestData['total_qty'];
        $this->createChallanRequest['total'] = $requestData['total'];
        $this->createChallanRequest['discount_total_amount'] = $requestData['discount_total_amount'];
        $this->createChallanRequest['series_num'] =  null;


        if($this->updateForm == false)
        {
            $request->merge($this->addReceiverData);
            // dd($request, $this->existingUser);
            $receiverId = null;
            $receiverPhone = null;
            $result = null;

            if ($this->existingUser) {
                // Use the existing user's data
                $this->createChallanRequest['receiver_id'] = $this->existingUser->id;
                $this->createChallanRequest['receiver'] = $request->receiver_name ?? $this->existingUser->name;
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
                        $this->createChallanRequest['receiver_id'] = $user->id;
                        $this->createChallanRequest['receiver'] = $request->receiver_name;
                    } else {
                        // Handle new user scenario
                        $ReceiversController = new ReceiversController;
                        $response = $ReceiversController->addManualReceiver($request);
                        $result = $response->getData();
                        $this->createChallanRequest['receiver_id'] = $result->receiver->id;
                        $this->createChallanRequest['receiver'] = $result->receiver->receiver_name;
                    }
                } else {
                    // No email or phone provided, set receiver_id to null and use the provided receiver_name
                    $this->createChallanRequest['receiver_id'] = null;
                    $this->createChallanRequest['receiver'] = $request->receiver_name ?: 'Default';
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
                // $this->createChallanRequest['series_num'] = $seriesNum;
                // if (isset($result->receiver->receiver_name)) {
                //     $this->createChallanRequest['receiver'] = $result->receiver->receiver_name;
                //     $this->createChallanRequest['receiver_id'] = $result->receiver->receiver_user_id;
                // } elseif (isset($request->receiver_name)) {
                //     $this->createChallanRequest['receiver'] = $request->receiver_name;
                //     $this->createChallanRequest['receiver_id'] = null;
                //     $this->sendButtonDisabled = false;
                // } else {
                //     $this->createChallanRequest['receiver'] = 'Others';
                //     $this->createChallanRequest['receiver_id'] = null;

                // }

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
        $ChallanController = new ChallanController;

        $response = $ChallanController->store($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // Remove this line
        // dd($result);

        if ($result->status_code === 200) {
            $this->challanId = $result->challan_id;
            $this->emit('itemIdUpdated', $this->challanId);
            $this->inputsDisabled = false;
            $this->inputsResponseDisabled = true;
            $this->dispatchBrowserEvent('show-success-message', ['Challan saved as draft']);
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } elseif ($result->status_code === 422 && $result->message === "Feature usage limit is over or expired.") {
            $this->errorMessage = $result->message;
            $this->featureUsageLimitExceeded = true;
            $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
        } else {
            $this->errorMessage = is_string($result->errors) ? $result->errors : json_encode($result->errors);
            $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
        }
    }
    public $existingUser = null;


    // Validate Phone Number
    public function updatedAddReceiverDataPhone($value)
    {
        $this->resetErrorBag('addReceiverData.phone');
        $this->existingUser = null;

        if (strlen($value) === 10) {
            $user = User::where('phone', $value)->first();
            if ($user) {
                $this->existingUser = $user;
                $this->addError('addReceiverData.phone', 'This phone number already exists in our records.');
            } else {
                $this->validateOnly('addReceiverData.phone', [
                    'addReceiverData.phone' => ['required', 'digits:10'],
                ]);
            }
        } elseif (!empty($value)) {
            $this->addError('addReceiverData.phone', 'Phone number must be 10 digits.');
        }
    }


    public function useExistingUserDetails()
    {
        if ($this->existingUser) {
            $this->addReceiverData = [
                'receiver_name' => $this->existingUser->name,
                'company_name' => $this->existingUser->company_name,
                'email' => $this->existingUser->email,
                'address' => $this->existingUser->address,
                'pincode' => $this->existingUser->pincode,
                'state' => $this->existingUser->state,
                'city' => $this->existingUser->city,
                'phone' => $this->existingUser->phone,
                'organisation_type' => $this->existingUser->organisation_type,
                'receiver_special_id' => $this->existingUser->receiver_special_id,
                'gst_number' => $this->existingUser->gst_number,
            ];
            $this->existingUser = null;
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

        return view('livewire.sender.screens.test-challan-component', [
            'stocks' => $products,
        ]);
    }

}
