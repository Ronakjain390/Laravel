<?php

namespace App\Http\Livewire\Buyer\Sidebar;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Events\Panel\FeatureRouteEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Seller\SellerController;
class Sidebar extends Component
{
    public $panelId;
    public $sidenav;

    public $user, $UserDetails, $successMessage, $errorMessage;
    public $template;
    public $activeFeature;

    public function featureRedirect($template, $activeFeature)
    {
        $panel_id = 4;
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
        $this->emit('featureRoute', $template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
    public function mount(Request $request)
    {
        // $UserResource = new UserAuthController;
        // $response = $UserResource->user_details($request);
        // $response = $response->getData();
        // if ($response->success == "true") {
        //     $this->UserDetails = $response->user->plans;
        //     $this->user = json_encode($response->user);
        //     $this->successMessage = $response->message;
        //     $this->reset(['errorMessage']);
        // } else {
        //     $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        // }
        // $query = new UserAuthController;
        // $response = $query->userActivePlan($request);
        // $response = $response->getData();
        // if ($response->success == "true") {
        //     $this->activePlan = json_encode($response->user);
        //     // dd($this->activePlan);
        //     $this->successMessage = $response->message;
        //     $this->reset(['errorMessage']);
        // } else {
        //     $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        // }
        // return $this->UserDetails;
    }
    public function render()
    {
        $request = request();
        $UserResource = new UserAuthController;
        $response = $UserResource->features($request, '4');
        $response = $response->getData();
        $templates = $response->features;
        // dd($this->tableTdData);
        return view('livewire.buyer.sidebar.sidebar', [
            'templates' => $templates, 
        ]);
    }
}
