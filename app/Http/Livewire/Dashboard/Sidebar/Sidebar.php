<?php

namespace App\Http\Livewire\Dashboard\Sidebar;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Challan\ChallanController;
use Livewire\Component;

class Sidebar extends Component
{
    public $panelId;
    public $sidenav;

    public $user, $UserDetails, $successMessage, $errorMessage;
    public $template;
    public $activeFeature, $tableTdData;

    public function featureRedirect($template, $activeFeature)
    {
        $this->emit('featureRoute', $template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }
    public function render()
    {
        $request = request();
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->UserDetails = $response->user->plans;
            $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        return view('livewire.side-bar.sidebar');
    }
}
