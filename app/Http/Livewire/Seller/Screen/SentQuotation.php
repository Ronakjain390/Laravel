<?php

namespace App\Http\Livewire\Seller\Screen;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Estimates;
use App\Models\TagsTable;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use App\Models\BuyerDetails;
use ZipArchive;
use App\Mail\ExportReadyMail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Session;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Exports\Seller\SentEstimateExport;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;



use Livewire\Component;

class SentQuotation extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$estimate_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $searchQuery = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    protected $lruCache;
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;
    public $tags;
    public $isLoading = true;
    // sfp
    public $team_user_id;
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
    use WithPagination;

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

    public function mount(){
        // Simulate a delay
        // sleep(3); // Delay for 3 seconds
        // $this->isLoading = false;

        $request = request();
        $sessionId = session()->getId();
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
        $template = request('template', 'index');


            // $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            // $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);


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
            $this->ColumnDisplayNames = ['Estimate No', 'Date', 'Creator', 'Buyer', 'Qty', 'Amount', 'State', 'Status', 'SFP', 'Comment', 'Tags'];

    }

 public function innerFeatureRedirect($template, $activeFeature)
    {

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

         $this->persistedTemplate = view()->exists('components.panel.seller.' . $template) ? $template : 'index';
         $this->persistedActiveFeature = view()->exists('components.panel.seller.' . $template) ? $activeFeature : null;
         //    dd( $this->persistedTemplate,
         //    $this->persistedActiveFeature);
         $this->savePersistedTemplate($template, $activeFeature);
        //  $template = 'sent_invoice';

         // Redirect to the 'seller' route with the template as a query parameter
         return redirect()->route('seller', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);

     }

    public function poToInvoice($id)
    {
        // Store the ID in the session
        Session::put('quotation_to_invoice_id', $id);

         // Redirect to the specified route
        return redirect()->route('seller', ['template' => 'quotation_to_invoice']);
    }

    public function updated($propertyName)

    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['errorMessage','successMessage']);
    }
    public function resetFilters()

    {
        $this->reset(['searchQuery', 'from', 'to', 'buyer_id', 'variable', 'value','seller_id','status','state']);
    }

    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;
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
        $filters = [
            'estimate_series' => $this->estimate_series,
            'seller_id' => $this->seller_id,
            'status' => $this->status,
            'buyer_id' => $this->buyer_id,
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
        $query = Estimates::query()->where('seller_id', $userId);

        $combinedValues = [];

        $distinctEstimateSeries = $query->distinct('estimate_series')->pluck('estimate_series');
        $distinctEstimateSeriesNum = $query->distinct()->pluck('series_num');
        foreach ($distinctEstimateSeries as $series) {
            foreach ($distinctEstimateSeriesNum as $num) {
                $combinedValues[] = $series . '-' . $num;
            }
        }
        $distinctSellerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('seller', 'seller_id');
        $distinctBuyerIds = Estimates::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');

        $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
            $query->select('id')->from('buyers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        if ($request->estimate_series != null) {
            $searchTerm = $request->estimate_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);
                $query->where('estimate_series', $series)
                    ->where('series_num', $num);
            }
        }

        if ($request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

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

        $estimates = $query
        ->with(['statuses', 'orderDetails', 'sfpBy', 'tableTags','team'])
        ->orderBy('created_at', 'desc')
        ->paginate(50);

        $allTagss = TagsTable::where('panel_id', 3)
        ->where('user_id', $userId)
        ->where('table_id', 5)
        // ->select('id', 'name')
        ->where(function($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->pluck('id', 'name'); // Changed from paginate(10) to get() to ensure it's a collection
        // dd($allTags);
        $tags = TagsTable::where('panel_id', 3)
            ->where('user_id', $userId)
            ->where('table_id', 5)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->paginate(10);

        $allTags = TagsTable::where('panel_id', 3)
            ->where('user_id', $userId)
            ->where('table_id', 5)
            ->get();


        return view('livewire.seller.screen.sent-quotation')
        ->with([
            'estimates' => $estimates,
            'estimate_series' => $distinctEstimateSeries,
            'series_num' => $distinctEstimateSeriesNum,
            'merged_estimate_series' => $combinedValues,
            'seller_ids' => $distinctSellerIds,
            'buyer_ids' => $distinctBuyerIds,
            'states' => $distinctStates,
        ]);
    }
}
