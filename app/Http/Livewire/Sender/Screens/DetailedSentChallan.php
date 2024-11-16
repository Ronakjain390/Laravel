<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;
use ZipArchive;
use App\Models\Challan;
use App\Models\Receiver;
use Illuminate\Http\Request;
use App\Mail\ExportReadyMail;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Storage;
use App\Exports\Sender\ExportDetailedChallan;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class DetailedSentChallan extends Component
{
    use WithPagination;

    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment;
    public $mainUser, $teamMembers, $variable, $value, $receiver_id, $status, $state, $from, $to;
    public $challan_series;
    public $exportOption = 'current_page';

    public function mount()
    {
        $request = request();
        // Retrieve the persisted value from the session, if available
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
        }

        $userAgent = $request->header('User-Agent');

        // Check if the User-Agent indicates a mobile device
        $this->isMobile = isMobileUserAgent($userAgent);
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->mainUser = json_encode($response->user);
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

    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }

    public function handleFeatureRoute($template, $activeFeature)
    {
        $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        redirect()->route('sender')->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
    }

    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
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
        $this->mergeRequestData($request);

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()
            ->where('sender_id', $userId)
            ->with(['orderDetails', 'orderDetails.columns']);

        // Fetch distinct filter values
        $distinctData = $this->fetchDistinctData($userId, $request);

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

        $this->applyFilters($query, $request);

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

        // Get the count of total challans after filters are applied, only if any filter is applied
        $totalChallansCount = $isFilterApplied ? $query->count() : null;

        // Paginate the results
        $challans = $query->paginate(50);

        return view('livewire.sender.screens.detailed-sent-challan', [
            'challans' => $challans,
            'distinctChallanSeries' => $distinctData['distinctChallanSeries'],
            'merged_challan_series' => $distinctData['combinedValues'],
            'sender_id' => $distinctData['distinctSenderIds'],
            'receiver_ids' => $distinctData['distinctReceiverIds'],
            'state' => $distinctData['distinctStates'],
            'city' => $distinctData['distinctCities'],
            'status' => $distinctData['distinctStatuses'],
            'series_num' => $distinctData['distinctChallanSeriesNum'],
            'totalChallansCount' => $totalChallansCount,
            'isFilterApplied' => !empty($isFilterApplied)
        ]);
    }

    private function mergeRequestData($request)
    {
        $filters = ['challan_series', 'receiver_id', 'status', 'state', 'from', 'to'];
        foreach ($filters as $filter) {
            if ($this->{$filter} !== null) {
                $request->merge([$filter => $this->{$filter}]);
            }
        }
        if ($this->from !== null || $this->to !== null) {
            $request->merge(['from_date' => $this->from, 'to_date' => $this->to]);
        }
    }

    private function fetchDistinctData($userId, $request)
    {
        if (!$request->has('sender_id') && !$request->has('receiver_id')) {
            $baseQuery = Challan::where('sender_id', $userId);
        } else {
            $baseQuery = Challan::where('receiver_id', $userId);
        }

        $distinctChallanSeries = $baseQuery->distinct()->pluck('challan_series');
        $distinctChallanSeriesNum = $baseQuery->distinct()->pluck('series_num');

        $combinedValues = $distinctChallanSeries->crossJoin($distinctChallanSeriesNum)
                                                 ->map(fn($pair) => $pair[0] . '-' . $pair[1])
                                                 ->all();

        $distinctSenderIds = $baseQuery->distinct()->pluck('sender', 'sender_id');
        $distinctReceiverIds = $baseQuery->distinct()->pluck('receiver', 'receiver_id');
        $distinctStatuses = ChallanStatus::distinct()->pluck('status');

        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        return compact('distinctChallanSeries', 'distinctChallanSeriesNum', 'combinedValues', 'distinctSenderIds', 'distinctReceiverIds', 'distinctStatuses', 'distinctStates', 'distinctCities');
    }

    private function applyFilters($query, $request)
    {
        if ($request->challan_series) {
            $this->applyChallanSeriesFilter($query, $request->challan_series);
        }
        if ($request->sender_id) {
            $query->where('sender_id', $request->sender_id);
        }
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('challan_date', [$request->from_date, $request->to_date]);
        }
        if ($request->receiver_id) {
            $query->where('receiver_id', $request->receiver_id);
        }
        if ($request->status) {
            $this->applyStatusFilter($query, $request->status);
        }
        if ($request->deleted) {
            $query->where('deleted', $request->deleted);
        }
        if ($request->state) {
            $query->whereHas('receiverDetails', fn($q) => $q->where('state', $request->state));
        }
        if ($request->city) {
            $query->whereHas('receiverDetails', fn($q) => $q->where('city', $request->city));
        }
    }

    private function applyChallanSeriesFilter($query, $searchTerm)
    {
        $lastDashPos = strrpos($searchTerm, '-');
        if ($lastDashPos !== false) {
            $series = substr($searchTerm, 0, $lastDashPos);
            $num = substr($searchTerm, $lastDashPos + 1);
            $query->where('challan_series', $series)
                  ->where('series_num', $num);
        }
    }

    private function applyStatusFilter($query, $status)
    {
        $subquery = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
                                 ->groupBy('challan_id');

        $query->joinSub($subquery, 'latest_statuses', fn($join) => $join->on('challans.id', '=', 'latest_statuses.challan_id'))
              ->join('challan_statuses', fn($join) => $join->on('challans.id', '=', 'challan_statuses.challan_id')
                                                            ->on('latest_statuses.max_created_at', '=', 'challan_statuses.created_at')
                                                            ->where('challan_statuses.status', '=', $status));
    }

    // public function export($exportOption)
    // {
    //     $request = request();
    //     $this->mergeRequestData($request);

    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //     $query = Challan::query()->where('sender_id', $userId)->orderByDesc('id');

    //     $this->applyFilters($query, $request);

    //     if ($exportOption === 'current_page') {
    //         $challans = $query->paginate(50);
    //     } elseif ($exportOption === 'filtered_data') {
    //         $challans = $query->get();
    //     } else { // all_data
    //         $challans = Challan::where('sender_id', $userId)->get();
    //     }

    //     $exportData = [];
    //     foreach ($challans as $challan) {
    //         $exportData[] = [
    //             'Challan No' => $challan->challan_series . '-' . $challan->series_num,
    //             'Time' => $challan->created_at->format('H:i:s'),
    //             'Date' => $challan->challan_date,
    //             'Creator' => $challan->sender,
    //             'Receiver' => $challan->receiver,
    //             'Total Amount' => $challan->total_amount,
    //             // Add more fields as needed
    //         ];
    //     }

    //     // Check if the user has the required plan for barcode
    //     // $userHasSilverPlan = $this->checkUserPlan('Silver');

    //     // if ($userHasSilverPlan) {
    //     //     // Add barcode to the export data
    //     //     foreach ($exportData as &$row) {
    //     //         $row['Barcode'] = $this->generateBarcode($row['Challan No']);
    //     //     }
    //     // }

    //     $export = new ExportDetailedChallan($exportData);

    //     $fileName = 'detailed_challan_export_' . now()->format('Y-m-d_His') . '.xlsx';

    //     return Excel::download($export, $fileName);
    // }

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
        $challanExport = new ExportDetailedChallan($request);

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
            return redirect()->route('sender', ['template' => 'detailed_sent_challan'])->with('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully,File is sent on Email successfully, please check']);
        }

    }


}
