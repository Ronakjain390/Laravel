<?php

namespace App\Http\Livewire\Receiver\Content;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Challan;
use App\Models\Receiver;
use Illuminate\Http\Request;
use App\Models\ChallanStatus;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class DetailedReceivedChallan extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status, $state, $from, $to, $challan_series;
    public $team_user_ids = [];

    public function mount()
    {
        $request = request();
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.receiver.' . session('persistedTemplate')) ? session('persistedTemplate') : 'index';
            $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
            $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);
            $UserResource = new UserAuthController();
            $response = $UserResource->user_details($request);
            $response = $response->getData();
            if ($response->success == 'true') {
                $this->mainUser = json_encode($response->user);
                // $this->successMessage = $response->message;
                $this->reset(['errorMessage']);
            } else {
                $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
            }
            $query = new TeamUserController();
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
            $this->ColumnDisplayNames = ['Challan No', 'Time', 'Date', 'Creator', 'Receiver', 'Article', 'Hsn', 'Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];
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
        $this->persistedTemplate = view()->exists('components.panel.receiver.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        redirect()
            ->route('receiver')
            ->with('message', $this->successMessage ?? $this->errorMessage);

        // Emit the 'featureRoute' event with two separate parameters
        // $this->emit('featureRoute', $template, $activeFeature);
    }

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
        //    dd($variable, $value);
    }
    public $sortField = null;
    public $sortDirection = null;

    public function sortBy($fields)
    {
        // dd($field);
        $field = 'qty';
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

            // Add sorting based on sortField and sortDirection
        if ($this->sortField) {
            if ($this->sortField === 'qty') {
                // Optimize sorting by using selectRaw to calculate the sum of quantities
                $query->selectRaw('return_challans.*, COALESCE(SUM(CAST(return_challan_order_details.qty AS UNSIGNED)), 0) as qty')
                    ->leftJoin('return_challan_order_details', 'return_challans.id', '=', 'return_challan_order_details.challan_id')
                    ->groupBy('return_challans.id')
                    ->orderBy('qty', $this->sortDirection);
            } else {
                // Sort by other fields directly on the Challan model
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        } else {
            // Default sorting
            $query->orderByDesc('id');
        }

            $detailedChallans = $query
                ->with([ 'orderDetails', 'orderDetails.columns' ])
                ->select('challans.*')
                ->latest()
                ->paginate(50);

        return view('livewire.receiver.content.detailed-received-challan',compact('detailedChallans' ))->with(['distinctChallanSeries' =>  $distinctChallanSeries,
        // 'challan_series' =>  $combinedValues,
        'merged_challan_series' => $combinedValues,
        'sender_id' => $distinctSenderIds,
        'receiver_ids' => $distinctReceiverIds,
        'state' => $distinctStates,
        'city' => $distinctCities,
        'status' => $distinctStatuses,
        'series_num' => $distinctChallanSeriesNum]);
    }

}
