<?php

namespace App\Http\Livewire\Buyer\Content;
use ZipArchive;
use Carbon\Carbon;
use App\Models\TagsTable;
use App\Models\PurchaseOrder;
use Livewire\WithPagination;
use App\Mail\ExportReadyMail;
use App\Exports\Buyer\PurchaseOrderExport;
use Illuminate\Http\Response;
use App\Models\PurchaseOrderStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

use Livewire\Component;

class SentPurchaseOrders extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$goods_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $purchase_order_series;
    public $searchQuery = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    public $searchTerm = '';
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


    protected $listeners = [
        'actions' => 'handleAction',
    ];


    public function loadData()
    {
        $this->isLoading = false;
    }


    public function handleAction($message)
    {
        // Show the success message
        $this->dispatchBrowserEvent('show-success-message', [$message]);

        // Update the table data (you can call a method to refresh the data)
        $this->render();
    }

    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;

        if($variable == 'receipt_note_sfp'){
            $this->sfpModal = true;
            $this->purchase_order_id = $value;
        }
    }
    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
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
                $this->user = json_encode($response->user);
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
                // dd($this->teamMembers);
            } else {
                $this->errorMessage = json_encode($queryData->errors);
                $this->reset(['status', 'successMessage']);
            }
            $columnFilterDataset = [
                'feature_id' => 19,
                'panel_id' => 4,
            ];

            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['PO No',  'Date', 'Creator', 'Buyer',  'Amount', 'Qty', 'State', 'Status', 'SFP', 'Comment', 'Tags'];
    }
    public function sfpInvoice()
    {
        $request = request();
        $authId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $admin_ids = []; // Initialize $admin_ids as an empty array

        if (in_array($authId, $this->team_user_ids)) {
            $admin_ids[] = $authId; // Assign it to the $admin_ids array
            $this->team_user_ids = array_diff($this->team_user_ids, [$authId]); // Remove it from the team_user_ids array
        }

        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'purchase_order_id' => $this->purchase_order_id,
            'comment' => $this->comment,
        ]);

        $invoiceController = new PurchaseOrderController;

        $response = $invoiceController->poSfpCreate($request);
        $result = $response->getData();

        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $this->sfpModal = false;
        session()->flash('message', 'Challan SFP successfully.');
        return redirect()->route('buyer', ['template' => 'purchase_order'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }
    public function searchTag()
    {
        if (!empty($this->searchTerm)) {
            $this->tagName = $this->searchTerm;
            $this->tagExists = TagsTable::where('name', $this->searchTerm)->exists();
        }
    }

    public function export($exportOption)
    {
        $request = request();

        // Merge the filters into the request
        $filters = [
            'purchase_order_series' => $this->purchase_order_series,
            'buyer_id' => $this->buyer_id,
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
        $purchaseOrderExport = new PurchaseOrderExport($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['purchase_order_series', 'buyer_id', 'status', 'state', 'from', 'to']);
        // Get the data and count the number of rows
        $data = $purchaseOrderExport->collection();
        $rowCount = $data->count();
        // dd($rowCount);
        if ($rowCount <= 100) {
            $response = tap($purchaseOrderExport->download('purchase_order.csv'), function () {
                // Redirect to the current page after the CSV file is downloaded
                $this->redirect(request()->header('Referer'));
            });

            return $response;

        } else {
            // dd('Mail');
            // Generate and store the CSV file
            $filePath = 'exports/purchase_order.csv';
            $purchaseOrderExport->store($filePath, 'local');

            // Create a ZIP file and add the CSV file to it
            $zipFilePath = 'exports/purchase_order.zip';
            $zip = new ZipArchive();
            if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
                $zip->addFile(Storage::path($filePath), basename($filePath));
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP file');
            }
          // Define the S3 path for the ZIP file
            $s3ZipFilePath = 'exports/purchase_order.zip';

            // Move the ZIP file to S3
            Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');


           // Generate a temporary URL for the ZIP file on S3
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
            $heading = 'Sent Challan Export';
            $message = 'The Challan export is ready. You can download it from the following link:';

            // Send the email with the temporary link to the ZIP file
            Mail::to($userEmail)->send(new ExportReadyMail($temporaryUrl, $heading, $message));

            session()->flash('sentMessage', ['type' => 'success', 'content' => 'File is sent on Email successfully, please check']);
            return redirect()->route('buyer', ['template' => 'purchase_order'])->with('sentMessage', ['type' => 'success', 'content' => 'Purchase Order exported successfully,File is sent on Email successfully, please check']);
        }
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
        // dd($this->tags);
        $request = request();

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $filters = [
            'purchase_order_series' => $this->purchase_order_series,
            'seller_id' => $this->seller_id,
            'status' => $this->status,
            'buyer_id' => $this->buyer_id,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
            'tag' => $this->tags, // Add the tag filter
        ];
        $query = PurchaseOrder::query()->where('seller_id', $userId);
        //
        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }
        $combinedValues = [];

        if ($request->has('status')) {
            $subquery = PurchaseOrderStatus::select('purchase_order_id', DB::raw('MAX(created_at) as max_created_at'))
                        ->groupBy('purchase_order_id');

            $query->joinSub($subquery, 'latest_statuses', function ($join) {
                $join->on('purchase_orders.id', '=', 'latest_statuses.purchase_order_id');
            })
            ->join('purchase_order_statuses', function ($join) use ($request) {
                $join->on('purchase_orders.id', '=', 'purchase_order_statuses.purchase_order_id')
                    ->on('latest_statuses.max_created_at', '=', 'purchase_order_statuses.created_at')
                    ->where('purchase_order_statuses.status', '=', $request->status);
            });
        }
        if ($request->has('tag') && $request->tag !== null) {
            $tagId = $request->tag;
            $query->whereHas('tableTags', function ($q) use ($tagId) {
                $q->where('tags_table.id', $tagId); // Ensure the correct column name and table are referenced
            });
        }

         // Check if any filter is applied
         $isFilterApplied = array_filter($filters, function ($value) {
            return $value !== null;
        });


        // Get the count of total challans after filters are applied, only if any filter is applied
        $totalChallansCount = $isFilterApplied ? $query->count() : null;

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $userId = Auth::getDefaultDriver() == 'team-user'
        ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
        : Auth::guard(Auth::getDefaultDriver())->user()->id;



        $distinctChallanSeries = $query->distinct()->pluck('purchase_order_series');
        $distinctChallanSeriesNum = $query->distinct()->pluck('series_num');
        $distinctStatuses = PurchaseOrderStatus::distinct()->pluck('status');

        foreach ($distinctChallanSeries as $series) {
            foreach ($distinctChallanSeriesNum as $num) {
                $combinedValues[] = $series . '-' . $num;
            }
        }

        // dd($query);
        $distinctSellerIds = $query->distinct()->pluck('seller_name', 'seller_id');
        $distinctBuyerIds = $query->distinct()->pluck('buyer_name', 'buyer_id');

        $query->with([
            'orderDetails' => function ($query) {
                $query->select('id', 'purchase_order_id');
            },
            'statuses' => function ($query) {
                $query->select('id', 'purchase_order_id', 'status', 'created_at', 'user_name');
            },
            'sfpBy' => function ($query) {
                $query->select('id', 'purchase_order_id', 'sfp_by_name', 'sfp_by_id','sfp_to_id', 'sfp_to_name', 'created_at');
            },
            'sellerUser',
            'buyerUser',
            'tableTags',
        ]);


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

        $purchaseOrders = $query->latest('purchase_orders.created_at')->paginate(50);

        $allTags = TagsTable::where('panel_id', 4)
            ->where('user_id', $userId)
            ->where('table_id', 7)
            ->get();

        $nonMatchingTags = $allTags->filter(function($tag) {
            return stripos($tag->name, $this->searchTerm) === false;
        });

        $isSearchTermMatched = $allTags->contains(function($tag) {
            return stripos($tag->name, $this->searchTerm) !== false;
        });

        return view('livewire.buyer.content.sent-purchase-orders', [
            'purchaseOrders' => $purchaseOrders,
            'totalChallansCount' => $totalChallansCount,
            'distinctSellerIds' => $distinctSellerIds,
            'distinctStatuses' => $distinctStatuses,
            'distinctBuyerIds' => $distinctBuyerIds,
            'allTagss' => $allTags,
            'purchase_order_series' => $combinedValues,
        ]);
    }
}
