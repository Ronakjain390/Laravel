<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class activeplans extends Component
{
    public $activePlan,$companyLogoData, $errorMessage,$successMessage, $index;

    public function mount(Request $request)
    {
        $query = new UserAuthController;
        $response = $query->userActivePlan($request);
        $response = $response->getData();
        // dd($response);
        if ($response->success == "true") {
            $this->activePlan = json_encode($response->user);
            // $this->user = json_encode($response->user);
            $this->successMessage = $response->message;
            $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
    }


    public function render()
    {
        return view('livewire.setting.screens.activePlans');
    }
}
