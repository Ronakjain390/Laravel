<?php

namespace App\Http\Livewire\Admin\Dashboard;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use stdClass;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;

class Allsubusers extends Component
{
    public $allUsers,$validationErrorsJson, $statusCode, $updatedProfileData, $message, $planId, $data, $isChecked,$plans, $subUserData;
    public $selectedCheckboxes = [];
    public $toggleDataset = array(
        'sender' => '',
        'receiver' => '',
        'seller' => '',
        'buyer' => '',
    );
    public function mount()
    {
        // dd('ash');
        $request = request();
        // dd($request);
        $allUsers = new TeamUserController;
        $columnsResponse = $allUsers->allTeamUsers($request);
        // dd($columnsResponse);
        $this->subUserData = json_encode($columnsResponse->getData()->data);
       
        // dd($this->companyLogoData);
        // $this->updateCompanyData();

    }

    public function render()
    {
        // dd('jsdn');
        return view('livewire.admin.dashboard.allSubUsers.allSubUsers');
    }
}