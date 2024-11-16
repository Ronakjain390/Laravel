<?php

namespace App\Http\Livewire\Sender\Screens;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\ChallanStatus;
use App\Models\Challan;
use App\Models\Team;
use App\Models\Receiver;
use App\Models\TagsTable;
use App\Models\ReceiverDetails;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

use App\Models\ChallanDelivery;

class DeletedSentChallans extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode, $message, $sentMessage, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status,$tag, $state, $from, $to, $signature, $attributes, $columnId;
    public $isLoading = true;
    public $challan_series;
    public $tags;
    public $searchTerm = '';
    public $searchQuery = '';
    public $bulksearchQuery = '';
    public $challanId;
    public $availableTags;
    public $isOpen = false;
    public $BulkisOpen = false;
    public $modalHeading;
    public $bulkmodalHeading;
    public $modalButtonText;
    public $modalAction;
    public $BulkModalAction;
    public $bulkSubHeading;
    public $bulkActions;
    public $availableDeliveryStatus;
    public $bulkAvailableEntities;
    public $sfpModal = false;
    public $openSearchModal = false;
    public $openPaymentStatusModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    public $tagExists = false;
    public $sortField = null;
    public $sortDirection = null;

    public function loadData()
    {
        $this->isLoading = false;
    }

    public function mount()
    {
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
                $this->user = json_encode($response->user);
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

            // if (!Cache::has('ColumnDisplayNames')) {
                $this->ColumnDisplayNames = ['Challan No',  'Date', 'Creator', 'Receiver',  'Qty', 'Amount', 'State', 'Status'];
                if(Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->payment_status){
                    $this->ColumnDisplayNames[] = 'Payment Status';
                }

                $this->ColumnDisplayNames[] = 'SFP';
                $this->ColumnDisplayNames[] = 'Comment';

                if (Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->tags) {
                    $this->ColumnDisplayNames[] = 'Tags';
                }

                $this->ColumnDisplayNames = $this->ColumnDisplayNames;
            // }
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }

        // Reset pagination to the first page when sorting
        // $this->resetPage();
    }


    public function render()
    {
        $request = request();

        $filters = [
            'challan_series' => $this->challan_series,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
            'tag' => $this->tags, // Add the tag filter
        ];

        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->onlyTrashed(); // Include soft deleted records
        $combinedValues = [];

        $query->where('sender_id', $userId);
        $distinctChallanSeries = $query->distinct()->pluck('challan_series');
        $distinctChallanSeriesNum = $query->distinct()->pluck('series_num');

        foreach ($distinctChallanSeries as $series) {
            foreach ($distinctChallanSeriesNum as $num) {
                $combinedValues[] = $series . '-' . $num;
            }
        }

        $distinctSenderIds = $query->distinct()->pluck('sender', 'sender_id');
        $distinctReceiverIds = $query->distinct()->pluck('receiver', 'receiver_id');
        $distinctStatuses = ChallanStatus::distinct()->pluck('status');

        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        if ($request->challan_series != null) {
            $searchTerm = $request->challan_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('challan_series', $series)
                    ->where('series_num', $num);
            }
        }

        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }
        if ($request->has('receiver_id')) {
            $query->where('receiver_id', $request->receiver_id);
        }
        if ($request->from_date && $request->to_date) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('challan_date', [$from, $to]);
        }

        if ($request->has('status')) {
            $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                        ->groupBy('challan_id');

            $query->joinSub($subquery, 'latest_statuses', function ($join) {
                $join->on('challans.id', '=', 'latest_statuses.challan_id');
            })
            ->join('challan_statuses', function ($join) use ($request) {
                $join->on('challans.id', '=', 'challan_statuses.challan_id')
                    ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                    ->where('challan_statuses.status', '=', $request->status);
            });
        }

        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }
        if ($request->has('state')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }
        if ($request->has('city')) {
            $query->whereHas('receiverDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->has('tag') && $request->tag !== null) {
            $tagId = $request->tag;
            $query->whereHas('tableTags', function ($q) use ($tagId) {
                $q->where('tags_table.id', $tagId); // Ensure the correct column name and table are referenced
            });
        }

        $teams = Team::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->id : null)->pluck('view_preference')->first();
        $defaultDriver = Auth::getDefaultDriver();
        $user = Auth::guard($defaultDriver)->user();
        $userTeamId = $user->team_id;
        $viewPreference = 'own_team'; // Assuming this is set somewhere, for example from user preferences or settings

        // Add sorting based on sortField and sortDirection
        if ($this->sortField) {
            if ($this->sortField === 'qty') {
                // Optimize sorting by using selectRaw to calculate the sum of quantities
                $query->selectRaw('challans.*, COALESCE(SUM(CAST(challan_order_details.qty AS UNSIGNED)), 0) as qty')
                    ->leftJoin('challan_order_details', 'challans.id', '=', 'challan_order_details.challan_id')
                    ->groupBy('challans.id')
                    ->orderBy('qty', $this->sortDirection);
            } else {
                // Sort by other fields directly on the Challan model
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        } else {
            // Default sorting
            $query->orderByDesc('id');
        }

        $challans = $query->with([
            'orderDetails' => function ($query) {
                $query->select('id', 'challan_id', 'remaining_qty');
            },
            'statuses',
            'tableTags',
            'deliveryStatus',
            'sfpBy',
            'team'
        ])->select('challans.*');

        // Apply filters based on default driver and view preference
        if ($defaultDriver == 'team-user' && $viewPreference == 'own_team') {
            $query->where(function ($q) use ($userTeamId) {
                $q->where('team_id', $userTeamId)
                ->orWhereNull('team_id');
            });
        }

        $challans = $query->latest()->paginate(50);

        $this->challansFiltered = $challans->toArray();

        $allStatuses = ChallanDelivery::all();
        $this->deliveryStatus = collect([strtolower($this->searchQuery)]);

        if ($this->searchQuery) {
            $this->availableDeliveryStatus = $allStatuses->filter(function ($status) {
                return strpos(strtolower($status->name), strtolower($this->searchQuery)) !== false;
            });
        } else {
            $this->availableDeliveryStatus = $allStatuses;
        }

        // Retrieve tags (Ensure this returns a collection)
        $allTagss = TagsTable::where('panel_id', 1)
        ->where('user_id', $userId)
        ->where('table_id', 1)
        ->where(function($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->pluck('id', 'name'); // Changed from paginate(10) to get() to ensure it's a collection

        $tags = TagsTable::where('panel_id', 1)
            ->where('user_id', $userId)
            ->where('table_id', 1)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->paginate(10);

        $allTags = TagsTable::where('panel_id', 1)
            ->where('user_id', $userId)
            ->where('table_id', 1)
            ->get();

        $nonMatchingTags = $allTags->filter(function($tag) {
            return stripos($tag->name, $this->searchTerm) === false;
        });

        $isSearchTermMatched = $allTags->contains(function($tag) {
            return stripos($tag->name, $this->searchTerm) !== false;
        });

        return view('livewire.sender.screens.deleted-sent-challans')
        ->with([
            'challans' => $challans,
            'tagss' => $tags,
            'allTagss' => $allTags,
            'isSearchTermMatched' => $isSearchTermMatched,
            'deliveryStatus' => $this->availableDeliveryStatus,
            'distinctChallanSeries' => $distinctChallanSeries,
            'merged_challan_series' => $combinedValues,
            'sender_id' => $distinctSenderIds,
            'receiver_ids' => $distinctReceiverIds,
            'state' => $distinctStates,
            'city' => $distinctCities,
            'status' => $distinctStatuses,
            'series_num' => $distinctChallanSeriesNum,
        ]);
    }
}
