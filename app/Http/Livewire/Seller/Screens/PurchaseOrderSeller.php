<?php

namespace App\Http\Livewire\Seller\Screens;

use App\Http\Controllers\V1\Team\TeamController;
use Livewire\WithPagination;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PurchaseOrderSeller extends Component
{
    use WithPagination;
    public $persistedTemplate, $persistedActiveFeature,$successMessage, $isMobile, $mainUser, $UserDetails, $user, $teamMembers, $errorMessage,$status,$tag, $state, $from, $to;
    public $isLoading = true;
    public $itemId = [];
    public $isOpen = false;
    public $BulkisOpen = false;
    public $statusCode;
    public $modalHeading;
    public $bulkmodalHeading;
    public $status_comment;
    public $modalButtonText;
    public $modalAction;
    public $BulkModalAction;
    public $bulkSubHeading;
    public $bulkActions;
    public $tags;
    public $searchTerm = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    public $sfpModal = false;
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
    // public $purchaseOrders; // Change this to protected

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

    public function innerFeatureRedirect($template, $activeFeature)
    {
        // dd($template, $activeFeature);
        $panel_id = 3;
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
         $this->persistedTemplate = view()->exists('components.panel.seller.' . $template) ? $template : 'index';
         $this->persistedActiveFeature = view()->exists('components.panel.seller.' . $template) ? $activeFeature : null;
         $this->savePersistedTemplate($template, $activeFeature);

         // $template = 'sent_challan';

         // Redirect to the 'seller' route with the template as a query parameter
         return redirect()->route('seller', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);

     }
     public function openModal($itemId, $action)
    {
        $this->itemId = $itemId;
        $this->isOpen = true;

        if ($action == 'addComment') {
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        } elseif ($action == 'accept'){
            $this->modalHeading = 'Accept Purchase Order';
            $this->modalButtonText = 'Accept';
            $this->modalAction = 'accept';
        } elseif ($action == 'reject'){
            $this->modalHeading = 'Reject Purchase Order';
            $this->modalButtonText = 'Reject';
            $this->modalAction = 'reject';
        }
    }
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction']);
    }

    public function accept()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);
        // dd($request, $this->itemId);
        $ReturnChallanController = new PurchaseOrderController;
        $response = $ReturnChallanController->accept($request, $this->itemId);
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

    public function reject()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $PurchaseOrderController = new PurchaseOrderController;
        $response = $PurchaseOrderController->reject($request, $this->itemId);
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

    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }

    public function poToInvoice($id)
    {
        // Store the ID in the session
        Session::put('po_to_invoice_id', $id);

         // Redirect to the specified route
        return redirect()->route('seller', ['template' => 'po-to-invoice']);
    }

    public function mount()
    {
        $request = request();

        session()->put('previous_url', url()->current());
        $template = request('template', 'index');

        switch ($this->persistedTemplate) {
            case 'po-to-invoice':
                // dd($this->persistedTemplate);
                $this->poToInvoice($request);
                break;
            }


        // if (view()->exists('components.panel.sender.' . $template)) {
            // $this->persistedTemplate = $template;
            // $this->persistedActiveFeature = $template;
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

            $this->ColumnDisplayNames = ['PO No',  'Date', 'Creator',   'Qty', 'Amount', 'State', 'Status', 'SFP', 'Comment', 'Tags'];

            // if (Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->payment_status) {
            //     $this->ColumnDisplayNames[] = 'Payment Status';
            // }


            $this->ColumnDisplayNames = $this->ColumnDisplayNames;
        // }

        // $this->fetchPurchaseOrders();
    }

    // public function fetchPurchaseOrders()
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user'
    //         ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
    //         : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //     $query = PurchaseOrder::query()->orderByDesc('id')->where('seller_id', $userId);

    //     $query->with([
    //         'orderDetails' => function ($query) {
    //             $query->select('id', 'purchase_order_id');
    //         },
    //         'statuses' => function ($query) {
    //             $query->select('id', 'purchase_order_id', 'status', 'created_at', 'user_name');
    //         },
    //         'sfpBy' => function ($query) {
    //             $query->select('id', 'purchase_order_id', 'sfp_by_name', 'sfp_by_id','sfp_to_id', 'sfp_to_name', 'created_at');
    //         },
    //         'sellerUser',
    //         'buyerUser',
    //     ]);


    // //     // Store the result in a protected property
    //     $purchaseOrders = $query->paginate(50);
    // //       // Debugging: Check what data is being fetched
    // // dd($this->purchaseOrders);
    // // }

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
        $userId = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = PurchaseOrder::query()->orderByDesc('id')->where('buyer_id', $userId);

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

        $purchaseOrders = $query->paginate(50);
        // dd($purchaseOrders);
        return view('livewire.seller.screens.purchase-order-seller', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }
}
