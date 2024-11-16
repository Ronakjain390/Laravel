<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Challan;
use App\Models\Receiver;
use Illuminate\Http\Request;
use App\Exports\Sender\ExportDetailedReceivedChallan;
use Livewire\WithPagination;
use App\Models\ReceiverDetails;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Storage;
use App\Models\ReturnChallan;
use Illuminate\Support\Facades\Cache;
use App\Models\ChallanStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class DetailedReceivedChallan extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment ;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status, $state, $from, $to;
    use WithPagination;
    public function mount(){
        $request = request();
        // Retrieve the persisted value from the session, if available
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;

        $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);
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
        $columnFilterDataset = [
            'feature_id' => 2,
        ];
        $request->merge($columnFilterDataset);
        $this->ColumnDisplayNames = ['Challan No', 'Time', 'Date', 'Creator', 'Receiver', 'Article', 'Hsn', 'Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];
    }
    }

        // Method to save the $persistedTemplate value to the session
        public function savePersistedTemplate($template, $activeFeature = null)
        {
            // dd($template, $activeFeature);
            session(['persistedTemplate' => $template]);
            session(['persistedActiveFeature' => $activeFeature]);
        }
        public function handleFeatureRoute($template, $activeFeature)
        {
            // dd($template, $activeFeature);
            $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
            $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
            $this->savePersistedTemplate($template, $activeFeature);

            redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);

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

    public $challan_series;

    public function export($exportOption)
    {
        // dd($exportOption, $this->challan_series);
        $request = request();

        // Merge the filters into the request
        $filters = [
            'challan_series' => $this->challan_series,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
        ];

        $request->merge($filters);
        if ($exportOption === 'current_page') {
            $request->merge(['page' => $this->page]);
        } elseif ($exportOption === 'all_data') {
            $request->merge(['all_data' => 'all_data']);
        } elseif ($exportOption === 'filtered_data') {
            $request->merge(['filtered_data' => 'filtered_data']);
        }
        $userEmail = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->email;
        // dd($userId);
        $challanExport = new ExportDetailedReceivedChallan($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['challan_series', 'receiver_id', 'status', 'state', 'from', 'to']);
        // Get the data and count the number of rows
        $data = $challanExport->collection();
        $rowCount = $data->count();
        // dd($rowCount);
        if ($rowCount <= 100) {
            $response = tap($challanExport->download('challans.csv'), function () {
                // Redirect to the current page after the CSV file is downloaded
                $this->redirect(request()->header('Referer'));
            });

            return $response;

        } else {
            // dd('Mail');
            // Generate and store the CSV file
            $filePath = 'exports/challans.csv';
            $challanExport->store($filePath, 'local');

            // Create a ZIP file and add the CSV file to it
            $zipFilePath = 'exports/challans.zip';
            $zip = new ZipArchive();
            if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
                $zip->addFile(Storage::path($filePath), basename($filePath));
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP file');
            }
          // Define the S3 path for the ZIP file
            $s3ZipFilePath = 'exports/challans.zip';

            // Move the ZIP file to S3
            Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');

           // Generate a temporary URL for the ZIP file on S3
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
            $heading = 'Sent Challan Export';
            $message = 'The Challan export is ready. You can download it from the following link:';

            // Send the email with the temporary link to the ZIP file
            Mail::to($userEmail)->send(new ExportReadyMail($temporaryUrl, $heading, $message));

            session()->flash('sentMessage', ['type' => 'success', 'content' => 'File is sent on Email successfully, please check']);
            return redirect()->route('sender', ['template' => 'detailed_received_challan'])->with('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully,File is sent on Email successfully, please check']);
        }

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
        // dd($this->persistedActiveFeature);
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
        $query = ReturnChallan::query()->where('receiver_id', $userId);

         // Check if any filter is applied
         $filters = [
            'challan_series' => $this->challan_series,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'state' => $this->state,
            'from' => $this->from,
            'to' => $this->to
        ];

        $isFilterApplied = array_filter($filters, function ($value) {
            return $value !== null;
        });

        // Get the count of total challans after filters are applied, only if any filter is applied
        $totalChallansCount = $isFilterApplied ? $query->count() : null;


        $combinedValues = [];
        // dd($request->has('sender_id'), $request->has('receiver_id'));
        // if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('receiver_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('challan_series');
            $distinctReturnChallanSeriesNum = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('series_num');
            // dd($distinctReturnChallanSeriesNum);
            $distinctReceiverIds = ReturnChallan::where('receiver_id', $userId)->distinct()->pluck('receiver', 'receiver_id');
            // dd($distinctReceiverIds);
            $distinctSenderIds = ReturnChallan::where('sender_id', $userId)->distinct()->pluck('receiver_id');

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
        // foreach ($distinctReturnChallanSeries as $series) {
        //     // Loop through each element of $distinctChallanSeriesNum
        //     foreach ($distinctReturnChallanSeriesNum as $num) {
        //         // Combine the series and number and push it into the combinedValues array
        //         $combinedValues[] = $series . '-' . $num;
        //     }
        // }
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


        // Filter by column_name in ReturnChallanOrderColumn
        if ($request->has('article')) {
            $query->whereHas('orderDetails.columns', function ($q) use ($request) {
                $q->where('column_name', 'Article')
                ->where('column_value', $request->article);
            });
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

         // Fetch the paginated results
        $returnChallans = $query->with([ 'orderDetails', 'orderDetails.columns'])
        ->paginate(50);

        //  dd($returnChallans);
        return view('livewire.sender.screens.detailed-received-challan', compact('returnChallans' ))->with(['distinctReturnChallanSeries' =>  $distinctReturnChallanSeries,
        // 'challan_series' =>  $combinedValues,
        'merged_challan_series' => $combinedValues,
        'sender_id' => $distinctSenderIds,
        'receiver_ids' => $distinctReceiverIds,
        'state' => $distinctStates,
        'city' => $distinctCities,
        'series_num' => $distinctReturnChallanSeriesNum,
        'isFilterApplied' => !empty($isFilterApplied),
        'totalChallansCount' => $totalChallansCount
    ]);
    }
}
