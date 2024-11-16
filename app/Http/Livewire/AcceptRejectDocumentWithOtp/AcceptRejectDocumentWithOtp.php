<?php

namespace App\Http\Livewire\AcceptRejectDocumentWithOtp;

use App\Models\Challan;
use App\Models\Invoice;
use App\Models\Estimates;
use Livewire\Component;
use App\Models\CompanyLogo;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Estimate\EstimateController;

class AcceptRejectDocumentWithOtp extends Component
{
    public $documentType;
    public $documentId;
    public $action;
    public $emailOrPhone = '';
    public $otp = '';
    public $email;
    public $phone;
    public $successMessage;
    public $errorMessage;
    public $status;
    public $documentHeading;
    public $showOtpModal = false;

    public function mount($type, $documentId)
    {
        $this->documentType = $type;
        $this->documentId = str_replace('{{1}}', '', $documentId);

        $document = $this->getDocument()->first();

        if (!$document) {
            throw new \Exception("Document not found");
        }

        switch ($this->documentType) {
            case 'challan':
                $this->userName = $document->sender;
                $this->documentHeading = CompanyLogo::where('user_id', $document->receiver_id)->pluck('challan_heading')->first() ?: 'Challan';
                $this->email = $document->receiverUser->email;
                $this->phone = $document->receiverUser->phone;
                break;

            case 'invoice':
                $this->userName = $document->buyer;
                $headingField = $this->documentType . '_heading';
                $this->documentHeading = CompanyLogo::where('user_id', $document->buyer_id)->pluck($headingField)->first() ?: 'Invoice';
                $this->email = $document->buyerUser->email;
                $this->phone = $document->buyerUser->phone;
                break;

            case 'estimate':
                $this->userName = $document->buyer;
                $headingField = $this->documentType . '_heading';
                $this->documentHeading = CompanyLogo::where('user_id', $document->buyer_id)->pluck($headingField)->first() ?: 'Estimate';
                $this->email = $document->buyerUser->email;
                $this->phone = $document->buyerUser->phone;
                break;
            case 'purchase_order':
                $this->userName = $document->buyer_name;
                $headingField = $this->documentType . '_heading';
                $this->documentHeading = CompanyLogo::where('user_id', $document->buyer_id)->pluck('po_heading')->first()?: 'Purchase Order';
                $this->email = $document->buyerUser->email;
                $this->phone = $document->buyerUser->phone;
                break;
        }

        if ($document->statuses->isNotEmpty()) {
            $latestStatus = $document->statuses->first()->status;
            if ($latestStatus === 'draft' || $latestStatus === 'sent') {
                // Email and phone are already set above
            } elseif ($latestStatus === 'accept' || $latestStatus === 'reject') {
                $this->status = $latestStatus;
                session()->flash('Action Performed', 'Action Performed');
            }
        }
    }

    private function getDocument()
    {
        switch ($this->documentType) {
            case 'challan':
                return Challan::where('id', $this->documentId)->with('receiverUser', 'statuses');
            case 'invoice':
                return Invoice::where('id', $this->documentId)->with('buyerUser', 'sellerUser', 'statuses');
            case 'estimate':
                return Estimates::where('id', $this->documentId)->with('buyerUser', 'sellerUser', 'statuses');
            default:
                throw new \Exception("Invalid document type");
        }
    }

    private function getHeadingField()
    {
        switch ($this->documentType) {
            case 'challan':
                return 'challan_heading';
            case 'invoice':
                return 'invoice_heading';
            case 'estimate':
                return 'estimate_heading';
            default:
                throw new \Exception("Invalid document type");
        }
    }

    public function acceptReject($action){
        $this->action = $action;
        $this->showOtpModal = true;
        $request = request();
            $request->merge([
                'email_or_phone'  => $this->phone,
            ]);
            // dd($action);
        $userAuthController = new UserAuthController;

            // Login the user and get the response
            $response = $userAuthController->sendOTPForAcceptReject($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->successMessage = $response->message;
                $this->reset(['errorMessage']);


            } else {
                $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
            }
            // Emit event to open the modal
            $this->emit('openOtpModal');
    }

    public function validateOTPForLogin()
    {
        $request = request();
        $request->merge([
            'phone_number' => $this->phone,
            'otp' => $this->otp,
        ]);

        $userAuthController = new UserAuthController;
        $response = $userAuthController->validateOTPForLogin($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);

            // Check the action and perform the corresponding logic
            if ($this->action == 'accept') {
                $this->processAcceptAction($request);
            } elseif ($this->action == 'reject') {
                $this->processRejectAction($request);
            }

        } else {
            $this->errorMessage = json_encode($response->error ?? [[$response->message]]);
        }
    }

    protected function processAcceptAction($request)
    {
        $controller = $this->getController();
        $response = $controller->accept($request, $this->documentId);
        $this->processDocumentControllerResponse($response, ucfirst($this->documentType) . ' accepted successfully.');
    }

    protected function processRejectAction($request)
    {
        $controller = $this->getController();
        $response = $controller->reject($request, $this->documentId);
        $this->processDocumentControllerResponse($response, ucfirst($this->documentType) . ' rejected successfully.');
    }

    private function getController()
    {
        switch ($this->documentType) {
            case 'challan':
                return new ChallanController;
            case 'invoice':
                return new InvoiceController;
            case 'estimate':
                return new EstimateController;
            default:
                throw new \Exception("Invalid document type");
        }
    }

    protected function processDocumentControllerResponse($response, $successMessage)
    {
        $result = $response->getData();

        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            session()->flash('success', $successMessage);
            // return redirect()->back()->with('Challan Accepted');
            session()->flush();
            // $this->reset(['errorMessage']);
            return redirect()->route('login')->with(['Challan' => $this->action]);
        } else {
            $this->errorMessage = json_encode($result->errors);
            session()->flush();
        }
    }

    public function render()
    {
        return view('livewire.accept-reject-document-with-otp.accept-reject-document-with-otp')
            ->extends('layouts.home.app')->section('body');
    }
}
