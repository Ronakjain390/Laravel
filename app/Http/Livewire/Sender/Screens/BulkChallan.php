<?php

namespace App\Http\Livewire\Sender\Screens;
use Livewire\WithFileUploads;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use App\Jobs\CreateBulkChallanJob;
use App\Models\UploadLog;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use App\Http\Controllers\V1\Challan\ChallanController;


class BulkChallan extends Component
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
    public $errorFileUrl;

    public function bulkChallantUpload()
    {
        $this->reset(['errorFileUrl', 'errorMessage', 'successMessage', 'showButtons']);

        if (!$this->uploadFile) {
            $this->dispatchBrowserEvent('show-error-message', ['message' => 'No file was uploaded.']);
            return;
        }

        $allowedMimeTypes = ['text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($this->uploadFile->getMimeType(), $allowedMimeTypes)) {
            $this->dispatchBrowserEvent('show-error-message', ['message' => 'Invalid file type. Please upload a CSV file.']);
            return;
        }

        $request = new Request();
        $requestData = [
            'file' => $this->uploadFile,
        ];
        $request->merge($requestData);

        $ChallanController = new ChallanController;

        try {
            $response = $ChallanController->bulkChallanImport($request);
            $result = $response->getData();

            $this->statusCode = $result->status_code;

            switch ($this->statusCode) {
                case 200:
                    $this->handleSuccessResponse($result);
                    break;
                case 400:
                    $this->handleValidationErrors($response);
                    break;
                case 422:
                    $this->handleValidationErrors($response);
                    break;
                case 500:
                    $this->handleServerError($result);
                    break;
                default:
                    $this->handleUnknownError($result);
                    break;
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function handleSuccessResponse($result)
    {
        $this->challanIds = $result->data->challan_ids;
        $this->successMessage = $result->message;
        $this->showButtons = true;
        $this->dispatchBrowserEvent('show-success-message', ['message' => $this->successMessage]);
        $this->reset(['statusCode', 'errorMessage']);
    }

    private function handleValidationErrors($response)
    {
        $errors = json_decode($response->content(), true)['errors'];
        // $errorMessage = is_array($errors) ? implode(', ', $errors) : $errors;
        $errorMessage = "Validation errors occurred, please check the uploaded file for errors. ". (isset($errors['file'])? "File: ". $errors['file'][0] : '');
        $this->dispatchBrowserEvent('show-error-message', ['message' => $errorMessage]);
        if ($this->statusCode === 422) {
            $this->errorFileUrl = $this->createErrorFile($errors);
            $this->reset('uploadFile');
        }
    }

    private function handleServerError($result)
    {
        $this->showButtons = false;
        $this->errorMessage = isset($result->error)
            ? "Error occurred while creating challans: " . $result->error
            : "An unknown error occurred while creating challans.";
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function handleUnknownError($result)
    {
        $this->errorMessage = json_encode((array) $result->errors);
        $this->showButtons = false;
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function handleException(\Exception $e)
    {
        $this->errorMessage = "An unexpected error occurred: " . $e->getMessage();
        $this->dispatchBrowserEvent('show-error-message', ['message' => $this->errorMessage]);
    }

    private function createErrorFile($errors)
    {
        $content = "Error Report\n\n";
        foreach ($errors as $error) {
            $content .= $error . "\n";
        }
        $fileName = 'error_report_' . time() . '.txt';
        Storage::put('public/error_reports/' . $fileName, $content);
        return Storage::url('error_reports/' . $fileName);
    }

    // public function bulkChallantUpload()
    // {
    //     $request = request();
    //     $request->merge(['file' => $this->uploadFile]);
    //     // dd($request, $this->uploadFile);
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|file|mimes:csv,txt',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors(),
    //             'status_code' => 422,
    //         ], 422);
    //     }

    //     $file = $request->file;
    //     // dd($file);
    //     $fileName = time() . '_' . $file->getClientOriginalName();
    //     $filePath = $file->storeAs('bulkChallans', $fileName);

    //      // Check if the PDF file already exists
    //     //  if (Storage::disk('s3')->exists($filePath)) {
    //     //     // Delete the older file
    //     //     Storage::disk('s3')->delete($filePath);
    //     // }
    //     // $result = Storage::disk('s3')->put($filePath, file_get_contents($file));
    //     // dd($result);

    //     $log = UploadLog::create([
    //         'file_path' => $fileName,
    //         'file_name' => $file->getClientOriginalName(),
    //         'type' => 'new',
    //         'status' => 'Pending',
    //     ]);

    //     CreateBulkChallanJob::dispatch(storage_path('app/' . $filePath), $log->id);
    //     // dd($log);
    //     return response()->json([
    //         'message' => 'File uploaded successfully. Processing will start shortly.',
    //         'log_id' => $log->id,
    //         'status_code' => 200,
    //     ], 200);

    // }

    // public function logStatus($id)
    // {
    //     $log = UploadLog::find($id);
    //     return response()->json(['status' => $log->status, 'message' => $log->message]);
    // }

    public function removeFile()
    {
        $this->uploadFile = null;
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
        return view('livewire.sender.screens.bulk-challan');
    }
}
