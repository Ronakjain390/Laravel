<?php

namespace App\Http\Livewire\Buyer\Content;
use Livewire\Component;
use App\Models\Invoice;
use App\Models\BuyerDetails;
use App\Models\Buyer;
use App\Models\InvoiceStatus;
use App\Models\InvoiceStatuses;
use App\Models\TagsTable;
use Livewire\WithPagination;
use App\Mail\ExportReadyMail;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class AllInvoice extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$goods_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $invoice_series;
    public $searchQuery = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $isLoading = true;
    public $modalButtonText;
    public $modalAction;
    public $tags;
    public $selectedTags = [];
    public $team_user_id;
    public $sfpModal = false;

    public function loadData()
    {
        $this->isLoading = false;
    }
    protected $listeners = [
        'actions' => 'handleAction',
    ];

    public function handleAction($message)
    {
        // Show the success message
        $this->dispatchBrowserEvent('show-success-message', [$message]);

        // Update the table data (you can call a method to refresh the data)
        $this->render();
    }




    public function mount(){
        $request = request();
        $sessionId = session()->getId();
        $template = request('template', 'index');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
             $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            $userAgent = $request->header('User-Agent');

            // Check if the User-Agent indicates a mobile device
            $this->isMobile = isMobileUserAgent($userAgent);
            $UserResource = new UserAuthController;
            $response = $UserResource->user_details($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->mainUser = json_encode($response->user);
                $this->UserDetails = $response->user->plans;
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
                'panel_id' => 4,
            ];

            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Invoice No',  'Date', 'Creator', 'Buyer',  'Amount', 'Qty', 'State', 'Status', 'SFP', 'Comment', 'Tags'];
    }

    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;

        if($variable == 'invoice_sfp'){
            $this->sfpModal = true;
            $this->invoice_id = $value;
        }
    }

    public function sfpInvoice()
    {

        $request = request();
        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'invoice_id' => $this->invoice_id,
            'comment' => $this->comment,
        ]);

        // dd($request);
        $invoiceController = new InvoiceController;

        $response = $invoiceController->invoiceSfpCreate($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage' ]);
            // $this->innerFeatureRedirect('sent_invoice', '13');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        $this->sfpModal = false;
        session()->flash('message', 'Challan SFP successfully.');
        return redirect()->route('buyer', ['template' => 'all_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 4;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems);
            $this->panel = $item->panel;
            // Session::put('panel', $this->panel);

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
        $viewPath = 'components.panel.buyer.' . $template;
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        return redirect()->route('buyer', ['template' => $this->persistedTemplate]);
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

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $filters = [
            'invoice_series' => $this->invoice_series,
            'seller_id' => $this->seller_id,
            'status' => $this->status,
            'buyer_id' => $this->buyer_id,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
            'tag' => $this->tags, // Add the tag filter
        ];
        //
        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }
         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
         $query = Invoice::query()->where('buyer_id', $userId);
          // Check if any filter is applied
        // $isFilterApplied = array_filter($filters, function ($value) {
        //     return $value !== null;
        // });

        // // Get the count of total challans after filters are applied, only if any filter is applied
        // $totalChallansCount = $isFilterApplied ? $query->count() : null;
         $combinedValues = [];

         $distinctInvoiceSeries = Invoice::where('seller_id', $userId)->distinct()->pluck('invoice_series');
         $distinctInvoiceSeriesNum = Invoice::where('seller_id', $userId)->distinct()->pluck('series_num');

         foreach ($distinctInvoiceSeries as $series) {
             foreach ($distinctInvoiceSeriesNum as $num) {
                 $combinedValues[] = $series . '-' . $num;
             }
         }

         $distinctSellerIds = Invoice::where('seller_id', $userId)->distinct()->pluck('seller', 'seller_id');
         $distinctBuyerIds = Invoice::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');

         $distinctStates = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
             $query->select('id')->from('buyers')->where('user_id', $userId);
         })->distinct()->pluck('state');

         $distinctCities = BuyerDetails::whereIn('buyer_id', function ($query) use ($userId) {
             $query->select('id')->from('buyers')->where('user_id', $userId);
         })->distinct()->pluck('city');

         if ($request->invoice_series != null) {
             $searchTerm = $request->invoice_series;
             $lastDashPos = strrpos($searchTerm, '-');

             if ($lastDashPos !== false) {
                 $series = substr($searchTerm, 0, $lastDashPos);
                 $num = substr($searchTerm, $lastDashPos + 1);
                 $query->where('invoice_series', $series)
                     ->where('series_num', $num);
             }
         }

         if ($request->buyer_id) {
             $query->where('buyer_id', $request->buyer_id);
         }

         if ($request->has('seller_id')) {
             $query->where('seller_id', $request->seller_id);
         }

         if ($request->from_date && $request->to_date) {
             $from = $request->from_date;
             $to = $request->to_date;
             $query->whereBetween('invoice_date', [$from, $to]);
         }

         if ($request->has('deleted')) {
             $query->where('deleted', $request->deleted);
         }

         if ($request->has('state')) {
             $query->whereHas('buyerDetails', function ($q) use ($request) {
                 $q->where('state', $request->state);
             });
         }

         if ($request->has('city')) {
             $query->whereHas('buyerDetails', function ($q) use ($request) {
                 $q->where('city', $request->city);
             });
         }
        // Check if any filter is applied
        $isFilterApplied = array_filter($filters, function ($value) {
            return $value !== null;
        });

        // Get the count of total challans after filters are applied, only if any filter is applied
        $totalChallansCount = $isFilterApplied ? $query->count() : null;

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

        $invoices = $query
                 ->with(['statuses', 'orderDetails', 'sfpBy', 'tableTags','team'])
                 ->orderBy('invoices.created_at', 'desc')
                 ->paginate(50);

        return view('livewire.buyer.content.all-invoice', [
            'invoices' => $invoices,
            'distinctInvoiceSeries' => $combinedValues,
            'distinctSellerIds' => $distinctSellerIds,
            'distinctBuyerIds' => $distinctBuyerIds,
            'distinctStates' => $distinctStates,
            'distinctCities' => $distinctCities,
            'totalChallansCount' => $totalChallansCount,
        ]);
    }
}
