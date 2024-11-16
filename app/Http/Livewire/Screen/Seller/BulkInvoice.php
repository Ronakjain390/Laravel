<?php

namespace App\Http\Livewire\Screen\Seller;

use Livewire\WithFileUploads;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use App\Jobs\CreateBulkChallanJob;
use App\Models\UploadLog;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use App\Http\Controllers\V1\Invoice\InvoiceController;

class BulkInvoice extends Component
{
    use WithFileUploads;
    public $uploadFile;
    public $updateFile;
    public $challanIds;
    public $showButtons = false;
    public $fileF;
    public $sendChoice;
    public $statusCode;
    public $errorMessage;
    public $persistedTemplate;
    public $persistedActiveFeature;
    public $features = [];
    public $template;
    public $activeFeature;
    public $successMessage;
    public function innerFeatureRedirect($template, $activeFeature)
    {
        $this->handleFeatureRoute($template, $activeFeature);
        // $this->emit('innerFeatureRoute',$template,$activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
    public function handleFeatureRoute($template, $activeFeature)
    {

        $viewPath = 'components.panel.' . 'sender' . '.' . $template;

        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        // dd($this->persistedTemplate, $activeFeature);
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('sender', ['template' => $this->persistedTemplate]);
    }

     // Method to save the $persistedTemplate value to the session
     public function savePersistedTemplate($template, $activeFeature = null)
     {
         session(['persistedTemplate' => $template]);
         session(['persistedActiveFeature' => $activeFeature]);
     }
    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
    ];

    public function bulkInvoiceUpload()
    {
        $request = request();
        // Create a new Request instance
        $request = new Request();

        // Merge the file data with the existing request data
        $requestData = [
            'field_name' => 'value', // Replace with your other request data
            'file' => $this->uploadFile,
        ];
        // dd($request);
        $this->uploadFile = $this->uploadFile;
        $request->merge($requestData);
        // Create instances of necessary classes
        // dd($request);
        $InvoiceController = new InvoiceController;


        $response = $InvoiceController->bulkInvoiceImport($request);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        // $invoiceIds   = $result->data->invoice_ids;
        // $this->invoiceIds = $invoiceIds;

        if ($result->status_code === 200) {
            $invoiceIds = $result->data->invoice_ids;

            // Assign the challan_ids to the $invoiceIds property
            $this->invoiceIds = $invoiceIds;

            $this->successMessage = $result->message;
            $this->showButtons = true;
            session()->flash('success', $this->successMessage);

            $this->reset(['statusCode', 'errorMessage']);
        } elseif ($this->statusCode === 400) {
            // Handle the error
            $this->showButtons = false;
            $this->errorMessage = $result->message;
            session()->flash('error', $this->errorMessage);
        } elseif ($this->statusCode === 422) {
            // Handle the validation error
            $this->showButtons = false;
            $this->errorMessage = $result->message;
            session()->flash('error', $this->errorMessage);
        } elseif ($this->statusCode === 500) {
            // Handle the error
            $this->showButtons = false;
           // Handle the error
           $this->showButtons = false;

           // Check if the error property exists
            if (isset($result->error)) {
                $this->errorMessage = "Error occurred while creating challans: " . $result->error;
            } else {
                $this->errorMessage = "An unknown error occurred while creating challans.";
            }
            session()->flash('error', $this->errorMessage);
        } else {
            $this->errorMessage = json_encode((array) $result->errors);
            $this->showButtons = false;
        }
    }

            public function sendChallans($choice)
    {
        // dd($choice);
        $request = request();
        $this->sendChoice = $choice;
        // Perform the necessary actions based on the user's choice
        if ($choice === 'send') {
            foreach($this->challanIds as $id){
                // dd($challanId);
                $ChallanController = new ChallanController;

                $response = $ChallanController->send($request, $id);
                $result = $response->getData();
            }
            $this->innerFeatureRedirect('sent_challan', '3');
            return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
            // Code to send the challans immediately
            // ...
        } else {
            // Code to send the challans later
            // ...
            return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);
        }
    }




    public function render()
    {
        return view('livewire.screen.seller.bulk-invoice');
    }
}
