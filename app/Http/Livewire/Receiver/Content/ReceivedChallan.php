<?php

namespace App\Http\Livewire\Receiver\Content;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Challan;
use App\Models\Receiver;
use App\Models\TagsTable;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\Notification;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Validation\Rule;
use ZipArchive;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
class ReceivedChallan extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status, $state, $from, $to, $challan_series;
    public $team_user_ids = [];
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;
    public $tags;
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
    public $selectedProducts = [];

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


    public function mount()
    {
        $request = request();
        $template = request('template', 'index');

        if (view()->exists('components.panel.receiver.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            $this->isMobile = isMobileUserAgent($request->header('User-Agent'));
            $UserResource = new UserAuthController;
            $response = $UserResource->user_details($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->mainUser = json_encode($response->user);
                $this->UserDetails = $response->user->plans;
                $this->reset(['errorMessage']);
            } else {
                $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
            }
            $query = new TeamUserController;
            $query = $query->index();
            $status = $query->getStatusCode();
            $queryData = $query->getData();
            if ($status === 200) {
                $this->teamMembers = $queryData->data;
            } else {
                $this->errorMessage = json_encode($queryData->errors);
                $this->reset(['status', 'successMessage']);
            }

            $columnFilterDataset = [
                'feature_id' => '9',
                'panel_id' => '2',
            ];
            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Challan No',  'Date', 'Creator','Receiver', 'Qty', 'Amount', 'State', 'Status', 'SFP', 'Comment', 'Tags'];

        }
    }

    public function innerFeatureRedirect($template, $activeFeature)
    {
        // dd($template, $activeFeature);
        $panel_id = 2;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems);
            $this->panel = $item->panel;
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
      public function handleFeatureRoute($template, $activeFeature)
      {

          $this->persistedTemplate = view()->exists('components.panel.receiver.' . $template) ? $template : 'index';
          $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . $template) ? $activeFeature : null;
          $this->savePersistedTemplate($template, $activeFeature);

          return redirect()->route('receiver', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);


          // Emit the 'featureRoute' event with two separate parameters
          // $this->emit('featureRoute', $template, $activeFeature);
      }

      public function updateVariable($variable, $value)
      {
          $this->{$variable} = $value;
      //    dd($variable, $value);
      }

      public function resetDates()
      {
          $this->from = null;
          $this->to = null;
          $this->emit('dates-reset');
      }

      public function openModal($itemId, $action)
    {
        // dd($itemId, $action);
        $this->itemId = $itemId;
        $this->isOpen = true;

        if ($action == 'addComment') {
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        } elseif ($action == 'accept'){
            $this->modalHeading = 'Accept Challan';
            $this->modalButtonText = 'Accept';
            $this->modalAction = 'accept';
        } elseif ($action == 'reject'){
            $this->modalHeading = 'Reject Challan';
            $this->modalButtonText = 'Reject';
            $this->modalAction = 'reject';
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction']);
    }

    public function accept()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ChallanController = new ChallanController;
        $response = $ChallanController->accept($request, $this->itemId);
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
        $ChallanController = new ChallanController;
        $response = $ChallanController->reject($request, $this->itemId);
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



      public function addComment()
      {
          $request = request();
          $request->merge(['status_comment' => $this->status_comment, 'receiver' => 'receiver']);

          $ChallanController = new ChallanController;
          $response = $ChallanController->addComment($request, $this->itemId);
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
            // $this->innerFeatureRedirect('received_return_challan', '8');
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // $request = $request();
        return redirect()->route('receiver', ['template' => 'received_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
     }

    //  Tags Add and Remove
    public function tagModal($itemId, $action)
    {
        if($action == 'addTags'){

            $this->itemId = $itemId;
            $this->selectedTags = $this->getTagsForChallan($itemId);
            $this->openSearchModal = true;
            $this->searchModalHeading = 'Add Tag';
            $this->searchModalButtonText = 'Add';
            $this->searchModalAction = 'saveTags';
        }
        // $this->closeModal();
    }
    public function getTagsForChallan($itemId)
    {
        // Fetch the Challan instance by its ID
        $challan = Challan::find($itemId);
        $challan = $challan->load('tableTags');

        // Check if the Challan instance exists
        if ($challan) {
            // Use the tags relationship to get the associated tags and pluck their IDs
            return $challan->tableTags()->pluck('tags_table.id')->toArray();
        }

        // Return an empty array if the Challan instance does not exist
        return [];
    }
    public function closeTagModal()
    {
        // dd('close');
        $this->openSearchModal = false;
        $this->openPaymentStatusModal = false;

        $this->reset(['selectedTags', 'searchTerm' ]);
    }

    public function createTag()
    {
        // dd($this->searchTerm);
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $tag = TagsTable::create([
                'name' => $this->searchTerm,
                'user_id' => $userId,
                'panel_id' => 2,
                'table_id' => 4,
            ]);
            $this->tagExists = true; // Update tagExists since the tag is now created
            // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
            $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
            // $this->saveTags();
            $this->render();
            $this->searchTerm = ''; // Clear the search term

    }
    // public function saveTags()
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


    //     $selectedTagIds = $this->selectedTags;
    //     // Assuming you have a GoodsReceipt model instance you're working with
    //     $goodsReceipt = Challan::find($this->itemId); // Example: Find the GoodsReceipt by its ID

    //     // Prepare the additional pivot data
    //     $pivotData = [];
    //     foreach ($selectedTagIds as $tagId) {
    //         $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 2, 'table_id' => 4]; // Example: Set the user_id and panel_id
    //     }

    //     // Attach the selected tags to the GoodsReceipt with additional pivot data
    //     $goodsReceipt->tableTags()->sync($pivotData);
    //     $this->closeTagModal();
    //     // Add any additional logic here, such as flashing a success message to the session
    //     session()->flash('message', 'Tags saved successfully.');

    //     // Optionally, redirect or perform other actions
    // }
    public function saveTags()
    {
        if ($this->selectedProducts) {
            // dd($this->selectedProducts, $this->itemId)s;
            $this->itemId = $this->selectedProducts;
            $this->selectedProducts = [];
        }

        // dd($this->itemId, $this->selectedTags, $this->selectedProducts);
        $this->itemId = is_array($this->itemId) ? $this->itemId : [$this->itemId];
        // dd($this->itemId);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $selectedTagIds = $this->selectedTags;
        // dd($selectedTagIds);
        foreach ($this->itemId as $itemId) {
            $goodsReceipt = Challan::find($itemId); // Find each Challan by its ID

            $pivotData = [];
            foreach ($selectedTagIds as $tagId) {
                $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 2, 'table_id' => 4];
            }

            // Attach the selected tags to each GoodsReceipt with additional pivot data
            if ($goodsReceipt) {
                $goodsReceipt->tableTags()->sync($pivotData);
                // dd($goodsReceipt->tableTags());
            }
        }

        $this->closeTagModal();
        session()->flash('message', 'Tags saved successfully.');
    }

    public function assignTags($id)
    {
        $challan = Challan::find($id);
        $challan->tags()->sync($this->selectedTags);
        $this->selectedTags = [];
        $this->successMessage = 'Tags assigned successfully';
        $template = 'received_return_challan';
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
        // $request = new Request(['page' => $page]);
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
        // dd($request->all());

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query();
        $combinedValues = [];


        if ($request->challan_series != null) {

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


        // Filter by sender_id
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

         // Filter by date
         if ($request->from_date && $request->to_date) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by receiver_id
        // dd($request);

            // dd('adf');
            $query->where('receiver_id', $userId);

            // Fetch the distinct filter values for Challan table (for this user)
            $distinctChallanSeries = $query->distinct()->pluck('challan_series');
            $distinctChallanSeriesNum = $query->distinct()->pluck('series_num');

            foreach ($distinctChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }
            // $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->get();
            $distinctSenderIds = Challan::where('receiver_id', $userId)->distinct()->pluck('sender', 'receiver_id');
            // dd($distinctSenderIds );
            $distinctReceiverIds = Challan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');

            $distinctStatuses = ChallanStatus::distinct()->pluck('status');

            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
                $query->select('id')->from('receivers')->where('user_id', $userId);
            })->distinct()->pluck('city');

        // Fetch the distinct filter values for status

            // Filter by status
            if ($request->has('status')) {
                $query->whereHas('statuses', function ($q) use ($request) {
                    $q->latest()->where('status', $request->status);
                });
            }
            // Filter by status


            if ($request->has('status')) {
                // Subquery that gets the maximum created_at for each challan_id
                $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('challan_id');

                // Main query that joins the subquery with the challan_statuses table and filters by status
                $query->joinSub($subquery, 'latest_statuses', function ($join) {
                    $join->on('challans.id', '=', 'latest_statuses.challan_id');
                })
                ->join('challan_statuses', function ($join) use ($request) {
                    $join->on('challans.id', '=', 'challan_statuses.challan_id')
                        ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                        ->where('challan_statuses.status', '=', $request->status);
                });
            }
        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
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

        $perPage = $request->perPage ?? 100;
            $page = $request->page ?? 1;
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


            $challans = $query
                ->with([ 'statuses', 'orderDetails',  'sfp', 'tableTags'])
                ->select('challans.*')
                ->latest()
                ->paginate(50);

                // $tags = TagsTable::where('panel_id', 2)
                // ->where('user_id', $userId)
                // ->where('table_id', 4)
                // ->where(function($query) {
                //     $query->where('name', 'like', '%' . $this->searchTerm . '%');
                // })
                // ->paginate(10);

                // $allTags = TagsTable::where('panel_id', 2)
                //         ->where('user_id', $userId)
                //         ->where('table_id', 4)
                //         ->get();

                         // Retrieve tags (Ensure this returns a collection)
                $allTagss = TagsTable::where('panel_id', 2)
                ->where('user_id', $userId)
                ->where('table_id', 4)
                // ->select('id', 'name')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%');
                })
                ->pluck('id', 'name'); // Changed from paginate(10) to get() to ensure it's a collection
                // dd($allTags);
                $tags = TagsTable::where('panel_id', 2)
                    ->where('user_id', $userId)
                    ->where('table_id', 4)
                    ->where(function($query) {
                        $query->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->paginate(10);

                $allTags = TagsTable::where('panel_id', 2)
                    ->where('user_id', $userId)
                    ->where('table_id', 4)
                    ->get();

                $nonMatchingTags = $allTags->filter(function($tag) {
                return stripos($tag->name, $this->searchTerm) === false;
                });

                $isSearchTermMatched = $allTags->contains(function($tag) {
                    return stripos($tag->name, $this->searchTerm) !== false;
                });

                $notifications = Notification::where('user_id', $userId)
                ->where('template_name', 'received_return_challan')
                ->whereNull('read_at')
                ->get();

                // Mark these notifications as read
                foreach ($notifications as $notification) {
                $notification->update(['read_at' => now()]);
                }
                return view('livewire.receiver.content.received-challan', compact('challans' ))->with(['distinctChallanSeries' =>  $distinctChallanSeries,
                // 'challan_series' =>  $combinedValues,
                'tagss' => $tags,
                'allTagss' => $allTags,
                'isSearchTermMatched' => $isSearchTermMatched,
                'merged_challan_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_ids' => $distinctReceiverIds,
                'state' => $distinctStates,
                'city' => $distinctCities,
                'status' => $distinctStatuses,
                'series_num' => $distinctChallanSeriesNum]);
            }
}
