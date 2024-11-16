<?php

namespace App\Http\Livewire\Components;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\GoodsReceipt\GoodsReceiptsController;
use App\Http\Controllers\V1\Estimate\EstimateController;
use Livewire\Component;

class SfpComponent extends Component
{
    public $sfpModal = false;
    public $team_user_ids;
    public $admin_ids = [];
    public $challanId;
    public $invoiceId;
    public $comment;
    public $statusCode;
    public $message;
    public $errorMessage;
    public $validationErrorsJson;
    public $teamMembers;
    public $invoice_id;
    public $panelType;
    public $purchase_order_id;
    public $return_challan_id;
    public $selectPage = false;
    public $selectAll = false;


    protected $listeners = ['openSfpModal'];

    public function mount($panelType)
    {
        $query = new TeamUserController;
        $query = $query->index();
        $status = $query->getStatusCode();
        $query = $query->getData();
        if ($status === 200) {
            $this->teamMembers = $query->data;
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
        $this->panelType = $panelType;
    }

    public function openSfpModal($data)
    {
        // dd($data);
        $this->sfpModal = true;
        $this->challanId = $data['challanId'];

    }

    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }




    public function createSfp()
    {
        $request = request();
        $authId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Initialize $admin_ids
        $admin_ids = [];
        if (in_array($authId, $this->team_user_ids)) {
            $admin_ids[] = $authId; // Assign it to the $admin_ids array
            $this->team_user_ids = array_diff($this->team_user_ids, [$authId]); // Remove it from the team_user_ids array
        }

        // Merge common request data
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'comment' => $this->comment,
        ]);

        // Check the panel type and call the appropriate controller
        if ($this->panelType === 'challan') {
            $request->merge(['challan_id' => $this->challanId]);
            $controller = new ChallanController;
            $response = $controller->challanSfpCreate($request);
        } elseif ($this->panelType === 'invoice') {
            $request->merge(['invoice_id' => $this->challanId]);
            $controller = new InvoiceController;
            $response = $controller->invoiceSfpCreate($request);
        }elseif($this->panelType === 'po'){
            $request->merge(['purchase_order_id' => $this->challanId]);
            $controller = new PurchaseOrderController;
            $response = $controller->poSfpCreate($request);
        }elseif($this->panelType === 'sent_return_challan'){
            $request->merge(['challan_id' => $this->challanId]);
            $controller = new ReturnChallanController;
            $response = $controller->returnChallanSfpCreate($request);
        }elseif($this->panelType === 'received_return_challan'){
            $request->merge(['challan_id' => $this->challanId]);
            $controller = new ChallanController;
            $response = $controller->challanSfpCreate($request);
        }elseif($this->panelType === 'receipt_note'){
            $request->merge(['receipt_note_id' => $this->challanId]);
            $controller = new GoodsReceiptsController;
            $response = $controller->receiptNoteSfpCreate($request);
        }elseif($this->panelType === 'purchase_order'){
            $request->merge(['purchase_order_id' => $this->challanId]);
            $controller = new PurchaseOrderController;
            $response = $controller->poSfpCreate($request);
        }elseif($this->panelType === 'quotation'){
            $request->merge(['estimate_id' => $this->challanId]);
            $controller = new EstimateController;
            $response = $controller->estimateSfpCreate($request);
            // dd($response);
        }elseif($this->panelType === 'all_invoice'){
            $request->merge(['invoice_id' => $this->challanId]);
            $controller = new InvoiceController;
            $response = $controller->invoiceSfpCreate($request);
        }elseif($this->panelType === 'sent_purchase_order'){
            $request->merge(['purchase_order_id' => $this->challanId]);
            $controller = new PurchaseOrderController;
            $response = $controller->poSfpCreate($request);
        }

        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            if ($this->panelType === 'challan') {
            redirect()->route('sender', ['template' => 'sent_challan'] )->with('message', $this->successMessage ?? $this->errorMessage);
            } elseif($this->panelType === 'invoice') {
            redirect()->route('seller', ['template' => 'sent_invoice'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'po'){
            redirect()->route('buyer', ['template' => 'purchase_order'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'all_invoice'){
            redirect()->route('buyer', ['template' => 'all_invoice'] )->with('message', $this->successMessage ?? $this->errorMessage);
            } elseif($this->panelType === 'sent_return_challan'){
            redirect()->route('receiver', ['template' => 'sent_return_challan'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'received_return_challan'){
            redirect()->route('receiver', ['template' => 'received_return_challan'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'purchase_order'){
            redirect()->route('seller', ['template' => 'purchase_order_seller'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'sent_return_challan'){
                redirect()->route('receiver', ['template' =>'sent_return_challan'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'receipt_note'){
                redirect()->route('grn', ['template' =>'sent-goods-receipt'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }elseif($this->panelType === 'quotation'){
                redirect()->route('seller', ['template' => 'sent_quotation'] )->with('message', $this->successMessage ?? $this->errorMessage);
            }
            $this->closeSfpModal();
            $this->reset(['team_user_ids', 'challanId', 'invoiceId', 'purchase_order_id', 'return_challan_id']);
            $this->emit('actions', 'SFP saved successfully.');

            $this->reset(['statusCode', 'message', 'errorMessage', 'comment']);
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function hasSelectedTeamMembers()
    {
        return !empty($this->team_user_ids);
    }

    public function render()
    {
        return view('livewire.components.sfp-component');
    }
}
