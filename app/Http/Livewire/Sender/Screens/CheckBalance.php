<?php

namespace App\Http\Livewire\Sender\Screens;

use ZipArchive;
use Livewire\Component;
use Carbon\Carbon;
use Spatie\Async\Pool;
use App\Models\Challan;
use Livewire\Redirector;
use App\Mail\ExportReadyMail;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use App\Models\Receiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\HtmlPart;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\ChallanStatus;
use App\Exports\Sender\CheckBalanceExport;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class CheckBalance extends Component
{
    public $errorMessage, $successMessage, $persistedTemplate, $challanFiltersData, $status_comment, $statusCode, $message, $comment, $errors;
    public $mainUser ,$teamMembers ,$variable , $value , $receiver_id, $status, $state, $from, $to, $challan_series, $recvdfrom, $recvdto , $sent_article, $recvd_article;
    public $team_user_ids = [];
    public $isLoading = true;

    use WithPagination;
    public function mount(){
        $request = request();
        $this->loadData();
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            $this->persistedActiveFeature = view()->exists('components.panel.sender.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
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
        }
        $this->ColumnDisplayNames = ['Receiver', 'Sent Date', 'Challan No', 'Article', 'Qty Sent','Sent Status', 'Received Challan No', 'Received Date', 'Received Article', 'Received Qty','Received Status', 'Balance', 'Margin Qty'];
    }


    public function loadData()
    {
        $this->isLoading = true;

        // Your existing logic to fetch the data
        $this->render();

        $this->isLoading = false;
    }
    public function innerFeatureRedirect($template, $activeFeature)
    {
        $this->handleFeatureRoute($template, $activeFeature);
        // $this->emit('innerFeatureRoute',$template,$activeFeature);
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

        $this->persistedTemplate = view()->exists('components.panel.sender.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.sender.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);


        // Emit the 'featureRoute' event with two separate parameters
        // $this->emit('featureRoute', $template, $activeFeature);
    }


    // In your Livewire component
    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
        $this->{$variable} = $value;
    }


    public function export($exportOption)
    {
        $request = request();

        $request->merge(['challan_series' => $this->challan_series]);
        $request->merge(['receiver_id' => $this->receiver_id]);
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

        $challanExport = new CheckBalanceExport($request);
        $this->reset(['challan_series', 'receiver_id', 'status', 'state', 'from', 'to']);

        $userEmail = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->email;
        // Get the data and count the number of rows
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
            $heading = 'Challan Balance Export';
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

    public function placeholder()
    {
        return view('livewire.sender.screens.placeholders');
    }

    private function getTotalChallansCount($userId)
    {
        $query = $this->buildChallanQuery($userId);
        return $query->count();
    }

    public function render()
    {
        $request = request();

        $this->mergeRequestParameters($request);

        $userId = $this->getUserId();
        $query = $this->buildQuery($userId, $request);

        $this->filterByChallanSeries($request, $query);
        $this->filterByDateRange($request, $query);
        $this->filterByStatus($request, $query);

        $combinedValues = $this->getCombinedValues($query);

        $distinctSenderIds = $this->getDistinctValues($query, 'sender_id', 'sender');
        $distinctReceiverIds = $this->getDistinctValues($query, 'receiver_id', 'receiver');

        $distinctCities = $this->getDistinctCities($userId);

        $challans = $this->getChallans($userId);

        // Check if any filters are applied
        $filtersApplied = $this->areFiltersApplied($request);

        // Only calculate totalChallansCount if filters are applied
        $totalChallansCount = $filtersApplied ? $this->getTotalChallansCount($userId) : null;

        $distinctSentArticles = $this->getDistinctArticles('sent');
        $distinctReceivedArticles = $this->getDistinctArticles('received');

        $organizedData = $this->organizeData($challans);

        return view('livewire.sender.screens.check-balance')->with([
            'organizedData' => $organizedData,
            'challans' => $challans,
            'distinctReceivedArticles' => $distinctReceivedArticles,
            'merged_challan_series' => $combinedValues,
            'sender_id' => $distinctSenderIds,
            'receiver_ids' => $distinctReceiverIds,
            'distinctSentArticles' => $distinctSentArticles,
            'distinctReceivedArticles' => $distinctReceivedArticles,
            'city' => $distinctCities,
            'totalChallansCount' => $totalChallansCount,
        ]);
    }

    // New method to check if any filters are applied
    private function areFiltersApplied($request)
    {
        return $request->filled('receiver_id') ||
            $request->filled('status') ||
            $request->filled('challan_series') ||
            $request->filled('from') ||
            $request->filled('to') ||
            $request->filled('recvdfrom') ||
            $request->filled('recvdto');
    }

    private function mergeRequestParameters($request)
    {
        // dd($request);
        if ($this->challan_series) {
            $request->merge(['challan_series' => $this->challan_series]);
        }
        if ($this->receiver_id) {
            $request->merge(['receiver_id' => $this->receiver_id]);
        }
        if ($this->status) {
            $request->merge(['status' => $this->status]);
        }
        if ($this->state) {
            $request->merge(['state' => $this->state]);
        }
        if ($this->from || $this->to) {
            $request->merge([
                'from_date' => $this->from,
                'to_date' => $this->to,
            ]);
        }
    }

private function getUserId()
{
    return Auth::getDefaultDriver() == 'team-user'
        ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
        : Auth::guard(Auth::getDefaultDriver())->user()->id;
}

private function buildQuery($userId, $request)
{
    $query = Challan::query();
    $query->where('sender_id', $userId);
    // dd($this->receiver_id);
    if($this->receiver_id != null){
        $query->where('receiver_id', $this->receiver_id);
    }

    return $query;
}

private function filterByChallanSeries($request, $query)
{
    if ($request->has('challan_series')) {
        $searchTerm = $request->challan_series;
        $lastDashPos = strrpos($searchTerm, '-');

        if ($lastDashPos !== false) {
            $series = substr($searchTerm, 0, $lastDashPos);
            $num = substr($searchTerm, $lastDashPos + 1);

            $query->where('challan_series', $series)
                  ->where('series_num', $num);
        }
    }
}

private function filterByDateRange($request, $query)
{
    if ($request->from_date && $request->to_date) {
        $query->whereBetween('challan_date', [$request->from_date, $request->to_date]);
    }

    if ($request->has('recvdfrom') && $request->has('recvdto')) {
        $query->whereBetween('challan_date', [$request->recvdfrom, $request->recvdto]);
    }
}

private function filterByStatus($request, $query)
{
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
}

private function getCombinedValues($query)
{
    $combinedValues = [];
    $distinctChallanSeries = $query->distinct('challan_series')->pluck('challan_series');
    $distinctChallanSeriesNum = $query->distinct()->pluck('series_num');

    foreach ($distinctChallanSeries as $series) {
        foreach ($distinctChallanSeriesNum as $num) {
            $combinedValues[] = $series . '-' . $num;
        }
    }

    return $combinedValues;
}

private function getDistinctValues($query, $column, $relation)
{
    return $query->distinct($column)->pluck($relation, $column);
}

private function getDistinctCities($userId)
{
    return ReceiverDetails::whereIn('receiver_id', function ($query) use ($userId) {
        $query->select('id')->from('receivers')->where('user_id', $userId);
    })->distinct()->pluck('city');
}

// private function getChallans($userId)
// {
//     $query = Challan::where('sender_id', $userId)
//         ->with([
//             'receiverUser',
//             'receiverDetails',
//             'orderDetails.columns',
//             'sfp',
//             'returnChallan.statuses',
//             'returnChallan.orderDetails.columns'
//         ])
//         ->when($this->receiver_id, function ($query) {
//             $query->where('receiver_id', $this->receiver_id);
//         })
//         ->when($this->status, function ($query) {
//             $query->whereHas('statuses', function ($query) {
//                 $query->where('status', $this->status);
//             });
//         })
//         ->when($this->challan_series, function ($query) {
//             // Assuming $this->challan_series could be in the format "ABC-37"
//             if (strpos($this->challan_series, '-') !== false) {
//                 [$challanSeries, $seriesNum] = explode('-', $this->challan_series, 2);
//                 $query->where('challan_series', $challanSeries)
//                       ->where('series_num', $seriesNum);
//             } else {
//                 // If no '-' is found, filter by challan_series alone
//                 $query->where('challan_series', $this->challan_series);
//             }
//         })
//         ->when($this->from && $this->to, function ($query) {
//             $query->whereBetween('challan_date', [$this->from, $this->to]);
//         })
//         ->when($this->recvdfrom && $this->recvdto, function ($query) {
//             // Adjusted to filter based on returnChallan.created_at
//             $query->whereHas('returnChallan', function ($query) {
//                 $query->whereBetween('created_at', [$this->recvdfrom, $this->recvdto]);
//             });
//         })
//         ->select('challans.*')
//         ->latest();

//     $challans = $query->paginate(30);

//     // Filter out challans with latest status as 'draft' or 'reject'
//     $filteredChallans = $challans->getCollection()->filter(function ($challan) {
//         $latestStatus = $challan->statuses->sortByDesc('created_at')->first();
//         return !in_array(optional($latestStatus)->status, ['draft', 'reject']);
//     });

//     // Replace the items in the paginator with the filtered items
//     $challans->setCollection($filteredChallans);

//     return $challans;
// }
private function buildChallanQuery($userId)
{
    return Challan::where('sender_id', $userId)
        ->with([
            'receiverUser',
            'receiverDetails',
            'orderDetails.columns',
            'sfp',
            'returnChallan.statuses',
            'returnChallan.orderDetails.columns'
        ])
        ->when($this->receiver_id, function ($query) {
            $query->where('receiver_id', $this->receiver_id);
        })
        ->when($this->status, function ($query) {
            $query->whereHas('statuses', function ($query) {
                $query->where('status', $this->status);
            });
        })
        ->when($this->challan_series, function ($query) {
            if (strpos($this->challan_series, '-') !== false) {
                [$challanSeries, $seriesNum] = explode('-', $this->challan_series, 2);
                $query->where('challan_series', $challanSeries)
                      ->where('series_num', $seriesNum);
            } else {
                $query->where('challan_series', $this->challan_series);
            }
        })
        ->when($this->from && $this->to, function ($query) {
            $query->whereBetween('challan_date', [$this->from, $this->to]);
        })
        ->when($this->recvdfrom && $this->recvdto, function ($query) {
            $query->whereHas('returnChallan', function ($query) {
                $query->whereBetween('created_at', [$this->recvdfrom, $this->recvdto]);
            });
        })
        ->select('challans.*')
        ->latest();
}

private function getChallans($userId)
{
    $query = $this->buildChallanQuery($userId);
    $challans = $query->paginate(30);

    $filteredChallans = $challans->getCollection()->filter(function ($challan) {
        $latestStatus = $challan->statuses->sortByDesc('created_at')->first();
        return !in_array(optional($latestStatus)->status, ['draft', 'reject']);
    });

    $challans->setCollection($filteredChallans);

    return $challans;
}


private function organizeData($challans)
{
    $organizedData = collect();
    $totalBalance = 0;

    $chunks = $challans->chunk(10);
    $pool = Pool::create();
    $request = request(); // get the request here

    foreach ($chunks as $chunk) {
        $pool->add(function () use ($chunk, $request) { // pass $request to the closure
            $chunkData = collect();
            $totalBalanceChunk = 0;

            foreach ($chunk as $challan) {
                $latestStatus = $challan->statuses->sortByDesc('created_at')->first();
                $challanStatus = optional($latestStatus)->status;
                if (in_array($challanStatus, ['draft', 'reject'])) {
                    continue;
                }

                $receiver = $challan->receiver;
                $sentDate = $challan->created_at->format('Y-m-d');
                $challanNo = $challan->challan_series . '-' . $challan->series_num;

                foreach ($challan->orderDetails as $orderDetail) {
                    $articleSent = optional($orderDetail->columns->first())->column_value;
                    $qtySent = $orderDetail->qty;
                    $balance = $orderDetail->remaining_qty;
                    $totalBalanceChunk += $balance;

                    $organizedDataItem = [
                        'Challan Id' => $orderDetail->id,
                        'Receiver' => $receiver,
                        'Sent Date' => $sentDate,
                        'Challan No.' => $challanNo,
                        'Article' => $articleSent,
                        'QTY Sent' => $qtySent,
                        'Challan Status' => $challanStatus,
                        'Margin QTY' => $orderDetail->margin,
                        'Balance' => $balance,
                    ];

                    $hasReturnChallan = false;

                    foreach ($challan->returnChallan as $returnChallan) {
                        $returnLatestStatus = $returnChallan->statuses->sortByDesc('created_at')->first();
                        $returnChallanStatus = optional($returnLatestStatus)->status;

                        $recvdChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;

                        foreach ($returnChallan->orderDetails as $returnOrderDetail) {
                            $returnArticle = optional($returnOrderDetail->columns->first())->column_value;
                            if ($articleSent !== $returnArticle) {
                                continue;
                            }

                            $recvdDate = $returnOrderDetail->created_at->format('Y-m-d');
                            if ($request->has('recvdfrom') && $request->has('recvdto')) {
                                if ($recvdDate < $request->recvdfrom || $recvdDate > $request->recvdto) {
                                    continue;
                                }
                            }

                            $recvdQty = $returnOrderDetail->qty;

                            $returnDataItem = $organizedDataItem;
                            $returnDataItem['Recvd Challan No.'] = $recvdChallanNo;
                            $returnDataItem['RecvArticle'] = $returnArticle;
                            $returnDataItem['Recvd Date'] = $recvdDate;
                            $returnDataItem['Recvd QTY'] = $recvdQty;
                            $returnDataItem['Return Challan Status'] = $returnChallanStatus;
                            $returnDataItem['Margin QTY'] = $orderDetail->margin;
                            $returnDataItem['Balance'] = $balance;
                            $returnDataItem['Action'] = '';

                            $chunkData->push($returnDataItem);
                            $hasReturnChallan = true;
                        }
                    }

                    if (!$hasReturnChallan) {
                        $chunkData->push($organizedDataItem);
                    }
                }
            }

            return ['data' => $chunkData, 'balance' => $totalBalanceChunk];
        })->then(function ($output) use (&$organizedData, &$totalBalance) {
            $organizedData = $organizedData->merge($output['data']);
            $totalBalance += $output['balance'];
        });
    }

    $pool->wait();

    return $organizedData->all();
}



private function getDistinctArticles($type)
{
    $userId = $this->getUserId();

    if ($type == 'sent') {
        return Challan::with('orderDetails.columns')
            ->where('sender_id', $userId)
            ->whereHas('orderDetails', function ($query) {
                $query->whereHas('columns', function ($query) {
                    $query->where('column_name', 'Article');
                });
            })
            ->get()
            ->flatMap(function ($challan) {
                return $challan->orderDetails->flatMap(function ($orderDetail) {
                    return $orderDetail->columns->where('column_name', 'Article')->pluck('column_value');
                });
            })
            ->unique()
            ->values();
    } else if ($type == 'received') {
        return Challan::with('returnChallan.orderDetails.columns')
            ->where('sender_id', $userId)
            ->whereHas('returnChallan.orderDetails', function ($query) {
                $query->whereHas('columns', function ($query) {
                    $query->where('column_name', 'Article');
                });
            })
            ->get()
            ->pluck('returnChallan.*.orderDetails.*.columns.*.column_value')
            ->flatten()
            ->unique()
            ->values();
    }

    return collect();
}

}
