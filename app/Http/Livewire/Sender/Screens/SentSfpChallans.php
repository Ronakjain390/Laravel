<?php

namespace App\Http\Livewire\Sender\Screens;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Challan;
use App\Models\Team;
use App\Models\TagsTable;
use App\Models\Notification;
use App\Cache\LruCache;
use Livewire\Redirector;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Receiver;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\ChallanStatus;
use App\Models\ChallanDelivery;
use App\Exports\Exports\ChallanExport;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class SentSfpChallans extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode, $message, $sentMessage, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status,$tag, $state, $from, $to, $signature, $attributes, $columnId;
    public $team_user_ids = [];
    public $admin_ids = [];
    protected $lruCache;
    public $isMobile;
    public $itemId = [];
    public $isOpen = false;
    public $BulkisOpen = false;
    public $modalHeading;
    public $bulkmodalHeading;
    public $modalButtonText;
    public $modalAction;
    public $isLoading = true;
    public $BulkModalAction;
    public $bulkSubHeading;
    public $bulkActions;
    public $tags;
    public $searchTerm = '';
    public $add_signature;
    public $self_delivery;
    public $self_return;
    public $allInvoiceIds = [];

    use WithPagination;
    protected $listeners = [
        'printChallans' => 'printChallans',
        'sendSelectedIds' => 'sendSelectedIds',
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
    public function mount()
    {
        $request = request();

        session()->put('previous_url', url()->current());
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

            // Fetch panel settings
            $panelSettings = \App\Models\PanelSettings::where('user_id', Auth::user()->id)->first();

            // Default column names
            $defaultColumnNames = ['Challan No', 'Date', 'Creator', 'Receiver', 'Qty', 'Amount', 'State', 'Status', 'Payment Status', 'SFP', 'Comment'];

            if ($panelSettings) {
                // $settings = json_decode($panelSettings->settings, true);
                $settings = json_decode($panelSettings)->settings;
                // dd($settings);
                if (isset($settings) && isset($settings->sender)) {
                    $senderSettings = $settings->sender;
                    $this->self_delivery = $senderSettings->self_delivery?? false;
                    $this->add_signature = $senderSettings->add_signature?? false;
                    $this->self_return = $senderSettings->self_return?? false;

                    $this->ColumnDisplayNames = ['Challan No', 'Date', 'Creator', 'Receiver', 'Qty', 'Amount', 'State', 'Status', 'Payment Status', 'SFP', 'Comment'];

                    if (isset($senderSettings->tags) && $senderSettings->tags) {
                        $this->ColumnDisplayNames[] = 'Tags';
                    }
                } else {
                    $this->ColumnDisplayNames = $defaultColumnNames;
                }
            } else {
                $this->ColumnDisplayNames = $defaultColumnNames;
            }
            // }
        }
    }
    public $items = ['Item1', 'Item2', 'Item3'];
    public $selectedProducts = [];
    public $singleSelection = null;
    public $selectAll = false;



    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 1;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems);
            $this->panel = $item->panel;
            // Store $this->panel in session data
            Session::put('panel', $this->panel);

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
        // dd($template, $activeFeature);
        $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        // $template = 'sfp_challans';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);

    }

    public $sfpModal = false;
    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;

        if($variable == 'challan_sfp'){
            $this->sfpModal = true;
            $this->challan_id = $value;
        }

        $this->render(); // Ensure the render method is called tso update the filters
    }

    public $challan_series;

    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }

    public function addComment()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'sender' => 'sender']);

        $ChallanController = new ChallanController;
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

    public $selectedIds = [];


    public function sendChallan()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ChallanController = new ChallanController;
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





    public function openModal($itemId, $action)
    {
        $this->itemId = $itemId;
        $this->isOpen = true;
        // dd($itemId, $action);
        if ($action == 'sendChallan') {
            $this->modalHeading = 'Send Challan';
            $this->modalButtonText = 'Send';
            $this->modalAction = 'sendChallan';
        } elseif ($action == 'addComment') {
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        }
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

    // public function handlePrintAction()
    // {
    //         $this->printChallans($this->selectedProducts);
    // }


    public function printChallans()
    {
        $this->pdfUrls = Challan::whereIn('id', $this->selectedProducts)
                                ->get()
                                ->map(function ($challan) {
                                    return url('/pdf/' . ltrim($challan->pdf_url, '/'));
                                })
                                ->toArray();

        $this->dispatchBrowserEvent('printPdfs', ['pdfUrls' => $this->pdfUrls]);
    }

    public function printBulkChallan()
    {
        $selectedProducts = $this->selectedProducts;
        $challans = Challan::whereIn('id', $selectedProducts)->get();

        $pdfUrls = [];
        foreach ($challans as $challan) {
            $pdfUrls[] = Storage::disk('s3')->temporaryUrl($challan->pdf_url, now()->addMinutes(5));
        }

        $this->isOpen = false;
        foreach ($pdfUrls as $pdfUrl) {
            return Storage::download($pdfUrl);
        }
        // return Storage::download($pdfUrls);
    }

    // public function bulkActionsTagAndPaymentStatus($actionType, $variable)
    // {
    //     $selectedProducts = $this->selectedProducts;
    //     $this->BulkisOpen = true;
    //     if ($variable == 'bulkTags') {
    //         $this->bulkmodalHeading = 'Assign Tags';
    //         $this->modalButtonText = 'Assign';
    //         $this->bulkActions = 'assignBulkTags';

    //         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //         $allTags = Tag::where('user_id', $userId)->get();

    //         // Ensure $this->tags is a collection
    //         $this->tags = $this->tags ?? collect([]);

    //         $this->bulkAvailableEntities = $allTags->filter(function ($tag) {
    //             return !$this->tags->contains(strtolower($tag->name));
    //         });
    //         // dd($this->bulkAvailableEntities);
    //     } elseif ($variable == 'bulkPaymentStatus') {
    //         $this->bulkmodalHeading = 'Assign Payment Status';
    //         $this->modalButtonText = 'Assign';
    //         $this->bulkActions = 'assignPaymentStatus';

    //         $allStatuses = ChallanDelivery::all();

    //         // Ensure $this->selectedDeliveryStatus is a collection
    //         $this->selectedDeliveryStatus = $this->selectedDeliveryStatus ?? collect([]);
    //         // dd($this->selectedDeliveryStatus);
    //         $this->bulkAvailableEntities = $allStatuses->filter(function ($status) {
    //             return !$this->selectedDeliveryStatus->contains(strtolower($status->name));
    //         });
    //         // dd($this->bulkAvailableEntities);
    //     }
    // }




    public function assignBulkTags()
    {
        // Ensure $this->selectedTags and $this->selectedProducts are arrays
        $this->selectedTags = $this->selectedTags ?? [];
        $this->selectedProducts = $this->selectedProducts ?? [];

        // Loop through each selected item
        foreach ($this->selectedProducts as $id) {
            // Find the challan and sync the selected tags
            $challan = Challan::find($id);
            if ($challan) {
                $challan->tags()->sync($this->selectedTags);
            }
        }

        // Reset the selected tags and set the success message
        $this->selectedTags = [];
        $this->successMessage = 'Tags assigned successfully';

        // Flash the success message
        session()->flash('sentMessage', ['type' => 'success', 'content' => $this->successMessage]);

        // Close the bulk action modal
        $this->BulkModalAction();

        // Redirect to the 'sender' route with the template as a query parameter
        // return redirect()->route('sender', ['template' => 'sfp_challans']);
    }


    public function closeModal()
    {
        $this->isOpen = false;
        $this->openSearchModal = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction', 'selectedTags']);
    }
    public function BulkModalAction()
    {
        $this->BulkisOpen = false;
        $this->reset([ 'bulkmodalHeading',  'bulkActions']);
    }

    public function addBulkComment(){
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'sender' => 'sender']);
        $ChallanController = new ChallanController;
        $response = $ChallanController->addBulkComment($request, $this->selectedProducts);
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

    public function sendBulkChallan()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ChallanController = new ChallanController;
        $response = $ChallanController->sendBulk($request,  $this->selectedProducts);
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





    public function sendSelectedIds($selectedIds)
    {
        // Handle the selected IDs
        logger('Selected IDs:', $selectedIds);
    }

    public function selfAcceptChallan($id)
    {

        $request = request();
        $ChallanController = new ChallanController;

        $response = $ChallanController->selfAccept($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $template = 'sfp_challans';

            // Redirect to the 'sender' route with the template as a query parameter
            return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }
    public $saveButtonDisabled = false;

    public function saveSelfReturnChallan(){
        // dd('save');
        $this->inputsResponseDisabled = false;
    }
    public function selfReturnChallan($id)
    {

        $request = request();

        $request->merge($this->createChallanRequest);
        // dd($request);
        $ChallanController = new ChallanController;


        $response = $ChallanController->selfReturn($request, $id);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->saveButtonDisabled = true; // Adjust the condition as needed
            $template = 'sfp_challans';

            // Redirect to the 'sender' route with the template as a query parameter
            return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

        // Use the `redirect` method with a Livewire route
     }


    public function sfpChallan()
    {
        // dd($this->team_user_ids, $this->challan_id, $this->comment);
        $request = request();
        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);

        $ChallanController = new ChallanController;

        $response = $ChallanController->challanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
           // $this->innerFeatureRedirect('sfp_challans', '2');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        $request = request();
        // $request = new Request(['page' => $page]);
        $challanController = new ChallanController();
        $tableTdData = $challanController->index($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        $this->sfpModal = false;
        session()->flash('message', 'Challan SFP successfully.');
        // return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

        public $challansFiltered;

    // public function export(){

    //     $request = request();

    //     $request->merge(['challan_series' => $this->challan_series]);
    //     $request->merge(['receiver_id' => $this->receiver_id]);
    //     $request->merge(['status' => $this->status]);
    //     $request->merge(['state' => $this->state]);
    //     $request->merge([
    //         'from_date' => $this->from,
    //         'to_date' => $this->to,
    //     ]);

    //     // dd($challans);

    //     // Create an array to store the exported data
    //         $exportData  = new ChallanController;
    //         $exportedData = $exportData->exportChallan($request);
    //     }


    // public function export()
    // {
    //     $request = request();

    //     $request->merge(['challan_series' => $this->challan_series]);
    //     $request->merge(['receiver_id' => $this->receiver_id]);
    //     $request->merge(['status' => $this->status]);
    //     $request->merge(['state' => $this->state]);
    //     $request->merge([
    //         'from_date' => $this->from,
    //         'to_date' => $this->to,
    //     ]);
    //     // dd($request->all());
    //     $challanExport = new ChallanExport($request);
        // Download the CSV file
    // $response = tap($challanExport->download('challans.csv'), function () {
    //     // Redirect to the current page after the CSV file is downloaded
    //     $this->redirect(request()->header('Referer'));
    // });


    // return $response;
    // }

    public function export()
    {
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

        $challanExport = new ChallanExport($request);

        // $this->reloadPage();
        session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['challan_series', 'receiver_id', 'status', 'state', 'from', 'to']);

        $response = tap($challanExport->download('challans.csv'), function () {
            // Redirect to the current page after the CSV file is downloaded
            $this->redirect(request()->header('Referer'));
        });

        return $response;

    }


    public function saveSignature()
    {
        $signature = $this->signature;
        $columnId = $this->columnId;
        // dd( $columnId);

        // Create a new Request instance and set the 'signed' attribute to the signature data
        $request = request();
        $request->setMethod('POST');
        $request->merge(['signed' => $signature,
        'column_id' => $columnId
                    ]);

        // Create a new instance of the ChallanController
        $challanController = new ChallanController;

        // Call the uploadSignature method and pass the Request instance
        $response = $challanController->uploadSignature($request);
        // Get the original data from the response
        $data = $response->getData(true);

        if ($data['status_code'] == 200) {
            $this->successMessage = $data['message'];
            $this->reset(['signature']);
        } else {
            $this->errorMessage = $data['message'];
        }
        $template = 'sfp_challans';

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);    }

    public $searchQuery = '';
    public $bulksearchQuery = '';
    public $challanId;
    public $availableTags;
    public $availableDeliveryStatus;
    public $bulkAvailableEntities;

    public $openSearchModal = false;
    public $openPaymentStatusModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    public $tagExists = false;


    public function tagModal($itemId, $action)
    {
        if($action == 'addTags'){

            $this->itemId = $itemId;
            $this->openSearchModal = true;
            $this->searchModalHeading = 'Add Tag';
            $this->searchModalButtonText = 'Add';
            $this->searchModalAction = 'saveTags';
        }
        // $this->closeModal();
    }
    public function closeTagModal()
    {
        // dd('close');
        $this->openSearchModal = false;
        $this->openPaymentStatusModal = false;

        $this->reset(['selectedTags', 'searchTerm' ]);
    }

    public function commentModal($itemId, $action){
        $this->openPaymentStatusModal = true;
        $this->searchModalHeading = 'Add Comment';
        $this->searchModalButtonText = 'Add';
        $this->searchModalAction = 'saveComment';
    }

    public function deleteChallans($id)
    {
        $request = request();
        $ChallanController = new ChallanController;
        $response = $ChallanController->delete($request, $id);
        $result = $response->getData();
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $template = 'sfp_challans';
            session()->flash('message', $this->successMessage);
            // Redirect to the 'sender' route with the template as a query parameter
            // return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function createTag()
    {
        // dd($this->searchTerm);
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $tag = TagsTable::create([
                'name' => $this->searchTerm,
                'user_id' => $userId,
                'panel_id' => 1,
                'table_id' => 1,
            ]);
            $this->tagExists = true; // Update tagExists since the tag is now created
            // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
            $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
            // $this->saveTags();
            $this->render();
            $this->searchTerm = ''; // Clear the search term

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
            $goodsReceipt = Challan::find($itemId); // Find each Challan by its ID

            $pivotData = [];
            foreach ($selectedTagIds as $tagId) {
                $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 1, 'table_id' => 1];
            }

            // Attach the selected tags to each GoodsReceipt with additional pivot data
            if ($goodsReceipt) {
                $goodsReceipt->tableTags()->sync($pivotData);
            }
        }

        $this->closeModal();
        session()->flash('message', 'Tags saved successfully.');
    }

    public function createDeliveryStatus($tagName)
    {
        // dd($tagName);
        if (!empty($tagName)) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $newTag = ChallanDelivery::create(['name' => $tagName , 'user_id' => $userId]);
            $this->searchQuery = '';

            // Add the ID of the newly created tag to the selected tags
            $this->selectedDeliveryStatus[] = $newTag->id;
        } else {
            throw new \Exception('Tag name cannot be empty');
        }
    }

    public $selectedTags = [];
    public $selectedDeliveryStatus = [];
    public $columnName = [];
    // public $tagExists = false;
    public $tagName = '';
    public function searchTag()
    {
        if (!empty($this->searchTerm)) {
            $this->tagName = $this->searchTerm;
            $this->tagExists = TagsTable::where('name', $this->searchTerm)->exists();
        }
    }

    public function assignTags($id)
    {
        $challan = Challan::find($id);
        $challan->tags()->sync($this->selectedTags);
        $this->selectedTags = [];
        $this->successMessage = 'Tags assigned successfully';
        $template = 'sfp_challans';
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function assignDeliveryStatus($id)
    {
        // dd($this->selectedDeliveryStatus, $id);
        $challan = Challan::find($id);
        $challan->deliveryStatus()->sync($this->selectedDeliveryStatus);
        $this->selectedDeliveryStatus = [];
        $this->successMessage = 'Payment Status added successfully';
        $template = 'sfp_challans';
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }


    public function selfDelivery($id)
    {
        // dd($id);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'title' => 'Are you sure?',
            'text' => 'Are you sure you want to Self Delivered',
            'id' => $id,
        ]);
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
        $query = Challan::query();
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
        // dd($distinctCities);
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

        // Check if any filter is applied
        $isFilterApplied = array_filter($filters, function ($value) {
            return $value !== null;
        });

        // Get the count of total challans after filters are applied, only if any filter is applied
        $totalChallansCount = $isFilterApplied ? $query->count() : null;


        $teams = Team::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->id : null)->pluck('view_preference')->first();
        $defaultDriver = Auth::getDefaultDriver();
        $user = Auth::guard($defaultDriver)->user();
        $userTeamId = $user->team_id;
        $viewPreference = 'own_team'; // Assuming this is set somewhere, for example from user preferences or settings

        $challans = $query->with([
            'orderDetails' => function ($query) {
                $query->select('id', 'challan_id', 'remaining_qty');
            },
            'statuses',
            'tableTags',
            'deliveryStatus',
            'sfpBy',
            'team'
        ])
        ->whereHas('sfpBy', function ($query) {
            $query->whereNotNull('challan_id');
        })

        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('challan_statuses')
                ->whereColumn('challan_statuses.challan_id', 'challans.id')
                ->whereIn('challan_statuses.status', ['created', 'draft'])
                ->whereIn('challan_statuses.id', function ($subQuery) {
                    $subQuery->select(DB::raw('MAX(id)'))
                        ->from('challan_statuses')
                        ->whereColumn('challan_id', 'challans.id')
                        ->groupBy('challan_id');
                });
        })
        ->select('challans.*');

        // Apply filters based on default driver and view preference
        if ($defaultDriver == 'team-user' && $viewPreference == 'own_team') {
            $query->where(function ($q) use ($userTeamId) {
                $q->where('team_id', $userTeamId)
                ->orWhereNull('team_id');
            });
        }
        $this->allItemIds = $query->pluck('id')->toArray();
        $challans = $query->latest()->paginate(50);
        // dd($challans);
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
        // ->select('id', 'name')
        ->where(function($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->pluck('id', 'name'); // Changed from paginate(10) to get() to ensure it's a collection
        // dd($allTags);
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

        $this->emit('challansUpdated', $challans->pluck('id'));
        // Retrieve notifications for the given user where template_name is 'sfp_challans' and read_at is null
        $notifications = Notification::where('user_id', $userId)
        ->where('template_name', 'sfp_challans')
        ->whereNull('read_at')
        ->get();

        // Mark these notifications as read
        foreach ($notifications as $notification) {
        $notification->update(['read_at' => now()]);
        }
        return view('livewire.sender.screens.sent-sfp-challans')
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
            'totalChallansCount' => $totalChallansCount,
        ]);

    }
}
