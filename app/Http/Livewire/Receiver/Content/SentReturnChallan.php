<?php

namespace App\Http\Livewire\Receiver\Content;
use Carbon\Carbon;
use Livewire\WithPagination;
use App\Models\Challan;
use App\Models\Receiver;
use App\Models\TagsTable;
use App\Models\ReturnChallan;
use App\Mail\ExportReadyMail;
use Illuminate\Http\Request;
use App\Models\ChallanStatus;
use App\Models\Notification;
use ZipArchive;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Cache;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\Log;
use App\Exports\Receiver\ReturnChallanExport;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;


use Livewire\Component;

class SentReturnChallan extends Component
{
    use WithPagination;
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status, $state, $from, $to, $challan_series;
    public $team_user_ids = [];
    public $admin_ids = [];
    public $itemId;
    public $isOpen = false;
    public $modalHeading;
    public $isLoading = true;
    public $modalButtonText;
    public $modalAction;
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
    public function resetDates()
    {
        $this->from = null;
        $this->to = null;
        $this->emit('dates-reset');
    }


    protected $listeners = [
        'actions' => 'handleMessage',
    ];

    public function loadData()
    {
        $this->isLoading = false;
    }
    public function handleMessage($message)
    {
        // Show the success message
        $this->dispatchBrowserEvent('show-success-message', [$message]);

        // Update the table data (you can call a method to refresh the data)
        $this->render();
    }

    public function mount()
    {
        $request = request();
        $template = request('template', 'index');

        if (view()->exists('components.panel.receiver.' . $template)) {
            $this->persistedTemplate = $template;
            $this->persistedActiveFeature = $template; // Update this as needed
            $this->isMobile = isMobileUserAgent($request->header('User-Agent'));

            $response = app(UserAuthController::class)->user_details($request)->getData();
            // $response = $response->getData();
            if ($response->success == "true") {
                $this->UserDetails = $response->user->plans;
                $this->mainUser = json_encode($response->user);
                $this->successMessage = $response->message;
                $this->reset(['errorMessage', 'successMessage']);
            } else {
                $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
            }
            $query = new TeamUserController();
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
                'feature_id' => '8',
                'panel_id' => '2',
            ];
            $request->merge($columnFilterDataset);
            $this->ColumnDisplayNames = ['Challan No', 'Date', 'Creator', 'Receiver',  'Qty', 'Amount','State ', 'Status', 'SFP', 'Comment', 'Tags'];
        }
    }

    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    public function handleFeatureRoute($template, $activeFeature)
    {
        $this->persistedTemplate = view()->exists('components.panel.receiver.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.receiver.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

       // Redirect to the 'sender' route with the template as a query parameter
       return redirect()->route('receiver', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function updateVariable($variable, $value)
    {
        $this->{$variable} = $value;
        // dd($this->variable, $value);
        if($variable == 'challan_sfp'){
            $this->sfpModal = true;
            $this->challan_id = $value;
        }
    }
    public function closeSfpModal()
    {
        $this->sfpModal = false;
    }


    public function openModal($itemId, $action)
    {
        $this->itemId = $itemId;
        $this->isOpen = true;

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

    public function tagModal($itemId, $action)
    {
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
        $challan = ReturnChallan::find($itemId);
        $challan = $challan->load('tableTags');

        // Check if the Challan instance exists
        if ($challan) {
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

    public function closeModal()
    {
        $this->isOpen = false;
        $this->openSearchModal = false;
        $this->reset(['status_comment', 'itemId', 'modalHeading', 'modalButtonText', 'modalAction', 'selectedTags', 'searchTerm']);
    }
    public function createTag()
    {
        // dd($this->searchTerm);
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $tag = TagsTable::create([
                'name' => $this->searchTerm,
                'user_id' => $userId,
                'panel_id' => 2,
                'table_id' => 3,
            ]);
            $this->tagExists = true; // Update tagExists since the tag is now created
            // $this->emit('tagCreated', $tag->id); // Optional: Emit an event if needed
            $this->selectedTags[] = $tag->id; // Optional: Add the tag to the selected tags
            // $this->saveTags();
            $this->render();
            $this->searchTerm = ''; // Clear the search term

    }
    // public function saveTags()
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;


    //     $selectedTagIds = $this->selectedTags;
    //     // Assuming you have a GoodsReceipt model instance you're working with
    //     $goodsReceipt = ReturnChallan::find($this->itemId); // Example: Find the GoodsReceipt by its ID

    //     // Prepare the additional pivot data
    //     $pivotData = [];
    //     foreach ($selectedTagIds as $tagId) {
    //         $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 2, 'table_id' => 4]; // Example: Set the user_id and panel_id
    //     }

    //     // Attach the selected tags to the GoodsReceipt with additional pivot data
    //     $goodsReceipt->tableTags()->sync($pivotData);
    //     $this->closeTagModal();
    //     // Add any additional logic here, such as flashing a success message to the session
    //     session()->flash('message', 'Tags saved successfully.');

    //     // Optionally, redirect or perform other actions
    // }
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
        // dd($selectedTagIds);
        foreach ($this->itemId as $itemId) {
            $goodsReceipt = ReturnChallan::find($itemId); // Find each Challan by its ID

            $pivotData = [];
            foreach ($selectedTagIds as $tagId) {
                $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => 2, 'table_id' => 4];
            }

            // Attach the selected tags to each GoodsReceipt with additional pivot data
            if ($goodsReceipt) {
                $goodsReceipt->tableTags()->sync($pivotData);
                // dd($goodsReceipt->tableTags());
            }
        }

        $this->closeTagModal();
        session()->flash('message', 'Tags saved successfully.');
    }

    public function assignTags($id)
    {
        $challan = ReturnChallan::find($id);
        $challan->tags()->sync($this->selectedTags);
        $this->selectedTags = [];
        $this->successMessage = 'Tags assigned successfully';
        $template = 'sent_return_challan';
        // Redirect to the 'sender' route with the template as a query parameter
        return redirect()->route('receiver', ['template' => $template])->with('message', $this->successMessage ?? $this->errorMessage);
    }

    public function sfpReturnChallan()
    {

        $request = request();
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        $ChallanController = new ReturnChallanController;
        // dd($request);
        $response = $ChallanController->returnChallanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage', 'validationErrorsJson']);
            $this->innerFeatureRedirect('sent_return_challan', '9');
            $this->successMessage = $result->message;
        } else {
            // dd($result);
            $this->errorMessage = json_encode($result->errors);
        }
        redirect()->route('receiver')->with('message', $this->successMessage ?? $this->errorMessage);
    }
    // public function sendChallan($id)
    // {

    //     $request = request();
    //     $request->merge(['status_comment' => $this->status_comment]);
    //     // dd($request);
    //     $ChallanController = new ReturnChallanController;
    //     $response = $ChallanController->send($request, $id);
    //     $result = $response->getData();
    //     // dd($result);
    //     // Set the status code and message received from the result
    //     $this->statusCode = $result->status_code;

    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         $this->innerFeatureRedirect('sent_return_challan', '8');
    //         session()->flash('success', 'Challan send successfully.');

    //         $this->reset(['statusCode', 'message', 'errorMessage']);
    //     } else {
    //         $this->errorMessage = json_encode($result->errors);
    //     }
    //     return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    // }

    public function sendChallan()
    {
        $request = request();
        $request->merge(['status_comment' => $this->status_comment]);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->send($request, $this->itemId);
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
    public function sfpChallan()
    {
        $request = request();

        $admin_ids = is_array($this->admin_ids) ? $this->admin_ids : [$this->admin_ids];
        $request->merge([
            'team_user_ids' => $this->team_user_ids,
            'admin_ids' => $admin_ids,
            'challan_id' => $this->challan_id,
            'comment' => $this->comment,
        ]);
        // dd($request);
        $ChallanController = new ReturnChallanController;

        $response = $ChallanController->returnChallanSfpCreate($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        if ($result->status_code === 200) {
            $this->reset(['statusCode', 'message', 'errorMessage']);
            $this->innerFeatureRedirect('sent_return_challan', '8');
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        // $request = $request();

        return redirect()->route('receiver', ['template' => 'sent_return_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

    }
    public function innerFeatureRedirect($template, $activeFeature)
    {
        $panel_id = 2;
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });
        // dd($filteredItems);
        if (!empty($filteredItems)) {
            $item = (object) reset($filteredItems); // Get the first item
            $this->panel = $item->panel;
            // dd($this->panel);
            // Store $this->panel in session data
            Session::put('panel', $this->panel);

        }

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
    public function addComment()
    {
        // dd('sdf');
        $request = request();
        $request->merge(['status_comment' => $this->status_comment, 'sender'=> 'sender']);

        $ReturnChallanController = new ReturnChallanController;
        $response = $ReturnChallanController->addComment($request, $this->itemId);
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


    public function export($exportOption)
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
        $challanExport = new ReturnChallanExport($request);

        // $this->reloadPage();
        // session()->flash('sentMessage', ['type' => 'success', 'content' => 'Challans exported successfully.']);
        $this->reset(['challan_series', 'receiver_id', 'status', 'state', 'from', 'to']);
        $data = $challanExport->collection();
        $rowCount = $data->count();
        // dd($rowCount);
        if ($rowCount <= 100) {
            $response = tap($challanExport->download('challans.csv'), function () {
                // Redirect to the current page after the CSV file is downloaded
                $this->redirect(request()->header('Referer'));
            });

            return $response;

        } else {
            // dd('Mail');
            // Generate and store the CSV file
            $filePath = 'exports/challans.csv';
            $challanExport->store($filePath, 'local');

            // Create a ZIP file and add the CSV file to it
            $zipFilePath = 'exports/challans.zip';
            $zip = new ZipArchive();
            if ($zip->open(Storage::path($zipFilePath), ZipArchive::CREATE) === TRUE) {
                $zip->addFile(Storage::path($filePath), basename($filePath));
                $zip->close();
            } else {
                throw new Exception('Failed to create ZIP file');
            }
          // Define the S3 path for the ZIP file
            $s3ZipFilePath = 'exports/challans.zip';

            // Move the ZIP file to S3
            Storage::disk('s3')->put($s3ZipFilePath, Storage::get($zipFilePath), 'public');


           // Generate a temporary URL for the ZIP file on S3
            $temporaryUrl = Storage::disk('s3')->temporaryUrl($s3ZipFilePath, now()->addMinutes(30));
            $heading = 'Sent Challan Balance Export';
            $message = 'The Challan export is ready. You can download it from the following link:';

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

   public $tags;
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
        $filters = [
            'challan_series' => $this->challan_series,
            'receiver_id' => $this->receiver_id,
            'status' => $this->status,
            'state' => $this->state,
            'from_date' => $this->from,
            'to_date' => $this->to,
            'tag' => $this->tags, // Add the tag filter
        ];
        //
        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }
        // dd($request);
        $query = ReturnChallan::query();
        $combinedValues = [];
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $query->where('sender_id', $userId);

            // Fetch the distinct filter values for ReturnChallan table (for this user)
            $distinctReturnChallanSeries = $query->distinct()->pluck('challan_series');
            $distinctReturnChallanSeriesNum = $query->distinct()->pluck('series_num');
            // dd($distinctReturnChallanSeriesNum);
            $distinctSenderIds = $query->distinct()->pluck('sender_id');
            $distinctReceiverIds = $query->distinct()->pluck('receiver','receiver_id');
            // $distinctStatuses = Status::distinct()->pluck('status');


            // Fetch the distinct "state" and "city" values from ReceiverDetail table for receivers of this user
            $distinctStates = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('state');

            $distinctCities = ReceiverDetails::whereIn('receiver_id', function ($query) use ($request) {
                $query->select('id')->from('receivers')->where('user_id', $request->receiver_id);
            })->distinct()->pluck('city');

            // Loop through each element of $distinctChallanSeries
            foreach ($distinctReturnChallanSeries as $series) {
                // Loop through each element of $distinctChallanSeriesNum
                foreach ($distinctReturnChallanSeriesNum as $num) {
                    // Combine the series and number and push it into the combinedValues array
                    $combinedValues[] = $series . '-' . $num;
                }
            }
            // Filter by receiver_id
            if ($request->has('receiver_id')) {
                $query->where('receiver_id', $request->receiver_id);
            }
            if ($request->has('challan_series')) {
                $searchTerm = $request->challan_series;

                // Find the position of the last '-' in the string
                $lastDashPos = strrpos($searchTerm, '-');

                if ($lastDashPos !== false) {
                    // Split the string into series and number
                    $series = substr($searchTerm, 0, $lastDashPos);
                    $num = substr($searchTerm, $lastDashPos + 1);
                    // dd($series, $num);
                    // Perform the search
                    $query->where('challan_series', $series)
                          ->where('series_num', $num);
                } else {
                    // Invalid search term format, handle accordingly
                    // For example, you could return an error message or ignore the filter
                }
            }
            // Filter by date
            if ($request->from_date && $request->to_date) {
               $from = $request->from_date;
               $to = $request->to_date;
               $query->whereBetween('challan_date', [$from, $to]);
            }
            // Filter by status
            if ($request->has('status')) {
              $query->whereHas('statuses', function ($q) use ($request) {
                  $q->latest()->where('status', $request->status);
              });
            }
             // Check if any filter is applied
            $isFilterApplied = array_filter($filters, function ($value) {
                return $value !== null;
            });

            // Get the count of total challans after filters are applied, only if any filter is applied
            $totalChallansCount = $isFilterApplied ? $query->count() : null;

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

            $returnChallans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp','tableTags'])
            ->select('return_challans.*')->latest()->paginate(50);
            // dd($returnChallans);

            $allTagss = TagsTable::where('panel_id', 2)
            ->where('user_id', $userId)
            ->where('table_id', 3)
            // ->select('id', 'name')
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->pluck('id', 'name');

            $tags = TagsTable::where('panel_id', 2)
            ->where('user_id', $userId)
            ->where('table_id', 3)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->paginate(10);

            $allTags = TagsTable::where('panel_id', 2)
                    ->where('user_id', $userId)
                    ->where('table_id', 3)
                    ->get();

            $nonMatchingTags = $allTags->filter(function($tag) {
            return stripos($tag->name, $this->searchTerm) === false;
            });

            $isSearchTermMatched = $allTags->contains(function($tag) {
                return stripos($tag->name, $this->searchTerm) !== false;
            });

             // Retrieve notifications for the given user where template_name is 'sent_challan' and read_at is null
            $notifications = Notification::where('user_id', $userId)
            ->where('template_name', 'sent_return_challan')
            ->whereNull('read_at')
            ->get();

            // Mark these notifications as read
            foreach ($notifications as $notification) {
            $notification->update(['read_at' => now()]);
            }

            return view('livewire.receiver.content.sent-return-challan',compact('returnChallans'))
            ->with(['distinctReturnChallanSeries'=> $distinctReturnChallanSeries,
            'tagss' => $tags,
            'allTagss' => $allTags,
            'isSearchTermMatched' => $isSearchTermMatched,
            'merged_challan_series' => $combinedValues,
            'sender_id' => $distinctSenderIds,
            'receiver_ids' => $distinctReceiverIds,
            'state' => $distinctStates,
            'city' => $distinctCities,
            'series_num' => $distinctReturnChallanSeriesNum,
            'totalChallansCount' => $totalChallansCount,
        ]);
    }
}
