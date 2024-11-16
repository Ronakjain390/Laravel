<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class Feature extends Component
{
    public $hasseller;
    public $hasbuyer;
    public $hassender;
    public $hasreceipt_note;
    public $hasreceiver;
    public $updateMessage;

    public $dashboardDataset = [
        'sender' => '',
        'receiver' => '',
        'seller' => '',
        'buyer' => '',
    ];

    private $challanController;

    public function navigateToPricing($role)
    {
        Session::put('activeTab', $role);
        return redirect()->route('pricing');
    }

    public function __construct()
    {
        parent::__construct();

        $this->challanController = new UserAuthController;
    }

    public function mount()
    {
        $this->hasseller = (bool) auth()->user()->seller;
        $this->hasbuyer = (bool) auth()->user()->buyer;
        $this->hassender = (bool) auth()->user()->sender;
        $this->hasreceiver = (bool) auth()->user()->receiver;
        $this->hasreceipt_note = (bool) auth()->user()->receipt_note;
        // dd($this->hasreceipt_note, $this->hasreceiver, $this->hassender, $this->hasbuyer, $this->hasseller);
        $this->dashboardDataset = [
            'sender' => $this->hassender,
            'receiver' => $this->hasreceiver,
            'seller' => $this->hasseller,
            'buyer' => $this->hasbuyer,
            'receiptnote' => $this->hasreceipt_note,
        ];
        $request = request();
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        // dd($response->user);
        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            // dd($this->UserDetails);
            $this->successMessage = $response->message;
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        session()->forget('persistedTemplate');
         // Call the userExpiredPlan method
         $this->userExpiredPlan();
         $this->userActivePlan();
        return $this->UserDetails;



    }
    public $Seller, $Sender, $Receiver, $Buyer;
    public function userActivePlan()
    {
        $request = request();
        $activePlan = new UserAuthController;
        $response = $activePlan->userActivePlan($request);
        $response = $response->getData();
        // dd($response);
        if ($response->success == "true") {
            $this->activeUsers = json_decode(json_encode($response->user), true);
            // dd($this->activeUsers);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
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

    public function toggleRole($role)
    {
        // dd($role);
        $property = "has{$role}";
        $this->$property = $this->$property;
        $this->updateUserStatus($role);
    }

    public function updateUserStatus($role)
    {
         $data = [$role => $this->{"has{$role}"}];
        //  dd($data);
        // auth()->user()->update([$role => $this->{"has{$role}"}]);
        $request = request();
        $request->merge($data);
        // $this->updatePanel($request);
        $response = $this->challanController->userDashboardPanel($request);
        $result = $response->getData();
        $this->updateMessage = $result->message;
        $this->emit('userRolesUpdated');
    }

    public function render()
    {
        return view('livewire.dashboard.allFeature.allFeature');
    }
}
