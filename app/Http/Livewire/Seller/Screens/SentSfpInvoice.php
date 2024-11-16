<?php

namespace App\Http\Livewire\Seller\Screens;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\TagsTable;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InvoiceStatus;
use App\Models\BuyerDetails;
use Illuminate\Http\Response;
use ZipArchive;
use App\Mail\ExportReadyMail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Exports\Seller\SentInvoiceExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class SentSfpInvoice extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $invoiceFiltersData, $status_comment,$sentMessage, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers,$invoice_series, $buyer_id, $variable , $value , $seller_id, $status, $state, $from, $to, $signature, $attributes, $columnId;
    public $searchQuery = '';
    public $team_user_ids = [];
    public $admin_ids = [];
    protected $lruCache;
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;
    public $tags;
    public $isLoading = true;
    public $team_user_id;
    public $challansFiltered;
    public $openSearchModal = false;
    public $openPaymentStatusModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    public $tagExists = false;
    public $selectedTags = [];
    public $selectedDeliveryStatus = [];
    public $columnName = [];
    public $searchTerm = '';
    public $tagName = '';
    public $sfpModal = false;

    use WithPagination;
    protected $listeners = [
        'callsfpInvoice' => 'sfpInvoice',
        'loginSuccess' => 'handleLoginSuccess',
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

    public function mount(){
        // Simulate a delay
        // sleep(3); // Delay for 3 seconds
        // $this->isLoading = false;

        $request = request();
        $sessionId = session()->getId();
        $template = request('template', 'index');
        if (view()->exists('components.panel.seller.' . $template)) {

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
            $this->ColumnDisplayNames = ['Invoice No', 'Date', 'Creator', 'Buyer', 'Qty', 'Amount', 'State', 'Status', 'SFP', 'Comment', 'Tags'];

        }

    }

    public function handleLoginSuccess()
    {
        $this->loginSuccess = true;
        $this->showModal = false;
    }

    public function searchTag()
    {
        if (!empty($this->searchTerm)) {
            $this->tagName = $this->searchTerm;
            $this->tagExists = TagsTable::where('name', $this->searchTerm)->exists();
        }
    }

    public function innerFeatureRedirect($template, $activeFeature)
    {

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

         $this->persistedTemplate = view()->exists('components.panel.seller.' . $template) ? $template : 'index';
         $this->persistedActiveFeature = view()->exists('components.panel.seller.' . $template) ? $activeFeature : null;
         //    dd( $this->persistedTemplate,
         //    $this->persistedActiveFeature);
         $this->savePersistedTemplate($template, $activeFeature);
        //  $template = 'sent_invoice';

         // Redirect to the 'seller' route with the template as a query parameter
         return redirect()->route('seller', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);

     }

     public function openModal($itemId, $action)
    {
        // dd($itemId, $action);
        $this->itemId = $itemId;
        $this->isOpen = true;

        if ($action == 'sendInvoice') {
            $this->modalHeading = 'Send Invoice';
            $this->modalButtonText = 'Send';
            $this->modalAction = 'sendInvoice';
        } elseif ($action == 'addComment') {
            // dd('addComment');
            $this->modalHeading = 'Add Comment';
            $this->modalButtonText = 'Add';
            $this->modalAction = 'addComment';
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->openSearchModal = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction']);
    }


    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;
        if($variable == 'invoice_sfp'){
            $this->sfpModal = true;
            $this->invoice_id = $value;
        }
        // dd($this->{$variable}, $value, $variable);
    }
    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }

    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }

    public function export($exportOption)
    {
        $request = request();

        $request->merge(['invoice_series' => $this->invoice_series]);
        $request->merge(['buyer_id' => $this->buyer_id]);
        $request->merge(['status' => $this->status]);
        $request->merge(['state' => $this->state]);
        $request->merge([
            'from_date' => $this->from,
            'to_date' => $this->to,
        ]);
        if ($exportOption === 'current_page') {
            $request->merge(['page' => $this->page]);
        } elseif ($exportOption === 'all_data') {
            $request->merge(['all_data' => 'all_data']);
        } elseif ($exportOption === 'filtered_data') {
            $request->merge(['filtered_data' => 'filtered_data']);
        }
        $userEmail = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->email;

        $invoiceExport = new SentInvoiceExport($request);
        $this->reset(['invoice_series', 'buyer_id', 'status', 'state', 'from', 'to']);
       // Download the CSV file

       $data = $invoiceExport->collection();
       $rowCount = $data->count();

       if ($rowCount <= 100) {
           $response = tap($invoiceExport->download('challans.csv'), function () {
               // Redirect to the current page after the CSV file is downloaded
               $this->redirect(request()->header('Referer'));
           });

           return $response;

       } else {
        //    dd('Mail');
           // Generate and store the CSV file
           $filePath = 'exports/invoice.csv';
           $invoiceExport->store($filePath, 'local');

           // Create a ZIP file and add the CSV file to it
           $zipFilePath = 'exports/invoice.zip';
           $zip = new ZipArchive();
           if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
               $zip->addFile(Storage::path($filePath), basename($filePath));
               $zip->close();
           } else {
               throw new Exception('Failed to create ZIP file');
           }
         // Define the S3 path for the ZIP file
           $s3ZipFilePath = 'exports/invoice.zip';

           // Move the ZIP file to S3
           Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');


          // Generate a temporary URL for the ZIP file on S3
           $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
           $heading = 'Sent Invoice Export';
           $message = 'The Invoice export is ready. You can download it from the following link:';

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
           return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', 'Invoice exported successfully,File is sent on Email successfully, please check');
       }

    }

    public function sfpInvoice()
    {
        $request = request();
        $authId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $admin_ids = []; // Initialize the admin_ids array

        if (in_array($authId, $this->team_user_ids)) {
            $admin_ids[] = $authId; // Add authId to admin_ids array
            $this->team_user_ids = array_diff($this->team_user_ids, [$authId]); // Remove it from team_user_ids array
        }

        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids, // Use the initialized admin_ids array
            'invoice_id' => $this->invoice_id,
            'comment' => $this->comment,
        ]);

        $invoiceController = new InvoiceController;
        $response = $invoiceController->invoiceSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $this->sfpModal = false;
        session()->flash('message', 'Challan SFP successfully.');
        return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function sendInvoice()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $InvoiceController = new InvoiceController;
        $response = $InvoiceController->send($request, $this->itemId);
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

    public function addComment()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'seller' => 'seller']);

        $InvoiceController = new InvoiceController;
        $response = $InvoiceController->addComment($request, $this->itemId);
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

     //  Tags Add and Remove
     public function tagModal($itemId, $action)
     {
         if($action == 'addTags'){

             $this->itemId = $itemId;
             $this->openSearchModal = true;
             $this->selectedTags = $this->getTagsForChallan($itemId);
             $this->searchModalHeading = 'Add Tag';
             $this->searchModalButtonText = 'Add';
             $this->searchModalAction = 'saveTags';
         }
         // $this->closeModal();
     }
     public function getTagsForChallan($itemId)
    {
        // Fetch the Challan instance by its ID
        $challan = Invoice::find($itemId);

        // Check if the Challan instance exists
        if ($challan) {
            $challan = $challan->load('tableTags');
            // Use the tags relationship to get the associated tags and pluck their IDs
            return $challan->tableTags()->pluck('tags_table.id')->toArray();
        }

        // Return an empty array if the Challan instance does not exist
        return [];
    }
     public function closeTagModal()
     {
         // dd('close');
         $this->openSearchModal = false;
         $this->openPaymentStatusModal = false;

         $this->reset(['selectedTags', 'searchTerm' ]);
     }

     public function createTag()
     {
         // dd($this->searchTerm);
             $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
             $tag = TagsTable::create([
                 'name' => $this->searchTerm,
                 'user_id' => $userId,
                 'panel_id' => 3,
                 'table_id' => 5,
             ]);
             $this->tagExists = true; // Update tagExists since the tag is now created
             // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
             $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
             // $this->saveTags();
             $this->render();
             $this->searchTerm = ''; // Clear the search term

     }
    //  public function saveTags()
    //  {
    //      $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


    //      $selectedTagIds = $this->selectedTags;
    //      // Assuming you have a GoodsReceipt model instance you're working with
    //      $goodsReceipt = Invoice::find($this->itemId); // Example: Find the GoodsReceipt by its ID

    //      // Prepare the additional pivot data
    //      $pivotData = [];
    //      foreach ($selectedTagIds as $tagId) {
    //          $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 3, 'table_id' => 5]; // Example: Set the user_id and panel_id
    //      }

    //      // Attach the selected tags to the GoodsReceipt with additional pivot data
    //      $goodsReceipt->tableTags()->sync($pivotData);
    //      $this->closeTagModal();
    //      // Add any additional logic here, such as flashing a success message to the session
    //      session()->flash('message', 'Tags saved successfully.');

    //      // Optionally, redirect or perform other actions
    //  }
    public $selectedProducts = [];
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
            $goodsReceipt = Invoice::find($itemId); // Find each Challan by its ID

            $pivotData = [];
            foreach ($selectedTagIds as $tagId) {
                $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 3, 'table_id' => 5];
            }

            // Attach the selected tags to each GoodsReceipt with additional pivot data
            if ($goodsReceipt) {
                $goodsReceipt->tableTags()->sync($pivotData);
            }
        }
        // $openSearchModal = false;
        $this->closeModal();
        return redirect()->route('seller', ['template' => 'sent_invoice'])->with('message', 'Tags saved successfully.');
        session()->flash('message', 'Tags saved successfully.');
    }

     public function assignTags($id)
     {
         $challan = Invoice::find($id);
         $challan->tags()->sync($this->selectedTags);
         $this->selectedTags = [];
         $this->successMessage = 'Tags assigned successfully';
         $template = 'sent_invoice';
         // Redirect to the 'sender' route with the template as a query parameter
         return redirect()->route('receiver', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
     }



    // Delete Invoice
    public function deleteInvoice($id)
    {
        $request = request();
        $invoiceController = new InvoiceController;
        $response = $invoiceController->delete($request, $id);
        $result = $response->getData();
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            // $this->successMessage = $result->message;
            $template = 'sent_challan';
            session()->flash('message', $this->successMessage);
            // Redirect to the 'sender' route with the template as a query parameter
            // return redirect()->route('sender', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
            $this->reset(['statusCode', 'message', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($result->errors);
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
        return view('livewire.seller.screens.sent-sfp-invoice');
    }
}
