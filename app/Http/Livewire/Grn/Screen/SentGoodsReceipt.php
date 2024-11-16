<?php

namespace App\Http\Livewire\Grn\Screen;

use Livewire\Component;
use App\Models\Team;
use Carbon\Carbon;
use App\Models\GoodsReceipt;
use ZipArchive;
use App\Models\PaymentStatus;
use App\Models\TagsTable;
use Livewire\WithPagination;
use App\Mail\ExportReadyMail;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\GoodsReceiptStatus;
use App\Exports\ReceiptNote\ReceiptNoteExport;
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
class SentGoodsReceipt extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$goods_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $searchQuery = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    // public $selectedCount = [];
    protected $lruCache;
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
    // protected $listeners = ['callsfpInvoice' => 'sfpInvoice'];

    public function loadData()
    {
        $this->isLoading = false;
    }

    public function mount(){
        $request = request();
        $sessionId = session()->getId();
        $template = request('template', 'index');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

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
            $this->ColumnDisplayNames = ['Goods Receipt No',  'Date', 'Creator', 'Buyer',  'Amount', 'Qty', 'State', 'Status', 'SFP', 'Comment', 'Tags'];



    }

    public function openModal($itemId, $action)
    {
        // dd($itemId, $action);
        $this->itemId = $itemId;
        $this->isOpen = true;

        if ($action == 'sendGoodsReceipt') {
            $this->modalHeading = 'GoodsReceipt Note';
            $this->modalButtonText = 'Send';
            $this->modalAction = 'sendGoodsReceipt';
        } elseif ($action == 'addComment') {
            // dd('addComment');
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        }
    }
    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }

    public $openSearchModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;

    public function tagModal($itemId, $action)
    {
        // Destroy the session variables
        session()->forget('message');
        session()->forget('sentMessage'); // Add this line to clear the 'sentMessage'

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
        $challan = GoodsReceipt::find($itemId);
        $challan = $challan->load('tableTags');

        // Check if the Challan instance exists
        if ($challan) {
            // Use the tags relationship to get the associated tags and pluck their IDs
            return $challan->tableTags()->pluck('tags_table.id')->toArray();
        }

        // Return an empty array if the Challan instance does not exist
        return [];
    }

    public $selectedProducts = [];
    public function closeModal()
    {
        $this->isOpen = false;
        $this->openSearchModal = false;

        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction', 'searchModalHeading', 'searchModalButtonText', 'searchModalAction' , 'selectedTags']);
    }

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

        foreach ($this->itemId as $itemId) {
            $goodsReceipt = GoodsReceipt::find($itemId); // Find each Challan by its ID

            $pivotData = [];
            foreach ($selectedTagIds as $tagId) {
                $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 5, 'table_id' => 9];
            }

            // Attach the selected tags to each GoodsReceipt with additional pivot data
            if ($goodsReceipt) {
                $goodsReceipt->tableTags()->sync($pivotData);
            }
        }

        $this->closeModal();
        session()->flash('message', 'Tags saved successfully.');
    }

    public function handleAction($actionType, $variable)
    {
        $selectedProducts = $this->selectedProducts;
        $this->isOpen = true;

        if ($variable == 'variableForSend') {
            $this->modalHeading = 'Send Challan';
            $this->modalButtonText = 'Send';
            $this->modalAction = 'sendBulkChallan';

        } elseif ($variable == 'variableForAddComment') {
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addBulkComment';
        }
    }

    public function addBulkComment(){
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'sender' => 'sender']);
        $GoodsReceiptController = new GoodsReceiptsController;
        $response = $GoodsReceiptController->addBulkComment($request, $this->selectedProducts);
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

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;

        // dd($this->buyer_id);
        // dd($this->{$variable}, $value, $variable);
        // if($variable == 'receipt_note_sfp'){
        //     $this->sfpModal = true;
        //     $this->invoice_id = $value;
        // }
    }
    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }

    public function sendGoodsReceipt()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ChallanController = new GoodsReceiptsController;
        $response = $ChallanController->send($request, $this->itemId);
        $result = $response->getData();
        // dd($result);
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

    // public function sfpChallan()
    // {
    //     // dd($this->team_user_ids, $this->invoice_id, $this->comment);
    //     $request = request();
    //     $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
    //     $request->merge([
    //         'team_user_ids' => $this->team_user_ids,
    //         'admin_ids' => $admin_ids,
    //         'invoice_id' => $this->invoice_id,
    //         'comment' => $this->comment,
    //     ]);
    //     // dd($request);
    //     $ChallanController = new GoodsReceiptsController;

    //     $response = $ChallanController->receiptNoteSfpCreate($request);
    //     $result = $response->getData();

    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->reset(['statusCode', 'message', 'errorMessage']);
    //        // $this->innerFeatureRedirect('sent_challan', '2');
    //         $this->successMessage = $result->message;
    //     } else {

    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //     $request = request();
    //     $this->sfpModal = false;
    //     session()->flash('message', 'Challan SFP successfully.');
    // }



    public function addComment()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'sender' => 'sender']);

        $ChallanController = new GoodsReceiptsController;
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

    public function sfpInvoice()
    {

        $request = request();
        // dd($this->team_user_ids);
        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        dd($request->all());
        $invoiceController = new GoodsReceiptsController;

        $response = $invoiceController->invoiceSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage' ]);
             $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        $template = 'sent-goods-receipt';
        return redirect()->route('grn', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public $searchTerm = '';
    public $tagExists = false;
    public $tagName = '';

    public function searchTag()
    {
        if (!empty($this->searchTerm)) {
            $this->tagName = $this->searchTerm;
            $this->tagExists = TagsTable::where('name', $this->searchTerm)->exists();
        }
    }

    public function createTag()
    {
        if (!empty($this->tagName) && !$this->tagExists) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $tag = TagsTable::create([
                'name' => $this->tagName,
                'user_id' => $userId,
                'panel_id' => 5,
                'table_id' => 9,
            ]);
            $this->tagExists = true; // Update tagExists since the tag is now created
            // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
            $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
            // $this->saveTags();
            $this->render();
            $this->searchTerm = ''; // Clear the search term
        }
    }
    public function updatedSearchTerm()
    {
        $this->searchTag();
    }
    public function export($exportOption)
    {
        $request = request();

        // Merge the filters into the request
        $filters = [
            'goods_series' => $this->goods_series,
            // 'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
        ];

        $request->merge($filters);
        // dd($request, $exportOption);
        if ($exportOption === 'current_page') {
            $request->merge(['page' => $this->page]);
        } elseif ($exportOption === 'all_data') {
            $request->merge(['all_data' => 'all_data']);
        } elseif ($exportOption === 'filtered_data') {
            $request->merge(['filtered_data' => 'filtered_data']);
        }
        $userEmail = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->email;
        // dd($userId);
        $challanExport = new ReceiptNoteExport($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['goods_series',  'status', 'state', 'from', 'to']);
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
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
            $heading = 'Sent Receipt Note Export';
            $message = 'The Receipt Note export is ready. You can download it from the following link:';

            // Send the email with the temporary link to the ZIP file
            Mail::to($userEmail)->send(new ExportReadyMail($temporaryUrl, $heading, $message));

            // $emailContent = new HtmlString("
            // <p>The Challan export is ready. You can download it from the following link:</p>
            //         <a href=\"$temporaryUrl\" style=\"display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;\">Download Challan</a>
            //     ");

               // Send the email with the temporary link to the ZIP file
            // Mail::send([], [], function ($message) use ($emailContent) {
            //     $message->to('jainronak390@gmail.com')
            //             ->subject('Challan Export')
            //             ->html((string) $emailContent); // Cast HtmlString to string
            // });
            // Return response or flash message
            session()->flash('success', 'File is sent on Email successfully, please check');
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

        $request = request();
        // dd($request->buyer_id);


        $filters = [
            'goods_series' => $this->goods_series,
            // 'receiver_id' => $this->receiver_id,
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
        $query = GoodsReceipt::query();
        $combinedValues = [];

        $query->where('sender_id', $userId);
        // dd($query);
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
        $distinctBuyerIds = $query->distinct()->pluck('receiver_goods_receipts', 'receiver_goods_receipts_id');
        // $distinctBuyerIds = Invoice::where('seller_id', $userId)->distinct()->pluck('buyer', 'buyer_id');
        $distinctStatuses = GoodsReceiptStatus::distinct()->pluck('status');

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
        if ($this->buyer_id) {
            $query->where('receiver_goods_receipts_id', $this->buyer_id);
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

        if ($request->has('tag') && $request->tag !== null) {
            $tagId = $request->tag;
            $query->whereHas('tableTags', function ($q) use ($tagId) {
                $q->where('tags_table.id', $tagId); // Ensure the correct column name and table are referenced
            });
        }

        // $teams = Teams::where('team_owner_user_id', $userId)->pluck('view_preference')->get();

        $teams = Team::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->id : null)->pluck('view_preference')->first();
        $tags = TagsTable::where('panel_id', 5)
        ->where('user_id', $userId)
        ->where('table_id', 9)
        ->where(function($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->paginate(10);

    $allTags = TagsTable::where('panel_id', 5)
        ->where('user_id', $userId)
        ->where('table_id', 9)
        ->get();

    $nonMatchingTags = $allTags->filter(function($tag) {
        return stripos($tag->name, $this->searchTerm) === false;
    });

    $isSearchTermMatched = $allTags->contains(function($tag) {
        return stripos($tag->name, $this->searchTerm) !== false;
    });

     // Check if any filter is applied
     $isFilterApplied = array_filter($filters, function ($value) {
        return $value !== null;
    });

    // Get the count of total challans after filters are applied, only if any filter is applied
    $totalChallansCount = $isFilterApplied ? $query->count() : null;

        $defaultDriver = Auth::getDefaultDriver();
        $user = Auth::guard($defaultDriver)->user();
        $userTeamId = $user->team_id;
        $viewPreference = 'own_team'; // Assuming this is set somewhere, for example from user preferences or settings

        $goodsReceipt = $query->with([
            'orderDetails:id,details',
            'statuses',
            // 'tags',
            // 'deliveryStatus',
            'sfpBy',
            // 'team'
            'tableTags',
        ])->select('goods_receipts.*');

        // // Extract assigned tags from the goods receipt
        // $assignedTags = $goodsReceipt->tableTags->pluck('name')->toArray(); // Adjust 'name' if necessary

        // Apply filters based on default driver and view preference
        if ($defaultDriver == 'team-user' && $viewPreference == 'own_team') {
            $query->where(function ($q) use ($userTeamId) {
                $q->where('team_id', $userTeamId)
                  ->orWhereNull('team_id');
            });
        }
        // $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails','receiverDetails.details','sfp'])->paginate(200);
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

        $goodsReceipt = $goodsReceipt->latest()->paginate(50);
        // dd($goodsReceipt);
        $this->challansFiltered = $goodsReceipt->toArray();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;



        return view('livewire.grn.screen.sent-goods-receipt')
        ->with([
            'goodsReceipt' => $goodsReceipt,
            'tagss' => $tags,
            'allTagss' => $allTags,
            'isSearchTermMatched' => $isSearchTermMatched,
            // 'deliveryStatus' => $this->availableDeliveryStatus,
            'distinctGoodsReceiptSeries' => $distinctGoodsReceiptSeries,
            'merged_goods_series' => $combinedValues,
            'sender_id' => $distinctSenderIds,
            'buyer_ids' => $distinctBuyerIds,
            // 'state' => $distinctStates,
            // 'city' => $distinctCities,
            'status' => $distinctStatuses,
            'series_num' => $distinctGoodsReceiptSeriesNum,
            'totalChallansCount' => $totalChallansCount,
        ]);
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

        // $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
        //     $query->select('id')->from('receivers')->where('user_id', $userId);
        // })->distinct()->pluck('state');

        // $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
        //     $query->select('id')->from('receivers')->where('user_id', $userId);
        // })->distinct()->pluck('city');

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
        // if ($request->has('receiver_id')) {
        //     $query->where('receiver_id', $request->receiver_id);
        // }
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

    // private function filterTagsAndStatuses($allTags, $allStatuses)
    // {
    //     $this->tags = collect([strtolower($this->searchQuery)]);
    //     $this->deliveryStatus = collect([strtolower($this->searchQuery)]);

    //     if ($this->searchQuery) {
    //         $this->availableTags = $allTags->filter(function ($tag) {
    //             return strpos(strtolower($tag->name), strtolower($this->searchQuery)) !== false;
    //         });
    //         $this->availableDeliveryStatus = $allStatuses->filter(function ($status) {
    //             return strpos(strtolower($status->name), strtolower($this->searchQuery)) !== false;
    //         });
    //     } else {
    //         $this->availableTags = $allTags;
    //         $this->availableDeliveryStatus = $allStatuses;
    //     }

    //     $this->availableTags = $this->availableTags->filter(function ($tag) {
    //         return !$this->tags->contains(strtolower($tag->name));
    //     });
    // }

}
