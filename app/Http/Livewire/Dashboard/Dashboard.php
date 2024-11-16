<?php

namespace App\Http\Livewire\Dashboard;

use stdClass;
use Livewire\Component;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\InvoiceStatus;
use App\Models\ReturnChallanStatus;
use App\Models\GoodsReceiptStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;

class Dashboard extends Component
{
    public $user, $teamUser, $UserDetails;
    public $panel, $successMessage, $errorMessage, $sentCount, $expiredPlan, $activeUsers;
    private $challanController;
    private $returnChallanController;


    public function mount(Request $request)
    {
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        // dd($response->user);
        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            // dd($this->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        session()->forget('persistedTemplate');
         // Call the userExpiredPlan method
         $this->userExpiredPlan();
         $this->userActivePlan();
        return $this->UserDetails;
    }
    public function userExpiredPlan()
    {
        $request = request();
        $expiredPlan = new UserAuthController;
        $response = $expiredPlan->userExpiredPlan($request);
        $response = $response->getData();
        // dd($response);
        if ($response->success == "true") {
            // $this->UserDetails = $response->user->plans;
            // dd($this->UserDetails);
        $this->expiredPlan = $response->user->plans_expired;

        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
    }


public function userActivePlan()
{
    $request = request();
    $activePlan = new UserAuthController;
    $response = $activePlan->userActivePlan($request);
    $response = $response->getData();

    if ($response->success == "true") {
        $this->activeUsers = json_decode(json_encode($response->user), true);
    } else {
        $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
    }
}





public function panelRedirect($panel_id)
{
    // dd($panel_id);
    $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
        $item = (object) $item;
        return $item->panel_id == $panel_id;
    });

    if (!empty($filteredItems)) {
        $item = (object) reset($filteredItems); // Get the first item

        // Check if the panel_id is 5 and set the panel_name accordingly
        if ($panel_id == 5) {
            $item->panel['panel_name'] = 'goods-receipt-note';
        }

        $this->panel = $item->panel;

        // Redirect to the panel route with the panel name as a query parameter
        return redirect()->to(strtolower($item->panel['panel_name']))->with('panel', $this->panel);
    }
}


    public function featureRedirect($template, $activeFeature, $panel_id)
    {
        // dd($panel_id, $template, $activeFeature);
        $filteredItems = array_filter($this->UserDetails, function ($item) use ($panel_id) {
            $item = (object) $item;
            return $item->panel_id == $panel_id;
        });

        if($panel_id == 1){
            return redirect()->route('sender', ['template' => $template]);
        }elseif($panel_id == 2){
            return redirect()->route('receiver', ['template' => $template]);
        }elseif($panel_id == 3){
            return redirect()->route('seller', ['template' => $template]);
        }elseif($panel_id == 4){
            return redirect()->route('buyer', ['template' => $template]);
        }elseif($panel_id == 5){
            return redirect()->route('grn', ['template' => $template]);
        }

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
        // return redirect()->route('seller');
    }

    public function handleFeatureRoute($template, $activeFeature)
    {
        // dd($template, $activeFeature);
        $viewPath = 'components.panel.' . $this->panel['panel_name'] . '.' . $template;
        // dd($viewPath);
        $this->persistedTemplate = view()->exists($viewPath) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists($viewPath) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);


        switch ($template) {
            case 'sent_challan':
                return redirect()->route('sender', ['template' => $this->persistedTemplate]); // replace with your actual route name
            case 'sent_return_challan':
                return redirect()->route('receiver'); // replace with your actual route name
            case 'sent_invoice':
                return redirect()->route('seller'); // replace with your actual route name
            case 'all_invoice':
                return redirect()->route('buyer'); // replace with your actual route name
            case 'received_challan':
                return redirect()->route('sender', ['template' => $this->persistedTemplate]); // replace with your actual route name
            case 'received_return_challan':
                return redirect()->route('receiver'); // replace with your actual route name
            case 'purchase_order_seller':
                return redirect()->route('seller'); // replace with your actual route name
            case 'purchase_order':
                return redirect()->route('buyer'); // replace with your actual route name

        }
    }

    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    // private function isMobileUserAgent($userAgent)
    // {
    //     // Define an array of mobile device identifiers
    //     $mobileIdentifiers = [
    //         'Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone'
    //     ];

    //     // Check if the User-Agent contains any of the mobile identifiers
    //     foreach ($mobileIdentifiers as $identifier) {
    //         if (strpos($userAgent, $identifier) !== false) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }


    public $sentChallan , $receivedChallan, $sentReturnChallan, $receiverDatas,   $receivedCount, $rsentCount,   $invoiceData, $sentInvoiceCount, $allInvoice, $receiverSentCount,  $seriesNoData;
    public function render()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    // Optimized subquery using Eloquent, assuming 'ChallanStatus' is an Eloquent model
    $subqueryChallan = ChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('challan_id');

    // Main query optimized for readability and efficiency for 'sent' status
    $sentChallanCounts = DB::table('challan_statuses as cs')
        ->joinSub($subqueryChallan, 'latest_statuses', function ($join) {
            $join->on('cs.challan_id', '=', 'latest_statuses.challan_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'sent')
        ->count();
    // Adding query for 'draft' status
    $draftChallanCounts = DB::table('challan_statuses as cs')
        ->joinSub($subqueryChallan, 'latest_statuses', function ($join) {
            $join->on('cs.challan_id', '=', 'latest_statuses.challan_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'draft')
        ->count();

    // Adding query for 'received' status
    $receivedChallanCounts = ReturnChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))->groupBy('challan_id');

    $receivedChallanCounts = DB::table('return_challan_statuses as rcs')
        ->joinSub($receivedChallanCounts, 'latest_statuses', function ($join) {
            $join->on('rcs.challan_id', '=', 'latest_statuses.challan_id')
                ->on('rcs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('rcs.user_id', $userId)
        // ->where('rcs.status', 'sent')
        ->count();

    // // Adding query for 'received' status
    // $receivedDraftChallanCounts = DB::table('return_challan_statuses as rcs')
    // ->joinSub($receivedChallanCounts, 'latest_statuses', function ($join) {
    //     $join->on('rcs.challan_id', '=', 'latest_statuses.challan_id')
    //         ->on('rcs.created_at', '=', 'latest_statuses.max_created_at');
    // })
    // ->where('rcs.user_id', $userId)
    // // ->where('rcs.status', 'sent')
    // ->count();

    // Optimized subquery using Eloquent, assuming 'ChallanStatus' is an Eloquent model
    $subqueryInvoice = InvoiceStatus::select('invoice_id', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('invoice_id');

    // Main query optimized for readability and efficiency for 'sent' status
    $sentInvoiceCounts = DB::table('invoice_statuses as cs')
        ->joinSub($subqueryInvoice, 'latest_statuses', function ($join) {
            $join->on('cs.invoice_id', '=', 'latest_statuses.invoice_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'sent')
        ->count();

    // Adding query for 'draft' status
    $draftInvoiceCounts = DB::table('invoice_statuses as cs')
        ->joinSub($subqueryInvoice, 'latest_statuses', function ($join) {
            $join->on('cs.invoice_id', '=', 'latest_statuses.invoice_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'draft')
        ->count();


    // Optimized subquery using Eloquent, assuming 'ReturnChallanStatus' is an Eloquent model
    $subqueryReturn = ReturnChallanStatus::select('challan_id', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('challan_id');

    // Main query optimized for readability and efficiency for 'sent' status
    $sentReturnChallanCounts = DB::table('return_challan_statuses as cs')
        ->joinSub($subqueryReturn, 'latest_statuses', function ($join) {
            $join->on('cs.challan_id', '=', 'latest_statuses.challan_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'sent')
        ->count();

    // Adding query for 'draft' status
    $draftReturnChallanCounts = DB::table('return_challan_statuses as cs')
        ->joinSub($subqueryReturn, 'latest_statuses', function ($join) {
            $join->on('cs.challan_id', '=', 'latest_statuses.challan_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'draft')
        ->count();

        $subqueryPo = DB::table('purchase_order_statuses as pos')
        ->select('purchase_order_id', DB::raw('MAX(pos.created_at) as max_created_at'))
        ->groupBy('purchase_order_id');


        $sentPo = DB::table('purchase_order_statuses as cs')
        ->joinSub($subqueryPo, 'latest_statuses', function ($join) {
            $join->on('cs.purchase_order_id', '=', 'latest_statuses.purchase_order_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'sent')
        ->count();
        // dd($sentPo);

        $draftPo = DB::table('purchase_order_statuses as po')
        ->joinSub($subqueryPo, 'latest_statuses', function ($join) {
            $join->on('po.id', '=', 'latest_statuses.purchase_order_id')
                ->on('po.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('po.user_id', $userId)
        ->where('po.status', 'draft')
        ->count();
        // dd($draftPo);


        $draftPo = DB::table('purchase_order_statuses as cs')
        ->joinSub($subqueryPo, 'latest_statuses', function ($join) {
            $join->on('cs.purchase_order_id', '=', 'latest_statuses.purchase_order_id')
                ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
        })
        ->where('cs.user_id', $userId)
        ->where('cs.status', 'draft')
        ->count();

        // Optimized subquery using Eloquent, assuming 'GoodsReceiptStatus' is an Eloquent model
        $subqueryGoodsReceipt = GoodsReceiptStatus::select('goods_receipt_id', DB::raw('MAX(created_at) as max_created_at'))
        ->groupBy('goods_receipt_id');

        // Main query optimized for readability and efficiency for 'sent' status
        $sentGoodsReceiptCounts = DB::table('goods_receipt_statuses as cs')
            ->joinSub($subqueryGoodsReceipt, 'latest_statuses', function ($join) {
                $join->on('cs.goods_receipt_id', '=', 'latest_statuses.goods_receipt_id')
                    ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
            })
            ->where('cs.user_id', $userId)
            ->where('cs.status', 'sent')
            ->count();
        // Adding query for 'draft' status
        $draftGoodsReceiptCounts = DB::table('goods_receipt_statuses as cs')
            ->joinSub($subqueryGoodsReceipt, 'latest_statuses', function ($join) {
                $join->on('cs.goods_receipt_id', '=', 'latest_statuses.goods_receipt_id')
                    ->on('cs.created_at', '=', 'latest_statuses.max_created_at');
            })
            ->where('cs.user_id', $userId)
            ->where('cs.status', 'draft')
            ->count();


        // $receivedInvoiceCounts = InvoiceStatus::select('invoice_id', DB::raw('MAX(created_at) as max_created_at'))->groupBy('invoice_id');

        // $receivedChallanCounts = DB::table('return_challan_statuses as rcs')
        //     ->joinSub($receivedChallanCounts, 'latest_statuses', function ($join) {
        //         $join->on('rcs.invoice_id', '=', 'latest_statuses.invoice_id')
        //             ->on('rcs.created_at', '=', 'latest_statuses.max_created_at');
        //     })
        //     ->where('rcs.user_id', $userId)
        //     // ->where('rcs.status', 'sent')
        //     ->count();


        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Challan::query()->where('sender_id', $userId)->orderByDesc('id');
            $challans = $query->with(['statuses' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])->count();
            $this->sentChallan = $challans;



        // Challan Received
        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->indexCounts($request);
        $receivedChallan = $tableTdData->getData()->data;
        $this->receivedChallan = $receivedChallan;
        // $this->emit('challanDataReceived', $tableTdData);
        $receivedCount = 0;
        foreach($receivedChallan as $challan) {
            if(!empty($challan->statuses) && $challan->statuses[0]->status == 'sent') {
                $receivedCount++;
            }
        }
        $this->receivedCount = $receivedCount;

        // Receiver Counts
        $request = new request();
        $request->merge(['receiver_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        // $request->merge($this->createChallanRequest);
        $receiverScreen = new ChallanController;
        $response = $receiverScreen->indexCounts($request);
        // dd($response->getData());
        $receivedChallan = $response->getData()->data;
        $this->receivedChallan = count($receivedChallan);

        $rsentCount = 0;
        foreach($receivedChallan as $challan) {
            $lastStatus = ($challan->statuses);

            if(!empty($lastStatus->statuses) && $lastStatus->statuses[0]->status == 'sent') {
                $rsentCount++;
            }
        }
        $this->rsentCount = $rsentCount;


        // dd(count($this->receivedChallan));

        // Receiver Sent Challan Counts
        // request()->replace([]);
        $request = new request();
        // dd($request);
        // $request->merge(['receiver_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        $challanController = new ReturnChallanController();
        $tableTdData = $challanController->returnChallanCounts($request);
        $sentReturnChallan = $tableTdData->getData()->data;
        $this->sentReturnChallan = count($sentReturnChallan);
        $receiverSentCount = 0;
        foreach($sentReturnChallan as $challan) {
            if(!empty($challan->statuses) && $challan->statuses[0]->status == 'sent') {
                $receiverSentCount++;
            }
        }
        $this->receiverSentCount = $receiverSentCount;

        // Seller Invoice Counts
        $request = new request();
        $challanController = new InvoiceController();
        $invoiceData = $challanController->indexCounts($request);
        $invoiceData = $invoiceData->getData()->data;
        // $this->invoiceData = count($invoiceData);
        $sentInvoiceCount = 0;
        foreach($invoiceData as $challan) {
            $lastStatus = ($challan->statuses);
            // if($lastStatus[0]->status == 'sent')
            if(!empty($lastStatus->statuses) && $lastStatus->statuses[0]->status == 'sent') {
                $sentInvoiceCount++;
            }
        }
        $this->sentInvoiceCount = $sentInvoiceCount;

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $query = Invoice::query()->where('seller_id', $userId)->orderByDesc('id');
            $invoices = $query->with(['statuses' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])->count();
            $this->invoiceData = $invoices;

        $request = new request();
        $allBuyer = new BuyersController;
        $response = $allBuyer->index($request);
        $buyerData = $response->getData();
        $this->receiverDatas = $buyerData->data;

        // Buyer Invoice Counts
        $request = new request();
        $request->merge(['buyer_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        // dd($request);
        $receiverController = new InvoiceController;
        $response = $receiverController->indexCounts($request);
        // dd($response);
        $this->allInvoice = $response->getData()->data;

        return view('livewire.dashboard.dashboard', [
            'receivedChallanCounts' => $receivedChallanCounts,
            'sentChallanCounts' => $sentChallanCounts,
            'draftChallanCounts' => $draftChallanCounts,
            'sentInvoiceCounts' => $sentInvoiceCounts,
            'draftInvoiceCounts' => $draftInvoiceCounts,
            'sentReturnChallanCounts' => $sentReturnChallanCounts,
            'draftReturnChallanCounts' => $draftReturnChallanCounts,
            'draftGoodsReceiptCounts' => $draftGoodsReceiptCounts,
            'sentGoodsReceiptCounts' => $sentGoodsReceiptCounts,
            'draftPo' => $draftPo,
            'sentPo' => $sentPo,
        ]);
    }
}
