<?php

namespace App\Http\Livewire\Seller\Screens;

use Carbon\Carbon;
use App\Models\Invoice;
use Livewire\Component;
use ZipArchive;
use Aws\S3\S3Client;
use Livewire\WithPagination;
use App\Models\InvoiceStatus;
use App\Models\BuyerDetails;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\Seller\DetailedSentInvoiceExport;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class DetailedSentInvoice extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$invoice_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $searchQuery = '';
    public $team_user_ids = [];
    protected $lruCache;
    use WithPagination;

    public function mount(){
        $request = request();
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.seller.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            $this->persistedActiveFeature = view()->exists('components.panel.seller.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
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
                'feature_id' => 13,
                'panel_id' => 3,
            ];

            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Invoice No', 'Time', 'Date', 'Creator', 'Buyer',  'Article', 'hsn', 'Detail', 'Unit', 'Qty', 'Price', 'Tax' ,'Total Amount'];

        }

    }
    public function innerFeatureRedirect($template, $activeFeature)
    {

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }

     // Method to save the $persistedTemplate value to the session
     public function savePersistedTemplate($template, $activeFeature = null)
     {
         session(['persistedTemplate' => $template]);
         session(['persistedActiveFeature' => $activeFeature]);
     }
     public function handleFeatureRoute($template, $activeFeature)
     {

         $this->persistedTemplate = view()->exists('components.panel.seller.' . $template) ? $template : 'index';
         $this->persistedActiveFeature = view()->exists('components.panel.seller.' . $template) ? $activeFeature : null;
         //    dd( $this->persistedTemplate,
         //    $this->persistedActiveFeature);
         $this->savePersistedTemplate($template, $activeFeature);
         redirect()->route('seller');
     }

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
        // dd($this->{$variable}, $value, $variable);
    }

    public function export($exportOption)
    {
        // dd($exportOption);
        $request = request();

        $request->merge(['invoice_series' => $this->invoice_series]);
        $request->merge(['buyer_id' => $this->buyer_id]);
        $request->merge(['status' => $this->status]);
        $request->merge(['state' => $this->state]);
        $request->merge([
            'from_date' => $this->from,
            'to_date' => $this->to,
        ]);
        // $request->merge($filters);
        if ($exportOption === 'current_page') {
            $request->merge(['page' => $this->page]);
        } elseif ($exportOption === 'all_data') {
            $request->merge(['all_data' => 'all_data']);
        } elseif ($exportOption === 'filtered_data') {
            $request->merge(['filtered_data' => 'filtered_data']);
        }
        $userEmail = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->email;
        // dd($userEmail);
        $challanExport = new DetailedSentInvoiceExport($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['invoice_series', 'buyer_id', 'status', 'state', 'from', 'to']);
        // Get the data and count the number of rows
        $data = $challanExport->collection();
        $rowCount = $data->count();
        // dd($rowCount);
        if ($rowCount <= 100) {
            $response = tap($challanExport->download('invoices.csv'), function () {
                // Redirect to the current page after the CSV file is downloaded
                $this->redirect(request()->header('Referer'));
            });

            return $response;

        } else {
            // dd('Mail');
            // Generate and store the CSV file
            $filePath = 'exports/invoices.csv';
            $challanExport->store($filePath, 'local');

            // Create a ZIP file and add the CSV file to it
            $zipFilePath = 'exports/invoices.zip';
            $zip = new ZipArchive();
            if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
                $zip->addFile(Storage::path($filePath), basename($filePath));
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP file');
            }
          // Define the S3 path for the ZIP file
            $s3ZipFilePath = 'exports/invoices.zip';

            // Move the ZIP file to S3
            Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');


           // Generate a temporary URL for the ZIP file on S3
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
            $heading = 'Sent Invoice Export';
            $message = 'The Invoice export is ready. You can download it from the following link:';

            // Send the email with the temporary link to the ZIP file
            Mail::to($userEmail)->send(new ExportReadyMail($temporaryUrl, $heading, $message));

            session()->flash('sentMessage', ['type' => 'success', 'content' => 'File is sent on Email successfully, please check']);
            return redirect()->route('seller', ['template' => 'detailed_sent_invoice'])->with('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully,File is sent on Email successfully, please check']);
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
        session()->put('previous_url', url()->current());
        $request = request();
            // $request = new Request(['page' => $page]);
            // dd($this->invoicesFiltered);
            if ($this->invoice_series != null) {
                // dump($this->invoice_series);
                $request->merge(['invoice_series' => $this->invoice_series]);
            }
            if ($this->seller_id != null) {
                // dump($this->seller_id);
                $request->merge(['seller_id' => $this->seller_id]);
            }
            if ($this->buyer_id != null) {
                // dump($this->buyer_id);
                $request->merge(['buyer_id' => $this->buyer_id]);
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
            $query = Invoice::query()->where('seller_id', $userId);


            $combinedValues = [];
            // Fetch the distinct filter values for Invoice table (for this user)
        $distinctInvoiceSeries = Invoice::where('seller_id', $userId)->distinct()->pluck('invoice_series');
        $distinctInvoiceSeriesNum = Invoice::where('seller_id', $userId)->distinct()->pluck('series_num');
        // $distinctSellerIds = Invoice::where('seller_id', $userId)->distinct()->get();
            // dd($distinctInvoiceSeriesNum, $distinctInvoiceSeries);
        //    // Loop through each element of $distinctInvoiceSeries
        foreach ($distinctInvoiceSeries as $series) {
            // Loop through each element of $distinctInvoiceSeriesNum
            foreach ($distinctInvoiceSeriesNum as $num) {
                // Combine the series and number and push it into the combinedValues array
                $combinedValues[] = $series . '-' . $num;
            }
            $distinctSellerIds = Invoice::where('seller_id', $userId)->distinct()->pluck('seller', 'seller_id');
        // dd($distinctSellerIds );
        $distinctBuyerIds = Invoice::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
        // $distinctStatuses = Status::distinct()->pluck('status');

        // Fetch the distinct "state" and "city" values from BuyerDetail table for buyers of this user
        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');
        }
        if ($request->invoice_series != null) {

            $searchTerm = $request->invoice_series;

            // Find the position of the last '-' in the string
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                // Split the string into series and number
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                // Perform the search
                $query->where('invoice_series', $series)
                    ->where('series_num', $num);
            } else {
                // Invalid search term format, handle accordingly
                // For example, you could return an error message or ignore the filter
            }
        }
        // dd($request->buyer_id);

        // Filter by buyer_id
        if ($request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by specific seller_id if provided in the request
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by date
        if ($request->from_date && $request->to_date) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('invoice_date', [$from, $to]);
        }


            // Filter by status
            if ($request->has('status')) {
                $query->whereHas('statuses', function ($q) use ($request) {
                    $q->latest()->where('status', $request->status);
                });
            }
            // Filter by status


            if ($request->has('status')) {
                // Subquery that gets the maximum created_at for each invoice_id
                $subquery = InvoiceStatus::select('invoice_id', DB::raw('MAX(created_at) as max_created_at'))
                    ->groupBy('invoice_id');

                // Main query that joins the subquery with the invoice_statuses table and filters by status
                $query->joinSub($subquery, 'latest_statuses', function ($join) {
                    $join->on('invoices.id', '=', 'latest_statuses.invoice_id');
                })
                ->join('invoice_statuses', function ($join) use ($request) {
                    $join->on('invoices.id', '=', 'invoice_statuses.invoice_id')
                        ->on('latest_statuses.max_created_at', '=', 'invoice_statuses.created_at')
                        ->where('invoice_statuses.status', '=', $request->status);
                });
            }
        // Filter by deleted
        if ($request->has('deleted')) {
            $query->where('deleted', $request->deleted);
        }


        // Filter by state in ReceiverDetails
        if ($request->has('state')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        // Filter by city in ReceiverDetails
        if ($request->has('city')) {
            $query->whereHas('buyerDetails', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

              // Check if any filter is applied
              $filters = [
                'invoice_series' => $this->invoice_series,
                'buyer_id' => $this->buyer_id,
                'status' => $this->status,
                'state' => $this->state,
                'from' => $this->from,
                'to' => $this->to
            ];

            $isFilterApplied = array_filter($filters, function ($value) {
                return $value !== null;
            });

            // Get the count of total challans after filters are applied, only if any filter is applied
            $totalInvoiceCount = $isFilterApplied ? $query->count() : null;

            // Add sorting based on sortField and sortDirection
            if ($this->sortField) {
                if ($this->sortField === 'qty') {
                    // Optimize sorting by using selectRaw to calculate the sum of quantities
                    $query->selectRaw('invoices.*, COALESCE(SUM(CAST(invoice_order_details.qty AS UNSIGNED)), 0) as qty')
                        ->leftJoin('invoice_order_details', 'invoices.id', '=', 'invoice_order_details.invoice_id')
                        ->groupBy('invoices.id')
                        ->orderBy('qty', $this->sortDirection);
                } else {
                    // Sort by other fields directly on the Challan model
                    $query->orderBy($this->sortField, $this->sortDirection);
                }
            } else {
                // Default sorting
                $query->orderByDesc('id');
            }

        $invoices = $query
            ->with(['orderDetails','orderDetails.columns', 'sfp'])
            ->latest()
            ->paginate(50);

        return view('livewire.seller.screens.detailed-sent-invoice')
        ->with([
            'invoices' => $invoices,
            'invoice_series' => $distinctInvoiceSeries,
            'series_num' => $distinctInvoiceSeriesNum,
            'merged_invoice_series' => $combinedValues,
            'seller_id' => $distinctSellerIds,
            'buyer_ids' => $distinctBuyerIds,
            'state' => $distinctStates,
            'city' => $distinctCities,
            'isFilterApplied' => !empty($isFilterApplied),
            'totalInvoiceCount' =>  $totalInvoiceCount,
        ]);
    }

}
