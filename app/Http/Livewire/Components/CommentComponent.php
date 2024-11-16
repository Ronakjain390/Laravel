<?php

namespace App\Http\Livewire\Components;

use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\Estimate\EstimateController;

use Livewire\Component;

class CommentComponent extends Component
{
    public $itemId;
    public $panelId;
    public $tableId;
    public $status_comment;
    public $isOpen = false;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;

    protected $listeners = ['openCommentModal'];

    public function openCommentModal($itemId, $action)
    {
        // dd($itemId, $action);
        $this->itemId = $itemId;
        $this->isOpen = true;

        $modalProperties = [
            'sendPO' => ['Send Challan', 'Send', 'sendPO'],
            'sendEstimate' => ['Send Estimate', 'Send', 'sendEstimate'],
            'addComment' => ['Add Comment', 'Add', 'addComment'],
            'accept' => ['Accept Challan', 'Accept', 'accept'],
            'reject' => ['Reject Challan', 'Reject', 'reject'],
        ];

        if (isset($modalProperties[$action])) {
            [$this->modalHeading, $this->modalButtonText, $this->modalAction] = $modalProperties[$action];
        }
    }

    public function sendPO()
    {
        $this->prepareRequest();
        $controller = new PurchaseOrderController;
        $response = $controller->send(request(), $this->itemId);
        $this->handleResponse($response, "PO Sent Successfully");
    }

    public function sendEstimate()
    {
        $this->prepareRequest();
        $controller = new EstimateController;
        $response = $controller->send(request(), $this->itemId);
        $this->handleResponse($response, "Estimate Sent Successfully");
    }

    public function addComment()
    {
        $this->prepareRequest();
        $this->validate(['status_comment' => 'required']);

        // Add the new condition for tableId = 2 and panelId = 1
        if ($this->tableId == 2 && $this->panelId == 1) {
            request()->merge(['receiver' => 'receiver']);
        }

        $controller = $this->getController();

        $response = $controller->addComment(request(), $this->itemId);
        $this->handleResponse($response, $response->getData()->message);
        // Reset the selection
        $this->emit('resetSelection');
        $this->closeCommentModal();
    }

    public function accept()
    {
        $this->prepareRequest();
        // $this->validate(['status_comment' => 'required']);

        $controller = $this->getController();
        $response = $controller->accept(request(), $this->itemId);
        $this->handleResponse($response, "Invoice Accepted Successfully");
        $this->closeCommentModal();
    }

    public function reject()
    {
        $this->prepareRequest();
        // $this->validate(['status_comment' => 'required']);

        $controller = $this->getController();
        $response = $controller->reject(request(), $this->itemId);
        $this->handleResponse($response, "Invoice Rejected Successfully");
        $this->closeCommentModal();
    }

    private function prepareRequest()
    {
        request()->merge(['status_comment' => $this->status_comment]);
    }

    private function getController()
    {
        if ($this->panelId == 3 && $this->tableId == 6) {
            return new PurchaseOrderController;
        }elseif($this->panelId == 3 && $this->tableId == 5){
            return new InvoiceController;
        } elseif ($this->panelId == 4 && $this->tableId == 7) {
            return new PurchaseOrderController;
        } elseif ($this->panelId == 4 && $this->tableId == 8) {
            return new InvoiceController;
        }elseif($this->panelId == 1 && $this->tableId == 1){
            return new ChallanController;
        }elseif($this->panelId == 1 && $this->tableId == 2){
            return new ReturnChallanController;
        }elseif($this->panelId == 2 && $this->tableId == 3){
            return new ReturnChallanController;
        }elseif($this->panelId == 2 && $this->tableId == 4){
            return new ChallanController;
        }elseif($this->panelId == 6 && $this->tableId == 11){
            return new EstimateController;
        }
    }

    private function handleResponse($response, $message)
    {
        $result = $response->getData();
        $this->closeCommentModal();
        $this->emit('actions', $message);
    }

    public function closeCommentModal()
    {
        $this->isOpen = false;
        $this->status_comment = '';
    }

    public function mount($panelId, $tableId)
    {
        $this->panelId = $panelId;
        $this->tableId = $tableId;
    }

    public function render()
    {
        return view('livewire.components.comment-component');
    }
}
