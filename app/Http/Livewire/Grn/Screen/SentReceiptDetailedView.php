<?php

namespace App\Http\Livewire\Grn\Screen;

use Livewire\Component;
use App\Models\Team;
use Carbon\Carbon;
use App\Models\GoodsReceipt;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use App\Exports\ReceiptNote\DetailedReceiptNote;
use App\Models\GoodsReceiptStatus;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\GoodsReceipt\GoodsReceiptsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\ReceiverGoodsReceipt\ReceiverGoodsReceiptsController;

class SentReceiptDetailedView extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$goods_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $searchQuery = '';
    public $team_user_ids = [];
    protected $lruCache;
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;
    // sfp
    public $team_user_id;


    public function mount(){
        $request = request();
        $sessionId = session()->getId();
        $template = request('template', 'index');
        // if (view()->exists('components.panel.seller.' . $template)) {

            // $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            // $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
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
                // dd($this->mainUser);
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
            // $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Goods Receipt No', 'Time', 'Date', 'Creator', 'Buyer',  'Article', 'Hsn','Details', 'Unit', 'Qty', 'Unit Price', 'Total Amount'];
            // dd($this->ColumnDisplayNames);
        // }
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
        if ($this->goods_series != null) {
            $request->merge(['goods_series' => $this->goods_series]);
        }
        // if ($this->receiver_id != null) {
        //     $request->merge(['receiver_id' => $this->receiver_id]);
        // }
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }
        // if ($this->state != null) {
        //     $request->merge(['state' => $this->state]);
        // }
        if ($this->from != null || $this->to != null) {
            $request->merge([
                'from_date' => $this->from,
                'to_date' => $this->to,
            ]);
        }
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = GoodsReceipt::query()->where('sender_id', $userId);
        $combinedValues = [];

        // dd($query);

         // Check if any filter is applied
         $filters = [
            'goods_series' => $this->goods_series,
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


        $distinctGoodsReceiptSeries = $query->distinct()->pluck('goods_series');
        $distinctGoodsReceiptSeriesNum = $query->distinct()->pluck('series_num');
        // dd($distinctGoodsReceiptSeriesNum, $distinctGoodsReceiptSeries);
        foreach ($distinctGoodsReceiptSeries as $series) {
            foreach ($distinctGoodsReceiptSeriesNum as $num) {
                $combinedValues[] = $series . '-' . $num;
            }
        }
        // dd($combinedValues);
        $distinctSenderIds = $query->distinct()->pluck('sender', 'sender_id');
        $distinctReceiverIds = $query->distinct()->pluck('receiver_goods_receipts', 'receiver_goods_receipts_id');
        $distinctStatuses = GoodsReceiptStatus::distinct()->pluck('status');
        // dd(($distinctStatuses , $distinctReceiverIds));
        // $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
        //     $query->select('id')->from('receivers')->where('user_id', $userId);
        // })->distinct()->pluck('state');

        // $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
        //     $query->select('id')->from('receivers')->where('user_id', $userId);
        // })->distinct()->pluck('city');

        if ($request->goods_series != null) {
            $searchTerm = $request->goods_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('goods_series', $series)
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
            $query->whereBetween('goods_receipts_date', [$from, $to]);
        }

        if ($request->has('status')) {
            $subquery = GoodsReceiptStatus::select('goods_receipt_id', DB::raw('MAX(created_at) as max_created_at'))
                        ->groupBy('goods_receipt_id');

            $query->joinSub($subquery, 'latest_statuses', function ($join) {
                $join->on('goods_receipts.id', '=', 'latest_statuses.goods_receipt_id');
            })
            ->join('goods_receipt_statuses', function ($join) use ($request) {
                $join->on('goods_receipts.id', '=', 'goods_receipt_statuses.goods_receipt_id')
                    ->on('latest_statuses.max_created_at', '=', 'goods_receipt_statuses.created_at')
                    ->where('goods_receipt_statuses.status', '=', $request->status);
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

        // $teams = Teams::where('team_owner_user_id', $userId)->pluck('view_preference')->get();

        $teams = Team::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->id : null)->pluck('view_preference')->first();



        $defaultDriver = Auth::getDefaultDriver();
        $user = Auth::guard($defaultDriver)->user();
        $userTeamId = $user->team_id;
        $viewPreference = 'own_team'; // Assuming this is set somewhere, for example from user preferences or settings

        // Apply filters based on default driver and view preference
        if ($defaultDriver == 'team-user' && $viewPreference == 'own_team') {
            $query->where(function ($q) use ($userTeamId) {
                $q->where('team_id', $userTeamId)
                  ->orWhereNull('team_id');
            });
        }

        if ($this->sortField) {
            if ($this->sortField === 'qty') {
                // Optimize sorting by using selectRaw to calculate the sum of quantities
                $query->selectRaw('goods_receipts.*, COALESCE(SUM(CAST(goods_receipt_order_details.qty AS UNSIGNED)), 0) as qty')
                    ->leftJoin('goods_receipt_order_details', 'goods_receipts.id', '=', 'goods_receipt_order_details.goods_receipt_id')
                    ->groupBy('goods_receipts.id')
                    ->orderBy('qty', $this->sortDirection);
            } else {
                // Sort by other fields directly on the Challan model
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        } else {
            // Default sorting
            $query->orderByDesc('id');
        }

        $goodsReceipt = $query->with([
            'orderDetails',
            'orderDetails.columns',
        ])->paginate(50);



        // dd($goodsReceipt);
        $this->challansFiltered = $goodsReceipt->toArray();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // $allTags = Tag::where('user_id', $userId)->get();
        // $allStatuses = ChallanDelivery::all();

        // $this->tags = collect([strtolower($this->searchQuery)]);
        // $this->deliveryStatus = collect([strtolower($this->searchQuery)]);

        // if ($this->searchQuery) {
        //     $this->availableTags = $allTags->filter(function ($tag) {
        //         return strpos(strtolower($tag->name), strtolower($this->searchQuery)) !== false;
        //     });
        //     $this->availableDeliveryStatus = $allStatuses->filter(function ($status) {
        //         return strpos(strtolower($status->name), strtolower($this->searchQuery)) !== false;
        //     });
        // } else {
        //     $this->availableTags = $allTags;
        //     $this->availableDeliveryStatus = $allStatuses;
        // }

        // $this->availableTags = $this->availableTags->filter(function ($tag) {
        //     return !$this->tags->contains(strtolower($tag->name));
        // });
        // $this->emit('challansUpdated', $goodsReceipt->pluck('id'));
        return view('livewire.grn.screen.sent-receipt-detailed-view',
            [
                'goodsReceipt' => $goodsReceipt,
                // 'tags' => $this->availableTags,
                // 'deliveryStatus' => $this->availableDeliveryStatus,
                'distinctGoodsReceiptSeries' => $distinctGoodsReceiptSeries,
                'merged_goods_series' => $combinedValues,
                'sender_id' => $distinctSenderIds,
                'receiver_ids' => $distinctReceiverIds,
                // 'state' => $distinctStates,
                // 'city' => $distinctCities,
                'status' => $distinctStatuses,
                'series_num' => $distinctGoodsReceiptSeriesNum,
                'isFilterApplied' => !empty($isFilterApplied),
                'totalChallansCount' => $totalChallansCount
            ]
        );
    }
    public $receiver_id;

    public function export($exportOption)
    {
        // dd($exportOption, $this->goods_series);
        $request = request();

        // Merge the filters into the request
        $filters = [
            'goods_series' => $this->goods_series,
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
        $challanExport = new DetailedReceiptNote($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['goods_series', 'receiver_id', 'status', 'state', 'from', 'to']);
        // Get the data and count the number of rows
        $data = $challanExport->collection();
        $rowCount = $data->count();
        // dd($rowCount);
        if ($rowCount <= 100) {
            $response = tap($challanExport->download('receipt_note.csv'), function () {
                // Redirect to the current page after the CSV file is downloaded
                $this->redirect(request()->header('Referer'));
            });

            return $response;

        } else {
            // dd('Mail');
            // Generate and store the CSV file
            $filePath = 'exports/receipt_note.csv';
            $challanExport->store($filePath, 'local');

            // Create a ZIP file and add the CSV file to it
            $zipFilePath = 'exports/receipt_note.zip';
            $zip = new ZipArchive();
            if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
                $zip->addFile(Storage::path($filePath), basename($filePath));
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP file');
            }
          // Define the S3 path for the ZIP file
            $s3ZipFilePath = 'exports/receipt_note.zip';

            // Move the ZIP file to S3
            Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');


           // Generate a temporary URL for the ZIP file on S3
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(300));
            $heading = 'Sent Receipt Note Export';
            $message = 'The Receipt Note export is ready. You can download it from the following link:';

            // Send the email with the temporary link to the ZIP file
            Mail::to($userEmail)->send(new ExportReadyMail($temporaryUrl, $heading, $message));

            session()->flash('sentMessage', ['type' => 'success', 'content' => 'File is sent on Email successfully, please check']);
            return redirect()->route('grn', ['template' => 'sent-receipt-detailed-view'])->with('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully,File is sent on Email successfully, please check']);
        }

    }

    private function handleResponse($response)
    {
        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage', 'successMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
    }

    private function mergeRequestParameters($request)
    {
        if ($this->goods_series != null) {
            $request->merge(['goods_series' => $this->goods_series]);
        }
        if ($this->receiver_id != null) {
            $request->merge(['receiver_id' => $this->receiver_id]);
        }
        if ($this->status != null) {
            $request->merge(['status' => $this->status]);
        }
        if ($this->state != null) {
            $request->merge(['state' => $this->state]);
        }
        if ($this->from != null || $this->to != null) {
            $request->merge([
                'from_date' => $this->from,
                'to_date' => $this->to,
            ]);
        }
    }

    private function getUserId()
    {
        return Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    }

    private function getCombinedValues($query)
    {
        $distinctGoodsReceiptSeries = $query->distinct()->pluck('goods_series');
        $distinctGoodsReceiptSeriesNum = $query->distinct()->pluck('series_num');
        $combinedValues = [];

        foreach ($distinctGoodsReceiptSeries as $series) {
            foreach ($distinctGoodsReceiptSeriesNum as $num) {
                $combinedValues[] = $series . '-' . $num;
            }
        }

        return $combinedValues;
    }

    private function getDistinctValues($query, $userId)
    {
        $distinctSenderIds = $query->distinct()->pluck('sender', 'sender_id');
        $distinctReceiverIds = $query->distinct()->pluck('receiver_goods_receipts', 'receiver_goods_receipts_id');
        $distinctStatuses = ChallanStatus::distinct()->pluck('status');

        $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('state');

        $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
            $query->select('id')->from('receivers')->where('user_id', $userId);
        })->distinct()->pluck('city');

        return [
            'distinctSenderIds' => $distinctSenderIds,
            'distinctReceiverIds' => $distinctReceiverIds,
            'distinctStatuses' => $distinctStatuses,
            'distinctStates' => $distinctStates,
            'distinctCities' => $distinctCities,
            'distinctGoodsReceiptSeries' => $distinctGoodsReceiptSeries,
            'distinctGoodsReceiptSeriesNum' => $distinctGoodsReceiptSeriesNum,
        ];
    }

    private function applyFilters($request, $query)
    {
        if ($request->goods_series != null) {
            $searchTerm = $request->goods_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('goods_series', $series)
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
            $query->whereBetween('goods_receipts_date', [$from, $to]);
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
    }

    private function filterTagsAndStatuses($allTags, $allStatuses)
    {
        $this->tags = collect([strtolower($this->searchQuery)]);
        $this->deliveryStatus = collect([strtolower($this->searchQuery)]);

        if ($this->searchQuery) {
            $this->availableTags = $allTags->filter(function ($tag) {
                return strpos(strtolower($tag->name), strtolower($this->searchQuery)) !== false;
            });
            $this->availableDeliveryStatus = $allStatuses->filter(function ($status) {
                return strpos(strtolower($status->name), strtolower($this->searchQuery)) !== false;
            });
        } else {
            $this->availableTags = $allTags;
            $this->availableDeliveryStatus = $allStatuses;
        }

        $this->availableTags = $this->availableTags->filter(function ($tag) {
            return !$this->tags->contains(strtolower($tag->name));
        });
    }

}
