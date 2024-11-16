<?php

namespace App\Http\Livewire\Sender\Screens;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TagsTable;
use App\Models\ChallanStatus;
use Livewire\WithPagination;
use App\Models\ReceiverDetails;
use App\Models\ReturnChallan;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\TeamUser;
use App\Models\User;
use ZipArchive;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Models\Challan;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

use Livewire\Component;

class ReceivedChallan extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode,$sentMessage, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$isMobile , $variable , $value , $receiver_id, $status, $state, $from, $to, $challan_series;
    public $team_user_ids = [];
    public $team_user_names = [];
    public $itemId = [];
    public $isOpen = false;
    public $bulkmodalHeading;
    public $BulkModalAction;
    public $bulkSubHeading;
    public $bulkActions;
    public $isLoading = true;
    public $modalHeading;
    public $modalButtonText;
    public $admin_ids = [];
    public $modalAction;
    public $openSearchModal = false;
    public $openPaymentStatusModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    public $tagExists = false;
    public $selectedTags = [];
    public $selectedDeliveryStatus = [];
    public $columnName = [];
    public $searchTerm = '';
    public $tagName = '';
    public $sfpModal = false;
    public $selectedProducts = [];
    public $singleSelection = null;
    public $selectAll = false;

    protected $listeners = [
        'actions' => 'handleMessage',
    ];

    public function handleMessage($message)
    {
        // Show the success message
        $this->dispatchBrowserEvent('show-success-message', [$message]);

        // Update the table data (you can call a method to refresh the data)
        $this->render();
    }

    public function loadData()
    {
        $this->isLoading = false;
    }

    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }

    public function mount(){
        $request = request();
        $template = request('template', 'index');

        if (view()->exists('components.panel.sender.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            $this->isMobile = isMobileUserAgent($request->header('User-Agent'));

            $response = app(UserAuthController::class)->user_details($request)->getData();

            if ($response->success == "true") {
                $this->mainUser = json_encode($response->user);
                $this->UserDetails = $response->user->plans;
                $this->reset(['errorMessage']);
            } else {
                $this->errorMessage = $response->errors ?? [[$response->message]];
            }

            $query = app(TeamUserController::class)->index();
            $status = $query->getStatusCode();
            $queryData = $query->getData();

            if ($status === 200) {
                $this->teamMembers = $queryData->data;
            } else {
                $this->errorMessage = $queryData->errors;
                $this->reset(['status', 'successMessage']);
            }
            $columnFilterDataset = [
                'feature_id' => 3,
            ];
            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Challan No', 'Creator',  'Date',  'Qty', 'Amount', 'Status', 'SFP', 'Comment', 'Tags' ];
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
        // dd($template, $activeFeature);
        $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $itemCode = false;

    public function openModal($itemId, $action)
    {
        // dd($itemId, $action);
        // Load the ReturnChallan with orderDetails
        $returnChallan = ReturnChallan::with('orderDetails')->find($itemId);
        // Check if any item_code exists in the order details
        $this->itemCode = $returnChallan->orderDetails->isNotEmpty();
        // dd($this->itemCode);
        $this->itemId = $itemId;
        $this->isOpen = true;

        if ($action == 'addComment') {
            $this->modalHeading = 'Add Return Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        } elseif ($action == 'accept'){
            $this->modalHeading = 'Accept Return Challan';
            $this->modalButtonText = 'Accept';
            $this->modalAction = 'accept';
        } elseif ($action == 'reject'){
            $this->modalHeading = 'Reject Return Challan';
            $this->modalButtonText = 'Reject';
            $this->modalAction = 'reject';
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction']);
    }

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

    public function sfpReturnChallan()
    {

        $request = request();
        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
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
        $this->sfpModal = false;
        session()->flash('message', 'Challan SFP successfully.');    }

    public function addComment()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'receiver'=> 'receiver']);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->addComment($request, $this->itemId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('sentMessage', ['type' => 'success', 'content' => $result->message]);
            $this->reset(['statusCode', 'status_comment']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            session()->flash('sentMessage', ['type' => 'error', 'content' => json_encode($result->errors)]);

            $this->reset(['statusCode', 'status_comment']);
        }
        $this->closeModal();
    }



    public $add_stock_back = false;
    public function accept()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'add_stock_back' => $this->add_stock_back]);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->accept($request, $this->itemId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('sentMessage', ['type' => 'success', 'content' => $result->message]);
            $this->reset(['statusCode', 'status_comment']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            session()->flash('sentMessage', ['type' => 'error', 'content' => json_encode($result->errors)]);

            $this->reset(['statusCode', 'status_comment']);
        }
        $this->closeModal();
    }
    public function reject()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->reject($request, $this->itemId);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('sentMessage', ['type' => 'success', 'content' => $result->message]);
            $this->reset(['statusCode', 'status_comment']);
        } else {
            $this->errorMessage = json_encode($result->errors);
            session()->flash('sentMessage', ['type' => 'error', 'content' => json_encode($result->errors)]);

            $this->reset(['statusCode', 'status_comment']);
        }
        $this->closeModal();
    }


    public function createTag()
    {
        // dd($this->searchTerm);
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $tag = TagsTable::create([
                'name' => $this->searchTerm,
                'user_id' => $userId,
                'panel_id' => 1,
                'table_id' => 2,
            ]);
            $this->tagExists = true; // Update tagExists since the tag is now created
            // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
            $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
            // $this->saveTags();
            $this->render();
            $this->searchTerm = ''; // Clear the search term

    }
    public function saveTags()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


        $selectedTagIds = $this->selectedTags;
        // Assuming you have a GoodsReceipt model instance you're working with
        $goodsReceipt = ReturnChallan::find($this->itemId); // Example: Find the GoodsReceipt by its ID

        // Prepare the additional pivot data
        $pivotData = [];
        foreach ($selectedTagIds as $tagId) {
            $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 1, 'table_id' => 2]; // Example: Set the user_id and panel_id
        }

        // Attach the selected tags to the GoodsReceipt with additional pivot data
        $goodsReceipt->tableTags()->sync($pivotData);
        $this->closeTagModal();
        // Add any additional logic here, such as flashing a success message to the session
        session()->flash('message', 'Tags saved successfully.');

        // Optionally, redirect or perform other actions
    }

    public function assignTags($id)
    {
        $challan = ReturnChallan::find($id);
        $challan->tags()->sync($this->selectedTags);
        $this->selectedTags = [];
        $this->successMessage = 'Tags assigned successfully';
        $template = 'received_challan';
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('receiver', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $sortField = null;
    public $sortDirection = null;


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // Reset pagination to the first page when sorting
        $this->resetPage();
    }


    public function render()
    {
        $request = request();
        if ($this->challan_series != null) {
            // dump($this->challan_series);
            $request->merge(['challan_series' => $this->challan_series]);
        }
        if ($this->receiver_id != null) {
            // dump($this->receiver_id);
            $request->merge(['receiver_id' => $this->receiver_id]);
        }
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }

        // Filter by state in ReceiverDetails
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }

        // Filter by date range
        if ($this->from != null || $this->to != null) {
            $request->merge([
                'from_date' => $this->from,
                'to_date' => $this->to,
            ]);
        }
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = ReturnChallan::query()->where('receiver_id', $userId);
        $combinedValues = [];
        // dd($request->has('sender_id'), $request->has('receiver_id'));
        // if (!$request->has('sender_id') && !$request->has('receiver_id')) {


            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctReturnChallanSeriesNum = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // dd($distinctReturnChallanSeriesNum);
            $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
            $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');


            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');

            // Loop through each element of $distinctChallanSeries
            foreach ($distinctReturnChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctReturnChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }

        // }
        // Loop through each element of $distinctChallanSeries
        foreach ($distinctReturnChallanSeries as $series) {
            // Loop through each element of $distinctChallanSeriesNum
            foreach ($distinctReturnChallanSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
        }
        if ($request->has('challan_series')) {
            $searchTerm = $request->challan_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('challan_series', $series)
                      ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }
         // Filter by date
         if ($request->from_date && $request->to_date) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Filter by receiver_id
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
             // Fetch the distinct filter values for ReturnChallan table (for this user)
             $distinctReturnChallanSeries = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('challan_series');
             $distinctSenderIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('sender_id');
             $distinctReceiverIds = ReturnChallan::where('sender_id', $request->receiver_id)->distinct()->pluck('receiver_id');
             // $distinctStatuses = Status::distinct()->pluck('status');

             // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
             $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('state');

             $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                 $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
             })->distinct()->pluck('city');
        }

        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->whereHas('statuses', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }


        // Add any other desired filters

        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
        $this->allItemIds = $query->pluck('id')->toArray();

       // Apply sorting
        if ($this->sortField) {
            // Sort by total_qty as an integer
            if ($this->sortField === 'total_qty') {
                $query->orderByRaw('CAST(total_qty AS UNSIGNED) ' . $this->sortDirection);
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }

        $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfpBy', 'tableTags'])->select('return_challans.*')->latest()->paginate(50);

        // dd($challans );
        $tags = TagsTable::where('panel_id', 1)
        ->where('user_id', $userId)
        ->where('table_id', 2)
        ->where(function($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->paginate(10);

        $allTags = TagsTable::where('panel_id', 1)
                ->where('user_id', $userId)
                ->where('table_id', 2)
                ->get();

        $nonMatchingTags = $allTags->filter(function($tag) {
        return stripos($tag->name, $this->searchTerm) === false;
        });

        $isSearchTermMatched = $allTags->contains(function($tag) {
            return stripos($tag->name, $this->searchTerm) !== false;
        });
        $notifications = Notification::where('user_id', $userId)
        ->where('template_name', 'received_challan')
        ->whereNull('read_at')
        ->get();

         // Mark these notifications as read
        foreach ($notifications as $notification) {
            $notification->update(['read_at' => now()]);
        }
        // dd($challans);
        return view('livewire.sender.screens.received-challan',compact('returnChallans'))
        ->with(['distinctReturnChallanSeries'=> $distinctReturnChallanSeries,
        'tags' => $tags,
        'allTagss' => $allTags,
        'isSearchTermMatched' => $isSearchTermMatched,
        'merged_challan_series' => $combinedValues,
        'sender_id' => $distinctSenderIds,
        'receiver_ids' => $distinctReceiverIds,
        'state' => $distinctStates,
        'city' => $distinctCities,
        'series_num' => $distinctReturnChallanSeriesNum]);
    }
}
